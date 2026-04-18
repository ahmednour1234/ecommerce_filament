<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة تحكم مكتب الاستقدام')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { background-color: #eef2f7; }
        .stat-card { transition: box-shadow .2s; }
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    </style>
</head>
<body class="min-h-screen">
    @yield('content')
</body>
</html>
