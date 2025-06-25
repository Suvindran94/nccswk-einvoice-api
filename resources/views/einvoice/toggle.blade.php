<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
</head>

<body class="min-h-screen w-full bg-white flex items-center justify-center">
    <div
        class="border border-gray-200 px-6 py-4 bg-white rounded-xl flex items-start justify-center font-sans min-w-[600px]">
        <div class="mr-5 mt-[1px] text-lg flex items-center justify-center w-7 h-7 rounded-full border border-gray-300">
            <i class="hgi hgi-stroke hgi-time-schedule"></i>
        </div>
        <div class="flex flex-col">
            <h1 class="font-medium text-gray-700">In-Progress E-Invoice Scheduler</h1>
            <span class="text-xs text-gray-400 max-w-[500px]">
                Scheduled to run every minute, this task updates the status of eInvoice documents that remain in
                progress.
            </span>
        </div>
        <div class="flex justify-center my-auto">
            <label class="ml-auto relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle" class="sr-only peer">
                <div
                    class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors duration-200">
                </div>
                <div
                    class="absolute left-0.5 top-0.5 w-5 h-5 bg-white border border-gray-300 rounded-full transition-transform duration-200 peer-checked:translate-x-5">
                </div>
            </label>
        </div>

    </div>
</body>

</html>
