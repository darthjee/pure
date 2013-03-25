<?php
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>


<?Php

/* hack para escolher o id do usuario */
if ($_GET['hack_iduser'] !== null && $_SESSION['id_user'] != $_GET['hack_iduser'])
{
  $_SESSION['id_user'] = $_GET['hack_iduser'];
  $_SESSION['user'] = null;
}
?>
