<!-- Start Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title2" id="exampleModalLabel">¿Listo para salir, {$full_name_2}?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Selecciona "Cerrar sesión" si quieres terminar tu sesión actual.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="{$base_url}index.php?p=logout">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
<!-- End Logout Modal -->  

                <style>
                    body #modal_form .modal-content,
                    body #modal_form .modal-header,
                    body #modal_form .modal-footer,
                    body #modal_form .summary-errors,
                    body #modal_form .progress,
                    body #modal_form .progress-bar,
                    body #modal_form .btn,
                    body #modal_form .form-control,
                    body #modal_form .custom-select,
                    body #modal_form .input-group-text {
                        border-radius: 3px !important;
                    }

                    body #modal_form .modal-content {
                        overflow: hidden !important;
                    }

                    body #modal_form .input-group > .form-control:not(:last-child),
                    body #modal_form .input-group > .custom-select:not(:last-child) {
                        border-top-right-radius: 0 !important;
                        border-bottom-right-radius: 0 !important;
                    }

                    body #modal_form .input-group > .input-group-append > .input-group-text {
                        border-top-left-radius: 0 !important;
                        border-bottom-left-radius: 0 !important;
                    }
                </style>

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
										<input type="hidden" id="register_mode" name="register_mode" value="add">
										<input type="hidden" id="register_full_name" name="full_name" value="">
										<input type="hidden" id="register_user_email" name="user_email" value="">
										<input type="hidden" id="register_user_pass2" name="user_pass2" value="">
										<input type="hidden" id="client_default_password_enabled" value="{$client_default_password_enabled|default:0}">
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
										<div class="col-md-12" id="register_password_group">
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
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub-Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if} 
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrador</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">Administrador</option>
													{/if}
													{if $user_id_2 == 1}
													<option value="99">Super-Administrador</option>
													{/if}
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
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub-Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if} 
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrador</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">Administrador</option>
													{/if}
													{if $user_id_2 == 1}
													<option value="99">Super-Administrador</option>
													{/if}
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                                </div>
											</div>
										</div>
										
										<div id="client_mode" class="col-md-12 form-group">
										    <label for="client_type"><i class="icon wb-users"></i> Tipo de cliente:</label>
											<div class="input-group">
												<select class="custom-select" id="client_type" name="client_type" title="Tipo de cliente">
													<option value="{$premium_encrypt}">Cliente Premium</option>
													<!--option value="{$vip_encrypt}">VIP Client</option-->
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="{$private_encrypt}">Cliente privado</option>
													{/if}
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
    										{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
        										<div id="upline" class="col-md-12 form-group d-none">
        										    <label class="control-label" for="resellers"><i class="glyphicon glyphicon-stats"></i> Upline User:</label>
        											<div class="input-group">
        												<select class="custom-select" id="resellers" name="resellers" title="Reseller">
        												</select>
        												<div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                        </div>
        											</div>
        										</div>
    										{/if}
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
												<label for="username" class="font-weight-bold">Usuario:</label>
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
												    <label class="control-label"><i class="fas fa-user"></i> Usuario:</label>
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
													<input class="form-control" type="text" id="add_credits" name="add_credits" {if $sub_admin_2 == 1}min="1" max="{$credits_2}"{/if}>
												</div>
												<input type="hidden" class="form-control" id="credits_secret" name="credits_secret">
												<input type="hidden" class="form-control" id="credits_code" name="credits_code">
												<input type="hidden" class="form-control" id="submitted" name="submitted" value="Add Credits">
												{if $user_id_2=='1' || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
												<div class="form-group col-md-6">
												    <label class="control-label"><i class="fas fa-coins"></i> Action:</label>
													<select class="form-control" id="category" name="category">
														<option value="{$add_encrypt}" selected="selected">Add Credits</option>
														{if $user_id_2=='1' || $user_level_2 == 'superadmin'}
														<option value="{$substract_encrypt}">Substract Credits</option>
														{/if}
													</select>
												</div>
												{else}
												<input type="hidden" class="form-control" id="category" name="category" value="{$add_encrypt}">
												{/if}
										</div>
										<div class="control-group form-group">
													<div class="modal-footer">
														<button type="submit" id="submitReseller" name="submitReseller" class="btn btn-primary">Guardar</button>
														<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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

  {* SUPERADMIN/ADMIN: opciones amplias *}
  {if $user_id_2 == '1' || $user_level_2 == 'superadmin'}
    <option value="1">1 crédito = 7 días</option>
    <option value="2">2 créditos = 15 días</option>

    {* Generar 3,6,9... hasta 120 *}
    {math assign=loop_admin equation="floor((max-3)/3)+1" max=120}
    {section name=ad start=0 loop=$loop_admin}
      {math assign=i equation="3 + (idx*3)" idx=$smarty.section.ad.index}
      <option value="{$i}">{$i} créditos = {math equation="(x/3)*30" x=$i} días</option>
    {/section}
  {/if}


  {* OTROS USUARIOS: limitar por balance *}
  {if $user_id_2 != '1' && $user_level_2 != 'superadmin'}
    {if $credits_2 <= 0}
      <option>Insufficient Balance</option>
    {else}

      {if $credits_2 >= 1}
        <option value="1">1 crédito = 7 días</option>
      {/if}

      {if $credits_2 >= 2}
        <option value="2">2 créditos = 15 días</option>
      {/if}

      {* Generar 3,6,9... hasta $credits_2 *}
      {if $credits_2 >= 3}
        {math assign=loop_user equation="floor((c-3)/3)+1" c=$credits_2}
        {section name=cr start=0 loop=$loop_user}
          {math assign=i equation="3 + (idx*3)" idx=$smarty.section.cr.index}
          <option value="{$i}">{$i} créditos = {math equation="(x/3)*30" x=$i} días</option>
        {/section}
      {/if}

    {/if}
  {/if}

</select>


    											<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                </div>
    										</div>
										</div>
										
										<div class="form-group">
										    <label for="category"><i class="icon wb-users"></i> Tipo de cliente:</label>
											<div class="input-group">
												<select class="custom-select" id="category" name="category" title="Tipo de cliente">
													<option value="{$premium_encrypt}">Premium Client</option>
													<!--option value="{$vip_encrypt}">VIP Client</option-->
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="{$private_encrypt}">Cliente Private</option>
													{/if}
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Guardar</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
											<label class="control-label" for="generate_type">Tipo de cliente:</label>
    										<div class="input-group">
    											<select class="custom-select" id="generate_type" name="generate_type" title="Tipo de cliente">
    												<option value="{$premium_encrypt}" selected="selected">Premium Client</option>
    												<!--option value="{$vip_encrypt}">VIP Client</option-->
    												{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
    												<option value="{$private_encrypt}">Cliente Private</option>
    												{/if}
    											</select>
    											<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
    										</div>
										</div>
										
										
										<input type="hidden" class="form-control" id="submitted" name="submitted" value="Generate Account">
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Guardar</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
					<style>
					body #changepwd_modal .modal-content,
					body #changepwd_modal .modal-header,
					body #changepwd_modal .modal-footer {
						border-radius: 3px !important;
					}

					body #changepwd_modal .modal-content {
						overflow: hidden !important;
					}

					body #changepwd_modal .form-control,
					body #changepwd_modal .input-group-text,
					body #changepwd_modal .btn,
					body #changepwd_modal .progress,
					body #changepwd_modal .progress-bar,
					body #changepwd_modal .alert {
						border-radius: 3px !important;
					}
					</style>
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
										<input type="hidden" id="submitted" name="submitted" value="Cambiar contraseña">
										<div class="row">
											<div class="col-md-12">
											    
												<div class="col-md-12 form-group">
												    <label for="old_user_pass">Contraseña actual:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="old_user_pass" name="old_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Ingresa la contraseña actual" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="toggle_passwordz('old_user_pass');" id="showhidez2"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="oldpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>
												
												<div class="col-md-12 form-group">
												    <label for="new_user_pass">Nueva contraseña:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass" name="new_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Ingresa la nueva contraseña" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="new_passwordz('new_user_pass');" id="newshowhidez2"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="newpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>
												
												<div class="col-md-12 form-group">
												    <label for="new_user_pass2">Confirmar contraseña:</label>	
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass2" name="new_user_pass2"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Confirma la nueva contraseña" required>
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
												<button type="submit" id="submitChangePWD" name="submitChangePWD" class="btn btn-success">Cambiar contraseña</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
												<span align="left" id="loading"></span>
											</div>
										</div>
									</form>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<!-- End Bootstrap modal -->
					
						<style>
						body #profile_modal .modal-content,
						body #profile_modal .modal-header,
						body #profile_modal .modal-footer {
							border-radius: 3px !important;
						}

						body #profile_modal .modal-content {
							overflow: hidden !important;
						}

						body #profile_modal .form-control,
						body #profile_modal .input-group-text,
						body #profile_modal .custom-file-label,
						body #profile_modal .custom-file-label::after,
						body #profile_modal .profile-access-static,
						body #profile_modal .btn {
							border-radius: 3px !important;
						}

						body #profile_modal .profile-access-static {
							height: auto !important;
							min-height: calc(1.5em + 0.75rem + 2px) !important;
							padding: 0.5rem 0.75rem !important;
							line-height: 1.2 !important;
							word-break: break-word !important;
						}

						body #profile_modal .profile-access-role {
							display: inline-flex;
							align-items: center;
							justify-content: center;
							margin-bottom: 6px;
							padding: 2px 7px 3px !important;
							border-radius: 3px !important;
							background: linear-gradient(180deg, var(--pm-blue-600) 0%, var(--pm-blue-700) 100%) !important;
							color: #ffffff !important;
							font-family: inherit !important;
							font-size: .86rem !important;
							font-weight: 600 !important;
							letter-spacing: 0 !important;
							text-transform: none !important;
							line-height: 1 !important;
							vertical-align: middle !important;
						}

						body #profile_modal #profile_access_text {
							display: block;
							color: #244971 !important;
							font-family: inherit !important;
							font-size: 1.04rem !important;
							font-weight: 600 !important;
							letter-spacing: 0 !important;
							line-height: 1.2 !important;
						}
						</style>
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
    											<div id="profile_access_value" class="form-control profile-access-static" aria-live="polite">
    												<span id="profile_access_role" class="profile-access-role">Cliente normal</span>
    												<span id="profile_access_text"></span>
    											</div>
    										</div>

    										<div class="col-md-12 form-group">
    											<label for="profile_fb">Imagen de perfil:</label>
    											<div class="custom-file">
													<input type="file" class="custom-file-input" id="images" name="images[]" data-btn-text="Seleccionar imagen">
													<label class="custom-file-label" for="customFile">Seleccionar archivo</label>
                                                </div>
                                            </div>
    										
                                            <div class="col-md-12 form-group">
    										    <label for="full_name">Nombre:</label>	
    											<div class="input-group">
    												<input id="full_name" type="text" class="form-control" name="full_name" value="" autocomplete="off" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
    											</div>
    										</div>
    										
									        <div class="col-md-12 form-group">
    										    <label for="profile_number">Telefono:</label>	
    											<div class="input-group">
    												<input id="profile_number" type="text" class="form-control" name="profile_number" value="" autocomplete="off" inputmode="numeric" onkeypress="return IsNumeric(event);" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    </div>
    											</div>
    										</div>
									
											<div class="col-md-12 form-group">
    										    <label for="profile_fb">Facebook:</label>	
    											<div class="input-group">
    												<input id="profile_fb" type="text" class="form-control" name="profile_fb" value="" autocomplete="off" placeholder="https://facebook.com/tuusuario" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                                    </div>
    											</div>
    										</div>
												
											<div class="col-md-12 form-group">
    										    <label for="profile_address">Direccion:</label>	
    											<div class="input-group">
    												<textarea id="profile_address" class="form-control" name="profile_address" rows="2" wrap="hard" required></textarea>
    											</div>
    										</div>
        									
										</div>	
									</div>
									<input type="hidden"  id="profile_secret" name="profile_secret">

									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submitProfile" name="submitProfile" class="btn btn-success">Guardar perfil</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
										<label class="control-label" for="duration">Duracion:</label>
										<div class="input-group">   
    										<select class="custom-select credits" id="duration" name="duration">
    											<option value="">-- Elegir duracion --</option>
    											{foreach from=$duration key=id item=i}
    											{$i}
    											{/foreach}
    										</select>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                            </div>
    									</div>
									</div>
									
									<div class="form-group">
										<label class="control-label" for="category">Tipo de cliente:</label>
    									<div class="input-group">
    										<select class="custom-select conv2" id="category" name="category" title="Tipo de cliente">
    											<option value="{$premium_encrypt}">Premium</option>
    											<!--option value="{$vip_encrypt}">VIP</option-->
    											{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="{$private_encrypt}">Cliente Private</option>
												{/if}
    										</select>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            </div>
    									</div>
									</div>
									
									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submit" name="submit" class="btn btn-success">Aplicar</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
    										<label class="control-label" for="category">Tipo de duracion:</label>
        									<div class="input-group">
        										<select class="custom-select conv" id="category" name="category" title="Tipo de cliente">
        											<option value="{$premium_encrypt}">Premium</option>
        											<option value="{$vip_encrypt}">VIP</option>
        											<option value="{$private_encrypt}">Private</option>
        										</select>
        										<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
        									</div>
    									</div>
					
										<div class="control-group form-group">
    										<div class="modal-footer">
    											<button type="button" class="btn btn-success" id="convertSubmit" name="convertSubmit" onclick="conversion()">Convertir</button>
        										<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        										<input type="hidden" class="form-control" id="secret" name="secret" value="{$secret}" />
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
										    <label id="qty">Cantidad:</label>	
											<div class="input-group">
												<input type="number" class="form-control" id="qty" name="qty" min="1" 
        											{if $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
        											max="{$credits_2}"
        										{/if} autocomplete="off" onkeypress="return IsNumeric(event);"
        											ondrop="return false;" onpaste="return false;" value="1" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="form-group d-none">
    										<label class="control-label" for="category">Tipo de duracion:</label>
        									<div class="input-group">
        										<select class="custom-select credits" id="category" name="category" title="Tipo de cliente">
        											<option value="{$premium_encrypt}">Premium</option>
        											<!--option value="{$vip_encrypt}">VIP</option>
        											<option value="{$private_encrypt}">Private</option-->
        										</select>
        										<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
        									</div>
    									</div>
										
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Guardar</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
												<input type="hidden" class="form-control" id="code" name="code" value="{$secret}">
												<div id="vouchers_loader"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<!-- End Self Reload modal -->
