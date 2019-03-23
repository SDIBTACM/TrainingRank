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
        <header>


            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                        <li class="">
                            <a class="nav-link" href="{{ route('admin.contest.index') }}">
                                {{ __('Contest') }}
                            </a>
                        </li>

                        <li class="">
                            <a class="nav-link" href="{{ route('admin.student.index') }}">
                                {{ __('Student') }}
                            </a>
                        </li>

                        <li class="">
                            <a class="nav-link" href="{{ route('admin.user.index') }}">
                                {{ __('User') }}
                            </a>
                        </li>

                        <li class="">
                            <a class="nav-link" href="{{ route('admin.group.index') }}">
                                {{ __('Group') }}
                            </a>
                        </li>

                    </ul>

                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login_page') }}">{{ __('Login') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Welcome you: {{ is_null(Auth::user()->nickname) ? Auth::user()->nickname : Auth::user()->username }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.contest.index') }}">
                                        {{ __('Manager') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
            </nav>

        </header>

        <main>
            @yield('main')
        </main>

        <footer id="footer">
            <p>© 2018 - SDIBT Step Training - <a href="https://github.com/sdibtacm/steptraining" target="_blank"> Github </a></p>
            <p>run in {{ printf("%.5f", microtime(true) - LARAVEL_START) }} in seconds.</p>
            <p>Built by <a target="_blank" href="https://boxjan.com"> Boxjan </a> &amp; <a href="https://github.com/sdibtacm"> SDIBT ACM Team </a></p>
        </footer>
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
