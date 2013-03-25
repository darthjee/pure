<?php
  /**
   * @file ajax/main.php
   * @brief engine de analise de requisicoes ajax
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php

/**
 * Quando houver uma requisicao de formulario,
 * esta deve ser passada atraves do objeto
 * body
 */
if (array_key_exists('form', $_GET) && $_GET['form']=='true')
{
  ?>
    <html>
      <body>
  <?php
}

/**
 * toda requsicao de ajax refere-se a um modulo,
 * e este eh o primeiro ponto a ser analisado
 */
if (array_key_exists('id_mod',$_GET))
  $id_modulo = $_GET['id_mod'];
if (!isset($id_modulo) && array_key_exists('mod',$_GET))
  $modulo = $_GET['mod'];
else if (isset($id_modulo))
  $modulo = $id_modulo;
else
  $modulo = 0;

/** apenas modulos que sao do tipo ajax podem ser retornados */
if(Modulo::isAjax($modulo))
{
  if (!isset($id_moduo))
    $id_modulo = Modulo::getId($modulo);

  if (Modulo::checkAuth($id_modulo))
    include(Modulo::getPage($id_modulo));
}
/** quando o modulo nao eh do tipo ajax, um log eh gerado */
else
  Logger::logViolate('MOD');

if (array_key_exists('form', $_GET) && $_GET['form']=='true')
{
  ?>
      </body>
    </html>
  <?php

}

?>
