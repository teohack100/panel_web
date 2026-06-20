<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Webhook Finanzas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Webhook de recargas" name="description" />
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
        .webhook-box{
            border:1px solid #284569;
            border-radius:10px;
            background:#132640;
            color:#d6e9ff;
            padding:12px 14px;
            margin-bottom:12px;
            word-break:break-all;
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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Finanzas</a></li>
                                <li class="breadcrumb-item active">Webhook</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Webhook y callbacks</h4>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">URLs de callback</h5>
                    <div class="webhook-box"><strong>URL recomendada para VeriPagos:</strong><br>{$finance_callback_recommended_url}</div>
                    <div class="webhook-box"><strong>API webhook:</strong><br>{$finance_callback_api_url}</div>
                    <div class="webhook-box"><strong>Friendly webhook legacy:</strong><br>{$finance_callback_friendly_url}</div>
                    <div class="webhook-box"><strong>Webhook maestro sugerido:</strong><br>{$finance_callback_master_url}</div>

                    <h6 class="mt-3">Notas</h6>
                    <ul>
                        <li>En VeriPagos usa la URL <strong>API webhook</strong> o la <strong>URL recomendada</strong>. No uses la ruta legacy friendly.</li>
                        <li>Para acreditar saldo, el proveedor debe enviar <code>reference</code> y estado pagado (<code>paid/success/completed/approved</code>).</li>
                        <li>Si configuras <code>secret</code> en método de pago, se valida firma <code>X-Signature</code>.</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Últimos eventos webhook (desde recargas)</h5>
                    <div class="table-responsive">
                        <table id="financeWebhookTable" class="table table-striped table-bordered dt-responsive" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ref</th>
                                    <th>Estado</th>
                                    <th>Txn</th>
                                    <th>Actualizado</th>
                                    <th>Provider response (resumen)</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$finance_webhook_rows item=r}
                                <tr>
                                    <td>{$r.id}</td>
                                    <td>{$r.recharge_ref}</td>
                                    <td><span class="badge badge-{$r.status_badge}">{$r.status}</span></td>
                                    <td>{$r.provider_txn_id}</td>
                                    <td>{$r.updated_at}</td>
                                    <td><code>{$r.provider_response}</code></td>
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
    $('#financeWebhookTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 15
    });
});
</script>
</body>
</html>
