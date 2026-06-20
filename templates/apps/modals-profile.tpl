
					
					
					<!-- Start Bootstrap modal -->
					<div class="modal fade" id="changepwd_modal" tabindex="-1" role="dialog" aria-labelledby="changepwd_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<form id="change_pwd" name="change_pwd">
										<input type="hidden" id="submitted" name="submitted" value="Change Password">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
												    
													<div class="input-group">
														<input type="password" class="form-control" id="old_user_pass" name="old_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Old Password" required>
														<div class="input-group-append">
                                                            <span class="input-group-text" href="javascript:void(0);" onclick="toggle_passwordz('old_user_pass');" id="showhidez2"><i class="fas fa-eye"></i></span>
                                                        </div> 
													</div>
													<div class="progress password-meter" id="oldpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>

												<div class="form-group">
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass" name="new_user_pass"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="New Password" required>
														<a class="input-group-addon" href="javascript:;" onclick="new_passwordz('new_user_pass');" id="newshowhidez2"><i class="glyphicon glyphicon-eye-open"></i></a>
													</div>
													<div class="progress password-meter" id="newpwdMeter">
														<div class="progress-bar"></div>
													</div>
												</div>

												<div class="form-group">
													<div class="input-group">
														<input type="password" class="form-control" id="new_user_pass2" name="new_user_pass2"
														autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Verify Password" required>
														<a class="input-group-addon" href="javascript:;" onclick="new_passwordz2('new_user_pass2');" id="newshowhidez3"><i class="glyphicon glyphicon-eye-open"></i></a>
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
				
					
					
					