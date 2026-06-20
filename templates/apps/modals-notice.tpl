<!-- Bootstrap modal -->
					<div class="modal fade" id="download_view" tabindex="-1" role="dialog" aria-labelledby="download_view" aria-hidden="true">
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
											<div id="output_msg"></div>
											<hr />
											<div id="output_file"></div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-danger" data-dismiss="modal">
											Exit
										</button>
									</div>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					
					<div class="modal fade" id="download_form" tabindex="-1" role="dialog" aria-labelledby="download_form" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"></h5>
									<button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
								</div>
								<div class="modal-body">
									<form id="dlform" name="dlform" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>
										<input type="hidden" id="download_id" name="download_id">
										<input type="hidden" class="dl_frm" id="submitted" name="submitted" value="Download Upload">
										<div class="row">
										    
										    <div class="col-md-12 form-group">
    										    <label for="download_title">Titulo:</label>	
    											<div class="input-group">
    												<input class="form-control" type="text" id="download_title" name="download_title" placeholder="Ingresa el Titulo" required>
    											    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                                    </div>
    											</div>
    										</div>
    										
    										<div class="col-md-6 form-group">
    										    <label for="download_category">Visibilidad:</label>
    											<div class="input-group">
    												<select class="custom-select" id="download_category" name="download_category">
    													<option value="public">Publico</option>
														<option value="seller">Vendedor</option>
    												</select>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fab fa-expeditedssl"></i></span>
                                                    </div>
    											</div>
    										</div>
    										
    										<div class="col-md-6 form-group">
    										    <label for="download_network">Categoria:</label>
    											<div class="input-group">
    												<select class="custom-select" id="download_network" name="download_network">
    													<option value="NOTICE">Noticia</option>
														<option value="UPDATE">Actualizacion</option>
    												</select>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                                    </div>
    											</div>
    										</div>
											
											<div class="col-md-6 form-group">
    										    <label for="download_device">Plataforma:</label>
    											<div class="input-group">
    												<select class="custom-select" id="download_device" name="download_device">
    													<option value="ANDROID">Android</option>
														<option value="IOS">iOS</option>
														<option value="WINDOWS">Windows</option>
														<option value="CONFIG">Configs</option>
														<option value="OTHERS">Otros</option>
    												</select>
    												<div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-desktop"></i></span>
                                                    </div>
    											</div>
    										</div>
											
											<div class="col-md-6 form-group">
    										    <label for="download_file">Subir archivo:</label>
    											<div class="input-group">
    												<input type="file"  id="download_file" name="download_file" data-btn-text="Select a File">
    											</div>
    											<small class="text-muted block"> Carga máxima de archivos: 50MB (APK, EXE, MSI, ZIP, RAR)</small>
    										</div>
											
											<div class="col-md-12 form-group">
    										    <label for="download_msg">Mensaje de aviso:</label>	
    											<div>
    												<textarea id="download_msg" name="download_msg" class="form-control" rows="6" wrap="hard" placeholder="Mensaje de aviso"></textarea>
    											</div>
    										</div>
											
										
										</div>
										    <div class="progress mt-1">
												<div class="progress-bar"></div>
											</div>
											<div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-success" id="submitdownload" name="submitdownload">
												<i class="fas fa-check"></i> Confirmar 
											</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">
												Salir
											</button>
											</div>
											<span id="loading" class='loading text-left'></span>
										</div>
									</form>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
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
        										<select class="custom-select credits" id="category" name="category" title="Client Type">
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
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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