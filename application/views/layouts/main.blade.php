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
                    <a class="brand" href="home">Dumplings the Restaurant</a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            @section('navigation')
                            <li class="active"><a href="home">Home</a></li>
                            @yield_section
                        </ul>
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