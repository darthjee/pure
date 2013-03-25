<?php
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php
@require_once('conf.php');
@require_once('bd_class.php');
/* loading hack configuration */
if($Conf['hack'] == true)
  @require_once('engines/hack.php');
@require_once('logger.php');
@require_once('stdinc.php');


@require_once('menu_class.php');
@require_once('lang_class.php');
@require_once('user_class.php');
@require_once('modulo_class.php');
?>
