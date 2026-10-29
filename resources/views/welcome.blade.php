<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HERA v3 - {{ $currentUser['role_name'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.HERA_CURRENT_USER = {!! json_encode($currentUser, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
    </script>
</head>
<body class="min-h-full font-sans text-slate-800 antialiased overflow-hidden">
    <!-- Super Administrator / Tenant Administration Console -->
    <div id="admin-settings-mount"></div>
</body>
</html>
