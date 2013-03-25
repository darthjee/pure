<?php

/**
 * @file conf.php
 * Configuracao e inicializacao do componente
 *
 * Aqui sao inicializados os arrays e objetos
 * de configuracao
 */
?>

<?php

/*************
 Arrays
 *************/
/** @brief Array com campos de configuracao para
 * o banco de dados */
global $BDconf;
/** @brief Array com a configuracao da pagina (diretorios)  */
global $PGconf;
/** @brief Array com a configuracao geral */
global $Conf;

/*************
 Objetos
 *************/
/** @brief Objeto banco de dados */
global $BD;
/** @brief Objeto usuario */
global $PUser;

   
$PGconf=&$BDconf;
$Conf=&$BDconf;
/*************
 Arrays
**************/
$BDconf['pass'] = 'rb23dcCBk3VRYac';
$BDconf['user'] = 'rpg';
$BDconf['host'] = 'localhost';
$BDconf['banco'] = 'favini';
$BDconf['tnames'] = 'pure_tables';

$BDconf['debug'] = false;
$BDconf['hack'] = false;


?>

<?php
  /* codigo de checagem de acesso nao autorizado, chamado no final neste arquivo */
  if (!class_exists('Logger'))
  {
    if (class_exists('bd_class'))
      @require_once('../inc/logger.php');
    else
      @require_once('../inc/bd_class.php');
  }
?>
