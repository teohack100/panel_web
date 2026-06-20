<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - SaaS Tenants</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de tenants y dominios" name="description" />
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
        .domain-chip{
            display:inline-flex;
            align-items:center;
            gap:6px;
            border:1px solid #35527f;
            border-radius:999px;
            padding:4px 10px;
            margin:2px 4px 2px 0;
            font-size:.78rem;
            color:#dceaff;
            background:#183252;
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
                                <li class="breadcrumb-item active">Tenants</li>
                            </ol>
                        </div>
                        <h4 class="page-title">SaaS White-Label: Tenants y dominios</h4>
                    </div>
                </div>
            </div>

            {if $saas_tenant_error != ''}
            <div class="alert alert-danger">{$saas_tenant_error}</div>
            {/if}
            {if $saas_tenant_success != ''}
            <div class="alert alert-success">{$saas_tenant_success}</div>
            {/if}

            <div class="row">
                <div class="col-lg-5">
                    <div class="card saas-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">{if $saas_edit_tenant.id > 0}Editar tenant{else}Nuevo tenant{/if}</h4>
                            <form method="post" action="{$base_url}index.php?p=saas-tenants{if $saas_edit_tenant.id > 0}&edit={$saas_edit_tenant.id}{/if}">
                                <input type="hidden" name="save_tenant" value="1">
                                <input type="hidden" name="tenant_id" value="{$saas_edit_tenant.id}">

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Tenant key</label>
                                        <input type="text" name="tenant_key" class="form-control" required value="{$saas_edit_tenant.tenant_key}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Owner (user_id)</label>
                                        <select name="owner_user_id" class="custom-select">
                                            {foreach from=$saas_owner_rows item=ow}
                                            <option value="{$ow.user_id}" {if $saas_edit_tenant.owner_user_id == $ow.user_id}selected{/if}>{$ow.user_id} - {$ow.user_name} ({$ow.user_level})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Plan</label>
                                        <select name="plan_id" class="custom-select">
                                            <option value="0">Sin plan</option>
                                            {foreach from=$saas_plan_rows item=pl}
                                            <option value="{$pl.id}" {if $saas_edit_tenant.plan_id == $pl.id}selected{/if}>{$pl.plan_name} ({$pl.plan_code})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="custom-select">
                                            <option value="trial" {if $saas_edit_tenant.status=='trial'}selected{/if}>trial</option>
                                            <option value="active" {if $saas_edit_tenant.status=='active'}selected{/if}>active</option>
                                            <option value="suspended" {if $saas_edit_tenant.status=='suspended'}selected{/if}>suspended</option>
                                            <option value="cancelled" {if $saas_edit_tenant.status=='cancelled'}selected{/if}>cancelled</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nombre tenant</label>
                                        <input type="text" name="display_name" class="form-control" required value="{$saas_edit_tenant.display_name}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Marca visible</label>
                                        <input type="text" name="brand_name" class="form-control" value="{$saas_edit_tenant.brand_name}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Email soporte</label>
                                        <input type="email" name="support_email" class="form-control" value="{$saas_edit_tenant.support_email}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Creditos</label>
                                        <input type="number" min="0" name="credits_balance" class="form-control" value="{$saas_edit_tenant.credits_balance}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Moneda</label>
                                        <input type="text" name="default_currency" class="form-control" value="{$saas_edit_tenant.default_currency}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Mensual USD</label>
                                        <input type="number" min="0" step="0.01" name="monthly_price_usd" class="form-control" value="{$saas_edit_tenant.monthly_price_usd}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>USD/Credito</label>
                                        <input type="number" min="0" step="0.0001" name="credit_price_usd" class="form-control" value="{$saas_edit_tenant.credit_price_usd}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Timezone</label>
                                        <input type="text" name="timezone" class="form-control" value="{$saas_edit_tenant.timezone}">
                                    </div>
                                </div>

                                <h5 class="text-white mt-2 mb-2">Branding</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Logo URL</label>
                                        <input type="text" name="logo_url" class="form-control" value="{$saas_edit_tenant.logo_url}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Favicon URL</label>
                                        <input type="text" name="favicon_url" class="form-control" value="{$saas_edit_tenant.favicon_url}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Color primario</label>
                                        <input type="text" name="primary_color" class="form-control" value="{$saas_edit_tenant.primary_color}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Color accent</label>
                                        <input type="text" name="accent_color" class="form-control" value="{$saas_edit_tenant.accent_color}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Color fondo</label>
                                        <input type="text" name="background_color" class="form-control" value="{$saas_edit_tenant.background_color}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notas</label>
                                    <textarea name="notes" class="form-control" rows="2">{$saas_edit_tenant.notes}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Custom CSS</label>
                                    <textarea name="custom_css" class="form-control" rows="3">{$saas_edit_tenant.custom_css}</textarea>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success">Guardar tenant</button>
                                    <a href="{$base_url}index.php?p=saas-tenants" class="btn btn-secondary">Nuevo</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card saas-card mb-3">
                        <div class="card-body">
                            <h4 class="text-white mb-3">Agregar dominio a tenant</h4>
                            <form method="post" action="{$base_url}index.php?p=saas-tenants">
                                <input type="hidden" name="add_tenant_domain" value="1">
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label>Tenant</label>
                                        <select name="domain_tenant_id" class="custom-select" required>
                                            {foreach from=$saas_tenant_rows item=tn}
                                            <option value="{$tn.id}">{$tn.id} - {$tn.display_name} ({$tn.tenant_key})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Hostname</label>
                                        <input type="text" name="hostname" class="form-control" placeholder="panel.cliente.com" required>
                                    </div>
                                    <div class="form-group col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">Guardar dominio</button>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="dom_primary" name="is_primary" value="1">
                                            <label class="form-check-label" for="dom_primary">Marcar como principal</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="dom_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="dom_active">Activo</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Tenants registrados</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tenant</th>
                                            <th>Plan</th>
                                            <th>Status</th>
                                            <th>Creditos</th>
                                            <th>Dominios</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$saas_tenant_rows item=tn}
                                        <tr>
                                            <td>
                                                <strong>{$tn.display_name}</strong><br>
                                                <small>{$tn.tenant_key} | owner {$tn.owner_user_id}</small>
                                            </td>
                                            <td>
                                                {if $tn.plan_name != ''}{$tn.plan_name}<br><small>{$tn.plan_code}</small>{else}<span class="text-muted">Sin plan</span>{/if}
                                            </td>
                                            <td>
                                                {if $tn.status=='active'}<span class="badge badge-success">active</span>{/if}
                                                {if $tn.status=='trial'}<span class="badge badge-info">trial</span>{/if}
                                                {if $tn.status=='suspended'}<span class="badge badge-warning">suspended</span>{/if}
                                                {if $tn.status=='cancelled'}<span class="badge badge-danger">cancelled</span>{/if}
                                            </td>
                                            <td>{$tn.credits_balance}</td>
                                            <td>
                                                {foreach from=$tn.domains item=dm}
                                                <span class="domain-chip">
                                                    {$dm.hostname}
                                                    {if $dm.is_primary==1}<strong>(P)</strong>{/if}
                                                    {if $dm.is_active==0}<strong>OFF</strong>{/if}
                                                </span>
                                                <div class="d-inline">
                                                    <form method="post" action="{$base_url}index.php?p=saas-tenants" class="d-inline">
                                                        <input type="hidden" name="set_primary_domain" value="1">
                                                        <input type="hidden" name="domain_id" value="{$dm.id}">
                                                        <button type="submit" class="btn btn-sm btn-outline-info mb-1">Primary</button>
                                                    </form>
                                                    <form method="post" action="{$base_url}index.php?p=saas-tenants" class="d-inline">
                                                        <input type="hidden" name="toggle_domain" value="1">
                                                        <input type="hidden" name="domain_id" value="{$dm.id}">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary mb-1">On/Off</button>
                                                    </form>
                                                </div>
                                                <br>
                                                {/foreach}
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary mb-1" href="{$base_url}index.php?p=saas-tenants&edit={$tn.id}">Editar</a>
                                                <form method="post" action="{$base_url}index.php?p=saas-tenants" class="d-inline">
                                                    <input type="hidden" name="suspend_tenant" value="1">
                                                    <input type="hidden" name="suspend_tenant_id" value="{$tn.id}">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning mb-1" onclick="return confirm('Suspender tenant?')">Suspender</button>
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
