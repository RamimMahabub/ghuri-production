<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GHURI Integration Tester</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans antialiased p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-2">GHURI Integration Tester</h1>
        <p class="text-gray-400 mb-8">Click the button below to run an end-to-end sandbox test of the Flight Search, Fare Validation, and Booking pipeline.</p>

        <button id="run-btn" onclick="runTest()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-colors mb-6">
            Execute Full API Pipeline
        </button>

        <div id="loader" class="hidden text-blue-400 mb-6 animate-pulse">Running test... This may take up to 20-30 seconds.</div>

        <div id="logs-container" class="space-y-4 hidden">
            <!-- Logs will be appended here -->
        </div>
    </div>

    <script>
        async function runTest() {
            document.getElementById('run-btn').disabled = true;
            document.getElementById('run-btn').classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('loader').classList.remove('hidden');
            document.getElementById('logs-container').classList.remove('hidden');
            document.getElementById('logs-container').innerHTML = ''; // clear

            try {
                const response = await fetch('/ajax/integration-test-execute');
                const data = await response.json();

                data.logs.forEach(log => {
                    appendLog(log.step, log.message, log.type, log.data);
                });

            } catch (error) {
                appendLog('Error', error.message, 'error');
            } finally {
                document.getElementById('run-btn').disabled = false;
                document.getElementById('run-btn').classList.remove('opacity-50', 'cursor-not-allowed');
                document.getElementById('loader').classList.add('hidden');
            }
        }

        function appendLog(step, message, type = 'info', data = null) {
            const container = document.getElementById('logs-container');

            let badgeColor = type === 'success' ? 'bg-green-600' :
                             (type === 'error' ? 'bg-red-600' : 'bg-gray-600');

            let html = `
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow-md">
                    <div class="flex items-center mb-2">
                        <span class="text-xs font-bold px-2 py-1 uppercase tracking-wider text-white rounded shadow-sm ${badgeColor}">${step}</span>
                        <span class="ml-3 font-semibold text-gray-200">${message}</span>
                    </div>`;

            if (data) {
                html += `<pre class="mt-3 bg-gray-950 p-4 rounded text-sm text-green-400 overflow-x-auto"><code>${JSON.stringify(data, null, 2)}</code></pre>`;
            }

            html += `</div>`;

            container.innerHTML += html;
        }
    </script>
</body>
</html>
