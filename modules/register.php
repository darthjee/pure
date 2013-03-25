<?php
  /**
   * @file modules/register.php
   * @brief arquivo de direcionamento do modulo de registro de usuarios
   * @todo aumentar as possibilidades de cadastro de usuari (mais campos)
   * @todo adcionar checagem de get e post
   * @todo adcionar criptografia
   * @todo passar o formuliario via ajax
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>


<?php

@require_once('inc/user_class.php');
@require_once('inc/stdinc.php');


$required = array('name', 'surname', 'login', 'pass');

/**
 * ao analisar o parametro action passado na URL, o modulo pode
 * fornecer o formulario de inscricao ou registrar o usuario
 */
switch ($_GET['action'])
{
  case 'form' : include('templates/register.php');
    break;
  case 'new' :
    static $params = array(
      'name' => 'rname',
      'surname' => 'lname',
      'email' => 'email',
      'login' => 'nick',
      'pass' => 'pass'
    );
    /* cehcagem se todos os campos foram preenchidos */
    $reqok = true;
    foreach ($required as $key)
    {
      if ($_POST[$key] === null)
      {
        $reqok = false;
        error_log("falha do registro: falta $key");
        break;
      }
    }
    if ($reqok)
    {
      $vars = kcopy_array($_POST, $params);
      $id_user = User::register($vars, $params);
      echo "novo id =$id_user";
    }
    break;
}

?>