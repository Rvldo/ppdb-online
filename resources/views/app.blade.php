<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'PPDB Online') }}</title>
    @php
        $pengaturan = \App\Models\PengaturanPpdb::current();
        $faviconUrl = $pengaturan->favicon_path ? asset('storage/' . $pengaturan->favicon_path) : null;
        $primary = $pengaturan->warna_primary ?: '#7c3aed';
    @endphp
    @if($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
    @endif
    <meta name="theme-color" content="{{ $primary }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800" style="font-family: 'Inter', system-ui, sans-serif;">
    @inertia
</body>
</html>
