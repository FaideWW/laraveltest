<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Test</title>
        {{ Asset::styles() }}
        {{ Asset::scripts() }}
    </head>
 
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="home">Dumplings</a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            @foreach ($navigation as $n)
                            <li @if ($n['url'] == $active) class="active" @endif>
                                <a href="{{ $root_path . $n['url'] }}">{{ $n['name'] }}</a>
                            </li>
                            @endforeach
                        </ul>
                        @if (Auth::check() && Auth::user()->lvl == 1)
                        <ul class="nav pull-right">
                            <li @if ($active == 'admin') class="active" @endif>
                                <a href="{{ $root_path }}admin">Admin</a>
                            </li>
                        </ul>
                        @endif
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
 
        <div class="container">
            @yield('content')
            <hr>
            <footer>
            <p>&copy; faide 2012</p>
            </footer>
        </div> <!-- /container -->
    </body>
</html>