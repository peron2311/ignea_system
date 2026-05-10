<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ignea System | Modern Infrastructure</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
</head>

<body>
    <div class="bg-gradient"></div>
    <div class="glow" style="top: 0%; left: 0%;"></div>
    <div class="glow" style="bottom: 0%; right: 0%;"></div>

    <div class="container">
        <nav>
            <a href="/" class="logo-container">
                <img src="{{ asset('img/logo1.png') }}" alt="Ignea Logo" class="logo-img"
                    style="width: 80px; height: 80px; object-fit: contain;">
            </a>
            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 8px 16px;">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <header class="hero">
            <h1>Elevando o Padrão de <br><span style="color: var(--primary)">Sistemas Inteligentes</span></h1>
            <p>Bem-vindo ao Ignea System. Uma plataforma completa, segura e construída com as tecnologias mais modernas
                para transformar sua visão em realidade.</p>

            <div class="cta-group">
                <a href="{{ route('register') }}" class="btn btn-primary">Começar Agora</a>
                <a href="#features" class="btn btn-secondary">Saiba Mais</a>
            </div>
        </header>

        <section id="features" class="features">
            <div class="precision-card">
                <h5 class="precision-heading-5">Ignea</h5>
                <p>Construído sobre o framework PHP mais elegante do mundo, garantindo escalabilidade e
                    manutenibilidade.</p>
            </div>
            <div class="precision-card">
                <h5 class="precision-heading-5">Segurança Nativa</h5>
                <p>Sistema de autenticação integrado com Laravel Breeze, protegendo seus dados desde o primeiro dia.</p>
            </div>
            <div class="precision-card">
                <h5 class="precision-heading-5">Design Premium</h5>
                <p>Interface focada na experiência do usuário, com estética moderna, animações fluidas e modo claro
                    nativo.</p>
            </div>
        </section>
    </div>
</body>

</html>