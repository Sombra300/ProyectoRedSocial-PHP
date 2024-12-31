<?php
function getDBConnection(string $dbname, string $username, string $password, string  $host='127.0.0.1') {
    $dsn = 'mysql:dbname='. $dbname .';host='. $host;
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (Exception $ex) {
        return null;
    }
}