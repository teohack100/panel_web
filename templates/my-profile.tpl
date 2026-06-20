<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Perfil</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">

        <link href="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

        <!-- App css -->
        <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />

        <!-- Sweet Alert -->
        <link href="{$base_url}firenet/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="{$base_url}firenet/assets/plugins/animate/animate.css" rel="stylesheet" type="text/css">
        
        <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/alertify.css">
	    <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/themes/bootstrap.rtl.css">
        
        {include file='css/custom_css.tpl'}
        {include file='css/formvalidation_css.tpl'}
        <style>
        body .page-wrapper .page-content .card,
        body .page-wrapper .page-content .card .card-body,
        body .page-wrapper .page-content .met-pro-bg,
        body .page-wrapper .page-content .met-profile,
        body .page-wrapper .page-content .detail-list .card,
        body .page-wrapper .page-content .detail-list .card .card-body {
            border-radius: 3px !important;
            overflow: hidden;
        }
        .met-profile-main {
            display: flex;
            justify-content: flex-end;
        }
        .met-profile-main .met-profile-main-pic {
            width: 190px;
            height: 190px;
            border-radius: 50% !important;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .met-profile-main .met-profile-main-pic #img {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
        }
        .met-profile-main .met-profile-main-pic #img img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            border-radius: 50% !important;
            display: block;
        }
        .profile-access-card-view {
            display: block;
            width: 100%;
            margin: 0 0 16px;
            padding: 0;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        .profile-access-role-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            padding: 2px 8px 3px;
            border-radius: 3px !important;
            background: linear-gradient(180deg, var(--pm-blue-600) 0%, var(--pm-blue-700) 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: .86rem;
            font-weight: 600;
            letter-spacing: 0;
            line-height: 1;
            vertical-align: middle;
        }
        .profile-access-text-view {
            display: block;
            color: #f2f7ff;
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.2;
            word-break: break-word;
        }
        .profile-info-pane {
            padding-top: 7px;
        }
        .profile-personal-detail {
            margin: 0;
        }
        @media (max-width: 767.98px) {
            .met-profile-main {
                justify-content: flex-end;
                margin-bottom: 16px;
            }
            .profile-info-pane {
                padding-top: 0;
            }
        }
        </style>

</head>
<body>
{include file='apps/topnav.tpl'}
<!-- Site wrapper -->
<div class="page-wrapper">
{include file='apps/sidenavi.tpl'}
    <!-- Page Content-->
            <div class="page-content">

                <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="float-right">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Navegación Principal</a></li>
                                        <li class="breadcrumb-item active">Perfil</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Perfil</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    <div id="success"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body  met-pro-bg">
                                    <div class="met-profile">
                                        <div class="row align-items-start">
                                            <div class="col-md-7 order-1 align-self-start">
                                                <div class="profile-info-pane">
                                                    <div class="profile-access-card-view">
                                                        <span id="access_role_2" class="profile-access-role-view">Cliente normal</span>
                                                        <span id="access_value_2" class="profile-access-text-view"></span>
                                                    </div>
                                                    <ul class="list-unstyled personal-detail profile-personal-detail">
                                                        <li class=""><i class="dripicons-phone mr-2 text-info font-18"></i> <b> Teléfono </b> : <span class="text-success" id="number_2"></span></li>
                                                        <li class="mt-2"><i class="dripicons-location text-info font-18 mt-2 mr-2"></i> <b>Localización</b> : <span id="address_2"></span></li>
                                                        <li class="mt-2"><i class="fab fa-facebook-square text-info font-18 mt-1 mr-2"></i> <b>Red social</b> : <span id="fb_2"></span></li>
                                                    </ul>
                                                </div>
                                            </div><!--end col-->
                                            <div class="col-md-4 ml-md-auto order-2 mb-4 mb-md-0 align-self-start">
                                                <div class="met-profile-main">
                                                    <div class="met-profile-main-pic">
                                                        <span id="img" ></span>
                                                        <!--span class="fro-profile_main-pic-change">
                                                            <i class="fas fa-camera"></i>
                                                        </span-->
                                                    </div>
                                                </div>                                                
                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end f_profile-->                                                                                
                                </div><!--end card-body-->
                                <div class="card-body">
                                            <button class="btn btn-gradient-success px-3" type="button" onclick="profile()">Ajustes</button>
                                            <button class="btn btn-gradient-success px-3" type="button" onclick="changepwd()">Cambiar Contraseña</button>      
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content detail-list" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="general_detail">
                                    <div class="row">
                                        <div class="col-12">                                            
                                            <div class="card">
                                                <div class="card-body">
                                                   <div class="row">
                                                       <div class="col-md-12">
                                                           <div class="met-basic-detail">
                                                                <h3>{$full_name_2}</h3>
                                                                <p class="text-uppercase font-14">{$rank}</p>
                                                                <p class="text-muted font-14">
                                                                     La creatividad es la inteligencia Divirtiendose
                                                                </p>
                                                                 
                                                           </div>
                                                       </div>
                                                       <!--div class="col-lg-4">
                                                            <div class="row">
                                                                <div class="col-lg-6 mx-auto">
                                                                    <div class="own-detail bg-blue">
                                                                        <h4 class="text-white">Premium</h4>
                                                                        <h5>{$pre_days} Day(s), {$pre_hours} Hour(s) & {$pre_minutes} Minute(s)</h5>
                                                                    </div>
                                                                    <div class="own-detail own-detail-project bg-secondary">
                                                                        <h4 class="text-white">VIP</h4>
                                                                        <h5>{$vip_days} Day(s), {$vip_hours} Hour(s) & {$vip_minutes} Minute(s)</h5>
                                                                    </div>
                                                                    <div class="own-detail own-detail-happy bg-success">
                                                                        <h4 class="text-white">Private</h4>
                                                                        <h5>{$pri_days} Day(s), {$pri_hours} Hour(s) & {$pri_minutes} Minute(s)</h5>
                                                                    </div>
                                                                </div>                                        
                                                            </div>                                                                                                                       
                                                       </div-->
                                                   </div>         
                                                </div><!--end card-body-->
                                            </div><!--end card-->
                                        </div><!--end col-->
                                    </div><!--end row-->                                             
                                </div><!--end general detail-->

                                <div class="tab-pane fade" id="education_detail">                                                
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card">                                       
                                                <div class="card-body"> 
                                                    <h4 class="header-title mt-0 mb-3">Education</h4>
                                                    <div class="slimscroll education-activity">
                                                        <div class="activity">
                                                            <i class="mdi mdi-school icon-success"></i>
                                                            <div class="time-item">
                                                                <div class="item-info">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="m-0">University</h6>
                                                                        <span class="text-muted">Oct-2009 to Oct-2011</span>
                                                                    </div>
                                                                    <p class="text-muted mt-3">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.
                                                                        <a href="#" class="text-info">[more info]</a>
                                                                    </p>
                                                                    <div>
                                                                        <span class="badge badge-soft-secondary">Design</span>
                                                                        <span class="badge badge-soft-secondary">HTML</span>                                                    
                                                                        <span class="badge badge-soft-secondary">Css</span>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <i class="mdi mdi-medal icon-pink"></i>                                                                                                           
                                                            <div class="time-item">
                                                                <div class="item-info">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="m-0">Bachelor of Arts</h6>
                                                                        <span class="text-muted">Oct-2006 to Oct-209</span>
                                                                    </div>
                                                                    <p class="text-muted mt-3">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.
                                                                        <a href="#" class="text-info">[more info]</a>
                                                                    </p>
                                                                    <div>
                                                                        <span class="badge badge-soft-secondary">Python</span>
                                                                        <span class="badge badge-soft-secondary">Django</span>
                                                                    </div>
                                                                </div>                                            
                                                            </div>
                                                            <i class="mdi mdi-book-open-page-variant icon-purple"></i> 
                                                            <div class="time-item">
                                                                <div class="item-info">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="m-0">Secondary</h6>
                                                                        <span class="text-muted">Oct-2003 to Oct-2006</span>
                                                                    </div>
                                                                    <p class="text-muted mt-3">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.
                                                                        <a href="#" class="text-info">[more info]</a>
                                                                    </p>
                                                                </div>
                                                            </div>                                         
                                                            <i class="mdi mdi-lead-pencil icon-warning"></i>
                                                            <div class="time-item">
                                                                <div class="item-info">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="m-0">Primary</h6>
                                                                        <span class="text-muted">Oct-1996 to Oct-2003</span>
                                                                    </div>
                                                                    <p class="text-muted mt-3">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.
                                                                        <a href="#" class="text-info">[more info]</a>
                                                                    </p>                                                
                                                                </div>
                                                            </div>                                                                                                                                                                                                        
                                                        </div><!--end activity-->
                                                    </div><!--end education-activity-->
                                                </div>  <!--end card-body-->                                     
                                            </div><!--end card-->
                                        </div><!--end col-->

                                        <div class="col-lg-6">
                                            <div class="card">                                       
                                                <div class="card-body"> 
                                                    <h4 class="header-title mt-0 mb-3">My Skills</h4>
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <img src="../assets/images/widgets/education.svg" alt="" class="img-fluid">
                                                        </div>
                                                        <div class="col-8 align-self-center">
                                                            <p class="skill-detail">Contrary to popular belief, Lorem Ipsum is not simply random text. 
                                                                It has roots in a piece of classical Latin literature from 45 BC, 
                                                                making it over 2000 years old. Richard McClintock, a Latin professor 
                                                                at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="skills mt-4">
                                                        <div class="skill-box"> 
                                                            <h4 class="skill-title">HTML5 &amp; CSS3 </h4> 
                                                            <div class="progress-line"> 
                                                                <span data-percent="78" class="bg-warning" style="width: 78%;">
                                                                    <span class="percent-tooltip">78%</span>
                                                                </span> 
                                                            </div>
                                                        </div>
                                                        <div class="skill-box"> 
                                                            <h4 class="skill-title">Web Design</h4> 
                                                            <div class="progress-line"> 
                                                                <span data-percent="90" class="bg-pink" style="width: 90%;">
                                                                    <span class="percent-tooltip">90%</span>
                                                                </span> 
                                                            </div>
                                                        </div>
                                                        <div class="skill-box"> 
                                                            <h4 class="skill-title">UI/UX Design</h4> 
                                                            <div class="progress-line"> 
                                                                <span data-percent="80" class="bg-success" style="width: 80%;">
                                                                    <span class="percent-tooltip">80%</span>
                                                                </span> 
                                                            </div>
                                                        </div>
                                                        <div class="skill-box"> 
                                                            <h4 class="skill-title">Photoshop &amp; Ilistletor </h4> 
                                                            <div class="progress-line"> 
                                                                <span data-percent="95" class="bg-primary" style="width: 95%;">
                                                                    <span class="percent-tooltip">95%</span>
                                                                </span> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  <!--end card-body-->                                     
                                            </div><!--end card-->
                                        </div><!--end col-->
                        
                                    </div><!--end row-->  
                                </div><!--end education detail-->

                                <div class="tab-pane fade" id="portfolio_detail">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <ul class="col container-filter categories-filter mb-0" id="filter">
                                                            <li><a class="categories active" data-filter="*">All</a></li>
                                                            <li><a class="categories" data-filter=".branding">Branding</a></li>
                                                            <li><a class="categories" data-filter=".design">Design</a></li>
                                                            <li><a class="categories" data-filter=".photo">Photo</a></li>
                                                            <li><a class="categories" data-filter=".coffee">coffee</a></li>
                                                        </ul>
                                                    </div><!-- End portfolio  -->
                                                </div><!--end card-body-->
                                            </div><!--end card-->
                                            
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row container-grid nf-col-3  projects-wrapper">
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item branding design coffee spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-1.jpg" title="Consequat massa quis">
                                                                    <img class="item-container " src="../assets/images/small/img-1.jpg" alt="7" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Consequat massa quis</h5>
                                                                            <p class="text-white">Branding, Design, Coffee</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                        
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item photo spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-2.jpg" title="Vivamus elementum semper">
                                                                    <img class="item-container mfp-fade" src="../assets/images/small/img-2.jpg" alt="2" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Vivamus elementum semper</h5>
                                                                            <p class="text-white">Photo</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                        
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item branding coffee spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-3.jpg" title="Quisque rutrum">
                                                                    <img class="item-container" src="../assets/images/small/img-3.jpg" alt="4" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Quisque rutrum</h5>
                                                                            <p class="text-white">Branding, Design, Coffee</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                        
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item branding design spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-4.jpg" title="Tellus eget condimentum">
                                                                    <img class="item-container" src="../assets/images/small/img-4.jpg" alt="5" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Tellus eget condimentum</h5>
                                                                            <p class="text-white">Design</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                        
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item branding design spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-5.jpg" title="Nullam quis ant">
                                                                    <img class="item-container" src="../assets/images/small/img-5.jpg" alt="6" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Nullam quis ant</h5>
                                                                            <p class="text-white">Branding, Design</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                        
                                                        <div class="col-lg-4 col-md-6 p-0 nf-item photo spacing">
                                                            <div class="item-box">
                                                                <a class="cbox-gallary1 mfp-image" href="../assets/images/small/img-6.jpg" title="Sed fringilla mauris">
                                                                    <img class="item-container" src="../assets/images/small/img-6.jpg" alt="1" />
                                                                    <div class="item-mask">
                                                                        <div class="item-caption">
                                                                            <h5 class="text-white">Sed fringilla mauris</h5>
                                                                            <p class="text-white">Photo</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div><!--end item-box-->
                                                        </div><!--end col-->
                                                    </div><!--end row-->
                                                </div><!--end card-body-->
                                            </div><!--end card-->
                                        </div><!--end col-->
                                        <div class="col-lg-4">
                                            <div class="card ">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <h4><i class="fas fa-quote-left text-primary"></i></h4>
                                                    </div>                                            
                                                    <div id="carouselExampleFade2" class="carousel slide" data-ride="carousel">
                                                        <div class="carousel-inner">
                                                            <div class="carousel-item">
                                                                <div class="text-center">
                                                                    <p class="text-muted px-4">
                                                                        It is a long established fact that a reader will be distracted by 
                                                                        the readable content of a page when looking at its layout. 
                                                                        The point of using Lorem Ipsum is that it has a more-or-less 
                                                                        normal distribution of letters, as opposed to using.
                                                                    </p>
                                                                    <div class="">
                                                                        <img src="../assets/images/users/user-10.jpg" alt="" class="rounded-circle thumb-lg mb-2">
                                                                        <p class="mb-0 text-primary"><b>- Mary K. Myers</b></p>
                                                                        <small class="text-muted">CEO Facebook</small>
                                                                    </div>                                                            
                                                                </div>
                                                            </div>
                                                            <div class="carousel-item active">
                                                                <div class="text-center">
                                                                    <p class="text-muted px-4">                                                                
                                                                        Where does it come from?
                                                                        Contrary to popular belief, Lorem Ipsum is not simply random text. 
                                                                        It has roots in a piece of classical Latin literature from 45 BC, 
                                                                        making it over 2000 years  popular belief,old.
                                                                    </p>
                                                                    <div class="">
                                                                        <img src="../assets/images/users/user-4.jpg" alt="" class="rounded-circle  thumb-lg mb-2">
                                                                        <p class="mb-0 text-primary"><b>- Michael C. Rios</b></p>
                                                                        <small class="text-muted">CEO Facebook</small>
                                                                    </div>                                                            
                                                                </div>
                                                            </div>
                                                            <div class="carousel-item">
                                                                <div class="text-center">
                                                                    <p class="text-muted px-4">
                                                                        There are many variations of passages of Lorem Ipsum available, 
                                                                        but the majority have suffered alteration in some form, by injected humour, 
                                                                        or randomised words which don't look even slightly believable. 
                                                                        If you are going to use a passage of Lorem Ipsum. 
                                                                    </p>
                                                                    <div class="">
                                                                        <img src="../assets/images/users/user-5.jpg" alt="" class="rounded-circle  thumb-lg mb-2">
                                                                        <p class="mb-0 text-primary"><b>- Lisa D. Pullen</b></p>
                                                                        <small class="text-muted">CEO Facebook</small>
                                                                    </div>                                                            
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--end row-->
                                </div><!--end portfolio detail-->
                                
                                <div class="tab-pane fade" id="settings_detail">
                                    <div class="row">
                                        <div class="col-lg-12 col-xl-12 mx-auto">
                                            <div class="card">
                                                <div class="card-body">
                                                    <form method="post" class="card-box">
                                                        <input type="file" id="input-file-now-custom-1" class="dropify" data-default-file="../assets/images/users/user-4.jpg"/>
                                                    </form>
        
                                                    <div class="">
                                                        <form class="form-horizontal form-material mb-0">
                                                            <div class="form-group">
                                                                <input type="text" placeholder="Full Name" class="form-control">
                                                            </div>
                                                            
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <input type="email" placeholder="Email" class="form-control" name="example-email" id="example-email">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="password" placeholder="password" class="form-control">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="password" placeholder="Re-password" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-6">
                                                                    <input type="text" placeholder="Phone No" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <select class="form-control">
                                                                        <option>London</option>
                                                                        <option>India</option>
                                                                        <option>Usa</option>
                                                                        <option>Canada</option>
                                                                        <option>Thailand</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea rows="5" placeholder="Message" class="form-control"></textarea>
                                                                <button class="btn btn-gradient-primary btn-sm px-4 mt-3 float-right mb-0">Update Profile</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>                                            
                                            </div>
                                        </div> <!--end col-->                                          
                                    </div><!--end row-->
                                </div><!--end settings detail-->
                            </div><!--end tab-content--> 
                            
                        </div><!--end col-->
                    </div><!--end row-->

                </div><!-- container -->

                {include file='apps/footer.tpl'}
                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->
        {include file='apps/modals.tpl'}
        <!-- jQuery  -->
        <script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
        <script src="{$base_url}firenet/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{$base_url}firenet/assets/js/metisMenu.min.js"></script>
        <script src="{$base_url}firenet/assets/js/waves.min.js"></script>
        <script src="{$base_url}firenet/assets/js/jquery.slimscroll.min.js"></script>

        <script src="{$base_url}firenet/assets/plugins/moment/moment.js"></script>
        <script src="{$base_url}firenet/assets/plugins/apexcharts/apexcharts.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.eco_dashboard.init.js"></script>

        <!-- Required datatable js -->
        <script src="{$base_url}firenet/assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/jszip.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/pdfmake.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/vfs_fonts.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.html5.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.print.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.colVis.min.js"></script>
        <!-- Responsive examples -->
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.datatable.init.js"></script>
        
        <!-- App js -->
        <script src="{$base_url}firenet/assets/js/jquery.core.js"></script>
        <script src="{$base_url}firenet/assets/js/app.js"></script>
        
        <!-- Custom File Upload -->
        <script src="{$base_url}bootstrap/assets/custom.fileupload/custom.fle_upload.js"></script>
    	<script src="{$base_url}bootstrap/assets/jqueryform/jquery.form.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script type="text/javascript" src="{$base_url}bootstrap/main/assets/js/ie10-viewport-bug-workaround.js"></script>
    	<!-- Sweet-Alert  -->
        <script src="{$base_url}firenet/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.sweet-alert.init.js"></script>
        
        <script src="{$base_url}bootstrap/assets/alertifyjs/alertify.js"></script>
        <script src="{$base_url}bootstrap/dashboard/dist/js/app.js"></script>

{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='js/active-vip-client.tpl'}
{include file='js/profile.tpl'}
{include file='apps/liveclock.tpl'}
{include file='js/pass_toggle.tpl'}
</body>
</html>
