<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Tracking System</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

<nav class="bg-blue-900 text-white p-4">
    <div class="container mx-auto flex justify-between">
        <span class="font-bold">Document Tracking System</span>
        <span>{{ auth()->user()->name }}</span>
    </div>
</nav>

<main class="container mx-auto p-6">
    @yield('content')
</main>

</body>
</html>
