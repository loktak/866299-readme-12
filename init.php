<?php
session_start();
date_default_timezone_set("Europe/Moscow");
$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once 'functions.php';
require_once 'helpers.php';
require_once 'db_requests.php';

$link = database_conecting('localhost', 'root', 'root', 'readme');

define("PHOTO", 1);
define("VIDEO", 2);
define("TEXT", 3);
define("QUOTE", 4);
define("LINK", 5);
