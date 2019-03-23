<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | ': ''}} {{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/app.js') }}"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>
<div id="app">
    <div class="container-fluid">
        
        <main>
            @yield('main')
        </main>


    </div>
</div>

</body>



@yield('script')
<script>
    if (typeof data == "undefined") {
        data = {};
    }
    let vue = new Vue({
        mixins: [data],
        el: '#app',
        data: function() {
            return {

            };
        },
        methods: {

        }
    })
</script>
</html>
