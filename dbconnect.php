<?php

$_SETTINGS = parse_ini_file("config.ini");

$mysqli = mysqli_connect($_SETTINGS['DBHOST'], $_SETTINGS['DBUSER'], $_SETTINGS['DBPASS'], $_SETTINGS['DBNAME']);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>