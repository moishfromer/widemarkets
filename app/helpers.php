<?php

function old($fn) {
    return isset($_REQUEST[$fn]) ? $_REQUEST[$fn] : '';
}

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PW', '');
define('MYSQL_DB', 'widemarkets');



function csrf_token() {
    $token = sha1('widemarkets' . rand(1, 1000) . time());
    $_SESSION['token'] = $token;
    return $token;
}

function my_session_start($name = null){
    session_set_cookie_params(60 * 60 * 24 * 30);
    
    if( ! is_null($name) ) session_name($name);
    
    session_start();
    session_regenerate_id();
}
