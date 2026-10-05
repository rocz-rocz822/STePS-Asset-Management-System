<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Access Denied</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900">403</h1>
        <p class="mt-2 text-gray-500">You don't have permission to access this page.</p>
        <a href="{{ route('login') }}" class="mt-6 inline-block px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800">
            Back to Login
        </a>
    </div>
</body>
</html>