<?php

global $_INDEXED;
$_INDEXED['ip'] = $_SERVER['REMOTE_ADDR'];

session_start();

require_once('inc/logger.php');
require_once('inc/basic.php');

if (array_key_exists('ajax', $_GET) && $_GET['ajax'] == 'true')
{
  include('ajax/main.php');
}
else
  include('templates/main.php');
?>
