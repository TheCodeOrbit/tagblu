<?php
define("DB_SERVER","10.199.206.99:3306");
define("DB_DATABASE","deshwal");
define("DB_USER","root");

define("DB_PASS","Root@123");
function db_connect()
{
    $dsn = 'mysql:dbname=' . DB_DATABASE . ';host=' . DB_SERVER;
    $connection = new PDO($dsn, DB_USER, DB_PASS);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}
?>
