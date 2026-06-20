<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Admin Finanzas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Panel admin financiero" name="description" />
    <meta content="PROGRAMMIT" name="author" />

    <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    <style>
    {literal}
        .finance-stat-card{
            border:1px solid #263d63;
            border-radius:12px;
            background:linear-gradient(180deg,#1a2f4f 0%,#162844 100%);
            color:#eef4ff;
            box-shadow:0 14px 26px rgba(7,16,32,.2);
            padding:14px 16px;
            margin-bottom:16px;
        }
        .finance-stat-card .v{font-size:30px;font-weight:800;line-height:1.05;color:#fff;}
        .finance-stat-card .l{font-size:13px;color:#a9bfe0;}
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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Finanzas</a></li>
                                <li class="breadcrumb-item active">Monitor de recargas</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Monitor de recargas</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v">{$finance_admin_stats.total}</div><div class="l">Total recargas</div></div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v text-warning">{$finance_admin_stats.pending_total}</div><div class="l">Pendientes</div></div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v text-success">{$finance_admin_stats.paid_total}</div><div class="l">Pagadas</div></div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v text-danger">{$finance_admin_stats.failed_total}</div><div class="l">Fallidas/exp</div></div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v">${$finance_admin_stats.usd_total}</div><div class="l">Total USD</div></div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="finance-stat-card"><div class="v">Bs {$finance_admin_stats.bob_total}</div><div class="l">Total BOB</div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                        <div class="btn-group mb-2">
                            <a class="btn btn-sm {if $finance_admin_status=='all'}btn-primary{else}btn-outline-primary{/if}" href="{$base_url}index.php?p=finance-admin&status=all">Todos</a>
                            <a class="btn btn-sm {if $finance_admin_status=='pending'}btn-warning{else}btn-outline-warning{/if}" href="{$base_url}index.php?p=finance-admin&status=pending">Pendientes</a>
                            <a class="btn btn-sm {if $finance_admin_status=='paid'}btn-success{else}btn-outline-success{/if}" href="{$base_url}index.php?p=finance-admin&status=paid">Pagadas</a>
                            <a class="btn btn-sm {if $finance_admin_status=='failed'}btn-danger{else}btn-outline-danger{/if}" href="{$base_url}index.php?p=finance-admin&status=failed">Fallidas</a>
                            <a class="btn btn-sm {if $finance_admin_status=='expired'}btn-danger{else}btn-outline-danger{/if}" href="{$base_url}index.php?p=finance-admin&status=expired">Expiradas</a>
                            <a class="btn btn-sm {if $finance_admin_status=='cancelled'}btn-danger{else}btn-outline-danger{/if}" href="{$base_url}index.php?p=finance-admin&status=cancelled">Canceladas</a>
                        </div>
                        <div class="mb-2">
                            <a class="btn btn-sm btn-outline-info" href="{$base_url}admin.php">Volver a Admin Central</a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="financeAdminTable" class="table table-striped table-bordered dt-responsive" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ref</th>
                                    <th>Usuario</th>
                                    <th>Método</th>
                                    <th>Total USD</th>
                                    <th>Total BOB</th>
                                    <th>Créditos</th>
                                    <th>Estado</th>
                                    <th>Txn</th>
                                    <th>Creado</th>
                                    <th>Actualizado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$finance_admin_rows item=r}
                                <tr>
                                    <td>{$r.id}</td>
                                    <td>{$r.recharge_ref}</td>
                                    <td>{$r.user_name}</td>
                                    <td>{$r.method_name}</td>
                                    <td>${$r.total_usd}</td>
                                    <td>Bs {$r.total_bob}</td>
                                    <td>{$r.credits_to_add}</td>
                                    <td><span class="badge badge-{$r.status_badge}">{$r.status}</span></td>
                                    <td>{$r.provider_txn_id|default:'-'}</td>
                                    <td>{$r.created_at}</td>
                                    <td>{$r.updated_at}</td>
                                    <td><a href="{$base_url}index.php?p=finance-checkout&id={$r.id}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
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
<script src="{$base_url}firenet/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{$base_url}firenet/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="{$base_url}firenet/assets/js/app.js"></script>
<script>
$(function(){
    $('#financeAdminTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 20
    });
});
</script>
</body>
</html>

