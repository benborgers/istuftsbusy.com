<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }}</title>
    <link rel="icon" href="https://emojicdn.elk.sh/🐘">

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @vite(['resources/css/app.css'])

    <link href="https://api.fontshare.com/v2/css?f[]=clash-grotesk@400,500,600,700&display=swap" rel="stylesheet">
</head>

<body class="sm:max-w-lg mx-auto p-8 antialiased">
    {{ $slot }}

    @fluxScripts
</body>

</html>
