<!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title2" id="exampleModalLabel">Ready to Leave {$full_name_2}?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
  
<!-- Add User Modal-->
  <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave {$full_name_2}?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
  
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
									<form id="register">
										<input type="hidden" id="submitted" name="submitted" value="Register Account">
									<div class="row">
										<div id="usname" class="col-md-12 form-group">
										    <label for="user_name"><i class="glyphicon glyphicon-user"></i> Usuario:</label>	
											<div class="input-group">
												<input id="user_name" type="text" class="form-control" name="user_name" value="" data-parsley-maxlength="20" data-parsley-minlength="3" autocomplete="off" placeholder="Enter Username" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
											</div>
										</div>
										<div id="role_mgt" class="col-md-12 form-group">
										    <label for="role_acct"><i class="glyphicon glyphicon-stats"></i> Role Management:</label>
											<div class="input-group">
												<select class="custom-select" id="role_acct" name="role_acct" title="Role Management">
													<option value="1" selected="selected">Normal Client</option>
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub-Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if} 
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrator</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">Administrator</option>
													{/if}
													{if $user_id_2 == 1}
													<option value="99">Super-Administrator</option>
													{/if}
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                                </div>
											</div>
										</div>
										<div id="role_mgt2" class="col-md-12 form-group d-none">
											<label for="role"><i class="glyphicon glyphicon-stats"></i> Role Management:</label>
											<div class="input-group">
												<select class="custom-select" id="role" name="role" title="Role Management">
													<option value="1" selected="selected">Normal Client</option>
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub-Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if} 
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrator</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">Administrator</option>
													{/if}
													{if $user_id_2 == 1}
													<option value="99">Super-Administrator</option>
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
													<option value="{$premium_encrypt}">Premium Client</option>
													<option value="{$vip_encrypt}">VIP Client</option>
													<option value="{$private_encrypt}">Cliente Private</option>
												</select>
												<div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass"><i class="glyphicon-lock"></i> Password:</label>
												<div class="input-group">
													<input id="user_pass" type="password" class="form-control" name="user_pass" value=""
													autocomplete="off" ondrop="return false;" onpaste="return false;" data-parsley-equalto="#user_pass2" data-parsley-maxlength="20" data-parsley-minlength="6" placeholder="Enter Password" required>
													<div class="input-group-append">
                                                        <span class="input-group-text" href="javascript:;" onclick="toggle_password('user_pass');" id="showhide"><i class="fas fa-eye"></i></span>
                                                    </div> 
												</div>
												<div class="progress password-meter mt-1" id="signuppwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div>
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
				
					<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="view_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="username"><i class="fas fa-user"></i> Usuario:</label>
												<div class="input-group">
													<div class="form-control" id="username"></div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass2"><i class="fas fa-user-lock"></i> Password:</label>
												<div class="input-group">
													<div class="form-control" id="password"></div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass"><i class="fas fa-user-clock"></i> Date Created:</label>
												<div class="input-group">
													<div class="form-control" id="regdate"></div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="user_pass2"><i class="fas fa-user-clock"></i> Expiration Date:</label>
												<div class="input-group">
													<div class="form-control" id="premiumduration"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
					
					<!-- End Bootstrap modal -->
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
    												<option value="{$vip_encrypt}">VIP Client</option>
    												<option value="{$private_encrypt}">Cliente Private</option>
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
												<div class="form-group">
													<label class="control-label" for="old_user_pass"><i class="fas fa-lock"></i> Old Password:</label>
														<input type="password" class="form-control" id="old_user_pass" name="old_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Enter Old Password" required>
												</div>

												<div class="form-group">
												    <label class="control-label" for="new_user_pass"><i class="fas fa-lock"></i> New Password:</label>
														<input type="password" class="form-control" id="new_user_pass" name="new_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Enter New Password" required>
												</div>

												<div class="form-group">
													<label class="control-label" for="new_user_pass2"><i class="fas fa-lock"></i> Confirm New Password:</label>
														<input type="password" class="form-control" id="new_user_pass2" name="new_user_pass2"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Verify New Password" required>
												</div>
											</div>
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitChangePWD" name="submitChangePWD" class="btn btn-success">Change Password</button>
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
				
					
					<div class="modal fade profile_frm" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
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
											<div class="form-group">
												<label class="control-label" for="full_name">Full Name</label>
												<input class="form-control" id="full_name" type="text" name="full_name" value="" required> 	
											</div>
											<div class="form-group">
												<label class="control-label" for="user_email">Email Address</label>
												<input class="form-control" type="email" id="user_email" name="user_email" value="" required>
											</div>
											<div class="form-group">
												<label class="input-group-addon control-label" for="profile_number">Phone Number</label>
												<input id="profile_number" type="text" class="form-control"
												autocomplete="off" onkeypress="return IsNumeric(event);"
												ondrop="return false;" onpaste="return false;"
												maxlength="13" name="profile_number" value="" required>
											</div>
											<div class="form-group">
												<label class="control-label" for="profile_address">Address</label>
												<textarea id="profile_address" class="form-control"
												name="profile_address" rows="2" wrap="hard" required></textarea>
											</div>
											<span class="mb-3">Profile Image</span>
											<div class="form-group">
												<input type="file" id="images" name="images[]" data-btn-text="Select Image" >
											</div>
										</div>	
									</div>
									<input type="hidden"  id="profile_secret" name="profile_secret">

									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submitProfile" name="submitProfile" class="btn btn-success">Edit Profile</button>
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
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_name">NAME</label>
													<input class="form-control" id="server_name" name="server_name" placeholder="Server Name" />
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_category">CATEGORY</label>
													<select class="form-control" id="server_category" name="server_category">
														<option value="premium" selected="selected">Premium</option>
														<option value="vip">VIP</option>
														<option value="private">Private</option>
														<!--option value="ph">Philippine Server</option>
														<option value="free">Free Server</option-->
													</select>
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_ip">IP/V4</label>
													<input class="form-control" id="server_ip" name="server_ip" placeholder="IP Address" />
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_port">PORT</label>
													<input class="form-control" id="server_port" name="server_port" placeholder="Server Port" />
												</div>
											</div>
										</div>
										<div class="row">	
										    <div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="root_user">USERNAME</label>
													<input class="form-control" id="root_user" name="root_user" placeholder="Root Username" required/>
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="root_pass">PASSWORD</label>
													<input class="form-control" type="password" id="root_pass" name="root_pass" placeholder="Root Password" required/>
												</div>
											</div>
											<!--div class="form-group control-group col-md-4">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_folder">Port</label>
													<input class="form-control" id="server_folder" name="server_folder" placeholder="Server Folder" />
												</div>
											</div-->
											<!--div class="form-group control-group col-md-4">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_tcp">TCP</label>
													<input class="form-control" id="server_tcp" name="server_tcp" placeholder="Server TCP" />
												</div>
											</div-->
											<!--div class="col-md-12 text-center">
												<div id="server_parser" class="form-control col-md-12"></div>
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
									
									<div class="form-group">
										<label for="premiumduration" class="font-weight-bold">Premium Duracion:</label>
										<div class="input-group">
    										<input class="form-control" value="{$pre_duration}" disabled>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                            </div>
                                        </div>
									</div>
									<div class="form-group">
										<label for="vipduration" class="font-weight-bold">VIP Duracion:</label>
										<div class="input-group">
    										<input class="form-control" value="{$vip_duration}" disabled>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                            </div>
                                        </div>
									</div>
									<div class="form-group">
										<label for="privateduration" class="font-weight-bold">Private Duracion:</label>
										<div class="input-group">
    										<input class="form-control" value="{$pri_duration}" disabled>
    										<div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                            </div>
                                        </div>
									</div>
									
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
    										<select class="custom-select category" id="category" name="category" title="Tipo de cliente">
    											<option value="{$premium_encrypt}" selected="selected">Premium Client</option>
    											<option value="{$vip_encrypt}">VIP Client</option>
    											<option value="{$private_encrypt}">Cliente Private</option>
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
												<input type="text" class="form-control" id="qty" name="qty" min="1" 
        											{if $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
        											max="{$credits_2}"
        										{/if} autocomplete="off" onkeypress="return IsNumeric(event);"
        											ondrop="return false;" onpaste="return false;" value="1" required>
											    <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                </div>
											</div>
										</div>
										
										<div class="form-group">
    										<label class="control-label" for="category">Tipo de duracion:</label>
        									<div class="input-group">
        										<select class="custom-select credits" id="category" name="category" title="Tipo de cliente">
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