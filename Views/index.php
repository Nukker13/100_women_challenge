<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Famous Women</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #2b2b2b;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
        }

        header {
            text-align: center;
            margin-bottom: 28px;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #e0e0e0;
        }

        header p {
            color: #888;
            margin-top: 6px;
            font-size: 0.95rem;
        }

        #game-container {
            width: 100%;
            max-width: 600px;
        }

        /* Stats bar */
        #stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        #timer { color: #93c5fd; letter-spacing: 1px; }
        #count { color: #9ae6b4; }

        /* Progress bar */
        #progress-wrap {
            background: #323232;
            border-radius: 6px;
            height: 8px;
            margin-bottom: 18px;
            overflow: hidden;
        }

        #progress-bar {
            height: 100%;
            background: #9ae6b4;
            border-radius: 6px;
            width: 0;
            transition: width 0.3s ease;
        }

        /* Input row */
        #input-row {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        #name-input {
            flex: 1;
            padding: 12px 16px;
            border-radius: 8px;
            border: 2px solid #4a4a4a;
            background: #3c3c3c;
            color: #e0e0e0;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        #name-input:focus { border-color: #93c5fd; }

        #name-input.flash-green { border-color: #9ae6b4; animation: flash-green 0.5s ease; }
        #name-input.flash-red   { border-color: #fca5a5; animation: flash-red   0.5s ease; }

        @keyframes flash-green {
            0%   { background: #0d2e1a; }
            100% { background: #3c3c3c; }
        }
        @keyframes flash-red {
            0%   { background: #2e0d0d; }
            100% { background: #3c3c3c; }
        }

        button {
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
        }

        button:active { transform: scale(0.97); }
        button:disabled { opacity: 0.45; cursor: not-allowed; }

        #submit-btn {
            background: #93c5fd;
            color: #000;
        }

        #stop-btn {
            background: #323232;
            color: #888;
            border: 1px solid #4a4a4a;
        }

        /* Idle start button */
        #start-wrap {
            text-align: center;
            padding: 40px 0;
        }

        #start-btn {
            background: #93c5fd;
            color: #000;
            padding: 16px 48px;
            font-size: 1.2rem;
            border-radius: 12px;
        }

        /* Feedback message */
        #feedback {
            min-height: 22px;
            font-size: 0.9rem;
            margin-bottom: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        #feedback.ok  { color: #9ae6b4; }
        #feedback.err { color: #fca5a5; }

        /* Spinner */
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #93c5fd;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 6px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Names list */
        #names-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 420px;
            overflow-y: auto;
            padding-right: 4px;
        }

        #names-list::-webkit-scrollbar { width: 5px; }
        #names-list::-webkit-scrollbar-track { background: transparent; }
        #names-list::-webkit-scrollbar-thumb { background: #4a4a4a; border-radius: 4px; }

        .name-entry {
            background: #3c3c3c;
            border: 1px solid #4a4a4a;
            border-radius: 8px;
            padding: 10px 14px;
            animation: slide-in 0.25s ease;
        }

        @keyframes slide-in {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .name-entry .name-label {
            font-weight: 600;
            color: #9ae6b4;
        }

        .name-entry .name-num {
            color: #777;
            font-size: 0.8rem;
            margin-right: 6px;
        }

        .name-entry .name-desc {
            font-size: 0.82rem;
            color: #888;
            margin-top: 3px;
        }

        /* Win banner */
        #win-banner {
            display: none;
            text-align: center;
            padding: 28px;
            background: #3c3c3c;
            border: 2px solid #9ae6b4;
            border-radius: 16px;
            margin-bottom: 20px;
        }

        #win-banner h2 {
            font-size: 1.8rem;
            color: #9ae6b4;
        }

        #win-banner p {
            color: #888;
            margin-top: 8px;
        }

        #win-time {
            font-size: 2rem;
            font-weight: 700;
            color: #93c5fd;
            display: block;
            margin-top: 6px;
        }

        /* Suggestion */
        #name-input.flash-orange { border-color: #fcd34d; animation: flash-orange 0.5s ease; }
        @keyframes flash-orange {
            0%   { background: #2e200a; }
            100% { background: #3c3c3c; }
        }

        #suggestion-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: #1e1500;
            border: 1px solid #6b5300;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #fcd34d;
        }

        #suggestion-row strong { color: #fff; font-weight: 600; }
        .suggestion-btns { display: flex; gap: 6px; flex-shrink: 0; }

        #suggestion-yes-btn {
            background: #fcd34d;
            color: #000;
            padding: 5px 14px;
            font-size: 0.85rem;
        }

        #suggestion-no-btn {
            background: transparent;
            color: #888;
            padding: 5px 12px;
            font-size: 0.85rem;
            border: 1px solid #444;
        }

        /* Hidden helper */
        .hidden { display: none !important; }
    </style>
</head>
<body>

<header>
    <h1>100 Famous Women</h1>
    <p>Name 100 real famous women as fast as you can!</p>
</header>

<div id="game-container">

    <!-- Win banner -->
    <div id="win-banner">
        <h2>You did it! 🎉</h2>
        <p>You named 100 famous women in</p>
        <span id="win-time">00:00.0</span>
        <button onclick="resetGame()" style="margin-top:18px; background:#3f3f5e; color:#e8e8f0;">Play again</button>
    </div>

    <!-- Idle state -->
    <div id="start-wrap">
        <button id="start-btn" onclick="startGame()">Start</button>
    </div>

    <!-- Playing state -->
    <div id="playing-ui" class="hidden">
        <div id="stats">
            <span id="timer">00:00.0</span>
            <span id="count">0 / 100</span>
        </div>
        <div id="progress-wrap"><div id="progress-bar"></div></div>
        <div id="input-row">
            <input type="text" id="name-input" aria-label="Famous woman's name" placeholder="Enter a name…" autocomplete="off" spellcheck="false">
            <button id="submit-btn" onclick="submitName()">Submit</button>
            <button id="stop-btn" onclick="resetGame()">Stop</button>
        </div>
        <div id="feedback"></div>
        <div id="suggestion-row" class="hidden">
            <span>Did you mean <strong id="suggestion-name"></strong>?</span>
            <div class="suggestion-btns">
                <button id="suggestion-yes-btn" onclick="acceptSuggestion()">Yes!</button>
                <button id="suggestion-no-btn" onclick="dismissSuggestion()">No</button>
            </div>
        </div>
        <ul id="names-list"></ul>
    </div>

</div>

<script>
    let timerInterval = null;
    let elapsedMs = 0;
    let accepted = new Set();
    let acceptedCount = 0;
    let pendingSuggestion = null;
    const GOAL = 100;

    // ── Timer ────────────────────────────────────────────────
    function formatTime(ms) {
        const totalTenths = Math.floor(ms / 100);
        const tenths = totalTenths % 10;
        const totalSec = Math.floor(totalTenths / 10);
        const sec = totalSec % 60;
        const min = Math.floor(totalSec / 60);
        return String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0') + '.' + tenths;
    }

    function startTimer() {
        const startedAt = Date.now() - elapsedMs;
        timerInterval = setInterval(() => {
            elapsedMs = Date.now() - startedAt;
            document.getElementById('timer').textContent = formatTime(elapsedMs);
        }, 100);
    }

    function stopTimer() {
        clearInterval(timerInterval);
        timerInterval = null;
    }

    // ── Game state ───────────────────────────────────────────
    function startGame() {
        elapsedMs = 0;
        accepted.clear();
        acceptedCount = 0;
        document.getElementById('names-list').innerHTML = '';
        document.getElementById('count').textContent = '0 / ' + GOAL;
        document.getElementById('progress-bar').style.width = '0%';
        document.getElementById('feedback').textContent = '';
        document.getElementById('feedback').className = '';
        document.getElementById('timer').textContent = '00:00.0';

        document.getElementById('start-wrap').classList.add('hidden');
        document.getElementById('win-banner').style.display = 'none';
        document.getElementById('playing-ui').classList.remove('hidden');

        startTimer();
        document.getElementById('name-input').focus();
    }

    function resetGame() {
        stopTimer();
        elapsedMs = 0;
        accepted.clear();
        acceptedCount = 0;
        dismissSuggestion();

        document.getElementById('playing-ui').classList.add('hidden');
        document.getElementById('win-banner').style.display = 'none';
        document.getElementById('start-wrap').classList.remove('hidden');
    }

    function win() {
        stopTimer();
        const finalTime = formatTime(elapsedMs);
        document.getElementById('playing-ui').classList.add('hidden');
        document.getElementById('win-time').textContent = finalTime;
        document.getElementById('win-banner').style.display = 'block';
    }

    // ── Name submission ──────────────────────────────────────
    function setFeedback(msg, type) {
        const el = document.getElementById('feedback');
        el.className = type;
        el.textContent = msg;
    }

    function flashInput(type) {
        const el = document.getElementById('name-input');
        el.classList.remove('flash-green', 'flash-red', 'flash-orange');
        void el.offsetWidth;
        if (type === 'ok')   el.classList.add('flash-green');
        else if (type === 'warn') el.classList.add('flash-orange');
        else                 el.classList.add('flash-red');
    }

    // ── Suggestion ───────────────────────────────────────────
    function showSuggestion(data) {
        pendingSuggestion = data;
        document.getElementById('suggestion-name').textContent = data.suggestion;
        document.getElementById('suggestion-row').classList.remove('hidden');
        flashInput('warn');
    }

    function dismissSuggestion() {
        pendingSuggestion = null;
        const row = document.getElementById('suggestion-row');
        if (row) row.classList.add('hidden');
    }

    function acceptSuggestion() {
        if (!pendingSuggestion) return;
        const data = pendingSuggestion;
        dismissSuggestion();

        const key = data.suggestion.toLowerCase();
        if (accepted.has(key)) {
            flashInput('err');
            setFeedback('Already entered!', 'err');
            return;
        }

        flashInput('ok');
        setFeedback('✓ ' + data.suggestion + ' accepted!', 'ok');
        accepted.add(key);
        acceptedCount++;
        addToList(acceptedCount, data.suggestion, data.description);
        updateCounter();
        document.getElementById('name-input').value = '';
        document.getElementById('name-input').focus();
        if (acceptedCount >= GOAL) win();
    }

    // ── Shared helpers ───────────────────────────────────────
    function addToList(num, name, description) {
        const li = document.createElement('li');
        li.className = 'name-entry';
        li.innerHTML =
            '<div><span class="name-num">' + num + '.</span>' +
            '<span class="name-label">' + escHtml(name) + '</span></div>' +
            (description ? '<div class="name-desc">' + escHtml(description) + '</div>' : '');
        const list = document.getElementById('names-list');
        list.insertBefore(li, list.firstChild);
    }

    function updateCounter() {
        document.getElementById('count').textContent = acceptedCount + ' / ' + GOAL;
        document.getElementById('progress-bar').style.width = (acceptedCount / GOAL * 100) + '%';
    }

    async function submitName() {
        dismissSuggestion();

        const input = document.getElementById('name-input');
        const name = input.value.trim();

        if (!name) return;

        // Client-side duplicate check
        const key = name.toLowerCase();
        if (accepted.has(key)) {
            flashInput('err');
            setFeedback('Already entered!', 'err');
            input.select();
            return;
        }

        // Disable UI, show spinner
        input.disabled = true;
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('feedback').innerHTML = '<span class="spinner"></span>Checking…';

        const formData = new FormData();
        formData.append('name', name);

        let data;
        try {
            const resp = await fetch('/check-name', { method: 'POST', body: formData });
            data = await resp.json();
        } catch (e) {
            data = { valid: false, reason: 'Network error.' };
        }

        if (data.valid) {
            flashInput('ok');
            setFeedback('✓ ' + data.canonicalName + ' accepted!', 'ok');
            accepted.add(key);
            acceptedCount++;
            addToList(acceptedCount, data.canonicalName, data.description);
            updateCounter();
            input.value = '';
            if (acceptedCount >= GOAL) { win(); return; }
        } else if (data.suggestion) {
            showSuggestion(data);
            setFeedback('', '');
        } else {
            flashInput('err');
            setFeedback(data.reason || 'Not accepted.', 'err');
        }

        // Re-enable
        input.disabled = false;
        document.getElementById('submit-btn').disabled = false;
        input.focus();
    }

    function escHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Allow Enter key to submit
    document.getElementById('name-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') submitName();
    });
</script>
</body>
</html>
