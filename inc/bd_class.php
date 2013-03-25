<?php
  /**
   * @file bd_class.php
   * Classe de banco de dados @ref BDClass
   */
?>

<?php

/**
 * @brief Classe Banco de Dados
 *
 * Toda a comunicacao com o banco de dados passa
 * atraves deste objeto.
 * A configuracao deste objeto exige que ele seja
 * instanciado
 */

@require_once('conf.php');
@require_once('stdinc.php');

class BDClass
{
  /**
   * @addtogroup BDClassVars BDClass Vars
   * @{
   */
  /** @brief senha do banco de dados */
  var $Pass;
  /** @brief usuario do banco de dados */
  var $User;
  /** @brief servidor do banco de dados */
  var $Host;
  /** @brief nome do banco do banco de dados */
  var $Banco;
  /** @brief nomedo banco de dados (para salvar na lista de configuracoes) */
  var $Name = 'std';
  /** @brief vetor contendo os nomes das tabelas */
  var $Tables = array();
  /** Nome da tabela contendo o nome das tabelas Ref: $Tables */
  var $Tnames = null;
  /** @brief chave de debug (quando true, marca que devemos fazer debug sempre  */
  var $Debug = false;
  /** @brief  array com as varias configuracoes do banco */
  var $Confs = array();

  /** @brief ID do link com o banco */
  var $LinkID = 0;
  /** @brief ID da ultima Query */
  var $QueryID = 0;

  /** @brief codigo de erro */
  var $Errno = 0;
  /** @brief string de identificacao do erro */
  var $Error = "";

  /** @brief Array contendo os resultados tabelados */
  var $Results = array();
  /** @brief numero de linhas dos resultados */
  var $Rows = 0;
  /** @brief Quantas linhas formam afetadas no ultimo update/insert */
  var $Affected = 0;
  /** @brief ID retornado por uma operacao de insercao */
  var $NewID = 0;
  /**
   * @brief array com a relacao dos tags dos arrays de configuracao
   * para as propriedades do objeto @ref BDClass
   * * A relacao de propriedades se da de forma que cada campo do vetor
   * se relaciona com uma propriedade do objeto
   * @param pass => @ref BDClassVars "Pass"
   * @param user => @ref BDClassVars "User"
   * @param host => @ref BDClassVars "Host"
   * @param banco => @ref BDClassVars "Banco"
   * @param tables => @ref BDClassVars "Tables"
   * @param tnames => @ref BDClassVars "Tnames"
   * @param debug => @ref BDClassVars "Debug"
   */
  static $bdconftags = array(
      'pass' => 'Pass',
      'user' => 'User',
      'host' => 'Host',
      'banco' => 'Banco',
      'tables' => 'Tables',
      'tnames' => 'Tnames',
      'debug' => 'Debug',
      
      'linkid' => 'LinkID',
      'queryid' => 'QueryID',
      'errno' => 'Errno',
      'error' => 'Error'
      );
  /**
   * @}
   */


  /**
   * @brief inicializa uma instancia de @ref BDClass
   */
  function BDClass($pass=null, $user=null, $host=null, $banco=null, $name=null, $tnames=null, $debug=null)
  {
    $this->set_param_banco($pass, $user, $host, $banco, $name, $tnames, $debug);
  }

  /**
   * @brief ajusta os parametros iniciais do banco
   *
   * @param $Conf : Vetor de configuracao ou senha ($pass) do servidor.
   * A relacao entre o vetor e as variaveis de controle sao dados por
   * @ref BDClass::$bdconftags
   * @param $user : id do usuario do banco
   * @param $host : endereco do servidor
   * @param $banco : nome do banco de dados contendo as tabelas
   * @param $name : nome a ser dado para a configuracao
   * @param $tnames : nome da tabela de dados contendo os nomes das tabelas
   * @param $debug : se esta configuracao utiliza ou nao debug
   * @see BDClass
   */
  function set_param_banco($Conf=null, $user=null, $host=null, $banco=null, $name=null, $tnames=null, $debug=null)
  {
    global $BDconf;
    
    /* quando $Conf nao eh um array, ele deve ser a
    * variavel $pass */
    if (!is_array($Conf))
    {
      $pass = $Conf;
      $Conf =& $BDconf;
    }
    else
      $pass = null;

    /* extracao das configuracoes do vetor ou das variaveis de entrada */
    foreach(BDClass::$bdconftags as $ind => $Prop)
    {
      /* quando a variavel em questao foi definida, a configuracao
       * ignora o vetor */
      if ($$ind)
        $this->$Prop=$$ind;
      /* quando nao foi definida uma variavel especifica, 
       * o valor sera lido do vetor */
      else if ($Conf[$ind])
        $this->$Prop=$Conf[$ind];
      /* quando nao existe o item na variavel ou no vetor,
       * o programa chama acoes padroes */
      else
      {
        switch ($ind)
        {
          /* algumas propriedades numericas sao,
           * por padrao, definidas como nulas  */
          case 'linkid' :
          case 'queryid' :
          case 'errno' :
            $this->$Prop = 0;
            break;
          case 'error' :
            $this->Error = "";
            break;
        }
      }
      /* para cada tag, pode ser feita uma execucao especial */
      switch ($ind)
      {
        case "tnames" :
          if (($$ind || $Conf[$ind]) && !$Conf['tables'])
            $this->Tables = array();
            $this->read_tb_names($this->Tnames);
          break;
      }
    }
  }

  /**
   * @brief salva a configuracao do banco atual
   *
   * @param $name : nome da configuracao de banco
   */
  function save_conf($name=null)
  {
    /* caso o nome da configuracao nao tenha sido definido,
     * o nome sera dado por $this->Name ou pelo padrao 
     * 'std' */
    if (!$name)
    {
      if (!$this->Name)
        $name = 'std';
      else
        $name = $this->Name;
    }

    /* atraves de BDClass::$bdconftags, todos os itens
     * importantes da configuracao sao guardados
     * no vetor */
    foreach(BDClass::$bdconftags as $ind => $Prop)
      $this->Confs[$name][$ind] = $this->$Prop;
  }

  /**
   * @brief adciona uma configuracao ao rol de configuracoes (@ref BDClass->Confs)
   *
   * @param $Conf : vetor de configuracao ou senha do banco ($pass)
   * @param $user : id do usuario do banco
   * @param $host : endereco do servidor
   * @param $banco : nome do banco de dados contendo as tabelas
   * @param $name : nome a ser dado para a configuracao
   * @param $tnames : nome da tabela de dados contendo os nomes das tabelas
   * @param $debug : se esta configuracao utiliza ou nao debug
   * @see BDClass::set_param_banco
   * @see BDClass::save_conf
   */
  function add_conf($Conf=null, $user=null, $host=null, $banco=null, $name=null, $tnames=null, $debug=null)
  {
    global $BDconf;

    /* quando $Conf nao for um vetor, ele
     * entao eh a senha $pass */
    if (!is_array($Conf))
    {
      $pass = $Conf;
      /* quando $Conf nao eh um vetor, ele assume o
       * vetor de configuracao geral */
      $Conf =& $BDconf;
      /* para evitar utilizar a configuracao dada pelo
       * vetor geral $BDconf, avalia-se se $name foi
       * definido aqui */
      if (!$name)
        $name = 'aux';
    }
    else
      $pass=null;

    /* o nome da configuracao tem que ser recuperado
     * antes de se salvar os dados */
    if (!$name)
    {
      if ($Conf['name'])
        $name = $Conf['name'];
      else
        $name = 'aux';
    }

    /* cada variavel eh entao alojada no seu campo */
    foreach(BDClass::$bdconftags as $ind => $Prop)
    {
      if ($$ind)
        $this->Confs[$name][$ind]=$$ind;
      else if ($Conf[$ind])
        $this->Confs[$name][$ind]=$Conf[$ind];
    }
  }

  /**
   * @brief le uma configuracao ja salva para o objeto 
   */
  function load_conf($name=null)
  {
    if (!$name)
      $name = 'std';

    $Conf =& $this->Confs[$name];

    /** a leitura do  vetor de configuracoes eh dado pela
     * funcao @ref BDClass::set_param_banco */
    if ($Conf)
      set_param_banco($Conf);
  }

  /**
   * @brief conecta ao banco de dados
   */
  function connect($debug=null) 
  {
    if ($debug === null)
      $debug = $this->Debug;
    $this->Error = "";
    $this->Errno = 0;
    $this->Rows = 0;
    $this->Affected = 0;
    $this->NewID = 0;
    $this->QueryID = 0;
    $this->Results = array();

    /* quando LinkID == 0 ou mysql_get_server_info retorna null,
       temos uma conecxao inexistente */
    if ( 0 == $this->LinkID  || !mysql_get_server_info($this->LinkID)) {
      $host = $this->Host;
      $user = $this->User;
      $pass = $this->Pass;
      $banco = $this->Banco;

      $this->LinkID=mysql_connect($host, $user, $pass);
     

      if (!$this->LinkID)
      {
        if ($debug)
          echo "banco = $banco:<br />";
        $this->halt("Link-ID == false, falha de conex&atilde;o");
      }
      if (!mysql_select_db($this->Banco)) {
        $this->halt("O banco de dados n&atilde;o pode ser usado");
      }
    } 
  }

  /**
   * @brief para o sistema em caso de erro
   */
  function halt($msg)
  {
    die("$msg<br />Sistem indisponivel, tente mais tade<br />Sessao encerrada.<br />");
  }

  /**
   * @brief faz uma busca no banco retornando
   * o id da busca
   * @see BDClass::update
   * @see BDClass::insert
   * @see BDClass::delete
   */
  function query($query_str, $debug=null)
  {
    /* quando o debug eh falso, deve ser utilizado o debug
    das configuracoes */
    if ($debug === null)
      $debug = $this->Debug;
    $this->connect($debug);

    $this->QueryID = mysql_query($query_str);
    $this->Rows = mysql_num_rows($this->QueryID);
    $this->Errno = mysql_errno($this->LinkID);
    $this->Error = mysql_error($this->LinkID);
   
    /* quando deve ocorrer debug, */
    if ($debug)
    {
      ?><div class="debug"><?php
        echo "\"$query_str\"<br />";
        echo "Error:{$this->Error}<br />";
        echo "Errno:{$this->Errno}<br />";
        echo "Rows: {$this->Rows}";
      ?></div><?php
    }

    if ($this->Errno) {
      return false;
    }
    
    return $this->QueryID;
  }


  /**
   * @brief Faz uma busca retornando o numero de rows
   * encontrados;
   */
  function check_query($querystr, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    if ($debug)
    {
      ?>
        <div class="debug debugcheck">
          Check Query:<br />
      <?php
    }
    $this->query($querystr, $debug);

    /* quando existe debug, deve ser retornado alguns itens*/
    if ($debug)
    {
      echo "Resultados : ".$this->Rows;
      ?></div><?php
    }

    return $this->Rows;
  }


  /**
   * @brief Fecha a conexao com o banco
   */
  function close()
  {
    if ($this->LinkID)
      mysql_close($this->LinkID);
    $this->LinkID = 0;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em uma tabela
   */
  function query_to_table($query_str, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    /* inicio de debug */
    if ($debug)
    {
      ?>
        <div class="debug debugqtotable">
          Query to Table: <br />
      <?php
    }
    $this->query($query_str, $debug);

    while($row = mysql_fetch_array($this->QueryID))
      $this->Results[] = $row;

    /* quando existe debug, deve ser retornado alguns itens*/
    if ($debug)
    {
      echo "Resultados : ".$this->Rows."<br />";
      if ($this->Results)
        print_array($this->Results);
      ?></div><?php
    }


    return $this->Results;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em um vetor
   *
   * a busca deve ser relativa a apenas uma coluna
   * ja que o vetor retornado sera a propria coluna.
   * 
   * Todas as chaves serao perdidas
   * @see BDClass:query_to_table
   */
  function query_to_vector($query_str, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    /* inicio de debug */
    if ($debug)
    {
      ?>
        <div class="debug debugqtovec">
          Query to Vector: <br />
      <?php
    }

    /* para pegar um coluna, eh nescessario buscar a tabela
     inteira */
    $Table = $this->query_to_table($query_str, $debug);
    $vector = array();
    if ($Table) foreach($Table as $key => $row)
    {
      $vector[$key] = array_pop($row);
    }

    /* quando existe debug, deve ser retornado alguns itens*/
    if ($debug)
    {
      echo "Resultados : ".$this->Rows."<br />";
      if ($vector)
      {
        /* para que a impressao de debug seja correta,
         eh nescessario transformar o vetor em tabela com
         a chave correta */
        $key = array_pop(array_keys($Table[0]));
        $vector_aux[$key] = $vector;
        print_array($vector_aux);
      }
      ?></div><?php
    }

    return $vector;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em um array
   */
  function query_to_row($query_str, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    /* inicio de debug */
    if ($debug)
    {
      ?>
        <div class="debug debugqtorow">
          Query to Row: <br />
      <?php
    }

    $this->query($query_str, $debug);

    $row = mysql_fetch_assoc($this->QueryID);

    /* quando existe debug, deve ser retornado alguns itens*/
    if ($debug)
    {
      echo "Resultados : ".$this->Rows."<br />";
      if ($row)
        print_array($row);
      else
        echo "resposta : $row <br />";
      ?></div><?php
    }

    return $row;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em um valor
   */
  function query_to_val($query_str, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    /* inicio de debug */
    if ($debug)
    {
      ?>
        <div class="debug debugqtoval">
          Query to Val: <br />
      <?php
    }

    $Row = $this->query_to_row($query_str, $debug);

    if ($Row)
      $val = array_shift($Row);
    else
      $val = null;

    /* quando existe debug, deve ser retornado alguns itens*/
    if ($debug)
    {
      echo "Resultados : ".$this->Rows."<br />";
      if ($val)
        echo "Val : $val <br />";
      else
        echo "Val nulo <br />";
      ?></div><?php
    }

    return $val;
  }

  /**
   * @brief faz uma operacao de update atualizando
   * as propriedades certas do Objeto
   * @param $update : string SQL de update
   * @see BDClass::query
   * @see BDClass::insert
   * @see BDClass::delete
   */
  function update($update, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    $this->connect($debug);

    $this->QueryID = mysql_query($update);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->Errno = mysql_errno($this->LinkID);
    $this->Error = mysql_error($this->LinkID);

    if ($debug)
    {
      ?><div class="debug"><?php
        echo "\"$update\"<br />";
        echo "Error:{$this->Error}<br />";
        echo "Errno:{$this->Errno}<br />";
        echo "Rows: {$this->Affected}<br/ >";
      ?></div><?php
    }

    return $this->affected;
  }

  /**
   * @brief insere uma nova linha na tabela
   * atualizando as propriedades certas do
   * Objeto;
   * @param $insert : string SQL de insert
   * @see BDClass::query
   * @see BDClass::update
   * @see BDClass::delete
   */
  function insert($insert, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    $this->connect($debug);

    $this->QueryID = mysql_query($insert);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->NewID = mysql_insert_id($this->LinkID);
    $this->Errno = mysql_errno($this->LinkID);
    $this->Error = mysql_error($this->LinkID);

    if ($debug)
    {
      ?><div class="debug"><?php
        echo "\"$insert\"<br />";
        echo "Error:{$this->Error}<br />";
        echo "Errno:{$this->Errno}<br />";
        echo "Rows: {$this->Affected}<br />";
        echo "NewID:{$this->NewID}<br />";
      ?></div><?php
    }

    return $this->NewID;
  }

  /**
   * @brief fazz uma operacao de delete sql
   * atualizando as proprieades certas do banco
   * @param $insert : string SQL de insert
   * @see BDClass::query
   * @see BDClass::update
   * @see BDClass::insert
   */
  function delete($delete, $debug=null)
  {
    if ($debug === null)
      $debug = $this->Debug;

    $this->connect($debug);

    $this->QueryID = mysql_query($delete);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->Errno = mysql_errno($this->LinkID);
    $this->Error = mysql_error($this->LinkID);

    if ($debug)
    {
      ?><div class="debug"><?php
        echo "\"$delete\"<br />";
        echo "Error:{$this->Error}<br />";
        echo "Errno:{$this->Errno}<br />";
        echo "Affected: {$this->Affected}<br />";
      ?></div><?php
    }

    return $this->Affected;
  }

  /**
   * @brief copia os parametros de um banco para outro
   * @see BDClass
   */
  function cp_param_banco($BancoOBJ)
  {
    $this->Pass=$BancoOBJ->Pass;
    $this->User=$BancoOBJ->User;
    $this->Host=$BancoOBj->Host;
    $this->Banco=$BancoOBJ->Banco;
    $this->Tables=$BancoOBJ->Tables;
  }

  /**
   * @brief Le os nomes das tabelas a serem uilizadas
   */
  function read_tb_names($tablesname=null)
  {
    $debug = $this->Debug;

    if ($debug)
    {
      ?>
        <div class="debug">
          Read TB Names: <br />
      <?php
      echo "tanmes : $tablesname <br />";
    }
    
    if (!$tablesname)
      $tablesname = $this->Tnames;

    $query = "select global, local from $tablesname";

    $TablesList = $this->query_to_table($query, $debug);

    foreach ($TablesList as $Table)
    {
      $this->Tables[$Table['global']] = $Table['local'];
    }

    if ($debug)
    {
      echo "tables:<br />";
      print_array($this->Tables);
      ?></div><?php
    }
  }

}

/* declaracao do objeto banco */
global $BD;

if (!is_object($BD))
  $BD = new BDClass();

?>

<?php
  /* codigo de checagem de acesso nao autorizado, chamado no final neste arquivo */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>
