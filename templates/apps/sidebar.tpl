	<aside class="main-sidebar">
		<!-- sidebar: style can be found in sidebar.less -->
		<section class="sidebar">
			<!-- sidebar menu: : style can be found in sidebar.less -->
			<ul class="sidebar-menu">
				<li class="header">NAVEGACIÓN PRINCIPAL</li>
				<li class="treeview">
					<a href="/myaccount">
						<i class="fa fa-dashboard"></i> <span>My Account</span>
					</a>
				</li>
				<li class="treeview">
					<a href="/resellers">
						<i class="fa wb-star"></i> <span>Authorized Reseller</span>
					</a>
				</li>
				<li class="treeview">
					<a href="javascript:void(0);">
						<i class="fa fa-globe"></i>
						<span>Online Status</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
						<li><a href="/onlineusers"><i class="fa wb-users"></i> Online Users</a></li>
						<li><a href="/privateusers"><i class="fa wb-globe"></i> Private Users</a></li>
						<li><a href="/servers"><i class="fa wb-globe"></i> Online Servers</a></li>
					</ul>
				</li>
		{if $user_id_2 > 0}

			{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' }
				<li class="header">USERS NAVIGATION</li>

				{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
			     	<li class="treeview">
						<a href="/administrator">
							<i class="fa wb-star"></i> <span>Administrators</span>
						</a>
					</li>
					<li class="treeview">
						<a href="/subadmin">
							<i class="fa wb-star"></i> <span>Sub Administrator</span>
						</a>
					</li>
					
				{/if}

				{if $user_id_2 == 1 || $user_level_2 == 'superadmin'|| $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
					<li class="treeview">
						<a href="/reseller">
							<i class="fa wb-star"></i> <span>Reseller</span>
						</a>
					</li>
				{/if}

				{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
					<li class="treeview">
						<a href="/subreseller">
							<i class="fa wb-star"></i> <span>Sub Reseller</span>
						</a>
					</li>
				{/if}

			{/if}

			{if $user_id_2 == 1 || $user_level_2 != 'normal'}	
				<li class="header">CLIENT NAVIGATION</li>

				{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'}	
					<li class="treeview">
						<a href="/users">
							<i class="fa wb-users"></i> <span>All Users</span>
						</a>
					</li>
				{/if}
                {if $user_level_2 == 'subreseller'}
                <li class="treeview">
						<a href="/users">
							<i class="fa wb-users"></i> <span>All Clients</span>
						</a>
					</li>
				{/if}
				{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
					<li class="treeview">
						<a href="/client">
							<i class="fa wb-users"></i> <span>All Clients</span>
						</a>
					</li>
				{/if}

					<li class="treeview">
						<a href="/disconnection">
							<i class="fa wb-warning"></i> <span>NOD</span>
						</a>
					</li>
					<li class="treeview">
						<a href="/freeze">
							<i class="fa wb-stop"></i> <span>Freeze Request</span>
						</a>
					</li>
					<li class="treeview">
						<a href="/suspension">
							<i class="fa fa-ban"></i> <span>Suspended</span>
						</a>
					</li>
					<li class="treeview">
						<a href="/accountrecovery">
							<i class="fa wb-trash"></i> <span>Deleted Account</span>
						</a>
					</li>
			{/if}

			{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
				<li class="header">SYSTEM UTILITIES</li>
				<li class="treeview">
					<a href="/serverupload">
						<i class="fa wb-upload"></i> <span>Server Upload</span>
					</a>
				</li>
				<!--li class="treeview">
					<a href="/apk-jsoneditor">
						<i class="fa wb-upload"></i> <span>DNS Creator</span>
					</a>
				</li-->
				<li class="treeview">
					<a href="/gui-jsoneditor">
						<i class="fa wb-upload"></i> <span>Online Update</span>
					</a>
				</li>
				<li class="treeview">
					<a href="/downloadtutorials">
						<i class="fa wb-download"></i> <span>Tutorials Upload</span>
					</a>
				</li>
				<li class="treeview">
					<a href="/banned-ip">
						<i class="fa fa-ban"></i> <span>Banned IP</span>
					</a>
				</li>
			{/if}

		{/if}
			</ul>
		</section>
		<!-- /.sidebar -->
	</aside>