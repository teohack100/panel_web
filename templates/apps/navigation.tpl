<header class="main-header">
	<!-- Logo -->
	
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top">
		<!-- Sidebar toggle button-->
		<div class="navbar-custom-menu pull-left">
		    <ul class="nav navbar-nav">
		    <li class="dropdown user user-menu">
				
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bars"></i>
						<span class="hidden-xs"> MENU</span>
					</a>
					<ul class="dropdown-menu lenznav dropdown-user animated flipInY">
						<!-- User image -->
						<li class="user-header">
							{$avatar}
							<p>
								MENU
								<small>{$base_url}</small>
							</p>
						</li>
						<li class="divider"></li>
						<li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/myaccount';">
					           <i class="fa fa-dashboard" aria-hidden="true"></i> My Account
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/resellers';">
					            <i class="fa wb-star" aria-hidden="true"></i> Authorized Reseller
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/onlineusers';">
					            <i class="icon wb-users" aria-hidden="true"></i> Online Users
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/privateusers';">
					            <i class="fa wb-users" aria-hidden="true"></i> Private Users
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/servers';">
					            <i class="fa wb-globe" aria-hidden="true"></i> Online Servers
				            </button>
                        </li>
                        <li class="divider"></li>
                {if $user_id_2 > 0}
                    {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' }
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/administrator';">
					            <i class="fa wb-star" aria-hidden="true"></i> Administrator
				            </button>
                        </li>
                        {/if}
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/subadmin';">
					            <i class="fa wb-star" aria-hidden="true"></i> Sub Administrator
				            </button>
                        </li>
                        {/if}
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin'|| $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/reseller';">
					            <i class="fa wb-star" aria-hidden="true"></i> Reseller
				            </button>
                        </li>
                        {/if}
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/subreseller';">
					            <i class="fa wb-star" aria-hidden="true"></i> Sub Reseller
				            </button>
                        </li>
                        {/if}
                    {/if}
                        <li class="divider"></li>
                    {if $user_id_2 == 1 || $user_level_2 != 'normal'}
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'}	
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/users';">
					            <i class="fa wb-users" aria-hidden="true"></i> All Users
				            </button>
                        </li>
                        {/if}
                        {if $user_level_2 == 'subreseller'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/users';">
					            <i class="fa wb-users" aria-hidden="true"></i> All Clients
				            </button>
                        </li>
                        {/if}
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/client';">
					            <i class="fa wb-users" aria-hidden="true"></i> All Clients
				            </button>
                        </li>
                        {/if}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/disconnection';">
					            <i class="fa wb-warning" aria-hidden="true"></i> Disconnection
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/freeze';">
					            <i class="fa wb-stop" aria-hidden="true"></i> Freeze Request
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/suspension';">
					            <i class="fa fa-ban" aria-hidden="true"></i> Suspended
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/accountrecovery';">
					            <i class="fa wb-trash" aria-hidden="true"></i> Deleted Account
				            </button>
                        </li>
                    {/if}
                        <li class="divider"></li>
                    {if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/dns';">
					            <i class="fa fa-server" aria-hidden="true"></i> DNS Records (BETA)
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/serverupload';">
					            <i class="fa wb-upload" aria-hidden="true"></i> Server Update
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/gui-jsoneditor';">
					            <i class="fa wb-upload" aria-hidden="true"></i> Online Update
				            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/downloadtutorials';">
					            <i class="fa wb-download" aria-hidden="true"></i> Notice Update
				            </button>
                        </li>
                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/script';">
					            <i class="fa fa-terminal" aria-hidden="true"></i> Server Script
				            </button>
                        </li>
                        {/if}
                        <li>
                            <button type="button" class="btn btn-default btn-block waves-effect waves-light text-left" onclick="window.location.href = '/banned-ip';">
					            <i class="fa fa-ban" aria-hidden="true"></i> Banned IP
				            </button>
                        </li>
                        <li class="divider"></li>
                    {/if}
                {/if}
					</ul>
				</li>
				</ul>
		</div>
		
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<li>
					<a href="javascript:void(0);" id="liveTime"></a>
				<li>
				<!-- User Account: style can be found in dropdown.less -->
				
				
				
				<li class="dropdown user user-menu">
				{if $user_id_2 > 0}
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
						{$avatar}
						<span class="hidden-xs">{$full_name_2}</span>
					</a>
					<ul class="dropdown-menu dropdown-user animated flipInY lenznav2">
						<!-- User image -->
						<li class="user-header">
							{$avatar}
							<p>
								{$full_name_2}
								<small>{$rank}</small>
							</p>
						</li>
						<li role="separator" class="divider"></li>
						<li><a class="text-muted"><i class="fa fa-user fa-fw"></i> {$rank}</a></li>
						<li><a class="text-muted"><i class="fa fa-clock-o fa-fw"></i> {$lastlogin}</a></li>
						<li><a class="text-muted"><i class="fa fa-globe fa-fw"></i> {$getIP}</a></li>
						<li><a class="text-muted"><i class="fa fa-desktop fa-fw"></i> {$getBrowser}</a></li>
						<li class="user-footer">
							<div class="pull-left">
								<a type="button" href="/myaccount" class="btn btn-default btn-flat"><i class="fa wb-user"></i> My Account</a>
							</div>
							<div class="pull-right">
								<a type="button" href="/logout" class="btn btn-default btn-flat"><i class="fa fa-power-off fa-fw"></i> Sign Out</a>
							</div>
						</li>
					{else}
						<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
    						<img src="{$base_url}profile/default.png" class="user-image" alt="User Image">
						<span class="hidden-xs">{$siteTitle}</span>
				    	</a>
						<ul class="dropdown-menu dropdown-user animated flipInY lenznav2">
						    <li class="user-header">
							<img src="{$base_url}profile/default.png" alt="User Image">
							<p>
								{$siteTitle}
								<small>{$base_url}</small>
							</p>
						    </li>
						    <li role="separator" class="divider"></li>
							<li class="user-footer">
								<div class="pull-left">
									<a type="button" href="/login" class="btn btn-default btn-flat"><i class="fa wb-user"></i> Sign-in</a>
								</div>
								<div class="pull-right">
									<a href="/register" class="btn btn-default btn-flat"><i class="fa wb-user-add"></i> Sign-up</a>
								</div>
							</li>
						</ul>
					{/if}
					</ul>
				</li>
				
				
			</ul>
		</div>
    </nav>			
</header>