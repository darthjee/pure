<?php
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php

@require_once('bd_class.php');

class Lang
{
  /**
   * @brief retorna o id do proximo idioma da
   * lista de preferencias
   * @todo refazer tudo
   */
  function getNextLang($id_lang=null)
  {
    global $BD;

    if ($id_lang != 1)
      $id_lang = 1;
    else
      $id_lang = -1;
    return $id_lang;
  }

  function countLangs()
  {
    return 20;
  }
}
?>
