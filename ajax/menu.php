<?php
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php

if (array_key_exists('id',$_GET))
  $id_menu = $_GET['id'];
else
  $id_menu = 0;
  
if (Menu::checkAuth($id_menu))
  Menu::print_all_menus($id_menu);

?>
