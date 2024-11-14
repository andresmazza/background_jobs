<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Jobs Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Background Jobs Dashboard</h1>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Run a Job</h2>
            <form action="{{ route('background-jobs.run') }}" method="POST" class="bg-white p-4 rounded shadow">
                @csrf
                <div class="mb-4">
                    <label for="class" class="block mb-1">Class</label>
                    <input type="text" name="class" id="class" required class="w-full p-2 border rounded" value="App\Jobs\ExampleJob">
                </div>
                <div class="mb-4">
                    <label for="method" class="block mb-1">Method</label>
                    <input type="text" name="method" id="method" required class="w-full p-2 border rounded" value="handle">
                </div>
                <div class="mb-4">
                    <label for="params" class="block mb-1">Parameters for example (sleep 5 seconds)</label>
                    <input type="text" name="params" id="params" class="w-full p-2 border rounded" value="5">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Run Job</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h2 class="text-xl font-semibold mb-2">Job Logs</h2>
                <pre class="bg-white p-4 rounded shadow h-64 overflow-auto">{{ implode('', $jobLogs) }}</pre>
            </div>
            <div>
                <h2 class="text-xl font-semibold mb-2">Error Logs</h2>
                <pre class="bg-white p-4 rounded shadow h-64 overflow-auto">{{ implode('', $errorLogs) }}</pre>
            </div>
        </div>
    </div>
</body>
</html>