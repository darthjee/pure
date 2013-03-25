<?php
  /**
   * @file user_class.php
   * @brief Classe @ref User
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php

/** @brief flag para fazer a busca de todos os subgrupos @see User::getGrupos */
define('FULL_LIST',1);
/** @brief flag para fazer a busca apenas dos grupos @see User::getGrupos */
define('SHORT_LIST',0);
/** @brief id do usuario modelo */
define('MODEL_USER',3);


/**
 * @brief Classe contendo todas as
 * funcoes de usuario
 */
class User
{
  /**
   * @brief id do usuario
   */
  private $Id = null;
  /**
   * @brief lista basica de grupos do usuario
   */
  private $Grupos = null;
  /**
   * @brief Lista Completa dos grupos do usuario
   */
  private $FullGrupos = null;
  /**
   * @brief e-mail do usuario
   */
  private $Email = null;
  
  /**
   * @brief Funcao que instancia o usuario
   */
  function User($id_user=null)
  {
    if ($id_user === null)
      $id_user = $this->getId();
    $this->Id=$id_user;
    $this->getGrupos($id_user, SHORT_LIST);
    $this->getGrupos($id_user, FULL_LIST);
    $this->get_email($id_user);
  }

  /**
   * @brief registra um usuario
   * @todo utilizar o modelo
   * @todo enviar e-mail
   */
  static function register($vars, $pair)
  {
    global $BD;
    
    $table = $BD->Tables['users'];
    
    $insert = "insert into $table (".implode($pair, ",").") ";
    $insert.= "values ('".implode($vars, "','")."')";
    
    $newid = $BD->insert($insert);
    /* quando $newid != null, entao temos um novo usuario cadastrado */
    if ($newid)
    {
      $Grupos = User::getGrupos(MODEL_USER, SHORT_LIST);
      $Values = implode("),($newid, ", $Grupos);

      $table = $BD->Tables['users_grupos'];
      $insert = "insert into $table (id_user, id_grupo) values ($newid, $Values)";
      $BD->insert($insert);
    }
    
    return $newid;
  }

  static public function getUserId()
  {
    return $id = $_SESSION['id_user'];
  }

  /**
   * @brief retorna o id do usuario atual
   * @todo tudo
   */
  public function getId()
  {
    $id = null;

    $id = $this->Id;

    /* caso o id do objeto nao tenha sido definido,
     * ele eh recuperado atraves da funcao estatica */
    if (!$id)
      $id = User::getUserId();
    /* caso o id nao tenha sido recuperado, ele eh setado
    como 0 (publico) */
    if ($id == null)
      $id = 0;
    /* caso o objeto tenha sido instanciado,
    o id deve ser retornado no objeto */
    if ($this->Id === null)
      $this->Id = $id;
      
    return $id;
  }

  /**
   * @brief Funcao que retorna o e-mail do usuario
   * @todo tudo
   */
  function get_email()
  {
  }

  /**
   * @brief checa se um usuario tem acesso para um determinado grupo
   * @todo fazer a checagem de grupos do usuario utilizando a sessao
   *
   * @param $id_grupos : array dos ids do item protegido
   * @param $id_user : id do usuario
   */
  function checkAuth($id_grupos, $id_user=null, $type=FULL_LIST, $debug=null)
  {
    global $BD;
    global $PUser;
    global $Conf;

    if ($debug === null)
      $debug = $Conf['debug'];
    
    /* id grupo eh forcado para ser um vetor */
    if (!is_array($id_grupos))
      $id_grupos = array($id_grupos);
    /* checagem do id do usuario */
    if ($id_user === null)
      $id_user = User::getUserId();
  
    if ($debug)
    {
      if ($type == FULL_LIST)
        $pre = "User Class:<br />checkAuth()<br />type: FULL<br />";
      else
        $pre = "User Class:<br />checkAuth()<br />type: SHORT<br />";
      $dvars = array('id user'=>$id_user);
      openDebug($pre, $dvars);
      echo "Grupos - Menu<br />";
      print_array($id_grupos);
    }
    /* verificacao de autorizacao atraves do objeto externo $PUser */
    if (!isset($this) && $PUser->getId() === $id_user)
    {
      $return = $PUser->checkAuth($id_grupos, $id_user, $type, $debug);
    }
    /* verificacao de autorizacao atraves do objeto atual */
    else if (isset($this))
    {
      /* busca do grupo do usuario */
      if ($type == FULL_LIST )
        $Grupos = $this->FullGrupos;
      else
        $Grupos = $this->Grupos;
      /* quando debug == true  */
      if ($debug)
      {
        echo "Grupos:<br />";
        print_array($Grupos);
      }

      if (array_intersect($Grupos, $id_grupos))
        $return = true;
      else
        $return = false;
    }
    /* verificacao de autorizacao atraves de metodos estaticos */
    else
    {
      /* os grupos iniciais sao retirados da funcao
       *      * @ref  User::getGrupos */
      $Grupos = User::getGrupos($id_user, SHORT_LIST);
      /* quando debug == true  */
      if ($debug)
      {
        echo "Grupos:<br />";
        print_array($Grupos);
      }
      /* os subgrupos ao qual o usuario pertencem serao
       *      encontrados atraves da relacao de grupos */
      $table = $BD->Tables['grupos_rel'];

      /** Quando o usuario fizer parte do grupo root, ou quando
       * a chamada da funcao nao for do tipo @ref FULL_LIST,
       * nao eh nescessario buscar subgrupos */
      if (!(is_array($Grupos) && in_array(1,$Grupos)) && $type == FULL_LIST)
      {
        /* a busca deve continuar por todos os grupos e subgrupos */
        do
        {
          /* a busca de grupos autorizados eh feita utilizando
           *          a interseccao dos arrays */
          if (count(array_intersect($id_grupos,$Grupos)))
          {
            $cont = 0;
            break;
          }
          else
          {
            $cont = count($Grupos);
            $Grupos = User::get_subgrupos($Grupos, 1);
            $cont = count($Grupos) - $cont;
          }
        } while ($cont);
      }
      else if (in_array(1,$Grupos))
        /** Se o usuario pertencer ao grupo root, ele tem acesso total */
        $id_grupos = array(1);
  
      if (count(array_intersect($id_grupos,$Grupos)))
        $return = true;
      else
        $return = false;
    }
    
    if ($debug)
    {
      closeDebug();
    }
    return $return;
  }

  /**
   * @brief pega todos os ids dos grupos do usuario
   * @param $id_user : id do usuario
   * @param $all : define se deve haver uma busca pela arvore de grupos
   */
  function getGrupos($id_user=null, $all=false)
  {
    global $BD;
    
    if ($id_user === null)
      $id_user = User::getUserId();

    /* quando o objeto foi instanciado,
       a busca eh feita do objeto */
    if (isset($this) && $this->Id == $id_user)
    {
      if ($all && !$this->FullGrupos === null)
        $Grupos = $this->FullGrupos;
      if (!($this->Grupos === null))
      {
        $Grupos = $this->Grupos;
        if ($all)
          $Grupos = $this->get_subgrupos($Grupos);
      }
    }
    
    /*  Quando o grupo === null, deve buscar-se do
    objeto global */
    if (!isset($Grupos) || $Grupos === null) 
    {
      /* para utilizar o objeto PUser, deve-se
      declarar o objeto */
      global $PUser;
      if (is_object($PUser) && $PUser->Id == $id_user)
      {
        if ($all && !$PUser->Grupos === null)
          $Grupos = $PUser->FullGrupos;
        else if (!($PUser->Grupos === null))
        {
          $Grupos = $PUser->Grupos;
          if ($all)
            $Grupos = $PUser->get_subgrupos($Grupos);
        }
      }
    }

    /* Caso os grupos ainda nao tenham sido definidos (via obbjeto),
     eh hora de buscar os dados */
    if (!isset($Grupos) || $Grupos === null)
    {
      $table = $BD->Tables['users_grupos'];
    
      $query = "select id_grupo from $table where id_user=$id_user";
      
      $Grupos = $BD->query_to_vector($query);
      
      /* quando all esta ativado, a busca percorree retorna todos os grupos */
      if ($all)
        $Grupos = User::get_subgrupos($Grupos);
    }
    
    /* caso a chamada seja de dentro do objeto, deve-se fazer
    o update do objeto */
    if (isset($this) && $this->Id == $id_user)
    {
      if ($all && $this->FullGrupos === null)
        $this->FullGrupos = $Grupos;
      else
        $this->Grupos = $Grupos;
    }

    return $Grupos;
  }

  /**
   * @brief pega todos os subgrupos de uma lista de grupos
   */
  function get_subgrupos($grupos, $depth=0)
  {
    global $BD;
    
    $table = $BD->Tables['grupos_rel'];
    
    /* a busca de subgrupos deve ser feita enquanto o id
       id_grupo nao tiver sido encontrado */
    $i = 0;

    do
    {
      $cont = false;
      $Grupos = implode(",",$grupos);

      /* busca pelos grupos filhos em um nivel */
      $query = "select id_grupo from $table where id_parent in ($Grupos) and id_grupo not in ($Grupos)";
      $aux = $BD->query_to_vector($query);

      /* quando sao encontrados filhos, a busca continua */
      if ($aux)
      {
        $cont = true;
        $grupos = array_merge($aux, $grupos);
      }
      $i++;
    } while ($cont && ((!$depth) || ($i < $depth)));
    return $grupos;
  }

  /**
   * @brief checa o login
   * @param <type> $user: o nick do usuario (quando nulo, retorna o id do usuario publico)
   * @param <type> $pass: a senha encriptada
   * @return <type> o id do usuario caso haja sucesso ou false quando ha falha.
   *
   */
  static function checkLogin($user, $pass)
  {

    global $BD;

    $table = $BD->Tables['users'];

    /* se $user === null, entao o retorno deve ser o usuario publico */
    if ($user === null)
      $id = 0;
    else
    {
      /* busca no banco */
      $query = "select id_user from $table where nick='$user' AND pass='$pass' limit 1";

      $id = $BD->query_to_val($query);
    }
    /* quando $id !== null, o id eh retornado */
    if ($id !== null)
      $rtn = $id;
    else
      $rtn = false;
    return $rtn;
  }

  /**
   * @brief executa um login
   * @global <type> $PUser
   * @param <type> $user
   * @param <type> $pass
   * @return <type>
   */
  static function login($user, $pass)
  {
    global $PUser;

    $iduser = User::checkLogin($user, $pass);
    /* quando o login eh um sucesso, os objetos globais de sessao de
     * usuario devem ser alterados */
    if ($iduser)
      $_SESSION['id_user'] = $iduser;
    return $iduser;
  }
}

/* loading hack configuration */
if($Conf['hack'] == true)
  @require_once('engines/hack.php');

global $PUser;

if ($_SESSION['id_user'] === null)
  $_SESSION['id_user'] = 0;
$PUser = new User($_SESSION['id_user']);

?>
