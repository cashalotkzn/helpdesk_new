<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<div id="app">
    <section class="container">
        <header-app></header-app>
        <router-view></router-view>
    </section>
</div>
<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
