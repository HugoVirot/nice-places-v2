<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nice Places | Sorties Nature près de chez vous</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/icons/favicon.png') }}">
    <meta name="description"
        content="Découvrez de nouvelles sorties nature avec Nice Places  : forêts, plages, rivières, randonnées, parcs naturels, etc. 
        Partagez aussi vos lieux préférés. L'aventure vous attend !">
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css"> --}}
    <script src="https://kit.fontawesome.com/6259f9b52f.js" crossorigin="anonymous"></script>
    <!-- leaflet css -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <!-- leaflet js -->
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="app"></div> <!-- le front-end Vue JS est injecté ici -->
</body>

</html>
