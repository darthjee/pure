<?php
  /**
   * @file modules/login.php
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>

<?php

if (array_key_exists('action',$_GET))
  $action = $_GET['action'];
else
  $action = null;

if ($action !== null)
{
  switch ($action)
  {
    /* no logout, deve se realizar um login para usuario publico */
    case 'logout':
      $_SESSION['id_user'] = null;
      $_SESSION['user'] = null;
      $_POST['login'] = null;
      $_POST['password'] = null;
      $_POST['password-crypt'] = null;
    case 'login' :
      /* tentativa de login */
      if (User::login($_POST['login'], $_POST['password-crypt'])!==false)
      {
        /* quando ha sucesso, a pagina sofrera um refresh */
        ?>
        <script type="text/javascript">
          if (!window.location.href.match("mod=login&action=login"))
            window.location.href=window.location.href;
        </script>
        <?php
      }
      else
      {
        /* mensagem no caso de falha (tem que comecar a utilizar o XML para traducoes) */
        ?>
          Login Incorreto
        <?php
      }
      break;
  }
}
else
{
  ?>
  <form id="loginform" method="post" action="index.php?ajax=true&form=true&amp;mod=login&amp;action=login" class="ajax" return="div#message .inner" validate="return checkLogin(this);" onsubmit="return false;">
    <label for="login" class="required">Login</label><br />
    <input type="text" name="login" id="login" /><br />
    <label for="password" class="required">Password</label><br />
    <input type="password" name="password" id="password" /><br />
    <input type="hidden" name="password-crypt" id="password-crypt" />
    <input type="submit" id="submitbt" class="ajax" />
  </form>


  <script type="text/javascript">
    setTimeout("xmlLoad('xml/login.xml')", 1);
  </script>
  <?php
}
?>