<?php
/**
 * @file stdinc.php
 * arquivo com funcoes basicas que nao cabem a objetos
 */

/** @brief tag de horientacao horizontal @see print_array */
define('TABLE_ORI_HORIZ', 0);
/** @brief tag de horientacao vertical @see print_array */
define('TABLE_ORI_VERT', 1);

/** @brief string para busca de tipo de autorizacao aberta */
define('OPEN_GRP', 'open');
/** @brief string para busca de tipo de autorizacao negativa */
define('DEN_GRP', 'denial');
/** @brief string para busca de tipo de autorizacao fechada */
define('EXCL_GRP', 'excl');


/**
 * @brief imprime uma tabela a partir de uma matriz (array)
 * @param $array : array a ser impresso
 * @param $orient : parametro opcional que diz se a orientaca
 * eh horizontal (@ref TABLE_ORI_HORIZ) ou vertical (@ref TABLE_ORI_VERT)
 */
function print_array($array, $orient=TABLE_ORI_HORIZ)
{
  /* quando o array eh um vetor, ele eh transformado em matriz */
  if (!is_array(current($array)))
  {
    $new = $array;
    $array = null;
    $array[0] =& $new;
  }

  $keys = grab_keys($array);

  ?><table><?php

  /* para cada alinhamento, o array tera de ser percorrido
     de forma diferente */
  if ($orient == TABLE_ORI_HORIZ)
  {
    ?><tr><th></th><?php
      foreach($keys as $key)
        echo "<th>$key</th>";
    ?></tr><?php
    foreach($array as $ind => $row)
      print_row($row, $ind, $keys);
  }
  else
  {
    /* imprecao dos indices do array*/
    $inds = array_keys($array);
    ?><tr><th></th><?php
      foreach($inds as $ind)
        echo "<th>$ind</th>";
    ?></tr><?php

    foreach($keys as $key)
    {
      $col = extract_col($array, $key);
      print_row($col, $key, $inds);
    }
  }
  ?></table><?php
}

/**
 * @brief imprime um vetor baseado nas chaves passadas
 * como parametro
 *
 * a funcao inclui as tags td e tr mas nao table
 * @param $row : vetor a ser impresso
 * @param $ind : indice da linha (quando null, nao eh impresso)
 * @param $keys : chaves a serem impressas (na ordem)
 * e em caso de receber null, serao estraidas as chaves
 * do proprio vetor
 */
function print_row($row, $ind=null, $keys=null)
{
  if ($keys == null)
    $keys = array_keys($row);

  ?><tr><?php
    /* qunado nao tem idice, o espaco para o indice deve ser colocado*/
    if ($ind === null)
    {
      ?><th>&nbsp;</th><?php
    }
    else
      echo "<th>$ind</th>";

    /* para cada chave os valores,
     se exisitiem, serao impressos */
    foreach($keys as $key)
    {
      $val = $row[$key];
      if ($val !== null)
      {
        echo "<td>$val</td>";
      }
      else
      {
        ?><td>&nbsp;</td><?php
      }
    }
  ?></tr><?php
}


/**
 * @brief extrai a coluna de uma matriz
 *
 * a extracao ocorre buscando todos os elementos
 * chave da coluna e inserindo em uma linha auxiliar
 */
function extract_col($array, $key)
{
  foreach ($array as $ind => $row)
    $col[$ind] = $row[$key];

  return $col;
}

/**
 * @brief pega os keys de toda uma matriz
 *
 * a impressao eh feita buscando e unindo
 * todas as chaves de todas as linhas
 */
function grab_keys($array)
{
  $keys=array();
  foreach ($array as $row)
  {
    $keys=array_union($keys, array_keys($row));
  }
  
  return $keys;
}

/**
 * @brief retorna a uniao, eliminando dados duplicados, de 2 arrays
 */
function array_union($a1, $a2)
{
  return array_unique(array_merge($a1,$a2));
}

/**
 * @brief copy the array using the keys passed as referenc
 */
function kcopy_array($array, $keys)
{
  $out = array();
  foreach ($keys as $key => $val)
  {
    $out[$key] = $array[$key];
  }
  return $out;
}



/**
 * @brief Abre o div de debug
 * @param <type> $pre
 * @param <type> $vars
 * @param <type> $pos
 */
function openDebug($pre=null, $vars=null, $pos=null)
{
  ?><div class="debug"><?php
  if ($pre)
    echo $pre;
  if ($vars) foreach($vars as $text => $val)
    echo "$text: $val<br />";
  if ($pos)
    echo $pos;
}


/**
 * @brief Abre o div de debug
 * @param <type> $pre
 * @param <type> $vars
 * @param <type> $pos
 */
function closeDebug($pre=null, $vars=null, $pos=null)
{
  if ($pre)
    echo $pre;
  if ($vars) foreach($vars as $text => $val)
  {
    if (is_array($val))
      print_array($val);
    else
      echo "$text: $val<br />";
  }
  if ($pos)
    echo $pos;
  ?></div><?php
}
?>
