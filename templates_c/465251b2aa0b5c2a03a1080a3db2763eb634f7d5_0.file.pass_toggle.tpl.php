<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\js\pass_toggle.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347d07f70_72627809',
  'file_dependency' => 
  array (
    '465251b2aa0b5c2a03a1080a3db2763eb634f7d5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\js\\pass_toggle.tpl',
      1 => 1608667498,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347d07f70_72627809 ($_smarty_tpl) {
echo '<script'; ?>
>
function toggle_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhide2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_password2(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide3");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function toggle_passwordz(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhidez2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_passwordz(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhidez2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_passwordz2(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhidez3");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function downbold(){
    var a = document.body.appendChild(
        document.createElement("a")
    );
    a.download = "<?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
.html";
    a.href = "data:text/html," + document.getElementById("content").innerHTML;
    a.click();
}
<?php echo '</script'; ?>
><?php }
}
