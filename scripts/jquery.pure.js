/**
 * @file jquery.pure.js
 * @brief Contem prototipos extras para jquery
 * @todo
 */


/**
 * @brief faz uma busca do primeiro parent que bate com a selecao
 * @param sel : seletor de busca
 */
jQuery.prototype.findParent = function (sel)
{
  var ret = this.parent();
  while (ret.length > 0 && !ret.is(sel))
  {
    ret = ret.parent();
  }
  return ret;
}



/**
 * @brief Envia um formulario via ajax
 * @param rsel : seletor jquery do div de retorno
 * @param callback : funcao a ser chamada quando houver um callback
 * @return
 */
jQuery.prototype.ajaxFormSubmit = function(rsel, callback)
{
  /* tratamento de um callback inexistente */
  if (typeof callback == 'undefined')
    callback = function (){};
    
    
  /* form jQuery object */
  var form = this;
  /* form return div object */
  var retdiv=$(rsel);
  /* creation of the iframe where the request will be sent to */
  var frameid = "loadframe";
  var cont = 0;
  while ($("iframe#"+frameid).length > 0)
  {
    cont++;
    frameid="loadframe"+cont;
  }
  $("body").append('<iframe id="'+frameid+'" name="'+frameid+'" height="1" width="1" style="display:none"></iframe>');
  var frame = $('iframe#'+frameid);
  var oldtarget = form.attr('target');
  
  form.attr('target', frameid);
  /* whenever the frame loads (after a submition), its content must be passed to the returning div */
  
  frame.ready(function (){
    frame.load(function (){
      var html = frame.contents().find('body').html();
      if (html != null && html != "")
      {
        retdiv.html(html);
        frame.remove();
        callback();
      }
      else
        retdiv.html("Falha");
    });
  });

  form.submit();
}

/**
 * @brief Valida um formulario
 * @param reqClass
 * @return
 */
jQuery.prototype.validateForm = function (reqClass, alertClass, failCallback)
{
  var form = this;
  if (typeof failCallback == 'undefined')
    failCallback = function (){};
  
  var labels = form.find('label.'+reqClass);
  
  /* flag de retorno (bom ou mal) */
  var ret = true;
  
  labels.each(function (){
    var label = $(this);
    var name = label.attr("for");
    if (checkInput(form, name))
    {
      label.addClass(alertClass);
    }
    else
    {
      label.removeClass(alertClass);
      ret = false;
    }
  });


  return ret;
}

/**
 * @brief retorna a altura de uma janela 
 */
jQuery.prototype.windowHeight = function()
{
  if(window.innerHeight)
    return window.innerHeight;
  else if(document.documentElement.clientHeight && document.documentElement.clientHeight > 0)
    return document.documentElement.clientHeight;
  return document.body.clientHeight;
}

/**
 * @brief retorna a largura de uma janela 
 */
jQuery.prototype.windowWidth = function()
{
  if(window.innerWidth)
    return window.innerWidth;
  else if(document.documentElement.clientWidth && document.documentElement.clientWidth > 0)
    return document.documentElement.clientWidth;
  return document.body.clientWidth;
}

/**
 * @brief retorna o quanto de scroll vertical
 * foi utilizado (altura da visualizacao) 
 */
jQuery.prototype.scrollTop = function()
{
  if(document.documentElement.scrollTop!==0)
    return document.documentElement.scrollTop;
  else if(document.body.scrollTop!==0)
    return document.body.scrollTop;
  return 0;
}

/**
 * @brief retorna o quanto de scroll horizontal
 * foi utilizado (distancia da visualizacao) 
 */
jQuery.prototype.scrollLeft = function()
{
  if(document.documentElement.scrollLeft!==0)
    return document.documentElement.scrollLeft;
  else if(document.body.scrollLeft!==0)
    return document.body.scrollLeft;
  return 0;
}


/**
 * @brief posiciona o elemento
 */
jQuery.prototype.position = function(top, left)
{
  this.css('position','absolute');
  this.css('top', top);
  this.css('left', left);
}


/**
 * @brief centraliza o elemento
 */
jQuery.prototype.center = function()
{
  height = this.css('height');
  if (height !== null)
    height = height.split("px")[0];
  else
    height = this.attr('height').split("px")[0];
  
  width = this.css('width');
  if (width !== null)
    width = width.split("px")[0];
  else
    width = this.attr('width').split("px")[0];
  
  top = (this.windowHeight()-height)/2;
  top += this.scrollTop();
  left = (this.windowWidth()-width)/2;
  left += this.scrollLeft();
  this.position(top, left);
}



/**
 * @brief alteracao do objeto jQuery para que haja funcao open para o overlay
 */
jQuery.prototype.overlayOpen = function()
{
  this.show();
  this.center();
}

/**
 * @brief alteracao do objeto jQuery para que haja funcao close para o overlay
 */
jQuery.prototype.overlayClose = function()
{
  this.hide("500");
}


/*******************************
 * funcoes de apoio
 */


/**
 * @brief valida um formulario
 */
function checkInput(form, name)
{
 var input = form.find('input[name='+name+'],select[name='+name+']');
 var ret = false;
 
 if (input.is('input'))
   switch(input.attr('type'))
   {
     case 'file' :
     case 'text' :
       if (input.val())
         ret = true;
       if (input.hasClass("email"))
         ret = validateEmail(input.val());
       break;
     case 'checkbox' :
     case 'radio' :
       if (input.is(':checked'))
         ret = true;
       break;
     case 'password' :
       var value = input.val();
       ret = true;
       if (!value)
         ret = false;
       else if (input.hasClass("double"))
       {
         var form = input.findParent("form");
         var n = form.find("input.double[type='password']").length;
         if (n > 1)
         {
           var i;
           for (i = 0; i < n; i++)
             if (form.find("input.double[type='password']:eq("+i+")").val() != value)
               ret = false;
         }
       }
   }
 else if (input.is('select'))
 {
   if (input.val())
     ret = true;
 }
 
 return ret;
}


/**
 * @brief checa se um e-mail eh valido
 * @param email
 * @return
 */
function validateEmail(email)
{
  var login = '[_%A-z0-9]+[+%]?([._-][A-z0-9]+[+%]?)?';
  var server = '[A-z0-9]+([._-][A-z0-9]+)?([.][A-z0-9]{1,4})+';
  var reg = new RegExp('^'+login+'@'+server+'$');
  
  return reg.test(email);
}