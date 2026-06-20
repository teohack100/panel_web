<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - SaaS Planes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de planes SaaS" name="description" />
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
                                <li class="breadcrumb-item active">Planes</li>
                            </ol>
                        </div>
                        <h4 class="page-title">SaaS White-Label: Planes</h4>
                    </div>
                </div>
            </div>

            {if $saas_plan_error != ''}
            <div class="alert alert-danger">{$saas_plan_error}</div>
            {/if}
            {if $saas_plan_success != ''}
            <div class="alert alert-success">{$saas_plan_success}</div>
            {/if}

            <div class="row">
                <div class="col-lg-5">
                    <div class="card saas-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">{if $saas_edit_plan.id > 0}Editar plan{else}Nuevo plan{/if}</h4>
                            <form method="post" action="{$base_url}index.php?p=saas-plans{if $saas_edit_plan.id > 0}&edit={$saas_edit_plan.id}{/if}">
                                <input type="hidden" name="save_saas_plan" value="1">
                                <input type="hidden" name="plan_id" value="{$saas_edit_plan.id}">

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Codigo</label>
                                        <input type="text" name="plan_code" class="form-control" required value="{$saas_edit_plan.plan_code}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Nombre</label>
                                        <input type="text" name="plan_name" class="form-control" required value="{$saas_edit_plan.plan_name}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea name="description" class="form-control" rows="2">{$saas_edit_plan.description}</textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Mensual USD</label>
                                        <input type="number" step="0.01" min="0" name="monthly_price_usd" class="form-control" value="{$saas_edit_plan.monthly_price_usd}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Setup USD</label>
                                        <input type="number" step="0.01" min="0" name="setup_fee_usd" class="form-control" value="{$saas_edit_plan.setup_fee_usd}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>USD / Credito</label>
                                        <input type="number" step="0.0001" min="0.0001" name="credit_price_usd" class="form-control" value="{$saas_edit_plan.credit_price_usd}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Creditos</label>
                                        <input type="number" min="0" name="included_credits" class="form-control" value="{$saas_edit_plan.included_credits}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Paneles</label>
                                        <input type="number" min="1" name="panel_limit" class="form-control" value="{$saas_edit_plan.panel_limit}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Usuarios</label>
                                        <input type="number" min="1" name="user_limit" class="form-control" value="{$saas_edit_plan.user_limit}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Metodos</label>
                                        <input type="number" min="1" name="method_limit" class="form-control" value="{$saas_edit_plan.method_limit}">
                                    </div>
                                </div>

                                <div class="form-row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {if $saas_edit_plan.is_active == 1}checked{/if}>
                                            <label class="form-check-label" for="is_active">Activo</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {if $saas_edit_plan.is_public == 1}checked{/if}>
                                            <label class="form-check-label" for="is_public">Visible en venta</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success">Guardar plan</button>
                                    <a href="{$base_url}index.php?p=saas-plans" class="btn btn-secondary">Nuevo</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Planes SaaS</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Plan</th>
                                            <th>Precio</th>
                                            <th>USD/Cred</th>
                                            <th>Limites</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$saas_plan_rows item=pl}
                                        <tr>
                                            <td>{$pl.id}</td>
                                            <td><strong>{$pl.plan_name}</strong><br><small>{$pl.plan_code}</small></td>
                                            <td>${$pl.monthly_price_usd|string_format:"%.2f"} / mes</td>
                                            <td>${$pl.credit_price_usd|string_format:"%.4f"}</td>
                                            <td>
                                                P: {$pl.panel_limit}<br>
                                                U: {$pl.user_limit}<br>
                                                M: {$pl.method_limit}
                                            </td>
                                            <td>
                                                {if $pl.is_active==1}<span class="badge badge-success">Activo</span>{else}<span class="badge badge-secondary">Off</span>{/if}
                                                {if $pl.is_public==1}<span class="badge badge-info">Publico</span>{else}<span class="badge badge-dark">Privado</span>{/if}
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary mb-1" href="{$base_url}index.php?p=saas-plans&edit={$pl.id}">Editar</a>
                                                <form class="d-inline" method="post" action="{$base_url}index.php?p=saas-plans">
                                                    <input type="hidden" name="delete_saas_plan" value="1">
                                                    <input type="hidden" name="delete_plan_id" value="{$pl.id}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Eliminar plan?')">Eliminar</button>
                                                </form>
                                            </td>
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
