<?php
  /**
   * @file templates/register.php
   */
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>
<script type="text/javascript">
pureLoader('scripts/register.js');
</script>

<h2 id="title"></h2>

<form id="register" method="post" action="index.php?ajax=true&amp;mod=register&amp;action=new" class="ajax" return="div#view" validate="return registerUser(this);" onsubmit="return false;">
  <table>
    <tr>
      <td><label for="name" class="required"></label></td>
      <td><input type="text" id="name" name="name" value="" /></td>
    </tr>
    <tr>
      <td><label for="surname" class="required"></label></td>
      <td><input type="text" id="surname" name="surname" value="" /></td>
    </tr>
    <tr>
      <td><label for="email" class="required"></label></td>
      <td><input type="text" id="email" name="email" class="email" value="" /></td>
    </tr>
    <tr>
      <td><label for="login" class="required"></label></td>
      <td><input type="text" id="login" name="login" value="" /></td>
    </tr>
    <tr>
      <td><label for="pass" class="required"></label></td>
      <td><input type="password" id="pass" name="pass" class="double" value="" /></td>
    </tr>
    <tr>
      <td><label for="confpass" class="required"></label></td>
      <td><input type="password" id="confpass" name="confpass" class="double" value="" /></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" id="submitbt" class="ajax" /></td>
    </tr>
  </table>
</form>


<script type="text/javascript">
  setTimeout("xmlLoad('xml/register.xml')", 1);
</script>

