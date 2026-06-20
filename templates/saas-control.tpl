<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - SaaS Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Control central SaaS" name="description" />
    <meta content="PROGRAMMIT" name="author" />

    <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    <style>
    {literal}
        .saas-card{
            border:1px solid #263d63;
            border-radius:12px;
            background:linear-gradient(180deg,#192f50 0%,#162744 100%);
            color:#eef4ff;
        }
        .saas-card .form-control,
        .saas-card .custom-select{
            background:#13243d;
            border-color:#2d466e;
            color:#eef4ff;
        }
        .saas-card .form-control:focus,
        .saas-card .custom-select:focus{
            border-color:#58a4ff;
            box-shadow:none;
        }
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
                                <li class="breadcrumb-item">SaaS</li>
                                <li class="breadcrumb-item active">Control</li>
                            </ol>
                        </div>
                        <h4 class="page-title">SaaS Control Plane</h4>
                    </div>
                </div>
            </div>

            {if $saas_control_error != ''}
            <div class="alert alert-danger">{$saas_control_error}</div>
            {/if}
            {if $saas_control_success != ''}
            <div class="alert alert-success">{$saas_control_success}</div>
            {/if}

            <div class="row">
                <div class="col-lg-6">
                    <div class="card saas-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">Dominio maestro y sincronización</h4>
                            <form method="post" action="{$base_url}index.php?p=saas-control">
                                <input type="hidden" name="save_saas_control" value="1">

                                <div class="form-group">
                                    <label>Dominio Control Plane</label>
                                    <input type="text" name="saas_control_host" class="form-control" value="{$saas_control_settings.saas_control_host}" placeholder="panel.programmit.com">
                                    <small class="text-muted">Solo este dominio puede administrar todo el ecosistema.</small>
                                </div>

                                <div class="form-group">
                                    <label>Dominio panel por defecto</label>
                                    <input type="text" name="saas_default_panel_host" class="form-control" value="{$saas_control_settings.saas_default_panel_host}" placeholder="panel.programmit.com">
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="saas_auto_sync_enabled" name="saas_auto_sync_enabled" value="1" {if $saas_control_settings.saas_auto_sync_enabled == 1}checked{/if}>
                                    <label class="form-check-label" for="saas_auto_sync_enabled">Auto-sync backend habilitado (cada ~45s por tráfico)</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="saas_allow_local_control" name="saas_allow_local_control" value="1" {if $saas_control_settings.saas_allow_local_control == 1}checked{/if}>
                                    <label class="form-check-label" for="saas_allow_local_control">Permitir localhost como control (solo desarrollo)</label>
                                </div>

                                <button type="submit" class="btn btn-primary">Guardar configuración</button>
                            </form>

                            <hr class="bg-info">
                            <p class="mb-1"><strong>Última sincronización:</strong> {$saas_control_settings.saas_last_sync_at|default:'N/A'}</p>
                            <form method="post" action="{$base_url}index.php?p=saas-control">
                                <input type="hidden" name="run_saas_sync" value="1">
                                <button type="submit" class="btn btn-success">Ejecutar sync manual ahora</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Logs de sincronización</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Source</th>
                                            <th>Status</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$saas_sync_logs item=lg}
                                        <tr>
                                            <td>{$lg.id}</td>
                                            <td>{$lg.sync_source}</td>
                                            <td>{$lg.status}</td>
                                            <td>{$lg.started_at}</td>
                                            <td>{$lg.ended_at}</td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
</body>
</html>
