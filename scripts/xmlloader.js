/**
 * @file xmlloader.js
 * @brief executa o parse de um XML
 *
 * @todo fazer a leitura e parse dos dados
 */

/**
 * @brief gera a string selector do campo
 * @see transField
 */
function transSeleGen()
{
  /* quando nobase for true, o selector base nao podera ser utilizado */
  if (this.nobase == 'true')
  {
    this.selector = '';
  }
  
  
  /* o selector utilizando o id e o type eh gerado em separado */
  var sel = '';
  if (this.type != '')
  {
    sel = this.type;
  }
  /* o id entao eh adcionado dependendo do tipo de atributo idat */
  if (this.id != '')
  {
    if (this.idat == 'id')
      sel += "#"+this.id;
    else
      sel += "["+this.idat+"='"+this.id+"']";
  }
  
  
  /* no final, os selectors sao uniodos */
 
  if (sel != '')
  {
    if (this.selector != '')
      this.selector += " ";
    this.selector += sel;
  }
  
}

/**
 * @brief retorna um objeto
 * com os dados de um field ja prontos para serem inseridos
 *
 * @param field:objeto jquery do campo selecionado com os dados de definicao
 * @param data: objeto jquery  do idioma selecionado com os textos
 * @param selector: selector base
 * @todo fazer com que a funcao
 * jqeury possa colocar texto em html que nao utilizem $().text()
 */
function transField(field, data, selector)
{
  /*  os dados basicos podem ser extraidos diretamente  */
  var name = field.attr('name');
  var id = field.attr('id');
  var type = field.attr('type');
  var nobase = field.attr('nobase');
  if (nobase == null)
    nobase = 'false';
  var idat = field.attr('idat');
  if (idat == null)
    idat = 'id';
  var ff = field.attr('ff');
  if (ff == null)
    ff = 'text';
  
  /* o objeto a ser retornardo ja esta pronto para ser jogado no HTML */
  var ret =
  {
    name:name,
    id:id,
    type:type,
    nobase:nobase,
    idat:idat,
    ff:ff,
    value:data.find("[name='"+name+"']").text(),
    selector:selector,
    genSelector:transSeleGen,
    fill:function()
    {
      if (this.ff == 'text')
        $(this.selector).text(this.value);
      else if (this.ff == 'val')
        $(this.selector).val(this.value);
    }
  };
  
  ret.genSelector();
  
  return ret;
}

/**
 * @brief faz um parse do XML buscando os dados
 * @param xml: xml retornado do $jquery.ajax()
 * @param data: parte do xml ja com os dados no idioma selecionado
 * @return ret: array contendo todos os objetos fields selecionados
 * @see transField
 *
 * @todo melhorar o parser com mais opecoes como tipo de texto
 */
function transGetFields(xml, data)
{
  var selector = $(xml).find('base selector').text();
  var ret=[];

  var fields = $(xml).find('base fields').children();
  var n = fields.length;
  var i;


  for (i = 0; i < n; i++)
  {
    ret[i] = new transField(fields.eq(i), data, selector);
  }

  return ret;
}

/**
 * @brief faz o parse buscando um objeto jQuery
 * pelo idioma
 *
 * @return um objeto jquery contendo apenas os dados de um idioma
 * @todo utilizar o idioma default
 */
function transGetData(data)
{
  var i=0;
  var max = langlist.length;
  do
  {
    lang=langlist[i];
    lang = $(data).find("data[lang='"+lang+"']").attr('lang');
    i++;
  } while(!lang && i < max);
  return $(data).find("data[lang='"+lang+"']");
}


/**
 * @brief joga os dados na pagina
 *
 * @param fields: array contendo os objetos utilizados
 */
function transParseFill(fields)
{
  var n = fields.length;
  for (i = 0; i < n; i++)
  {
    /** a funcao chamada ja foi definida em @ref transField */
    fields[i].fill()
  }
}

/**
 * @brief abre um xml e joga os dados nos campos
 * @param url : caminho do xml
 */
function xmlLoad(url)
{
  /* requestor eh o objeto utilizado por ajax */
  var requestor=
  {
    url:url,
    async:false,
    success:function(xml)
    {
      /* busca-se os dados de acordo com o idioma */
      var data = transGetData(xml);
      /* gera-se os objetos com os dados e os selectors */
      var fields = transGetFields(xml, data);
      /* joga-se os dadso no pagina */
      transParseFill(fields);
      /* libera-se a memoria */
      var i;
      var n = fields.length;
      for (i = 0; i < n; i++)
        fields[i] = null;
        fields = null;
    }
  };

  $.ajax(requestor);
}
