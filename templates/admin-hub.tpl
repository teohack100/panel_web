<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Admin Central</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Panel maestro administrativo" name="description" />
    <meta content="PROGRAMMIT" name="author" />

    <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    <style>
    {literal}
        .admin-hub-card{
            border:1px solid #263d63;
            border-radius:12px;
            background:linear-gradient(180deg,#192f50 0%,#162744 100%);
            color:#eef4ff;
            box-shadow:0 14px 26px rgba(7,16,32,.2);
            height:100%;
        }
        .admin-hub-card .icon{
            width:44px;height:44px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            background:#12335e;color:#7cc3ff;font-size:20px;
            margin-bottom:12px;
        }
        .admin-hub-card h5{color:#fff;margin-bottom:6px;}
        .admin-hub-card p{color:#b8cdee;min-height:44px;}
        .admin-hub-action{
            display:inline-block;
            border:1px solid #4b80c4;
            color:#d9ebff;
            border-radius:8px;
            padding:7px 12px;
            font-weight:700;
            text-decoration:none;
        }
        .admin-hub-action:hover{color:#fff;background:#214777;text-decoration:none;}

        /* Admin central: keep only the categories used by this view */
        .left-sidenav .pm-sidebar-scroll{
            display:flex;
            flex-direction:column;
        }
        .left-sidenav .pm-nav-category:not([data-category="finance"]):not([data-category="panels"]){
            display:none !important;
        }
        .left-sidenav .pm-nav-category[data-category="panels"]{ order:1; }
        .left-sidenav .pm-nav-category[data-category="finance"]{ order:2; }
    {/literal}
    </style>
</head>
<body>
{include file='apps/topnav.tpl'}
<div class="page-wrapper">
{include file='apps/sidenavi.tpl'}
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="float-right">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                <li class="breadcrumb-item active">Admin Central</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Admin Central (`admin.php`)</h4>
                    </div>
                </div>
            </div>

            {if $admin_hub_is_master_host == 0}
            <div class="alert alert-warning">
                Estás en <strong>{$admin_hub_current_host}</strong>. El host admin principal es <strong>{$admin_hub_master_host}</strong>.<br>
                Abrir panel central: <a href="{$admin_hub_master_url}" target="_blank">{$admin_hub_master_url}</a>
            </div>
            {/if}

            <div class="row">
                {foreach from=$admin_hub_sections item=s}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-hub-card">
                        <div class="card-body">
                            <div class="icon"><i class="{$s.icon}"></i></div>
                            <h5>{$s.title}</h5>
                            <p>{$s.desc}</p>
                            <a class="admin-hub-action" href="{$s.url}">Abrir</a>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
        {include file='apps/footer.tpl'}
    </div>
</div>

<script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
<script src="{$base_url}firenet/assets/js/bootstrap.bundle.min.js"></script>
<script src="{$base_url}firenet/assets/js/metisMenu.min.js"></script>
<script src="{$base_url}firenet/assets/js/waves.min.js"></script>
<script src="{$base_url}firenet/assets/js/jquery.slimscroll.min.js"></script>
<script src="{$base_url}firenet/assets/js/app.js"></script>
<script>
{literal}
(function(){
    function openOnly(categoryName){
        var categories = document.querySelectorAll('.left-sidenav .pm-nav-category');
        for (var i = 0; i < categories.length; i++) {
            categories[i].classList.remove('is-open');
        }
        var target = document.querySelector('.left-sidenav .pm-nav-category[data-category="' + categoryName + '"]');
        if (target) {
            target.classList.add('is-open');
        }
    }

    if (document.querySelector('.left-sidenav .pm-nav-category[data-category="panels"]')) {
        openOnly('panels');
    } else if (document.querySelector('.left-sidenav .pm-nav-category[data-category="finance"]')) {
        openOnly('finance');
    }
})();
{/literal}
</script>
</body>
</html>
