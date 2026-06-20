{include file='apps/header.tpl'}
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
{include file='css/datatables2.tpl'}
{include file='css/breakpoints_script.tpl'}
{include file='apps/body_start.tpl'}
{include file='apps/navigation.tpl'}
{include file='apps/sidebar.tpl'}
<!--[if lt IE 8]>
	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<!-- Page Content -->
<div id="squid" class="page animsition">
	<div class="page-content padding-30 container-fluid">
		<div class="col-md-12">
			<div class="padding-20">
				<div class="alert alert-info">
					<p>
						<h4 class="text-center"><i class="glyphicon glyphicon-attention"></i> WHEN TO REACTIVATE YOUR CLIENTS</h4>
						<ul class="list-group">
							<li>1ST OFFENSE - AFTER <span class="label label-info">3 DAYS</span> OR <span class="label label-info">72 HOURS</span></li>
							<li>2ND OFFENSE - AFTER <span class="label label-warning">7 DAYS</span> OR <span class="label label-warning">168 HOURS</span></li>
							<li>3RD OFFENSE - <span class="label label-danger">"SORRY- AUTOMATIC BANNED"</span></li>
						</ul>
						<h4 class="text-center"> STRICT COMPLIANCE! </h4>
					</p>
				</div>
			</div>
			<fieldset class="padding-20">
				<legend class="text-center">
					<h4 class="text-white">
						<div class="panel-heading">
							<i class="glyphicon glyphicon-user"></i> User's Offense List
						</div>
					</h4>
				</legend>
					<table id="" class="table table-striped table-bordered display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th class="text-center">Username</th>
								<th class="text-center">Premium</th>
								<th class="text-center">VIP</th>
								<th class="text-center">Offense</th>
								{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
								<th class="text-center">Upline</th>
								{/if}
								<th class="text-center">Suspended Date</th>
								<th class="text-center">Time Elapsed</th>
							</tr>
						</thead>
						<tbody>
							{foreach item=i from=$suspended}
								{$i}
							{/foreach}
						</tbody>
					</table>
			</fieldset>
		</div>
	</div>
	{include file='apps/footer.tpl'}
</div>


{include file='js/global_js.tpl'}
{include file='apps/liveclock.tpl'}
{include file='js/datatables2.tpl'}	 
<script>
    (function(document, window, $) {
      'use strict';

      var Site = window.Site;
      $(document).ready(function() {
        Site.run();
      });
	  
    })(document, window, jQuery);
</script> 
{include file='apps/body_end.tpl'}