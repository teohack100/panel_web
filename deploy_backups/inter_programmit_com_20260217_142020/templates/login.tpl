<!DOCTYPE html>
<html class="no-js" lang="es">
    <head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Acceso</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">
        <link rel="shortcut icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">



        <!-- App css -->
        <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
        {include file='css/formvalidation_css.tpl'}
        {include file='css/sweetalert2.tpl'}
    </head>

    <body class="account-body" style="background: #1c233f;">
        
        <!-- Log In page -->
        <div class="row vh-100 ">
            <div class="col-12 align-self-center">
                <div class="auth-page">
                    <!--div class="login-errors alert alert-dismissible">
            			<ul class="text-center"></ul>
            		</div-->
                    <div class="card auth-card" style="box-shadow: 0 0 2rem rgba(77, 121, 246,.275)!important;">
                        <div class="card-body">
                            <div class="px-3">
                                <div class="auth-logo-box">
                                    <a href="{$base_url}" class="logo logo-admin"><img src="{$base_url}logo/icon_panel.png" height="54" alt="logo" ></a>
                                </div><!--end auth-logo-box-->
                                
                                <div class="text-center auth-logo-text">
                                    <h4 class="mt-0 mb-3 mt-5">{$siteTitle}</h4>
                                    <p class="text-muted mb-0">Regístrate para continuar</p>  
                                </div> <!--end auth-logo-text-->  
                                
                                <div class="text-center auth-logo-text">
                                    <div id="success"></div>
                                    <div id="error"></div>
                                </div> <!--end auth-logo-text-->  
                                
                                <form class="form-horizontal auth-form my-4" name="form" id="form" method="post" accept-charset="UTF-8" novalidate>
                                    <input type="hidden" id="submitted" name="submitted" value="Login Account" />
				                    <input type="hidden" id="code" name="code" value="{$code}" />
                                    <div class="form-group">
                                        <label for="username">Nombre de Usuario</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i> 
                                            </span>                                                                                                              
                                            <input type="text" class="form-control" id="user" placeholder="Introduzca su nombre de usuario" name="user_name" value="{$user_name}" autocomplete="off" required>
                                        </div>                                    
                                    </div><!--end form-group--> 
        
                                    <div class="form-group">
                                        <label for="userpassword">Contraseña</label>                                            
                                        <div class="input-group mb-3"> 
                                            <span class="auth-form-icon" href="javascript:;" onclick="toggle_password('user_pass');" id="showhide2">
                                                <i class="fas fa-eye"></i> 
                                            </span>                                                       
                                            <input type="password" class="form-control" id="user_pass" placeholder="Introducir la contraseña" name="user_pass" autocomplete="off" required>
                                        </div> 
                                        <!--div class="progress password-meter" id="signinpwdMeter">
                    						<div class="progress-bar"></div>
                    					</div-->
                                    </div><!--end form-group--> 
                                    
                                    <div class="form-group has-feedback d-none">
                    					<select id="category" name="category" class="form-control">
                    						<option value="{$login_encrypt}" selected="selected">Login Account</option>
                    						<option value="{$unfreeze_encrypt}">Unfreeze Account</option>
                    					</select>
                    					<label class="contriol-label" for="category">
                    					</label>
                    				</div>
                                    <br>

                                    <!--div class="form-group row mt-4">
                                        <div class="col-sm-6 text-left">
                                            <a href="auth-recover-pw.html" class="text-muted font-13"><i class="dripicons-lock"></i> Request activation?</a>                                    
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <a href="auth-recover-pw.html" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot password?</a>                                    
                                        </div>
                                    </div--><!--end form-group--> 
                                    
                                    <div class="form-group mb-0 row">
                                        <div class="col-12 mt-2">
                                            <input type='hidden' name='CSRFtoken' value='{$token}' >
                                            <button class="btn btn-gradient-primary btn-round btn-block waves-effect waves-light" name="submitLogin" type="submit">Iniciar sesión <i class="fas fa-sign-in-alt ml-1"></i></button>
            
		</div>
	</div>
                                        </div><!--end col--> 
                                    </div> <!--end form-group-->                           
                                </form><!--end form-->
                            </div><!--end /div-->
                            <!--div class="m-3 text-center text-muted">
                                <p class="">Don't have an account ?  <a href="../authentication/auth-register.html" class="text-primary ml-2">Free Register</a></p>
                            </div-->
                        </div><!--end card-body-->
                    </div><!--end card-->
                    <!--div class="account-social text-center mt-4">
                        <h6 class="my-4">Or Login With</h6>
                        <ul class="list-inline mb-4">
                            <li class="list-inline-item">
                                <a href="" class="">
                                    <i class="fab fa-facebook-f facebook"></i>
                                </a>                                    
                            </li>
                            <li class="list-inline-item">
                                <a href="" class="">
                                    <i class="fab fa-twitter twitter"></i>
                                </a>                                    
                            </li>
                            <li class="list-inline-item">
                                <a href="" class="">
                                    <i class="fab fa-google google"></i>
                                </a>                                    
                            </li>
                        </ul>
                    </div-->
                </div><!--end auth-page-->
            </div><!--end col-->           
        </div><!--end row-->

        <!-- End Log In page -->
    

        <!-- jQuery  -->
        <script src="{$base_url}/firenet/assets/js/jquery.min.js"></script>
        <script src="{$base_url}/firenet/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{$base_url}/firenet/assets/js/metisMenu.min.js"></script>
        <script src="{$base_url}/firenet/assets/js/waves.min.js"></script>
        <script src="{$base_url}/firenet/assets/js/jquery.slimscroll.min.js"></script>

        <!-- App js -->
        <script src="{$base_url}/firenet/assets1/js/app.js"></script>
{include file='js/global_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='ajax/login.tpl'}
{include file='js/pass_toggle.tpl'}
<script>
$('document').ready(function()
{
	var loading = $('#loader').html('<div class="text-center padding-40">'+
	'<img src="{$base_url}/bootstrap/global/css/loader/ajax-loader.gif" aria-hidden="true"><i></i> ¡Por favor! Espere mientras revisa su cuenta...'+
	'</div>').addClass('hide');
	var $form = $('#form');
	$form.ajaxForm({
		type: "POST",
		url: "{$base_url}/serverside/forms/login.php",
		data: $form.serialize(),
		beforeSend: function() {
			loading.show().removeClass('hide');
			$form.show();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#success').html(data);
			$form.trigger('reset');
			$form[0].reset();
			$form.show();
		},
		success: function(data){
			$('#success').html(data);
			$form.trigger('reset');
			$form[0].reset();
			$form.show();
		},
		complete: function(){
			loading.hide().addClass('hide');
			$form.show();
		}
	});	
});
</script>
    </body>
</html>