<?php
  /* codigo de checagem de acesso nao autorizado */
  if (!class_exists('Logger'))
    @require_once('../inc/logger.php');
?>
<?php if (Menu::has_submenu($id_menu)) { ?>

  <?php if (Menu::has_cont($id_menu)) { ?>

    <!-- Menu <?php echo $menu_title ?> contem submenu e conteudo -->
    <div id="menu_<?php echo $menu ?>">
      <?php if ($param) { ?>
        <a href="#view" onclick="javascript:Menu.openBoth(<?php echo "$id_menu, $id_parent, '$modulo', '$param'" ?>); return false;"><?php echo $menu_title ?></a>
      <?php } else {?>
        <a href="#view" onclick="javascript:Menu.openBoth(<?php echo "$id_menu, $id_parent, '$modulo'" ?>); return false;"><?php echo $menu_title ?></a>
      <?php } ?>
      <div id="sub_<?php echo $id_menu ?>">
      </div>
    </div>
    
  <?php } else { ?>
    <!-- Menu <?php echo $menu_title ?> contem submenu -->
    <div id="menu_<?php echo $menu ?>">
      <a href="#menu" onclick="javascript:Menu.open(<?php echo "$id_menu, $id_parent" ?>); return false;"><?php echo $menu_title ?></a>
      <div id="sub_<?php echo $id_menu ?>">
      </div>
    </div>

  <?php } ?>
<?php } else { ?>

  <?php if (Menu::has_cont($id_menu)) { ?>
  
    <!-- Menu <?php echo $menu_title ?> contem conteudo -->
    <div id="menu_<?php echo $menu ?>">
      <?php if ($param) { ?>
        <a href="#view" onclick="javascript:Menu.openCont(<?php echo "$id_menu, '$modulo', '$param'" ?>); return false;"><?php echo $menu_title ?></a>
      <?php } else {?>
        <a href="#view" onclick="javascript:Menu.openCont(<?php echo "$id_menu, '$modulo'" ?>); return false;"><?php echo $menu_title ?></a>
      <?php } ?>
    </div>
    
  <?php } else { ?>
    <!-- Menu <?php echo $menu_title ?> contem link tenho que introduzir no sistema -->
    <div id="menu_<?php echo $menu ?>">
      <?php echo $menu_title ?>
    </div>
  <?php } ?>
<?php } ?>
