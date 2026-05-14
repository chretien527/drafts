<?php

define("DB_HOST","localhost");
define("DB_USER","root");
define("DB_PASS","2010");
define("DB_NAME","user_app");

$conn = new mysqli(DB_HOST,DB_USER,DB_PASS);

if(!$conn->connect_error){
    die("Connection Failed".$conn->connect_error);
}

$conn->query("CREATE DATABASE".DB_NAME);
$conn->select_db(DB_NAME);

$conn->query ("

create database if not exists users(

id int primary key not null,
name varchar(100) not null,
email varchar(100) not null unique,
gender enum('male', 'female', 'other') not null,
password varchar(100) not null

)

");

?>