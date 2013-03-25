<?php
  /**
   * @file modulo_class.php
   * @brief Classe @ref Modulo
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php
@require_once('bd_class.php');
@require_once('user_class.php');
@require_once('stdinc.php');


/**
 * @brief Conten o contlrole dos modulos
 */
class Modulo
{

  /**
   * @brief verifica se um modulo existe
   */
  static function isModule($name)
  {
    global $BD;
    $table = $BD->Tables['modulos'];

    $query = "select id_modulo from $table where name='$name' or id_modulo='$name' limit 1";

    if ($BD->query_to_val($query))
      $ret = true;
    else
      $ret = false;

    return $ret;
  }

  /**
   * @brief verifica se um modulo tem ajax
   */
  static function isAjax($name)
  {
    global $BD;
    $table = $BD->Tables['modulos'];

    $query = "select id_modulo from $table where (name='$name' or id_modulo='$name') and ajax='S' limit 1";

    if ($BD->query_to_val($query))
      $ret = true;
    else
      $ret = false;

    return $ret;
  }


  /**
   * @brief recupera o id de um modulo
   */
  static function getId($name)
  {
    global $BD;
    $table = $BD->Tables['modulos'];

    $query = "select id_modulo from $table where name='$name' limit 1";

    $id = $BD->query_to_val($query);

    return $id;
  }

  /**
   * @brief recupera a pagina de um modulo
   */
  static function getPage($id_modulo)
  {
    global $BD;
    $table = $BD->Tables['modulos'];

    $query = "select page from $table where id_modulo=$id_modulo limit 1";
    
    $page = $BD->query_to_val($query);

    return $page;
  }

  /**
   * @brief checa se um usuario tem autorizacao sobre um modulo
   *
   * A checagem chama @ref Modulo::checkAuth_type varias vezes,
   * cada uma chacando de forma diferente.
   */
  static function checkAuth($id_mod, $id_user=null)
  {
    /* checagem do id do usuario */
    if (!$id_user)
      $id_user = User::getUserId();
      
    $return = Modulo::authByType($id_mod, $id_user, DEN_GRP);
    if (!$return)
    {
      $return = Modulo::authByType($id_mod, $id_user, OPEN_GRP);
      if (!$return)
        $return = Modulo::authByType($id_mod, $id_user, EXCL_GRP);
    }
    else
      $return = Modulo::authByType($id_mod, $id_user, EXCL_GRP);

    return $return;
  }

  /**
   * @brief checa se um usuario tem autorizacao sobre um menu
   * 
   * Para a checagem, o sistema busta todos os grupos
   * de um menu e checa se os grupos do usuario batem
   *
   * @param $id_mod : id do modulo
   * @param $id_user : id d usuario
   * @param $type : tipo da busca
   */
  static function authByType($id_mod, $id_user=null, $type=OPEN_GRP, $debug=null)
  {
    global $Conf;

    if ($debug === null)
      $debug = $Conf['debug'];

    if ($debug)
    {
      unset($dvars);
      $dvars['Id Modulo'] = $id_mod;
      $dvars['Id User'] = $id_user;
      $dvars['Type'] = $type;
      $pre = "Modulo Class:<br />\nAuth By Type: <br />";
      openDebug($pre, $dvars, "");
    }

    if (!$id_user)
      $id_user = User::getUserId();

    $return = false;
    
    $Grupos = Modulo::getGrupos($id_mod,$type, $debug);

    if ($Grupos)
    {
      if ($type == OPEN_GRP)
        $return = User::checkAuth($Grupos, $id_user, FULL_LIST);
      else
        $return = User::checkAuth($Grupos, $id_user, SHORT_LIST);
    }
    else if (User::checkAuth(1, $id_user))
      $return = true;
    if ($debug)
    {
      unset($dvars);
      $dvars['Id_user'] = $id_user;
      $dvars['Auth:'] = $type;
      $dvars['Id Modulo'] = $id_mod;
      if ($return)
        $pre = "Permitido <br/>";
      else
        $pre = "Nao Permitido <br/>";
      closeDebug($pre, $dvars);
    }
   
    return $return;
  }


  /**
   * @brief pega todos os grupos ao qual um modulo pertence
   */
  static function getGrupos($id_mod, $auth=OPEN_GRP, $debug=null)
  {
    global $BD;
    global $Conf;

    if ($debug === null)
      $debug = $Conf['debug'];

    $table = $BD->Tables['mods_grupos'];
    $query = "select id_grupo from $table where id_mod=$id_mod and auth='$auth' order by id_grupo desc";

    if ($debug)
    {
      openDebug();
      $Grupos = $BD->query_to_vector($query, false);
      if ($Grupos)
      {
        unset($dvars);
        $dvars[] = $Grupos;
      }
      else
        $dvars = null;
      $pre = "Mod Class:<br />\nGet Grupos: <br />";
      closeDebug($pre, $dvars, "");
    }
    else
      $Grupos = $BD->query_to_vector($query, false);

    return $Grupos;
  }
}

?>
