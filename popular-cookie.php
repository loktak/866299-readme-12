<?php

//файл создан потому, что я так и не разобрался как сразу же использовать значение куки. 
//Только после перезагрузки страницы. это связано с тем что куки и записываются и читаются одновременно.
$expire = strtotime("+30 days");
$path = "/";

if (isset($_GET['type'])) {
    setcookie('type', $_GET['type'], $expire, $path);
}

if (isset($_GET['sort_value'])) {
    setcookie('sort_value', $_GET['sort_value'], $expire, $path);
}

if (isset($_GET['sorting'])) {
    setcookie('sorting', $_GET['sorting'], $expire, $path);
}

header('Location: popular.php');