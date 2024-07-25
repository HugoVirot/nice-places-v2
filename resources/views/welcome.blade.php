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
    <script src="https://kit.fontawesome.com/6259f9b52f.js" crossorigin="anonymous"></script>
    <!-- leaflet css -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- leaflet js -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="app"></div> <!-- le front-end Vue JS est injecté ici -->
</body>

</html>
