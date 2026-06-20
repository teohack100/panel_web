<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\apps\modals.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347cb6915_11389081',
  'file_dependency' => 
  array (
    '35ddcdb0483197606a1ec943fe744789d2e994bf' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\apps\\modals.tpl',
      1 => 1773274372,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347cb6915_11389081 ($_smarty_tpl) {
if (!is_callable('smarty_function_math')) require_once 'C:\\xampp\\htdocs\\panel_web\\includes\\smarty\\plugins\\function.math.php';
?>
<!-- Start Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title2" id="exampleModalLabel">¿Listo para salir, <?php echo $_smarty_tpl->tpl_vars['full_name_2']->value;?>
?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Selecciona "Cerrar sesión" si quieres terminar tu sesión actual.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=logout">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
<!-- End Logout Modal -->  

                <!-- Start User Add Bootstrap modal -->
					<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="register" autocomplete="off">
										<input type="hidden" id="submitted" name="submitted" value="Register Account">
										<input type="hidden" id="register_full_name" name="full_name" value="">
										<input type="hidden" id="register_user_email" name="user_email" value="">
										<input type="hidden" id="register_user_pass2" name="user_pass2" value="">
										<div class="summary-errors alert alert-danger alert-dismissible" style="display:none;">
											<ul class="mb-0"></ul>
										</div>
									<div class="row">
									    <div class="col-md-12 form-group">
										    <label for="v2ray_id"><i class="glyphicon glyphicon-user"></i> V2Ray UUID:</label>	
											<div class="input-group">
												<input id="v2ray_id" type="text" class="form-control" name="v2ray_id" value="" autocomplete="off" readonly="readonly">
											    <div class="input-group-append">
                                                    <span class="input-group-append">
                                                        <span class="input-group-text" onclick="v2rayrefresh();" id="v2rayrefresh"><i class="fas fa-redo"></i></span>
                                                    </span>
                                                </div>
											</div>
											<small class="form-text text-muted">Haga clic para cambiar el UUID de V2Ray.</small>
										</div>
										<div id="usname" class="col-md-12 form-group">
										    <label for="user_name"><i class="glyphicon glyphicon-user"></i> Nombre de usuario:</label>	
											<div class="input-group">
												<input id="user_name" type="text" class="form-control" name="user_name" value="" data-parsley-maxlength="128"
 data-parsley-minlength="3" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder="Introduzca su nombre de usuario" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass"><i class="glyphicon-lock"></i> Contraseña:</label>
												<div class="input-group">
													<input id="user_pass" type="password" class="form-control" name="user_pass" value=""
													autocomplete="new-password" ondrop="return false;" onpaste="return false;" data-parsley-equalto="#register_user_pass2" data-parsley-maxlength="128"
 data-parsley-minlength="6" placeholder="Introducir la contraseña" required>
													<div class="input-group-append">
                                                        <span class="input-group-text" href="javascript:;" onclick="toggle_password('user_pass');" id="showhide2"><i class="fas fa-eye"></i></span>
                                                    </div> 
												</div>
												<div class="progress password-meter mt-1" id="signuppwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div>
										<div id="role_mgt" class="col-md-12 form-group">
										    <label for="role_acct"><i class="glyphicon glyphicon-stats"></i> Gestión de roles:</label>
											<div class="input-group">
												<select class="custom-select" id="role_acct" name="role_acct" title="Role Management">
													<option value="1" selected="selected">Cliente normal</option>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="2">Sub-Reseller</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="3">Reseller</option>
													<?php }?> 
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="5">Sub-Administrador</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
													<option value="4">Administrador</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1) {?>
													<option value="99">Super-Administrador</option>
													<?php }?>
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                                </div>
											</div>
										</div>
										<div id="role_mgt2" class="col-md-12 form-group d-none">
											<label for="role"><i class="glyphicon glyphicon-stats"></i> Gestión de roles:</label>
											<div class="input-group">
												<select class="custom-select" id="role" name="role" title="Role Management">
													<option value="1" selected="selected">Cliente normal</option>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="2">Sub-Reseller</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="3">Reseller</option>
													<?php }?> 
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
													<option value="5">Sub-Administrador</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
													<option value="4">Administrador</option>
													<?php }?>
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1) {?>
													<option value="99">Super-Administrador</option>
													<?php }?>
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                                </div>
											</div>
										</div>
										
										<div id="client_mode" class="col-md-12 form-group">
										    <label for="client_type"><i class="icon wb-users"></i> Tipo de cliente:</label>
											<div class="input-group">
												<select class="custom-select" id="client_type" name="client_type" title="Client Type">
													<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
">Cliente Premium</option>
													<!--option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP Client</option-->
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Cliente privado</option>
													<?php }?>
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
											</div>
										</div>
										
									</div>
									<div class="row">
										<!--div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass2"><i class="glyphicon-lock"></i> Confirm Password:</label>
												<div class="input-group">
													<input id="user_pass2" type="password" class="form-control" name="user_pass2" value=""
													autocomplete="off" ondrop="return false;" onpaste="return false;" required>
													<a class="input-group-addon" href="javascript:;" onclick="new_password('user_pass2');" id="newshowhide"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="chkpwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div-->
									</div>
									    <div class="row">
    										<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
        										<div id="upline" class="col-md-12 form-group">
        										    <label class="control-label" for="resellers"><i class="glyphicon glyphicon-stats"></i> Upline User:</label>
        											<div class="input-group">
        												<select class="custom-select" id="resellers" name="resellers" title="Reseller">
        												</select>
        												<div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                        </div>
        											</div>
        										</div>
    										<?php }?>
										    <input type="hidden" id="secret" name="secret">	
									    </div>
										<div class="control-group form-group">
											<div class="modal-footer">
            									<button type="submit" id="submitRegister" name="submitRegister" class="btn btn-success btn-xs waves-effect waves-light">
            									    <span id="loader"></span>
            									    <span id="butext"></span>
            									</button>
            									<button type="button" class="btn btn-danger btn-xs waves-effect waves-light" data-dismiss="modal">Cancelar</button>
            								</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>	
				<!-- End User Add Bootstrap modal -->
				
				<!-- Start User Details Bootstrap modal -->
					<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="view_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body" id="content">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="username" class="font-weight-bold">Username:</label>
												<div class="input-group">
    												<div class="form-control" id="username"></div>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="password" class="font-weight-bold">Password:</label>
												<div class="input-group">
    												<div class="form-control" id="password"></div>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-lock"></i></span>
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="v2rayid" class="font-weight-bold">V2Ray UUID:</label>
												<div class="input-group">
    												<div class="form-control" id="v2rayid"></div>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="regdate" class="font-weight-bold">Date Created:</label>
												<div class="input-group">
    												<div class="form-control" id="regdate"></div>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="premiumduration" class="font-weight-bold">Date Expired:</label>
												<div class="input-group">
    												<div class="form-control" id="premiumduration"></div>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
								</div>
								<div class="control-group form-group">
									<div class="modal-footer">
            							<button type="button" class="btn btn-primary btn-xs waves-effect waves-light" data-dismiss="modal" onclick="downbold()">Download</button>
            						</div>
								</div>
							</div>
						</div>
					</div>
				<!-- End User Details Bootstrap modal -->
					
				<!-- Start Bootstrap modal -->
					<div class="modal fade" id="credits_form" tabindex="-1" role="dialog" aria-labelledby="credits_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="formCredits" name="formCredits" method="post">
										<div class="row">
												<div class="form-group col-md-6">
												    <label class="control-label"><i class="fas fa-user"></i> Username:</label>
													<div id="get_user" class="form-control"></div>
												</div>
												<div class="form-group col-md-6">
													<label class="control-label"><i class="fas fa-coins"></i> Balance:</label>
													<div id="get_credits" class="form-control"></div>
												</div>
										</div>
										<div class="row">	
												<div class="form-group col-md-6">
													<label class="control-label" for="add_credits"><i class="fas fa-cart-plus"></i> Credits:</label>
													<input class="form-control" type="text" id="add_credits" name="add_credits" <?php if ($_smarty_tpl->tpl_vars['sub_admin_2']->value == 1) {?>min="1" max="<?php echo $_smarty_tpl->tpl_vars['credits_2']->value;?>
"<?php }?>>
												</div>
												<input type="hidden" class="form-control" id="credits_secret" name="credits_secret">
												<input type="hidden" class="form-control" id="credits_code" name="credits_code">
												<input type="hidden" class="form-control" id="submitted" name="submitted" value="Add Credits">
												<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == '1' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator') {?>
												<div class="form-group col-md-6">
												    <label class="control-label"><i class="fas fa-coins"></i> Action:</label>
													<select class="form-control" id="category" name="category">
														<option value="<?php echo $_smarty_tpl->tpl_vars['add_encrypt']->value;?>
" selected="selected">Add Credits</option>
														<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == '1' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
														<option value="<?php echo $_smarty_tpl->tpl_vars['substract_encrypt']->value;?>
">Substract Credits</option>
														<?php }?>
													</select>
												</div>
												<?php } else { ?>
												<input type="hidden" class="form-control" id="category" name="category" value="<?php echo $_smarty_tpl->tpl_vars['add_encrypt']->value;?>
">
												<?php }?>
										</div>
										<div class="control-group form-group">
													<div class="modal-footer">
														<button type="submit" id="submitReseller" name="submitReseller" class="btn btn-primary">Save</button>
														<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
														<span align="left" id="loading"></span>
													</div>
												</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
					
					<!-- Start Duration Bootstrap modal -->
					<div class="modal fade" id="voucher_form" tabindex="-1" role="dialog" aria-labelledby="voucher_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="formVouchers" name="formVouchers" method="post" class="form-horizontal padding-20">
										<input type="hidden" id="submitted" name="submitted" value="Generate Voucher">
										<div class="form-group">
											<label class="control-label" for="qty">Duration of extension:</label>
											<div class="input-group">   
    											<select class="custom-select credits" id="qty" name="qty">

  
  <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == '1' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
    <option value="1">1 crédito = 7 días</option>
    <option value="2">2 créditos = 15 días</option>

    
    <?php echo smarty_function_math(array('assign'=>'loop_admin','equation'=>"floor((max-3)/3)+1",'max'=>120),$_smarty_tpl);?>

    <?php
$__section_ad_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_ad']) ? $_smarty_tpl->tpl_vars['__smarty_section_ad'] : false;
$__section_ad_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['loop_admin']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_ad_0_start = min(0, $__section_ad_0_loop);
$__section_ad_0_total = min(($__section_ad_0_loop - $__section_ad_0_start), $__section_ad_0_loop);
$_smarty_tpl->tpl_vars['__smarty_section_ad'] = new Smarty_Variable(array());
if ($__section_ad_0_total != 0) {
for ($__section_ad_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_ad']->value['index'] = $__section_ad_0_start; $__section_ad_0_iteration <= $__section_ad_0_total; $__section_ad_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_ad']->value['index']++){
?>
      <?php echo smarty_function_math(array('assign'=>'i','equation'=>"3 + (idx*3)",'idx'=>(isset($_smarty_tpl->tpl_vars['__smarty_section_ad']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_ad']->value['index'] : null)),$_smarty_tpl);?>

      <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
 créditos = <?php echo smarty_function_math(array('equation'=>"(x/3)*30",'x'=>$_smarty_tpl->tpl_vars['i']->value),$_smarty_tpl);?>
 días</option>
    <?php
}
}
if ($__section_ad_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_ad'] = $__section_ad_0_saved;
}
?>
  <?php }?>


  
  <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value != '1' && $_smarty_tpl->tpl_vars['user_level_2']->value != 'superadmin') {?>
    <?php if ($_smarty_tpl->tpl_vars['credits_2']->value <= 0) {?>
      <option>Insufficient Balance</option>
    <?php } else { ?>

      <?php if ($_smarty_tpl->tpl_vars['credits_2']->value >= 1) {?>
        <option value="1">1 crédito = 7 días</option>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['credits_2']->value >= 2) {?>
        <option value="2">2 créditos = 15 días</option>
      <?php }?>

      
      <?php if ($_smarty_tpl->tpl_vars['credits_2']->value >= 3) {?>
        <?php echo smarty_function_math(array('assign'=>'loop_user','equation'=>"floor((c-3)/3)+1",'c'=>$_smarty_tpl->tpl_vars['credits_2']->value),$_smarty_tpl);?>

        <?php
$__section_cr_1_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_cr']) ? $_smarty_tpl->tpl_vars['__smarty_section_cr'] : false;
$__section_cr_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['loop_user']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_cr_1_start = min(0, $__section_cr_1_loop);
$__section_cr_1_total = min(($__section_cr_1_loop - $__section_cr_1_start), $__section_cr_1_loop);
$_smarty_tpl->tpl_vars['__smarty_section_cr'] = new Smarty_Variable(array());
if ($__section_cr_1_total != 0) {
for ($__section_cr_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_cr']->value['index'] = $__section_cr_1_start; $__section_cr_1_iteration <= $__section_cr_1_total; $__section_cr_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_cr']->value['index']++){
?>
          <?php echo smarty_function_math(array('assign'=>'i','equation'=>"3 + (idx*3)",'idx'=>(isset($_smarty_tpl->tpl_vars['__smarty_section_cr']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_cr']->value['index'] : null)),$_smarty_tpl);?>

          <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
 créditos = <?php echo smarty_function_math(array('equation'=>"(x/3)*30",'x'=>$_smarty_tpl->tpl_vars['i']->value),$_smarty_tpl);?>
 días</option>
        <?php
}
}
if ($__section_cr_1_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_cr'] = $__section_cr_1_saved;
}
?>
      <?php }?>

    <?php }?>
  <?php }?>

</select>


    											<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                </div>
    										</div>
										</div>
										
										<div class="form-group">
										    <label for="category"><i class="icon wb-users"></i> Client Type:</label>
											<div class="input-group">
												<select class="custom-select" id="category" name="category" title="Client Type">
													<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
">Premium Client</option>
													<!--option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP Client</option-->
													<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Private Client</option>
													<?php }?>
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Save</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<input type="hidden" class="form-control" id="voucher_code" name="voucher_code">
												<input type="hidden" class="form-control" id="voucher_secret" name="voucher_secret">
												<div id="vouchers_loader"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Duration Bootstrap modal -->
					
				<!-- Start Trial Bootstrap modal -->
					<div class="modal fade" id="instant_form" tabindex="-1" role="dialog" aria-labelledby="instant_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="formUsers" name="formUsers" method="post" class="form-horizontal padding-20">
									    
									    <div class="form-group">
										    <label for="add_users"><i class="glyphicon glyphicon-user"></i>Number of users:</label>	
											<div class="input-group">
												<input class="form-control" type="text" id="add_users" name="add_users" max="5" min="1" placeholder="Number of users" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="form-group">
										    <label for="prefix"><i class="glyphicon glyphicon-user"></i>Username Prefix:</label>	
											<div class="input-group">
												<input class="form-control" type="text" id="prefix" name="prefix" placeholder="Enter username prefix" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-edit"></i></span>
                                                </div>
											</div>
										</div>
										
										
										<div class="form-group">
											<label class="control-label" for="generate_type">Client Type:</label>
    										<div class="input-group">
    											<select class="custom-select" id="generate_type" name="generate_type" title="Client Type">
    												<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
" selected="selected">Premium Client</option>
    												<!--option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP Client</option-->
    												<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
    												<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Private Client</option>
    												<?php }?>
    											</select>
    											<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
    										</div>
										</div>
										
										
										<input type="hidden" class="form-control" id="submitted" name="submitted" value="Generate Account">
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Save</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<div id="generate_loader"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Trial Bootstrap modal -->
					
					<!-- Start Bootstrap modal -->
					<div class="modal fade" id="changepwd_modal" tabindex="-1" role="dialog" aria-labelledby="changepwd_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="change_pwd" name="change_pwd">
										<input type="hidden" id="submitted" name="submitted" value="Change Password">
										<div class="row">
											<div class="col-md-12">
											    
												<div class="col-md-12 form-group">
												    <label for="old_user_pass">Old Password:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="old_user_pass" name="old_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Enter old password" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="toggle_passwordz('old_user_pass');" id="showhidez2"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="oldpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>
												
												<div class="col-md-12 form-group">
												    <label for="new_user_pass">New Password:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass" name="new_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Enter new password" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="new_passwordz('new_user_pass');" id="newshowhidez2"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="newpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>
												
												<div class="col-md-12 form-group">
												    <label for="new_user_pass2">Verify Password:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass2" name="new_user_pass2"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Verify new password" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="new_passwordz2('new_user_pass2');" id="newshowhidez3"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="chkpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitChangePWD" name="submitChangePWD" class="btn btn-success">Change Password</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<span align="left" id="loading"></span>
											</div>
										</div>
									</form>
								</div><!-- /.modal-body -->
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				<!-- End Bootstrap modal -->
				
				<div class="modal fade" id="modal_server" tabindex="-1" role="dialog" aria-labelledby="modal_server" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="server_frm" name="server_frm">
										<input type="hidden" id="server_id" name="server_id">
										<input type="hidden" id="submitted" name="submitted" value="Server Upload">
										<div class="row">
											<div class="form-group col-md-12">
													<label class="control-label" for="server_name">Server Name</label>
													<input class="form-control" id="server_name" name="server_name" placeholder="Server Name" />
											</div>
											<!--div class="form-group col-md-12">
													<label class="control-label" for="server_category">Server Category</label>
													<select class="form-control" id="server_category" name="server_category">
														<option value="premium" selected="selected">Premium Server</option>
													</select>
											</div-->
											<div class="form-group col-md-12">
													<label class="control-label" for="server_ip">Server IP</label>
													<input class="form-control" id="server_ip" name="server_ip" placeholder="IP Address" />
											</div>
										</div>
										<div class="row">											
											<div class="form-group col-md-12">
													<label class="control-label" for="server_port">Port</label>
													<input class="form-control" id="server_port" name="server_port" placeholder="Server Port" />
											</div>
											<!--div class="form-group col-md-12">
													<label class="control-label input-group-addon" for="server_folder">Port</label>
													<input class="form-control" id="server_folder" name="server_folder" placeholder="Server Folder" />
											</div>
											<div class="form-group col-md-12">
													<label class="control-label input-group-addon" for="server_tcp">TCP</label>
													<input class="form-control" id="server_tcp" name="server_tcp" placeholder="Server TCP" />
											</div-->
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitServer" name="submitServer" class="btn btn-success">Server Upload</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<span align="left" id="loading"></span>
											</div>
										</div>
									</form>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<!-- End Bootstrap modal -->
					
						<div class="modal fade profile_frm" id="profile_modal" tabindex="-1" role="dialog" aria-labelledby="profile_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								    <div class="modal-header">
										<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
							<div class="modal-body">
								<form id="profile_frm" name="profile_frm" accept-charset="UTF-8" enctype="multipart/form-data"
								method="POST">
									<input type="hidden" id="submitted" name="submitted" value="Edit Profile">
									<div class="row">
										<div class="col-md-12">
										    <div class="col-md-12 form-group">
    											<label for="profile_fb">Profile Image:</label>
    											<div class="custom-file">
    											    <input type="file" class="custom-file-input" id="images" name="images[]" data-btn-text="Select Image">
                                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 form-group">
    										    <label for="full_name">Full Name:</label>	
    											<div class="input-group">
    												<input id="full_name" type="text" class="form-control" name="full_name" value="" autocomplete="off" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
    											</div>
    										</div>
    										
    										<div class="col-md-12 form-group">
    										    <label for="user_email">Email:</label>	
    											<div class="input-group">
    												<input id="user_email" type="email" class="form-control" name="user_email" value="" autocomplete="off" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    </div>
    											</div>
    										</div>
    										
									        <div class="col-md-12 form-group">
    										    <label for="profile_number">Phone Number:</label>	
    											<div class="input-group">
    												<input id="profile_number" type="text" class="form-control" name="profile_number" value="" autocomplete="off" onkeypress="return IsNumeric(event);"
												    ondrop="return false;" onpaste="return false;" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    </div>
    											</div>
    										</div>
									
											<div class="col-md-12 form-group">
    										    <label for="profile_fb">Facebook:</label>	
    											<div class="input-group">
    												<input id="profile_fb" type="text" class="form-control capitalize" name="profile_fb" value="" autocomplete="off" onpaste="return false;" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                                    </div>
    											</div>
    										</div>
												
											<div class="col-md-12 form-group">
    										    <label for="profile_address">Address:</label>	
    											<div class="input-group">
    												<textarea id="profile_address" class="form-control" name="profile_address" rows="2" wrap="hard" required></textarea>
    											</div>
    										</div>
        									
										</div>	
									</div>
									<input type="hidden"  id="profile_secret" name="profile_secret">

									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submitProfile" name="submitProfile" class="btn btn-success">Edit Profile</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
											<span align="left" id="loading"></span>
										</div>
									</div>
								</form>
							</div><!-- /.modal-body -->
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->
				
				<!-- Start Manual Duration modal -->
					<div class="modal fade" id="duration_form" tabindex="-1" role="dialog" aria-labelledby="duration_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="formDuration" name="formDuration">
									<input type="hidden" id="submitted" name="submitted" value="Reload Durations">
									
									
									<p id="conv2"></p>
									
									<hr>
									
									<div class="form-group">
										<label class="control-label" for="duration">Duration:</label>
										<div class="input-group">   
    										<select class="custom-select credits" id="duration" name="duration">
    											<option value="">-- Choose Duration --</option>
    											<?php
$_from = $_smarty_tpl->tpl_vars['duration']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_i_0_saved_item = isset($_smarty_tpl->tpl_vars['i']) ? $_smarty_tpl->tpl_vars['i'] : false;
$__foreach_i_0_saved_key = isset($_smarty_tpl->tpl_vars['id']) ? $_smarty_tpl->tpl_vars['id'] : false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['i']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['id']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
$__foreach_i_0_saved_local_item = $_smarty_tpl->tpl_vars['i'];
?>
    											<?php echo $_smarty_tpl->tpl_vars['i']->value;?>

    											<?php
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_local_item;
}
if ($__foreach_i_0_saved_item) {
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_item;
}
if ($__foreach_i_0_saved_key) {
$_smarty_tpl->tpl_vars['id'] = $__foreach_i_0_saved_key;
}
?>
    										</select>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                            </div>
    									</div>
									</div>
									
									<div class="form-group">
										<label class="control-label" for="category">Client Type:</label>
    									<div class="input-group">
    										<select class="custom-select conv2" id="category" name="category" title="Client Type">
    											<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
">Premium</option>
    											<!--option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP</option-->
    											<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Private Client</option>
												<?php }?>
    										</select>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            </div>
    									</div>
									</div>
									
									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submit" name="submit" class="btn btn-success">Submit</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
											<input type="hidden" class="form-control" id="duration_code" name="duration_code">
											<input type="hidden" class="form-control" id="duration_secret" name="duration_secret">
										</div>
									</div>
								</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Manual Duration modal -->
				
				<!-- End Bootstrap modal -->
					<div class="modal fade" id="convert_form" tabindex="-1" role="dialog" aria-labelledby="convert_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="convertForm" name="convertForm" method="post" class="form-horizontal">
										<div id="success_convert"></div>
										<p id="conv"></p>
					                    <hr>
					                    <div class="form-group">
    										<label class="control-label" for="category">Duration Category:</label>
        									<div class="input-group">
        										<select class="custom-select conv" id="category" name="category" title="Client Type">
        											<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
">Premium</option>
        											<option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP</option>
        											<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Private</option>
        										</select>
        										<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
        									</div>
    									</div>
					
										<div class="control-group form-group">
    										<div class="modal-footer">
    											<button type="button" class="btn btn-success" id="convertSubmit" name="convertSubmit" onclick="conversion()">Convert</button>
        										<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        										<input type="hidden" class="form-control" id="secret" name="secret" value="<?php echo $_smarty_tpl->tpl_vars['secret']->value;?>
" />
										        <input type="hidden" id="submitted" name="submitted" value="Convert Duration" />
        										<!--div id="conversion_loader"></div-->
    										</div>
    									</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Bootstrap modal -->
				
				<!-- Start Self Reload modal -->
					<div class="modal fade" id="selfreload_form" tabindex="-1" role="dialog" aria-labelledby="selfreload_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="formSelf" name="formSelf" method="post" class="form-horizontal padding-20">
										<input type="hidden" id="submitted" name="submitted" value="Generate Voucher">
										
										<div class="form-group">
										    <label id="qty">Quantity:</label>	
											<div class="input-group">
												<input type="number" class="form-control" id="qty" name="qty" min="1" 
        											<?php if ($_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller') {?>
        											max="<?php echo $_smarty_tpl->tpl_vars['credits_2']->value;?>
"
        										<?php }?> autocomplete="off" onkeypress="return IsNumeric(event);"
        											ondrop="return false;" onpaste="return false;" value="1" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="form-group d-none">
    										<label class="control-label" for="category">Duration Category:</label>
        									<div class="input-group">
        										<select class="custom-select credits" id="category" name="category" title="Client Type">
        											<option value="<?php echo $_smarty_tpl->tpl_vars['premium_encrypt']->value;?>
">Premium</option>
        											<!--option value="<?php echo $_smarty_tpl->tpl_vars['vip_encrypt']->value;?>
">VIP</option>
        											<option value="<?php echo $_smarty_tpl->tpl_vars['private_encrypt']->value;?>
">Private</option-->
        										</select>
        										<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
        									</div>
    									</div>
										
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Save</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<input type="hidden" class="form-control" id="code" name="code" value="<?php echo $_smarty_tpl->tpl_vars['secret']->value;?>
">
												<div id="vouchers_loader"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Self Reload modal -->
<?php }
}
