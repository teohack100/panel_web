<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} apk online editor">
<meta name="keywords" content="{$siteTitle} apk online editor">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>DNS Creator</title>
<link rel="apple-touch-icon" href="{$base_url}logo/favicon.ico">
<link rel="shortcut icon" href="{$base_url}logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="{$base_url}logo/favicon.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="{$base_url}logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="{$base_url}logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="{$base_url}logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="{$base_url}logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="{$base_url}logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="{$base_url}logo/favicon-152x152.png">
<meta name="msapplication-TileColor" content="#FFFFFF">	
<meta name="msapplication-TileImage" content="{$base_url}logo/favicon-144x144.png">
<meta name="application-name" content="{$siteTitle}">
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
{include file='css/jqueryui_css.tpl'}
{include file='css/datatables_css.tpl'}
</head>
<body class="hold-transition skin-main sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

{include file='apps/navigation.tpl'}
{include file='apps/sidebar.tpl'}

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Octavia DNS
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">dns-creator</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<!--div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<form id="APKFrm" name="APKFrm" novalidate>
							<textarea id="apkcode" class="form-control" name="apkcode" rows="100%" required>{$editor}</textarea>
							<input type="hidden" id="submitted" name="submitted" value="APK Update">
							<button type="button" class="btn btn-success btn-block" id="apkUpdate" name="apkUpdate" onclick="codeUpdate()">
								<i class="glyphicon glyphicon-edit"></i> Update
							</button>
						</form>
						<div id="success"></div>
					</div>
				</div>
			</div-->
			
		
		<div class="box" style="display: block;
    float: none;
    margin-left: auto;
    margin-right: auto;">
    <div class="box-body">
        <div class="col-md-12">
      <div>
        <?php echo $result; ?>
      </div><br>
      <div class="inner">
        <div class="card centered text-center">
          <div class="card-header">
            <div>
              <h3>Octavia DNS</h3>
            </div></br>
            <div class="card-subtitle text-gray">
              Powered by: Cloudflare
            </div></br>
          </div>
          <div class="card-body">
            <form action="/lenzpogi/index.php" method="post">
              <div class="form-group input-group">
                <input class="form-control" name="name"  placeholder="Hostname [ex. www.octavia.com.us01]" required="" type="text"> <span class="input-group-addon">.octaviavpn.net</span>
              </div><br>
              <div class="input-group" style="width:100%">
                <input class="form-control" name="value" placeholder="IPv4 Address [ex. 128.199.69.69]" required="" type="text">
              </div><br>
              <div class="form-group">
                <!--label class="form-label">Please choose the type of record</label--> <label class="form-radio"><input checked name="record" required="" type="radio" value="a"> <i class="form-icon"></i> IPv4 Address (A)</label> <!--label class="form-radio"><input name="record" required="" type="radio" value="PTR"> <i class="form-icon"></i> Domain Name (PTR)</label-->
              </div>
              <div class="card-footer">
            <div class="btn-group" role="group">
              <button class="btn btn-primary" type="submit">Create</buttocnan> 
            </div>
          </div>
            </form>
          </div><br>
          
        </div>
      </div>
      </div>
    </div>
  </div>
	</div>
	</section>
	{include file='apps/footer.tpl'}
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/datatables_js.tpl'}
<script>
function displayVals() {
	$.ajax({
		type: "GET",
		url: "{$base_url}serverside/users/get_apk_update.php",
		success: function(data){
			$('textarea').val(data);
		}
	});
}
displayVals();

function codeUpdate()
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to update?',function(){
		$.ajax({
			url: "{$base_url}/apk_update.php",
			type: "POST",
			data: $('#APKFrm').serialize(),
			cache: false,
			success: function(response)
			{
				alertify.success('Successfully Updated!...');
				$('#success').html(response);
				displayVals();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alertify.error('Failed to Update!...');
				$('#success').html(response);
				displayVals();
			}
		});
	},function(){
		alertify.error('Declined');
		displayVals();
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});	
}
</script>
{include file='apps/liveclock.tpl'}
</body>

</html>