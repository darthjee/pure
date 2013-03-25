/**
 * @file menu.js
 * @brief Contem o controle do menu
 * @todo sistema de cache de menu (controle de menus abertos)
 */

/**
 * @brief objeto principal de controle do menu
 */
var Menu = {
  /**
   * @brief lista de todos os menus aberto
   */
  opened : [],
  open : open,
  close : close,
  openCont : openCont
};
  
/**
 * @brief abre um menu abrindo seus submenus
 */
function open(id, id_parent)
{
  for (i = this.opened.length; i > 0; i--)
  {
    if (this.opened[i-1] != id_parent && this.opened[i-1] != id)
      this.close(this.opened[i-1]);
    else
      break;
  }
  if (this.opened[this.opened.length-1] != id)
  {
    this.opened[this.opened.length] = id;
    $('#sub_'+id).load('index.php?ajax=true&mod=menu&id='+id);
    $('#sub_'+id).toggleClass('submenu');
  }
}

/**
 * @brief fecha um menu desaparecendo com todo seu submenu
 * e eliminando-o da lista @ref opened
 */
function close (id)
{
  $('#sub_'+id).html('');
  $('#sub_'+id).toggleClass('submenu');
  delete this.opened[this.opened.length-1];
  this.opened.length--;
}

/**
 * @brief abre o conteudo do menu na janela de conteudo
 */
function openCont (id, content, param)
{
  if (param)
    $('#view').load('index.php?ajax=true&mod='+content+"&"+param);
  else
    $('#view').load('index.php?ajax=true&mod='+content);
}

/**
 * @brief abre ambos, menu e conteudo
 */
function opeanBoth(id, id_parent, content, param)
{
  this.openCont(id, content, param);
  this.open(id, id_parent);
}
