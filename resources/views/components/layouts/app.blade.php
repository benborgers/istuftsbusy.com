<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }}</title>
    <link rel="icon" href="https://emojicdn.elk.sh/🐘">

    @vite(['resources/css/app.css'])

    <link href="https://api.fontshare.com/v2/css?f[]=clash-grotesk@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    @production
        <script async src="https://beamanalytics.b-cdn.net/beam.min.js"
            data-token="dc450d08-0ea1-4a44-8ba3-ef4139b189a1"></script>
    @endproduction

    @fluxAppearance
</head>

<body class="sm:max-w-lg mx-auto px-4 antialiased">
    {{ $slot }}

    @fluxScripts
</body>

</html>
