<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Test</title>
        {{ Asset::styles() }}
        {{ Asset::scripts() }}
        <!-- Enable bootstrap-responsive -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
 
    <body @yield('body-props') >
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="home">Dumplings</a>
                    <!-- Login form -->
                        <ul class="nav pull-right">
                        @if (!Auth::check())
                            {{ Form::open('user/authenticate', 'post', array('class' => 'navbar-form')) }}
                                {{ Form::email('email', null, array('placeholder' => 'Email', 'class' => 'input-medium')) }}
                                {{ Form::password('password', array('placeholder' => 'password', 'class' => 'input-medium')) }}
                                {{ Form::submit('Login', array('class' => 'btn')) }}
                            {{ Form::close() }}
                        @else
                            <li>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-primary"><i class="icon-user icon-white"></i> {{ Auth::user()->name }}</a>
                                    <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href=""><i class="icon-heart"></i> Favorites</a></li>
                                        <li><a href=""><i class="icon-cog"></i> Account</a></li>
                                        <li><a href=""><i class="icon-off"></i> Logout</a></li>
                                        @if (Auth::user()->lvl == 1)
                                            <li class="divider"></li>
                                            <li><a href="#"><i class="i"></i> Admin</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        </ul>
                    <div class="nav-collapse">
                        <ul class="nav">
                            @foreach ($navigation as $n)
                            <li @if ($n['url'] == $active) class="active" @endif>
                                <a href="{{ $root_path . $n['url'] }}">{{ $n['name'] }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

        @yield('masthead')
 
        <div class="container">
            @yield('notifications')
            @yield('content')
            <hr>
            <footer>
            <p>&copy; faide 2013</p>
            </footer>
        </div> <!-- /container -->
        @yield('pagescripts')
    </body>
</html>