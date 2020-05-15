<?php
//class A{
//    function redis(){
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);


        $dsn = 'mysql:host=localhost;dbname=shix';
        $username = 'root';
        $password = 'root';
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $pdo = new PDO($dsn, $username, $password, $options);
//    }
//}