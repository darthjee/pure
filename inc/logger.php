<?php
/**
 * @file logger.php
 * @brief contem as funcoes de log para erros e violacoes
 */
@require_once('bd_class.php');
@require_once('user_class.php');
global $BD;

if(!is_object($BD))
  $BD = new bd_class();

/**
 * @brief classe contendo as funcoes de log
 */
class Logger
{
  /**
   * @brief loga uma tentativa de invasao
   */
  function logViolate($tipo, $coment=null, $ip=null, $id_user=null)
  {
    global $BD;
    global $BDconf;
    $debug = $Conf['debug'];

    if (!$coment)
      $coment = $_SERVER['REQUEST_URI'];

    if (!$ip)
      $ip = $_SERVER['REMOTE_ADDR'];

    if (!$id_user)
      $ip_user = User::getUserId();

    $logs = $BD->Tables['logs_violations'];
    $insert="insert into $logs (tipo, ip, coment, id_user) values ('$tipo','$ip','$coment','$id_user')";

    $BD->insert($insert, $debug);

    die('Acesso Restrito');
  }

  /**
   * @brief checa se houve uma tentativa primaria de invasao
   * 
   * as tentativas primaris de invasao (tentativa via request indevida)
   * sao logadas para futuras referencias.
   */
  function checkViolation()
  {
    global $_INDEXED;

    if ( !isset($_INDEXED['ip']) )
    {
      $request = $_SERVER['REQUEST_URI'];
      $ip = $_SERVER['REMOTE_ADDR'];
      $id_user = $_SESSION['id_user'];

      Logger::logViolate('ACS', $request, $ip, $id_user);
    }
  }
}

Logger::checkViolation();
?>
