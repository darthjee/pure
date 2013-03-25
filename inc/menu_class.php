<?php
  /**
   * @file menu_class.php
   * @brief Classe @ref Menu
   */

  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>


<?php

@require_once('lang_class.php');
@require_once('bd_class.php');
@require_once('user_class.php');
@require_once('stdinc.php');

define('MENU_TMENU','menu');
define('MENU_TTEMPL','tpl');


/**
 * @brief classe que controla o menu de acesso
 */
class Menu
{
  /** @brief id do menu */
  private $id_menu;
  /** @brief id do menu acima do menu */
  private $id_parent;
  /** @brief nome do menu no banco de dados */
  private $name;
  /** @brief titulo a ser exibido */
  private $title;
  /** @brief id do modulo a ser chamado */
  private $id_modulo;
  /** @brief nome do modulo referente ao menu */
  private $modulo;
  /** @brief parametro passado na hora da abertura do modulo */
  private $param;

  /**
   * @brief Gera um objeto Menu
   */
  function Menu($id_menu)
  {
    $this->set_props($id_menu);
    /* o titulo eh conseguido pela funcao a parte*/
    $this->get_menu_title($id_menu);

    return $this;
  }

  /**
   * @brief busca as propriedades basicas do menu utilizando
   * a view menus_mod
   */
  private function set_props($id_menu)
  {
    global $BD;

    $tmenus = $BD->Tables['menus'];
    $tmods = $BD->Tables['modulos'];

    $query ="select $tmenus.*, $tmods.name as modulo ";
    $query.="from $tmenus left join $tmods on $tmenus.id_modulo=$tmods.id_modulo ";
    $query.="where id_menu=$id_menu order by 'order','id_menu' limit 1";

    $Menu=$BD->query_to_row($query);

    /* jogando os valores nas propriedades */
    foreach ($Menu as $prop => $val)
    {
      if(property_exists('Menu', $prop))
        $this->$prop = $val;
    }
  }

  /**
   * @brief pega o nome do menu
   */
  public function get_menu_name($id_menu=null)
  {
    global $BD;

    /* quando o id_menu eh nulo, busca-se do objeto */
    if ($this && ($id_menu === null  || $id_menu == $this->id_menu))
    {
      /* id_menu eh buscado do objeto para caso name nao
         tenha sido definido no objeto */
      $id_menu = $this->id_menu;
      $name = $this->name;
    }
    /* a busca no banco so eh executada quando nescessaria e possivel */
    if ($name === null && $id_menu !== null)
    {
      $table = $BD->Tables['menus'];
      
      $query="select name from $table where id_menu=$id_menu limit 1";
      
      $name = $BD->query_to_val($query);
      
      /* ao final, caso seja nescessario, o name do objeto eh setado */
      if ($this && $this->id_menu == $id_menu)
        $this->name = $name;
    }

    return $name;
  }

  /**
   * @brief pega o id do menu pai
   */
  public function get_menu_parent($id_menu=null)
  {
    global $BD;

    /* o id parente eh buscado primeiramente do objeto */
    if ($this && ($id_menu === null  || $id_menu == $this->id_menu))
    {
      /* id_menu eh buscado do objeto para caso name nao
         tenha sido definido no objeto */
      $id_menu = $this->id_menu;
      $id_parent = $this->id_parent;
    }
    /* a busca no banco so eh executada quando nescessaria e possivel */
    if ($id_parent === null && !($id_menu === null))
    {
      $table = $BD->Tables['menus'];
      
      $query="select id_parent from $table where id_menu=$id_menu limit 1";
      
      $id_parent = $BD->query_to_val($query);
      
      /* quando mesmo assim nao temos um valor valido, ele sera 0 */
      if ($id_parent === null)
        $id_parent = 0;

      /* ao final, caso seja nescessario, o id_parent do objeto eh setado */
      if ($this && $this->id_menu == $id_menu)
        $this->id_parent = $id_parent;

    }

    return $id_parent;
  }

  /**
   * @brief pega o titulo traduzido de um menu utilizando
   * o sistema de traducao
   * @see Lang
   */
  public function get_menu_title($id_menu=null, $id_lang=null)
  {
    global $BD;

    /* o id titulo eh buscado primeiramente do objeto juntmente com
       o titulo ja definido */
    if ($this && ($id_menu === null  || $id_menu == $this->id_menu))
    {
      /* id_menu eh buscado do objeto para caso title nao
         tenha sido definido no objeto */
      $id_menu = $this->id_menu;
      $title = $this->title;
    }
    /* a busca no banco so eh executada quando nescessaria e possivel */
    if ($title === null && !($id_menu === null))
    {
      $table = $BD->Tables['menus_titles'];
      
      /* id_lang, quando nao especificado eh
         buscado no banco */
      if (!$id_lang)
        $id_lang=Lang::getNextLang();
      
      /* safe eh um contador para determinar
         qual o numero maximo de checagens */
      $safe=Lang::countLangs()+1;
      /* a busca da do titulos utiliza a ordem de preferencia
         de linguagens do usuario */
      do
      {
        $query="select title from $table where id_menu=$id_menu and id_lang=$id_lang limit 1";
        $title = $BD->query_to_val($query);
        $id_lang = Lang::getNextLang($id_lang);
        $safe--;
      }
      while (!$title && $safe && $id_lang >= 0);
      
      /* quando um titulo nao eh encontrado, utiliza-se o nome do menu */
      if (!$title)
      {
        /* se o objeto existir, a busca ocorre dentro do objeto */
        if ($this && $id_menu == $this->id_menu)
          $title = $this->get_menu_name($id_menu);
          /* caso tenha sido chamada dinamicamente, entao o nome
          * do menu eh chamado dinamicamente */
          else
          $title = Menu::get_menu_name($id_menu);
      }
      /* no final, o objeto recebe o titulo */
      if ($this && $id_menu == $this->id_menu)
        $this->title = $title;
    }

    return $title;
  }

  /**
   * @brief imprime o div de um menu
   * @todo: criar a funcao get_param() para ser usada aqui
   */
  public function print_menu($id_menu=null, $id_parent=null, $modulo=null, $param=null, $menu=null, $menu_title=null)
  {
    /* para a impressao do menu, deve-se extrair as variaveis, 
     * seja do banco ou do objeto*/

    /* caso o objeto exista, todas as informacoes
       serao extraidas dele*/
    if ($this && ($id_menu === null || $id_menu === $this->id_menu))
    {
      if ($id_menu===null)
        $id_menu = $this->id_menu;
      if ($id_parent==null)
        $id_parent = $this->get_menu_parent();
      if ($modulo == null)
        $modulo = $this->get_mod();
      if ($param == null)
        $param = $this->param;
      if ($menu == null)
        $menu = $this->get_menu_name();
      if ($menu_title == null)
        $menu_title = $this->get_menu_title();
    }
    /* caso o objeto nao exista
       (ou os ids_menu nao sejam compativeis), os
     dados sao recolhidos por funcoes */
    else
    {
       if ($id_menu===null)
         $id_menu = 0;
       if ($id_parent==null)
         $id_parent = Menu::get_menu_parent($id_menu);
       if ($modulo == null)
         $modulo = Menu::get_mod($id_menu);
       if ($param == null)
         $param = null;
       if ($menu == null)
         $menu = Menu::get_menu_name($id_menu);
       if ($menu_title == null)
         $menu_title = Menu::get_menu_title($id_menu);
    }

    include('templates/menu.php');
  }

  /**
   * @brief imprime toda uma lista de menus
   * @todo utilizar o id_user
   */
  public function print_all_menus($id_parent=0, $type=MENU_TMENU)
  {
    global $BD;

    $table = $BD->Tables['menus'];

    $query ="select id_menu from $table ";
    $query.="where id_parent=$id_parent AND type='$type'";
    $Menus=$BD->query_to_vector($query);

    if($Menus) foreach($Menus as $id_menu) if (Menu::checkAuth($id_menu))
    {
      $Menu = new Menu($id_menu);
      if ($type == MENU_TMENU)
      {
        $Menu->print_menu();
      }
      else if ($type==MENU_TTEMPL)
      {
        include(Modulo::getPage($Menu->getId_mod()));
      }
      unset($Menu);
    }
  }

  /**
   * @brief checa se um usuario tem autorizacao sobre um menu
   *
   * A checagem chama @ref Menu::checkAuth_type varias vezes,
   * cada uma checando de forma diferente.
   */
  public function checkAuth($id_menu=null, $id_user=null, $debug)
  {
    global $Conf; 
    if ($debug === null)
      $debug = $Conf['debug'];
      
    /* checagem do id do usuario */
    if (!$id_user)
      $id_user = User::getUserId();

    if (isset($this) && ($id_menu===null || $this->id_menu == $id_menu))
    {
      $id_menu = $this->id_menu;
      $return = $this->authByType($id_menu, $id_user, DEN_GRP, $debug);
      if (!$return)
      {
        $return = $this->authByType($id_menu, $id_user, OPEN_GRP, $debug);
        if (!$return)
          $return = $this->authByType($id_menu, $id_user, EXCL_GRP, $debug);
      }
      else
        $return = $this->authByType($id_menu, $id_user, EXCL_GRP, $debug);
    }
    else
    {
      $return = Menu::authByType($id_menu, $id_user, DEN_GRP, $debug);
      if (!$return)
      {
        $return = Menu::authByType($id_menu, $id_user, OPEN_GRP, $debug);
        if (!$return)
          $return = Menu::authByType($id_menu, $id_user, EXCL_GRP, $debug);
      }
      else
        $return = Menu::authByType($id_menu, $id_user, EXCL_GRP, $debug);
    }
    return $return;
  }

  /**
   * @brief checa se um usuario tem autorizacao sobre um menu
   * 
   * Para a checagem, o sistema busta todos os grupos
   * de um menu e checa se os grupos do usuario batem
   *
   * @param $id_menu : id do menu
   * @param $id_user : id d usuario
   * @param $type : tipo da busca
   */
  public function authByType($id_menu=null, $id_user=null, $type=OPEN_GRP, $debug=null)
  {
    global $Conf;

    if ($debug === null)
      $debug = $Conf['debug'];

    if ($debug)
    {
      $pre = "Menu Class:<br />Auth By Type: <br />";
      $dvars = array('Id Menu'=>$id_menu, 'Id User' => $id_user, 'Type' => $type);
      openDebug($pre, $dvars);
    }

    /* checagem do id do usuario */
    if (!$id_user)
      $id_user = User::getUserId();

    /* por padrao um menu nao deve ser exibido */
    $return = false;
    
    if (isset($this) && ($this->id_menu == $id_menu || $id_menu===null))
      $Grupos = $this->getGrupos(null,$type, $debug);
    else
      $Grupos = Menu::getGrupos($id_menu,$type, $debug);

    /* checagem com os grupos do usuario */
    if ($Grupos)
    {
      if ($type == OPEN_GRP)
        $return = User::checkAuth($Grupos, $id_user, FULL_LIST, $debug);
      else
        $return = User::checkAuth($Grupos, $id_user, SHORT_LIST, $debug);
    }
    else if (User::checkAuth(1, $id_user))
      $return = true;

    if ($debug)
    {
      if ($return)
        $pre = "Permitido <br/>AuthByType - Final<br />";
      else
        $pre = "Nao Permitido <br/>AuthByType - Final<br />";
      closeDebug($pre);
    }

    return $return;
  }

  /**
   * @brief Retorna todos os grupos do menu
   */
  public function getGrupos($id_menu=null, $auth=OPEN_GRP, $debug=null)
  {
    global $BD;
    global $Conf;
    
    if (isset($this) && $id_menu === null)
      $id_menu = $this->id_menu;

    if ($debug === null)
      $debug = $Conf['debug'];

    $table = $BD->Tables['menus_grupos'];
    $query = "select id_grupo from $table where id_menu=$id_menu and auth='$auth' order by id_grupo desc";


    if ($debug)
    {
      $pre="Menu Class:<br />Get Grupos: <br />";
      openDebug($pre);
      $Grupos = $BD->query_to_vector($query, true);
      if ($Grupos)
      {
        echo "Grupos:<br />";
        print_array($Grupos);
        $pre = "Fim getGrupos";
      }
      else
        $pre = "Sem Grupos<br />";
      closeDebug($pre);
    }
    else
      $Grupos = $BD->query_to_vector($query, false);

    return $Grupos;
  }

  /**
   * @brief checa se um menu tem submenu
   * @todo utilizar $this
   */
  public function has_submenu($id_menu)
  {
    global $BD;

    $table = $BD->Tables['menus'];
    $query = "select id_menu from $table where id_parent = $id_menu limit 1";

    $Menus = $BD->query_to_val($query);

    if ($Menus)
      $ret = true;
    else
      $ret = false;

    return $ret;
  }

  /**
   * @brief verifica se um menu tem conteudo
   * @todo utilizar $this
   */
  public function has_cont($id_menu)
  {
    $mod = Menu::getId_mod($id_menu);
    if ($mod)
      $ret = true;
    else
      $ret = false;
    return $ret;
  }

  
  /**
   * @brief verifica qual o id do modulo do menu
   */
  public function getId_mod($id_menu=null)
  {
    global $BD;

    if ($this && ($id_menu === null || $id_menu == $this->id_menu))
    {
      if ($this->id_modulo!==null)
        $id = $this->id_modulo;
      else
        $id_menu = $this->id_menu;
    }

    if ($id===null)
    {
      $table = $BD->Tables['menus'];
      $query = "select id_modulo from $table where id_menu = $id_menu limit 1";

      $id = $BD->query_to_val($query);
      if ($this && ($this->id_menu === null || $id_menu == $this->id_menu))
        $this->id_modulo = $id;
    }
    
    return $id;
  }

  
  /**
   * @brief verifica qual o nome do modulo do menu
   */
  public function get_mod($id_menu=null)
  {
    global $BD;

    if (isset($this) && ($id_menu === null || $id_menu == $this->id_menu))
    {
      if ($this->modulo!==null)
        $mod = $this->modulo;
      else
        $id_menu = $this->id_menu;
    }

    if (!isset($mod) || $mod===null)
    {
      $tmenus = $BD->Tables['menus'];
      $tmods = $BD->Tables['modulos'];
      $query = "select $tmods.name from $tmods left join $tmenus on $tmenus.id_modulo=$tmenus.id_modulo where id_menu = $id_menu limit 1";
        
      $mod = $BD->query_to_val($query);
      if ($this && ($this->id_menu === null || $id_menu == $this->id_menu))
        $this->modulo = $mod;
    }
    
    return $mod;
  }
  
  /**
   * pega o Id do menu
   * @return o id do menu
   */
  function getId()
  {
    return $this->id_menu;
  }
}

?>
