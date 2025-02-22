<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }}</title>
    <link rel="icon" href="https://emojicdn.elk.sh/🐘">

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @vite(['resources/css/app.css'])
    @fluxAppearance
</head>

<body>
    {{ $slot }}

    @fluxScripts
</body>

</html>
