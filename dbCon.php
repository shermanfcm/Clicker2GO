<?php
include('config.inc.php');
require_once 'idiorm.php';
ORM::configure('mysql:host=$database_host;dbname=$group_dbnames[0]');
ORM::configure('username', $database_user);
ORM::configure('password', $database_pass);
ORM::configure('return_result_sets', true);
?>
