<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Historial de recargas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Panel de finanzas" name="description" />
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
        .vp-history-shell{
            margin-top:14px;
        }
        .vp-history-card{
            background:#ffffff;
            border:1px solid #dbe7f7;
            border-radius:3px;
            box-shadow:0 8px 20px rgba(31, 73, 136, 0.06);
            padding:16px;
        }
        .vp-history-toolbar{
            display:grid;
            grid-template-columns:minmax(220px, 250px) minmax(250px, 290px) 1fr;
            gap:12px;
            align-items:center;
            margin-bottom:12px;
        }
        .vp-range-picker{
            position:relative;
        }
        .vp-range-button{
            width:100%;
            height:42px;
            border:1px solid #e4edf9;
            background:#f5f9ff;
            border-radius:3px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 8px 0 10px;
            color:#60739a;
            font-weight:600;
            cursor:pointer;
            text-align:left;
        }
        .vp-range-button:focus{
            outline:none;
            box-shadow:0 0 0 2px rgba(60, 124, 220, 0.12);
        }
        .vp-range-button-text{
            display:block;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:11.5px;
            flex:1 1 auto;
            min-width:0;
        }
        .vp-range-button-icon{
            width:20px;
            height:20px;
            border-radius:3px;
            border:1px solid #edf2fa;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#9aaacc;
            background:#fbfdff;
            flex:0 0 auto;
            margin-left:6px;
        }
        .vp-range-menu{
            position:absolute;
            top:calc(100% + 18px);
            left:0;
            right:auto;
            width:100%;
            background:#ffffff;
            border:1px solid #e8eef8;
            border-radius:8px;
            box-shadow:0 12px 24px rgba(31, 73, 136, 0.10);
            padding:8px;
            z-index:35;
            display:none;
        }
        .vp-range-menu.is-open{
            display:block;
        }
        .vp-range-menu-title{
            color:#8193b1;
            font-size:12px;
            font-weight:700;
            text-align:center;
            padding:3px 8px 7px;
        }
        .vp-range-option{
            display:flex;
            align-items:center;
            gap:10px;
            width:100%;
            border:0;
            background:#fff;
            text-decoration:none !important;
            color:#6b7f9f;
            padding:10px 10px;
            border-radius:6px;
            font-size:12px;
            font-weight:600;
        }
        .vp-range-option:hover{
            background:#f6f9ff;
            color:#43658f;
        }
        .vp-range-option + .vp-range-option{
            border-top:1px solid #eef3fa;
        }
        .vp-range-option.is-active{
            background:#f1f7ff;
            color:#315f99;
        }
        .vp-range-option-icon{
            width:22px;
            height:22px;
            border:1px solid #dfe7f5;
            border-radius:4px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#8ca0bc;
            font-size:12px;
            flex:0 0 auto;
        }
        .vp-range-cancel{
            width:100%;
            margin-top:6px;
            border:0;
            background:#f8fbff;
            color:#62779c;
            font-weight:700;
            border-radius:6px;
            height:36px;
            cursor:pointer;
        }
        .vp-range-cancel:hover{
            background:#eef4fd;
        }
        .vp-toolbar-box{
            height:42px;
            border:1px solid #e4edf9;
            background:#f5f9ff;
            border-radius:3px;
            display:flex;
            align-items:center;
            padding:0 12px;
            color:#5c7196;
            font-weight:600;
        }
        .vp-toolbar-box .icon{
            margin-right:10px;
            font-size:16px;
            color:#95a8c6;
        }
        .vp-toolbar-summary{
            color:#233a5f;
            font-size:13px;
            line-height:1.3;
            font-weight:700;
        }
        .vp-toolbar-summary span{
            display:block;
        }
        .vp-history-table{
            width:100% !important;
            margin:0 !important;
        }
        .vp-history-table thead th{
            color:#a5b3ca;
            font-size:12px;
            font-weight:700;
            border-bottom:1px dashed #e6edf7 !important;
            background:#fff !important;
            padding:10px 8px;
        }
        .vp-history-table tbody td{
            vertical-align:top;
            padding:9px 8px;
            border-top:1px solid #eef3fa !important;
            color:#7083a7;
            font-size:12px;
            line-height:1.2;
            font-variant-numeric:tabular-nums;
        }
        .vp-history-table tbody tr:hover{
            background:#fbfdff;
        }
        .vp-id-cell{
            color:#60749a;
            font-weight:600;
            font-size:12px;
            line-height:1.2;
            letter-spacing:0;
        }
        .vp-bcp-id{
            color:#60749a;
            font-weight:600;
            font-size:12px;
            line-height:1.2;
        }
        .vp-money{
            color:#60749a;
            font-weight:600;
            font-size:12px;
            line-height:1.2;
        }
        .vp-detail-main{
            color:#6f6298;
            font-weight:600;
            line-height:1.15;
            font-size:12px;
        }
        .vp-detail-sub{
            display:block;
            color:#7f8eb1;
            margin-top:1px;
            line-height:1.15;
            font-size:12px;
        }
        .vp-detail-name{
            display:block;
            color:#7a6e97;
            margin-top:2px;
            font-weight:600;
        }
        .vp-type-stack,
        .vp-state-stack{
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
            gap:4px;
            align-items:flex-start;
        }
        .vp-pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:18px;
            padding:2px 7px;
            border-radius:4px;
            font-size:11px;
            font-weight:700;
            line-height:1;
        }
        .vp-pill.api{
            background:#ffffff;
            color:#7d8fb2;
            border:1px solid #edf2fb;
        }
        .vp-pill.single-use{
            background:#55d78f;
            color:#ffffff;
        }
        .vp-pill.pending{
            background:#1ea1ff;
            color:#ffffff;
        }
        .vp-pill.success{
            background:#4ad28b;
            color:#ffffff;
        }
        .vp-pill.expired{
            background:#ff7a7a;
            color:#ffffff;
        }
        .vp-pill.failed,
        .vp-pill.cancelled{
            background:#ff9b57;
            color:#ffffff;
        }
        .vp-date-cell{
            color:#7b8dad;
            line-height:1.12;
            font-size:12px;
        }
        .vp-date-cell .muted{
            display:block;
            color:#95a4bf;
        }
        .vp-date-label{
            color:#7c8dac;
            font-size:10px;
            font-weight:600;
            letter-spacing:.01em;
            margin-right:4px;
        }
        .vp-date-row{
            display:block;
            white-space:nowrap;
        }
        .vp-info-cell{
            text-align:center;
        }
        .vp-info-btn{
            width:28px;
            height:28px;
            border-radius:50%;
            border:0;
            background:#000;
            color:#fff;
            font-weight:800;
            cursor:pointer;
            line-height:28px;
            padding:0;
            font-size:18px;
            box-shadow:0 8px 18px rgba(0, 0, 0, 0.18);
        }
        .vp-info-btn:hover{
            transform:translateY(-1px);
        }
        .vp-empty-info{
            color:#b1bdd1;
            font-weight:600;
        }
        .vp-history-table .dataTables_empty{
            color:#8092b2;
            padding:22px 10px !important;
        }
        .dataTables_filter,
        .dataTables_length{
            display:none !important;
        }
        .dataTables_info,
        .dataTables_paginate{
            margin-top:14px !important;
        }
        .vp-detail-modal .modal-content{
            border-radius:3px;
            border:1px solid #e3ebf8;
        }
        .vp-detail-modal .modal-header{
            padding:18px 24px;
            border-bottom:1px solid #edf2fb;
        }
        .vp-detail-modal .modal-title{
            color:#243c62;
            font-weight:800;
            font-size:18px;
        }
        .vp-detail-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            border:1px solid #eef3fa;
        }
        .vp-detail-grid-item{
            min-height:96px;
            padding:18px 16px;
            border-right:1px solid #eef3fa;
            border-bottom:1px solid #eef3fa;
        }
        .vp-detail-grid-item:nth-child(4n){
            border-right:0;
        }
        .vp-detail-label{
            color:#263c61;
            font-weight:800;
            margin-bottom:8px;
        }
        .vp-detail-value{
            color:#50627f;
            line-height:1.5;
            white-space:pre-wrap;
            word-break:break-word;
        }
        .vp-detail-footer{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            padding-top:16px;
        }
        .vp-detail-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:40px;
            padding:0 16px;
            background:#2b6fd6;
            color:#fff;
            border-radius:3px;
            font-weight:700;
            text-decoration:none !important;
        }
        .vp-detail-link:hover{
            color:#fff;
            background:#245fba;
        }
        @media (max-width: 1200px){
            .vp-history-toolbar{
                grid-template-columns:1fr 1fr;
            }
        }
        @media (max-width: 991px){
            .vp-history-toolbar{
                grid-template-columns:1fr;
            }
            .vp-range-menu{
                right:auto;
                left:0;
                width:100%;
                max-width:320px;
            }
            .vp-detail-grid{
                grid-template-columns:repeat(2, minmax(0, 1fr));
            }
            .vp-detail-grid-item:nth-child(4n){
                border-right:1px solid #eef3fa;
            }
            .vp-detail-grid-item:nth-child(2n){
                border-right:0;
            }
        }
        @media (max-width: 767px){
            .vp-detail-grid{
                grid-template-columns:1fr;
            }
            .vp-detail-grid-item{
                border-right:0 !important;
            }
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
                                <li class="breadcrumb-item active">Historial de recargas</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Historial de recargas</h4>
                    </div>
                </div>
            </div>

            <div class="vp-history-shell">
                <div class="vp-history-card">
                    <div class="vp-history-toolbar">
                        <div class="vp-toolbar-box">
                            <span class="icon"><i class="fas fa-search"></i></span>
                            <input type="text" id="vpHistorySearch" placeholder="Buscar Movimiento" style="border:0;background:transparent;outline:none;width:100%;color:#60739a;font-weight:600;">
                        </div>
                        <div class="vp-range-picker" id="vpHistoryRangePicker">
                            <button type="button" class="vp-range-button" id="vpHistoryRangeToggle">
                                <span class="vp-range-button-text">{$finance_history_range|default:'-'}</span>
                                <span class="vp-range-button-icon"><i class="far fa-calendar-alt"></i></span>
                            </button>
                            <div class="vp-range-menu" id="vpHistoryRangeMenu">
                                <div class="vp-range-menu-title">Intervalo de Fechas</div>
                                <a href="{$finance_history_range_url_7}" class="vp-range-option {if $finance_history_range_days eq 7}is-active{/if}">
                                    <span class="vp-range-option-icon">7</span>
                                    <span>Últimos 7 días</span>
                                </a>
                                <a href="{$finance_history_range_url_30}" class="vp-range-option {if $finance_history_range_days eq 30}is-active{/if}">
                                    <span class="vp-range-option-icon">30</span>
                                    <span>Últimos 30 días</span>
                                </a>
                                <a href="{$finance_history_range_url_90}" class="vp-range-option {if $finance_history_range_days eq 90}is-active{/if}">
                                    <span class="vp-range-option-icon">90</span>
                                    <span>Últimos 90 días</span>
                                </a>
                                <button type="button" class="vp-range-cancel" id="vpHistoryRangeCancel">Cancelar</button>
                            </div>
                        </div>
                        <div class="vp-toolbar-summary">
                            <span>Qrs pagados: {$finance_history_paid_total}</span>
                            <span>Monto Qrs pagados: {$finance_history_paid_bob} Bs.</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="financeHistoryTable" class="table vp-history-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>BCP ID</th>
                                    <th>Monto (Bs)</th>
                                    <th>Comisión (Bs)</th>
                                    <th>Detalle</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$finance_history_rows item=r}
                                <tr>
                                    <td><div class="vp-id-cell">{$r.provider_txn_id}</div></td>
                                    <td><div class="vp-bcp-id">{$r.bcp_id}</div></td>
                                    <td><div class="vp-money">{$r.amount_bs} Bs.</div></td>
                                    <td><div class="vp-money">{$r.commission_bs} Bs.</div></td>
                                    <td>
                                        <div class="vp-detail-main">{$r.detail_header}</div>
                                        <span class="vp-detail-sub">{$r.detail_reference}</span>
                                    </td>
                                    <td>
                                        <div class="vp-type-stack">
                                            <span class="vp-pill api">{$r.type_label}</span>
                                            {if $r.show_single_use}
                                            <span class="vp-pill single-use">Uso único</span>
                                            {/if}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="vp-state-stack">
                                            <span class="vp-pill {$r.status_class}">{$r.status_label}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="vp-date-cell">
                                            <span class="vp-date-row"><span class="vp-date-label">Creado</span>{$r.created_at}</span>
                                            <span class="vp-date-row muted"><span class="vp-date-label">Pagado</span>{if $r.paid_at neq '-'}{$r.paid_at}{else}-{/if}</span>
                                        </div>
                                    </td>
                                    <td class="vp-info-cell">
                                        {if $r.info_available}
                                        <button
                                            type="button"
                                            class="vp-info-btn js-vp-detail"
                                            data-title="Información del pago"
                                            data-checkout="{$r.checkout_url}"
                                            data-info="{$r.info_rows_json}">
                                            i
                                        </button>
                                        {else}
                                        <span class="vp-empty-info">-</span>
                                        {/if}
                                    </td>
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

<div class="modal fade vp-detail-modal" id="vpDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información del pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="vp-detail-grid" id="vpDetailGrid"></div>
                <div class="vp-detail-footer">
                    <a href="#" class="vp-detail-link" id="vpDetailCheckoutLink">Ver checkout</a>
                </div>
            </div>
        </div>
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
    var historyTable = $('#financeHistoryTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 15,
        language: {
            emptyTable: 'No hay movimientos conciliados para mostrar.',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ movimientos',
            infoEmpty: 'Mostrando 0 a 0 de 0 movimientos',
            infoFiltered: '(filtrado de _MAX_ movimientos)',
            zeroRecords: 'No se encontraron movimientos',
            paginate: {
                previous: 'Anterior',
                next: 'Siguiente'
            }
        }
    });

    $('#vpHistorySearch').on('input', function(){
        historyTable.search(this.value).draw();
    });

    var $rangePicker = $('#vpHistoryRangePicker');
    var $rangeMenu = $('#vpHistoryRangeMenu');

    $('#vpHistoryRangeToggle').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        $rangeMenu.toggleClass('is-open');
    });

    $('#vpHistoryRangeCancel').on('click', function(){
        $rangeMenu.removeClass('is-open');
    });

    $(document).on('click', function(e){
        if (!$rangePicker.length) {
            return;
        }
        if (!$(e.target).closest('#vpHistoryRangePicker').length) {
            $rangeMenu.removeClass('is-open');
        }
    });

    $('.js-vp-detail').on('click', function(){
        var infoRaw = $(this).attr('data-info') || '[]';
        var checkoutUrl = $(this).attr('data-checkout') || '#';
        var rows = [];
        try {
            rows = JSON.parse(infoRaw);
        } catch (e) {
            rows = [];
        }

        var html = '';
        rows.forEach(function(row){
            html += '<div class=\"vp-detail-grid-item\">'
                + '<div class=\"vp-detail-label\">' + $('<div>').text(row.label || '').html() + '</div>'
                + '<div class=\"vp-detail-value\">' + $('<div>').text(row.value || '-').html() + '</div>'
                + '</div>';
        });

        $('#vpDetailGrid').html(html);
        $('#vpDetailCheckoutLink').attr('href', checkoutUrl);
        $('#vpDetailModal').modal('show');
    });
});
</script>
</body>
</html>
