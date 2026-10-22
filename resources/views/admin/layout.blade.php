<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | Prisma Dana Gorontalo</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body>
<header class="topbar">
    <a href="{{ route('admin.index') }}" class="brand"><img src="{{ asset('images/prisma-dana-gorontalo.png') }}" alt="Bank Prisma Dana Gorontalo" width="6815" height="1854"><span>ADMIN</span></a>
    @if(auth()->user()?->is_admin)
        <div class="account"><span>{{ auth()->user()->name }}</span><form method="post" action="{{ route('admin.logout') }}">@csrf<button class="button secondary" type="submit">Keluar</button></form></div>
    @endif
</header>
<main class="page @yield('page-class')">
    @if($errors->any())
        <div class="alert" role="alert"><strong>Periksa kembali isian Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
</body>
</html>
