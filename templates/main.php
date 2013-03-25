<?php
  /**
   * @file templates/main.php
   * @brief pagina principal
   *
   * @todo adcionar o gerador de lista de translates
   */

  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
[
  <!ATTLIST form return CDATA "">
  <!ATTLIST form validate CDATA "">
]>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <title>Pure</title>
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <link rel='stylesheet' href='css/rpg.css' type='text/css' />
  <link rel="icon" href="templates/images/favicon.ico" type="image/x-icon"/>
  <script type="text/javascript" src='scripts/jquery-1.3.2.js'></script>
  <script type="text/javascript" src='scripts/jquery.cookie.js'></script>
  <script type="text/javascript" src='scripts/jquery.pure.js'></script>
  <script type="text/javascript" src='scripts/pure.js'></script>
  <script type="text/javascript" src='scripts/menu.js'></script>
  <script type="text/javascript" src='scripts/xmlloader.js'></script>
  <script type="text/javascript" src='scripts/md5.js'></script>
  <script type="text/javascript">
    var langlist=['pt_br', 'en_us'];
  </script>
  </head>

  <body>
    <div id="page">
      <div id="header">
        <!-- header -->
        &nbsp;
      </div>
      <div id="content">
        <div id="menu">
          <div id="menu_roll">
          <?php
            Menu::print_all_menus();
          ?>
          </div>
          <div id="menu_button">
          <?php
            Menu::print_all_menus(0, MENU_TTEMPL);
          ?>
          </div>
          <div id="promo">
            <div>
              <a href="http://users.skynet.be/mgueury/mozilla/">
                <img src="templates/images/tidy_32.gif" alt="Validated by HTML Validator (based on Tidy) " height="32" width="78"/>
              </a>
            </div>
            <div>
              <!-- Inicio do botao do Firefox -->
              <a href="http://br.mozdev.org/" target="_blank"><img src="http://sfx-images.mozilla.org/affiliates/Buttons/Firefox3.5/96x31_blue.png" width="96" height="31" style="border-style:none;" title="Mozilla Firefox" alt="Firefox" /></a>
              <!-- Fim do botao do Firefox -->
            </div>
          </div>
        </div>
        <div id="view">
          &nbsp;
        </div>
      </div>
      <div id="footer">
        <!-- footer -->
      </div>
      <div id="message" class="overlay">
        <div class="close"><span>[x]</span></div>
        <div class="inner"></div>
      </div>
    </div>

  </body>

</html>
