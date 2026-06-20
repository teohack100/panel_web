<!-- Top Bar Start -->
        <div class="topbar">

            <!-- LOGO -->
            <div class="topbar-left">
                <a href="{$base_url}" class="logo">
                    <span>
                        <img src="{$base_url}firenet/assets/images/logo-sm-94x94.png" alt="logo-small" class="logo-sm">
                    </span>
                    <span>
                        <img src="{$base_url}firenet/assets/images/vicath2.png" alt="logo-large" class="logo-lg" style="height: 40px !important;">
                    </span>                                     
                </a>
            </div>
            <!--end logo-->
            <!-- Navbar -->
            <nav class="navbar-custom">    
                <ul class="list-unstyled topbar-nav float-right mb-0"> 

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            {$avatar} 
                            <span class="ml-1 nav-user-name hidden-sm">{$full_name_2} <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item"><i class="fas fa-coins text-muted mr-2"></i>
                                {if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
                                    <span style="display:inline-block;font-size:1.35rem;line-height:1;font-weight:700;vertical-align:middle;">&infin;</span>
                                {else}
                                    {$credits_bal} Credito(s)
                                {/if}
                            </div>
                            <a class="dropdown-item" href="my-profile"><i class="dripicons-user text-muted mr-2"></i> Perfil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout" data-toggle="modal" data-target="#logoutModal"><i class="dripicons-exit text-muted mr-2"></i> Cerrar sesión</a>
                        </div>
                    </li>
                </ul><!--end topbar-nav-->
    
                <ul class="list-unstyled topbar-nav mb-0">                        
                    <li>
                        <button class="button-menu-mobile nav-link waves-effect waves-light">
                            <i class="dripicons-menu nav-icon"></i>
                        </button>
                    </li>
                </ul>
            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->
