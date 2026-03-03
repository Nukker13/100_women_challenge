<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GameController
{
    public function index(): string
    {
        return (string) View::make('index');
    }

    public function checkName(): void
    {
        header('Content-Type: application/json');

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            echo json_encode(['valid' => false, 'reason' => 'Please enter a name.']);
            return;
        }

        $client = new Client([
            'timeout' => 8,
            'headers' => ['User-Agent' => '100FamousWomenGame/1.0 (educational project)'],
        ]);

        try {
            // Step 1: search Wikidata; fall back to Wikipedia OpenSearch on empty result
            $results = $this->searchWikidata($client, $name);

            if (empty($results)) {
                $wikiData  = json_decode((string) $client->get('https://en.wikipedia.org/w/api.php', [
                    'query' => ['action' => 'opensearch', 'search' => $name, 'limit' => 1, 'namespace' => 0, 'format' => 'json'],
                ])->getBody(), true);
                $wikiTitle = $wikiData[1][0] ?? null;

                if (!$wikiTitle) {
                    echo json_encode(['valid' => false, 'reason' => 'Person not found.']);
                    return;
                }

                $results = $this->searchWikidata($client, $wikiTitle);
            }

            if (empty($results)) {
                echo json_encode(['valid' => false, 'reason' => 'Person not found.']);
                return;
            }

            $top      = $results[0];
            $entityId = $top['id'];
            // If Wikidata matched via an alias (e.g. "Amy Dumas" → label "Lita"),
            // use the alias as the canonical name so the similarity check passes.
            $canonicalName = ($top['match']['type'] ?? '') === 'alias'
                ? ($top['match']['text'] ?? $top['label'] ?? $name)
                : ($top['label'] ?? $name);
            $description   = $top['description'] ?? '';

            // Step 2: fetch all claims in one call (P31 instance-of + P21 gender)
            $allClaims = json_decode((string) $client->get('https://www.wikidata.org/w/api.php', [
                'query' => ['action' => 'wbgetclaims', 'entity' => $entityId, 'format' => 'json'],
            ])->getBody(), true)['claims'] ?? [];

            // P31 must include Q5 (human) — rejects fictional characters, mythological figures, etc.
            $isHuman = false;
            foreach ($allClaims['P31'] ?? [] as $claim) {
                if (($claim['mainsnak']['datavalue']['value']['numeric-id'] ?? null) === 5) {
                    $isHuman = true;
                    break;
                }
            }
            if (!$isHuman) {
                echo json_encode(['valid' => false, 'reason' => 'Not a real person.']);
                return;
            }

            // P21 must be Q6581072 (female)
            $genderClaims = $allClaims['P21'] ?? [];
            if (empty($genderClaims)) {
                echo json_encode(['valid' => false, 'reason' => 'Could not verify gender.']);
                return;
            }
            if (($genderClaims[0]['mainsnak']['datavalue']['value']['numeric-id'] ?? null) !== 6581072) {
                echo json_encode(['valid' => false, 'reason' => 'Not a woman.']);
                return;
            }

            // Anti-exploitation: reject inputs too vague to identify a specific person
            // (e.g. "Pamela" should not match "Pamela Anderson").
            // For alias matches (e.g. "Cleopatra VII" → alias "Cleopatra VII Thea Philopator",
            // label "Cleopatra") also check against the label and take the higher score.
            similar_text(strtolower($name), strtolower($canonicalName), $percent);
            if (($top['match']['type'] ?? '') === 'alias') {
                similar_text(strtolower($name), strtolower($top['label'] ?? ''), $percentLabel);
                $percent = max($percent, $percentLabel);
            }

            if ($percent < 65.0) {
                echo json_encode(['valid' => false, 'reason' => 'Name too vague — try entering the full name.']);
                return;
            }

            // Close match but not exact — offer as a suggestion
            if (strtolower($name) !== strtolower($canonicalName)) {
                echo json_encode(['valid' => false, 'suggestion' => $canonicalName, 'description' => $description]);
                return;
            }

            echo json_encode(['valid' => true, 'canonicalName' => $canonicalName, 'description' => $description]);

        } catch (GuzzleException $e) {
            echo json_encode(['valid' => false, 'reason' => 'API error: ' . $e->getMessage()]);
        }
    }

    private function searchWikidata(Client $client, string $term): array
    {
        $response = $client->get('https://www.wikidata.org/w/api.php', [
            'query' => [
                'action'   => 'wbsearchentities',
                'search'   => $term,
                'language' => 'en',
                'type'     => 'item',
                'format'   => 'json',
                'limit'    => 5,
            ],
        ]);
        return json_decode((string) $response->getBody(), true)['search'] ?? [];
    }
}
