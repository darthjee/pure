<?php
  /**
   * @file modules/login.php
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>
<?php
include('templates/login.php');
?>