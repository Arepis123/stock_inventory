<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="images/logo-clab.png" sizes="any">
<link rel="icon" href="images/logo-clab.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
<link rel="preload" as="image" href="{{ asset('images/background.jpg') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@fluxAppearance
