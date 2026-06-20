        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <a href="{$base_url}" class="brand">
                        <img src="{$base_url}logo/logo.png" width="120" height="120" alt="Logo" />
                    </a>
                    <!-- Navigation button, visible on small resolution -->
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <i class="icon-menu"></i>
                    </button>
                    <!-- Main navigation -->
                    <div class="nav-collapse collapse pull-right">
                        <ul class="nav" id="top-navigation">
							{if $user_id_2 > 0}
							<li><a href="{$base_url}index.php?p=myaccount">My Account</a></li>
							{else}
							<li><a href="{$base_url}index.php?p=login">Sign-in</a></li>
							{/if}
                            <li class="active"><a href="#home">Home</a></li>
                            <li><a href="#about">About</a></li>
							<li><a href="/download">Downloads</a></li>
							<li><a href="#service">Services</a></li>
                            <li><a href="#price">Price</a></li>
                            <li><a href="#contact">Contact</a></li>
                           
                        </ul>
                    </div>
                    <!-- End main navigation -->
                </div>
            </div>
        </div>
