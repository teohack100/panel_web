<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\js\formvalidation_js.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347ccff30_21645242',
  'file_dependency' => 
  array (
    'eb3a9b9d5cbb9de3bcafc1572143479103d6a4d6' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\js\\formvalidation_js.tpl',
      1 => 1608667498,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347ccff30_21645242 ($_smarty_tpl) {
?>
 	<!-- Formvalidation.io -->   
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/formvalidation/dist/js/formValidation.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/formvalidation/dist/js/framework/bootstrap.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/formvalidation/vendor/addon/src/reCaptcha2.js"><?php echo '</script'; ?>
><?php }
}
