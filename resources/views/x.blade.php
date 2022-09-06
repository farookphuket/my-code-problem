<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

<!-- google login START -->
<script src="https://apis.google.com/js/platform.js" async defer></script>
<meta name="google-signin-client_id" content="250943952533-r0frvqehp0nuav0ng4omape6tptcp0p2.apps.googleusercontent.com">
<!-- google login END -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="{{asset('img/eye_14-apr-2022.ico')}}" rel="icon">
        <title>see southern the great business solution for a real man like you</title>


<?php 
$data = [
    "laravel_version" => Illuminate\Foundation\Application::VERSION,
    "php_version" => PHP_VERSION
    ];
?> 
        @if(Session::has('USER_HAS_ACTIVATION_TOKEN'))
<?php
        $data["activation_token"] = Session::get("USER_HAS_ACTIVATION_TOKEN");
?>
        @endif
        @auth
<?php
    $data["user_id"] = Auth::user()->id;
    $data["USER_IS_LOGIN"] = true;
?>
            @if(Session::has('USER_IS_ADMIN'))
                <?php $data["is_root"] = Session::get("USER_IS_ADMIN"); ?>
            @endif
            @if(Session::has('USER_IS_MEMBER'))
                <?php $data["is_member"] = Session::get("USER_IS_MEMBER"); ?>
            @endif
        @endauth

        <script>
            window.lsDefault = @json($data)
        </script>


    </head>
    <body >

        @yield('content')
        <div id="app"></div>

    </body>
</html>
