<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - VPN Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Control multi-VPS" name="description" />
    <meta content="PROGRAMMIT" name="author" />
    <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    <style>
    {literal}
        html, body.vpn-embed-admin { min-height: 0 !important; height: auto !important; background: #122543 !important; background-image: none !important; }
        body.vpn-embed-admin { margin: 0; overflow-x: hidden; overflow-y: hidden; }
        .vpn-embed-wrapper { min-height: 0 !important; height: auto !important; background: #122543 !important; }
        .vpn-embed-content { margin-left: 0 !important; padding: 0 !important; min-height: 0 !important; height: auto !important; background: #122543 !important; }
        .vpn-embed-content .container-fluid { padding: 0 12px !important; margin: 0 !important; min-height: 0 !important; background: #122543 !important; }
        .vpn-embed-content .page-title-box { display: none !important; }
        body.vpn-embed-admin .vpn-embed-shell { padding: 10px 12px 8px !important; width: 100%; max-width: 100%; }
        .vpn-embed-shell .row { margin-left: -6px; margin-right: -6px; }
        .vpn-embed-shell .row > [class*="col-"] { padding-left: 6px; padding-right: 6px; }
        body.vpn-embed-admin .vpn-card,
        body.vpn-embed-admin .card {
            box-shadow: none;
            border-radius: 10px;
        }
        .vpn-card .card-body {
            padding: 16px;
        }
        .vpn-card {
            border: 1px solid #263d63;
            border-radius: 12px;
            background: linear-gradient(180deg, #192f50 0%, #162744 100%);
            color: #eef4ff;
        }
        .vpn-card .form-control,
        .vpn-card .custom-select,
        .vpn-card textarea {
            background: #13243d;
            border-color: #2d466e;
            color: #eef4ff;
        }
        .vpn-card .form-control:focus,
        .vpn-card .custom-select:focus,
        .vpn-card textarea:focus {
            border-color: #58a4ff;
            box-shadow: none;
        }
        .vpn-code {
            background: #0f1b2d;
            color: #d5e7ff;
            border: 1px solid #29456c;
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            line-height: 1.55;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .vpn-mini {
            font-size: 12px;
            color: #9ec0ec;
        }
        .vpn-table td,
        .vpn-table th {
            vertical-align: middle;
        }
        .vpn-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .vpn-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .vpn-stat-card {
            border: 1px solid #2a456a;
            border-radius: 10px;
            background: rgba(11, 24, 40, .48);
            padding: 12px;
        }
        .vpn-stat-card strong {
            display: block;
            font-size: 24px;
            line-height: 1;
            color: #f2f7ff;
        }
        .vpn-stat-card span {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #9ec0ec;
        }
        .vpn-port-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .vpn-port-pill {
            appearance: none;
            border: 1px solid #35567e;
            border-radius: 999px;
            background: rgba(10, 23, 40, .68);
            color: #d8e8ff;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
        }
        .vpn-port-pill:hover {
            border-color: #5fa7ff;
            color: #ffffff;
        }
        .vpn-advanced {
            margin-top: 12px;
            border: 1px solid #2a456a;
            border-radius: 10px;
            background: rgba(10, 22, 37, .36);
            overflow: hidden;
        }
        .vpn-advanced > summary {
            cursor: pointer;
            list-style: none;
            padding: 12px 14px;
            color: #e8f1ff;
            font-weight: 700;
            outline: none;
        }
        .vpn-advanced > summary::-webkit-details-marker {
            display: none;
        }
        .vpn-advanced > summary:after {
            content: "+";
            float: right;
            color: #87beff;
            font-size: 18px;
            line-height: 1;
        }
        .vpn-advanced[open] > summary:after {
            content: "-";
        }
        .vpn-advanced-body {
            padding: 0 14px 14px;
        }
        .vpn-preview-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-top: 12px;
        }
        .vpn-preview-item {
            border: 1px solid #29456c;
            border-radius: 8px;
            background: rgba(12, 25, 41, .52);
            padding: 10px 12px;
        }
        .vpn-preview-item strong {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #93b8e8;
        }
        .vpn-preview-item span {
            display: block;
            margin-top: 5px;
            color: #eef4ff;
            font-size: 13px;
            font-weight: 700;
            word-break: break-word;
        }
        .vpn-copy-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 8px 0 10px;
        }
        .vpn-copy-feedback {
            margin: 0 0 10px;
            font-size: 12px;
            font-weight: 700;
            color: #9ff3ca;
        }
        .vpn-save-success {
            border: 1px solid rgba(72, 201, 176, .45);
            background: linear-gradient(180deg, rgba(27, 92, 82, .92) 0%, rgba(20, 74, 66, .92) 100%);
            color: #eafff7;
            box-shadow: 0 10px 24px rgba(3, 16, 12, .18);
        }
        .vpn-save-success strong {
            display: block;
            margin-bottom: 3px;
            color: #ffffff;
        }
        .vpn-automation-box {
            margin-top: 14px;
            border: 1px solid #2a456a;
            border-radius: 10px;
            background: rgba(9, 20, 35, .38);
            padding: 14px;
        }
        .vpn-automation-box h5 {
            margin: 0 0 6px;
            color: #eef4ff;
            font-size: 15px;
        }
        .vpn-action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 12px 0 10px;
        }
        .vpn-output-card {
            margin-top: 14px;
            border: 1px solid #2a456a;
            border-radius: 10px;
            background: rgba(10, 20, 34, .32);
            padding: 12px;
        }
        .vpn-output-card strong {
            display: block;
            margin-bottom: 8px;
            color: #eef4ff;
        }
        .vpn-blocked-note {
            margin-top: 10px;
            border: 1px solid rgba(255, 193, 7, .28);
            border-radius: 10px;
            background: rgba(88, 60, 0, .22);
            color: #ffe7a3;
            padding: 12px 13px;
            font-size: 12px;
            line-height: 1.55;
        }
        .vpn-env-banner {
            margin-bottom: 12px;
            border: 1px solid #2a456a;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(17, 33, 55, .95) 0%, rgba(14, 25, 41, .95) 100%);
            padding: 14px 16px;
        }
        .vpn-env-banner-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .vpn-env-banner strong {
            color: #f2f7ff;
        }
        .vpn-env-banner p {
            margin: 0;
            color: #c7dbf8;
            font-size: 13px;
            line-height: 1.55;
        }
        .vpn-action-stack .btn[disabled] {
            opacity: .45;
            cursor: not-allowed;
            box-shadow: none !important;
            pointer-events: none;
        }
        .vpn-direct-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .vpn-direct-kpi {
            border: 1px solid #2a456a;
            border-radius: 10px;
            background: rgba(10, 22, 37, .36);
            padding: 12px;
        }
        .vpn-direct-kpi strong {
            display: block;
            font-size: 24px;
            line-height: 1;
            color: #f5f9ff;
        }
        .vpn-direct-kpi span {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #9ec0ec;
        }
        .vpn-direct-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .vpn-manage-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .vpn-manage-stack .vpn-advanced {
            margin-top: 0;
        }
        .vpn-manage-stack .vpn-advanced > summary {
            background: rgba(8, 18, 31, .28);
        }
        .vpn-table-clean td {
            background: rgba(255, 255, 255, .02);
        }
        .vpn-table-clean .vpn-server-title {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-bottom: 4px;
        }
        .vpn-cell-stack {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .vpn-inline-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
            align-items: center;
        }
        .vpn-inline-status {
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .vpn-inline-status .vpn-badge {
            font-size: 10px;
        }
        .vpn-badge.ok { background: rgba(46, 204, 113, .16); color: #8df0b3; }
        .vpn-badge.warn { background: rgba(241, 196, 15, .16); color: #ffe08a; }
        .vpn-badge.off { background: rgba(231, 76, 60, .16); color: #ff9f98; }
        @media (max-width: 1199.98px) {
            .vpn-embed-shell .col-lg-4,
            .vpn-embed-shell .col-lg-5,
            .vpn-embed-shell .col-lg-6,
            .vpn-embed-shell .col-lg-7,
            .vpn-embed-shell .col-lg-8,
            .vpn-embed-shell .col-lg-12 {
                -ms-flex: 0 0 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        @media (max-width: 991.98px) {
            body.vpn-embed-admin .vpn-embed-shell {
                padding: 8px !important;
            }
            .vpn-embed-content .container-fluid {
                padding: 0 8px !important;
            }
            .vpn-embed-shell .row {
                margin-left: -4px;
                margin-right: -4px;
            }
            .vpn-embed-shell .row > [class*="col-"] {
                padding-left: 4px;
                padding-right: 4px;
            }
            .vpn-embed-shell .form-row > .col-md-3,
            .vpn-embed-shell .form-row > .col-md-4,
            .vpn-embed-shell .form-row > .col-md-5,
            .vpn-embed-shell .form-row > .col-md-6,
            .vpn-embed-shell .form-row > .col-md-8 {
                -ms-flex: 0 0 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
            .vpn-card .card-body {
                padding: 14px;
            }
        }
        @media (max-width: 575.98px) {
            body.vpn-embed-admin .vpn-embed-shell {
                padding: 6px !important;
            }
            .vpn-embed-content .container-fluid {
                padding: 0 6px !important;
            }
            .vpn-card .card-body {
                padding: 12px;
            }
            .vpn-code {
                padding: 12px;
                font-size: 11px;
            }
        }
    {/literal}
    </style>
</head>
<body{if $vpn_embed_admin == 1} class="vpn-embed-admin"{/if}>
{if $vpn_embed_admin != 1}{include file='apps/topnav.tpl'}{/if}
<div class="{if $vpn_embed_admin == 1}vpn-embed-wrapper{else}page-wrapper{/if}">
{if $vpn_embed_admin != 1}{include file='apps/sidenavi.tpl'}{/if}
    <div class="{if $vpn_embed_admin == 1}page-content vpn-embed-content{else}page-content{/if}">
        <div class="container-fluid vpn-embed-shell">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="float-right">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                <li class="breadcrumb-item">Infraestructura</li>
                                <li class="breadcrumb-item active">VPN Control</li>
                            </ol>
                        </div>
                        <h4 class="page-title">VPN Multi-VPS Control</h4>
                    </div>
                </div>
            </div>

            {if $vpn_error != ''}
            <div class="alert alert-danger">{$vpn_error}</div>
            {/if}
            {if $vpn_success != ''}
            <div class="alert alert-success vpn-save-success" id="vpn-success-notice" tabindex="-1">
                <strong>Operación completada</strong>
                {$vpn_success}
            </div>
            {/if}
            {if $vpn_generated_token != ''}
            <div class="alert alert-warning">
                Token generado o actualizado para el agente:
                <strong style="font-family:Consolas,monospace;">{$vpn_generated_token}</strong>
            </div>
            {/if}
            {if isset($vpn_remote_exec_env.label)}
            <div class="vpn-env-banner">
                <div class="vpn-env-banner-top">
                    <span class="vpn-badge {$vpn_remote_exec_env.class|default:'warn'}">{$vpn_remote_exec_env.label|default:'Entorno'}</span>
                    <strong>{$vpn_remote_exec_env.title|default:'Estado del entorno'}</strong>
                </div>
                <p>{$vpn_remote_exec_env.summary|default:''}</p>
                <p class="vpn-mini mt-2">{$vpn_remote_exec_env.next_step|default:''}</p>
            </div>
            {/if}
            {if $vpn_remote_action_output != ''}
            <div class="card vpn-card mb-3" id="vpn-remote-console">
                <div class="card-body">
                    <div class="vpn-env-banner-top mb-2">
                        <span class="vpn-badge {if $vpn_remote_action_status == 'success'}ok{elseif $vpn_remote_action_status == 'error'}off{else}warn{/if}">
                            {if $vpn_remote_action_status == 'success'}ejecutado ok{elseif $vpn_remote_action_status == 'error'}ejecucion con error{else}salida remota{/if}
                        </span>
                        <strong>{$vpn_remote_action_title|default:'Consola remota'}</strong>
                    </div>
                    <div class="vpn-code">{$vpn_remote_action_output|escape:'html'}</div>
                </div>
            </div>
            {/if}

            <div class="row">
                <div class="{if $vpn_admin_view == 'create-server'}col-lg-8{else}col-lg-6{/if}">
                    {if $vpn_admin_view == 'create-server'}
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">1. Alta rápida de VPS</h4>
                            <p class="vpn-mini mb-3">Llena solo lo esencial. El panel detecta host o IP, genera la clave interna y deja el resto listo por dentro.</p>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}" class="js-vpn-create-form">
                                <input type="hidden" name="save_vpn_server" value="1">
                                <input type="hidden" name="server_id" value="0">

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Alias / nombre visible</label>
                                        <input type="text" name="server_name" class="form-control js-vpn-server-name" value="{$vpn_server_form.server_name|default:''}" placeholder="Brasil 01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Dirección principal</label>
                                        <input type="text" name="server_address" class="form-control js-vpn-server-address" value="{if $vpn_server_form.server_host|default:'' != ''}{$vpn_server_form.server_host}{else}{$vpn_server_form.server_ip|default:''}{/if}" placeholder="206.0.29.220 o vpn.tu-dominio.com">
                                        <small class="vpn-mini">Puedes pegar IP, hostname o incluso URL como <code>https://node.tu-dominio.com:443</code>.</small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Puerto base</label>
                                        <input type="number" name="server_port" class="form-control js-vpn-server-port" value="{$vpn_server_form.server_port|default:22}" min="1" max="65535">
                                        <div class="vpn-port-pills">
                                            <button type="button" class="vpn-port-pill" data-set-port="22">22 SSH</button>
                                            <button type="button" class="vpn-port-pill" data-set-port="80">80 HTTP</button>
                                            <button type="button" class="vpn-port-pill" data-set-port="443">443 TLS</button>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Grupo legacy</label>
                                        <select name="legacy_category" class="custom-select js-vpn-legacy-group">
                                            <option value="" {if $vpn_server_form.legacy_category|default:'' == ''}selected{/if}>Sin grupo</option>
                                            <option value="premium" {if $vpn_server_form.legacy_category|default:'' == 'premium'}selected{/if}>premium</option>
                                            <option value="vip" {if $vpn_server_form.legacy_category|default:'' == 'vip'}selected{/if}>vip</option>
                                            <option value="private" {if $vpn_server_form.legacy_category|default:'' == 'private'}selected{/if}>private</option>
                                            <option value="free" {if $vpn_server_form.legacy_category|default:'' == 'free'}selected{/if}>free</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Estado inicial</label>
                                        <select name="status" class="custom-select">
                                            <option value="active" {if $vpn_server_form.status|default:'active' == 'active'}selected{/if}>active</option>
                                            <option value="maintenance" {if $vpn_server_form.status|default:'' == 'maintenance'}selected{/if}>maintenance</option>
                                            <option value="disabled" {if $vpn_server_form.status|default:'' == 'disabled'}selected{/if}>disabled</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sync_enabled" name="sync_enabled" value="1" {if $vpn_server_form.sync_enabled|default:1 == 1}checked{/if}>
                                    <label class="form-check-label" for="sync_enabled">Activar sincronización del nodo</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {if $vpn_server_form.is_public|default:1 == 1}checked{/if}>
                                    <label class="form-check-label" for="is_public">Visible para la app</label>
                                </div>

                                <div class="vpn-automation-box">
                                    <h5>Acceso SSH del nodo</h5>
                                    <p class="vpn-mini mb-3">Aquí guardas el acceso cifrado para que el admin pueda activar el nodo por detrás sin rehacer comandos a mano.</p>
                                    <div class="form-row">
                                        <div class="form-group col-md-5">
                                            <label>Usuario SSH</label>
                                            <input type="text" name="ssh_user" class="form-control" value="{$vpn_server_form.ssh_user|default:'root'}" placeholder="root">
                                        </div>
                                        <div class="form-group col-md-7">
                                            <label>Contraseña SSH</label>
                                            <input type="password" name="ssh_password" class="form-control" value="" placeholder="Guardar acceso cifrado para automatización">
                                            <small class="vpn-mini">En alta nueva puedes dejarla vacía y cargarla después. Si la llenas, el panel podrá guardar y activar el nodo en un solo flujo.</small>
                                        </div>
                                    </div>
                                </div>

                                <details class="vpn-advanced">
                                    <summary>Opciones avanzadas y compatibilidad</summary>
                                    <div class="vpn-advanced-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Clave interna</label>
                                                <input type="text" name="server_key" class="form-control js-vpn-server-key" value="{$vpn_server_form.server_key|default:''}" placeholder="auto">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Host detectado</label>
                                                <input type="text" name="server_host" class="form-control js-vpn-server-host" value="{$vpn_server_form.server_host|default:''}" placeholder="vpn.tu-dominio.com">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>IP detectada</label>
                                                <input type="text" name="server_ip" class="form-control js-vpn-server-ip" value="{$vpn_server_form.server_ip|default:''}" placeholder="206.0.29.220">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Proveedor</label>
                                                <input type="text" name="server_provider" class="form-control" value="{$vpn_server_form.server_provider|default:'custom'}" placeholder="custom">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>País</label>
                                                <input type="text" name="country_code" class="form-control" value="{$vpn_server_form.country_code|default:''}" placeholder="BR">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Ubicación</label>
                                                <input type="text" name="location_label" class="form-control" value="{$vpn_server_form.location_label|default:''}" placeholder="Sao Paulo / Brasil">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Public base URL</label>
                                            <input type="text" name="public_base_url" class="form-control js-vpn-public-base" value="{$vpn_server_form.public_base_url|default:''}" placeholder="https://node.tu-dominio.com">
                                        </div>
                                        <div class="form-group">
                                            <label>Sync token</label>
                                            <input type="text" name="sync_token" class="form-control js-vpn-sync-token" data-token-saved="{$vpn_server_form.sync_token_saved|default:0}" value="" placeholder="Vacío = generar automático al crear">
                                            <div class="vpn-inline-tools">
                                                <button type="button" class="btn btn-sm btn-outline-light js-generate-token">Generar token</button>
                                                <button type="button" class="btn btn-sm btn-outline-light js-clear-token">Vaciar</button>
                                            </div>
                                            <div class="vpn-inline-status">
                                                <span class="vpn-badge js-token-status-badge {if $vpn_server_form.sync_token_saved|default:0 == 1}ok{else}warn{/if}">
                                                    {if $vpn_server_form.sync_token_saved|default:0 == 1}token guardado para auto{else}token nuevo pendiente{/if}
                                                </span>
                                            </div>
                                            <small class="vpn-mini">Si lo dejas vacío al crear, el panel genera uno automáticamente. Si quieres control total, aquí defines el token manual.</small>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Payload público JSON</label>
                                                <textarea name="public_payload_json" class="form-control" rows="4">{$vpn_server_form.public_payload_json|default:'{}'}</textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Meta JSON interno</label>
                                                <textarea name="meta_json" class="form-control" rows="4">{$vpn_server_form.meta_json|default:'{}'}</textarea>
                                                <small class="vpn-mini">Compat legacy opcional: <code>legacy_server_parser</code>, <code>legacy_server_folder</code>, <code>legacy_server_tcp</code>, <code>legacy_server_port</code>.</small>
                                            </div>
                                        </div>
                                    </div>
                                </details>

                                <div class="vpn-action-stack mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar servidor</button>
                                    <button type="submit" name="save_activate_vpn_server" value="1" class="btn btn-outline-light" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{else}Guardar y activar el nodo con SSH automático{/if}" {if $vpn_remote_exec_supported != 1}disabled{/if}>Guardar y activar</button>
                                </div>
                                <p class="vpn-mini mb-0">Si ya cargaste IP, usuario y contraseña SSH, <strong>Guardar y activar</strong> hará el alta, la prueba SSH y la instalación del agente en la misma jugada.</p>
                            </form>
                        </div>
                    </div>
                    {elseif $vpn_server_form.id|default:0 > 0}
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">1. Editar servidor VPS</h4>
                            <p class="vpn-mini mb-3">Estás editando una VPS existente. Para dar de alta una nueva usa la vista <strong>Agregar servidor</strong>.</p>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}">
                                <input type="hidden" name="save_vpn_server" value="1">
                                <input type="hidden" name="server_id" value="{$vpn_server_form.id|default:0}">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Clave</label>
                                        <input type="text" name="server_key" class="form-control" value="{$vpn_server_form.server_key|default:''}" placeholder="vps-bo-01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Nombre visible</label>
                                        <input type="text" name="server_name" class="form-control" value="{$vpn_server_form.server_name|default:''}" placeholder="Bolivia 01">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Host</label>
                                        <input type="text" name="server_host" class="form-control" value="{$vpn_server_form.server_host|default:''}" placeholder="vpn1.tu-dominio.com">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>IP</label>
                                        <input type="text" name="server_ip" class="form-control" value="{$vpn_server_form.server_ip|default:''}" placeholder="212.69.x.x">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Puerto</label>
                                        <input type="number" name="server_port" class="form-control" value="{$vpn_server_form.server_port|default:22}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Proveedor</label>
                                        <input type="text" name="server_provider" class="form-control" value="{$vpn_server_form.server_provider|default:'custom'}" placeholder="xray">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Legacy group</label>
                                        <input type="text" name="legacy_category" class="form-control" value="{$vpn_server_form.legacy_category|default:''}" placeholder="premium">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Estado</label>
                                        <select name="status" class="custom-select">
                                            <option value="active" {if $vpn_server_form.status|default:'active' == 'active'}selected{/if}>active</option>
                                            <option value="maintenance" {if $vpn_server_form.status|default:'' == 'maintenance'}selected{/if}>maintenance</option>
                                            <option value="disabled" {if $vpn_server_form.status|default:'' == 'disabled'}selected{/if}>disabled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>País</label>
                                        <input type="text" name="country_code" class="form-control" value="{$vpn_server_form.country_code|default:''}" placeholder="BO">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Ubicación</label>
                                        <input type="text" name="location_label" class="form-control" value="{$vpn_server_form.location_label|default:''}" placeholder="La Paz / Bolivia">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Public base URL</label>
                                    <input type="text" name="public_base_url" class="form-control" value="{$vpn_server_form.public_base_url|default:''}" placeholder="https://node.tu-dominio.com">
                                </div>
                                <div class="form-group">
                                    <label>Sync token</label>
                                    <input type="text" name="sync_token" class="form-control js-vpn-sync-token" data-token-saved="{$vpn_server_form.sync_token_saved|default:0}" value="" placeholder="Deja vacío para conservar el actual. Usa generar si quieres regenerarlo">
                                    <div class="vpn-inline-tools">
                                        <button type="button" class="btn btn-sm btn-outline-light js-generate-token">Generar nuevo token</button>
                                        <button type="button" class="btn btn-sm btn-outline-light js-clear-token">Conservar actual</button>
                                    </div>
                                    <div class="vpn-inline-status">
                                        <span class="vpn-badge js-token-status-badge {if $vpn_server_form.sync_token_saved|default:0 == 1}ok{else}warn{/if}">
                                            {if $vpn_server_form.sync_token_saved|default:0 == 1}token actual guardado{/if}
                                            {if $vpn_server_form.sync_token_saved|default:0 != 1}este nodo aún no tiene token usable{/if}
                                        </span>
                                    </div>
                                    <small class="vpn-mini">El valor plano solo se muestra una vez tras guardar. Si generas uno nuevo aquí, reemplaza el anterior para este nodo.</small>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Payload público JSON</label>
                                        <textarea name="public_payload_json" class="form-control" rows="4">{$vpn_server_form.public_payload_json|default:'{}'}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Meta JSON interno</label>
                                        <textarea name="meta_json" class="form-control" rows="4">{$vpn_server_form.meta_json|default:'{}'}</textarea>
                                        <small class="vpn-mini">Compat legacy opcional: <code>legacy_server_parser</code>, <code>legacy_server_folder</code>, <code>legacy_server_tcp</code>, <code>legacy_server_port</code>.</small>
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sync_enabled" name="sync_enabled" value="1" {if $vpn_server_form.sync_enabled|default:0 == 1}checked{/if}>
                                    <label class="form-check-label" for="sync_enabled">Sync habilitado</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {if $vpn_server_form.is_public|default:0 == 1}checked{/if}>
                                    <label class="form-check-label" for="is_public">Visible en endpoint de la app</label>
                                </div>
                                <div class="vpn-automation-box mb-3">
                                    <h5>Acceso SSH del nodo</h5>
                                    <div class="form-row">
                                        <div class="form-group col-md-5 mb-md-0">
                                            <label>Usuario SSH</label>
                                            <input type="text" name="ssh_user" class="form-control" value="{$vpn_server_form.ssh_user|default:'root'}" placeholder="root">
                                        </div>
                                        <div class="form-group col-md-7 mb-0">
                                            <label>Nueva contraseña SSH</label>
                                            <input type="password" name="ssh_password" class="form-control" value="" placeholder="Deja vacío para conservar la guardada">
                                            <small class="vpn-mini">
                                                {if $vpn_server_form.ssh_password_saved|default:0 == 1}
                                                Ya existe una contraseña cifrada guardada para este nodo.
                                                {else}
                                                Este nodo aún no tiene contraseña SSH cifrada guardada.
                                                {/if}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="vpn-action-stack">
                                    <button type="submit" class="btn btn-primary">Actualizar servidor</button>
                                    <button type="submit" name="save_activate_vpn_server" value="1" class="btn btn-outline-light" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{else}Actualizar y activar el nodo con SSH automático{/if}" {if $vpn_remote_exec_supported != 1}disabled{/if}>Actualizar y activar</button>
                                    <a href="{$base_url}index.php?p=vpn-control{$vpn_view_qs}" class="btn btn-outline-light">Cerrar edición</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    {else}
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">1. Resumen operativo</h4>
                            <div class="vpn-stat-grid">
                                <div class="vpn-stat-card">
                                    <strong>{$vpn_servers|@count}</strong>
                                    <span>VPS registradas</span>
                                </div>
                                <div class="vpn-stat-card">
                                    <strong>{$vpn_methods|@count}</strong>
                                    <span>Métodos lógicos</span>
                                </div>
                                <div class="vpn-stat-card">
                                    <strong>{$vpn_deployments|@count}</strong>
                                    <span>Relaciones activas</span>
                                </div>
                            </div>
                            <div class="vpn-code mt-3">Flujo recomendado
1. Agregar servidor -> alta nueva de la VPS o IP
2. Gestion VPS -> métodos, relaciones, sync y monitoreo
3. Editar servidor -> desde la tabla de servidores registrados</div>
                            <div class="mt-3">
                                <a href="{$base_url}admin.php#vpn-create-main" target="_top" class="btn btn-primary">Ir a Agregar servidor</a>
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>

                <div class="{if $vpn_admin_view == 'create-server'}col-lg-4{else}col-lg-6{/if}">
                    {if $vpn_admin_view == 'create-server'}
                    <div class="card vpn-card">
                        <div class="card-body">
                            {if $vpn_onboarding_ready == 1}
                            <h4 class="text-white mb-3">Activación automática</h4>
                            <p class="vpn-mini mb-2">La VPS <strong>{$vpn_onboarding_server_name|default:'Nodo nuevo'}</strong> ya fue guardada. Desde aquí puedes dejarla operativa sin abrir terminal ni pegar comandos manuales.</p>
                            <div class="vpn-preview-list">
                                <div class="vpn-preview-item">
                                    <strong>Nodo</strong>
                                    <span>{$vpn_onboarding_server_target|default:'-'}</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>Usuario SSH</strong>
                                    <span>{$vpn_onboarding_ssh_user|default:'root'}{if $vpn_onboarding_ssh_ready == 1} · acceso cifrado guardado{else} · falta contraseña guardada{/if}</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>Estado del token</strong>
                                    <span>
                                        {if $vpn_onboarding_token_ready == 1}
                                            {if $vpn_onboarding_token_visible == 1}Visible en esta alta{else}Guardado para automatización{/if}
                                        {else}
                                            Falta token usable, genera o define uno manual
                                        {/if}
                                    </span>
                                </div>
                            </div>
                            <div class="vpn-inline-status">
                                <span class="vpn-badge {if $vpn_onboarding_ssh_ready == 1}ok{else}warn{/if}">{if $vpn_onboarding_ssh_ready == 1}ssh listo{else}ssh pendiente{/if}</span>
                                <span class="vpn-badge {if $vpn_onboarding_token_ready == 1}ok{else}warn{/if}">{if $vpn_onboarding_token_ready == 1}token listo{else}token pendiente{/if}</span>
                                <span class="vpn-badge {if $vpn_onboarding_ready_for_install == 1}ok{else}off{/if}">{if $vpn_onboarding_ready_for_install == 1}auto listo{else}requiere completar datos{/if}</span>
                            </div>
                            <div class="vpn-action-stack">
                                <form method="post" action="{$base_url}index.php?p=vpn-control&onboard_server={$vpn_onboarding_server_id}{$vpn_embed_qs}&vpn_view=create-server">
                                    <input type="hidden" name="server_id" value="{$vpn_onboarding_server_id}">
                                    <button type="submit" name="vpn_activate_node" value="1" class="btn btn-sm btn-primary" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{elseif $vpn_onboarding_ready_for_install != 1}Completa SSH y token para habilitar la activación automática.{else}Probar SSH, copiar config, instalar agente y dejar el timer arriba{/if}" {if $vpn_onboarding_ready_for_install != 1 || $vpn_remote_exec_supported != 1}disabled{/if}>Activar nodo 1 clic</button>
                                </form>
                            </div>
                            <div class="vpn-code mt-3">Lo que hace por detrás
1. valida acceso SSH
2. genera y escribe config.json
3. copia agent.py y servicios systemd
4. habilita timer + arranca el sync inicial</div>
                            <p class="vpn-mini mb-0">
                                {if $vpn_remote_exec_supported == 1}
                                Automatización disponible en este panel. Si usas un usuario distinto de `root`, la activación remota asume `sudo` sin contraseña.
                                {else}
                                {$vpn_remote_exec_reason|default:'La automatización SSH no está disponible en este entorno.'}
                                {/if}
                            </p>
                            {if $vpn_remote_exec_supported != 1}
                            <div class="vpn-blocked-note">
                                <strong>Acciones remotas bloqueadas aquí.</strong><br>
                                Estás usando el panel en entorno local/XAMPP. `Activar nodo 1 clic` se habilita cuando este mismo código corre en el panel Linux del VPS.<br>
                                Paso recomendado: termina de guardar `SSH + token` aquí y luego prueba en `panel.programmit.com`.
                            </div>
                            {elseif $vpn_onboarding_ready_for_install != 1}
                            <div class="vpn-blocked-note">
                                <strong>Falta completar el nodo.</strong><br>
                                Si `Activar nodo 1 clic` no está activo, revisa que este servidor tenga contraseña SSH guardada y token usable.
                            </div>
                            {/if}
                            <details class="vpn-advanced mt-3">
                                <summary>Herramientas técnicas y modo manual</summary>
                                <div class="vpn-advanced-body">
                                    <div class="vpn-action-stack">
                                        <form method="post" action="{$base_url}index.php?p=vpn-control&onboard_server={$vpn_onboarding_server_id}{$vpn_embed_qs}&vpn_view=create-server">
                                            <input type="hidden" name="server_id" value="{$vpn_onboarding_server_id}">
                                            <button type="submit" name="vpn_test_ssh" value="1" class="btn btn-sm btn-outline-light" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{elseif $vpn_onboarding_ssh_ready != 1}Falta contraseña SSH guardada para este nodo.{else}Probar conexión SSH{/if}" {if $vpn_onboarding_ssh_ready != 1 || $vpn_remote_exec_supported != 1}disabled{/if}>Probar SSH</button>
                                        </form>
                                        <form method="post" action="{$base_url}index.php?p=vpn-control&onboard_server={$vpn_onboarding_server_id}{$vpn_embed_qs}&vpn_view=create-server">
                                            <input type="hidden" name="server_id" value="{$vpn_onboarding_server_id}">
                                            <button type="submit" name="vpn_install_agent" value="1" class="btn btn-sm btn-outline-light" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{elseif $vpn_onboarding_ready_for_install != 1}Completa SSH y token para habilitar instalación automática.{else}Instalar agente remoto{/if}" {if $vpn_onboarding_ready_for_install != 1 || $vpn_remote_exec_supported != 1}disabled{/if}>Solo instalar agente</button>
                                        </form>
                                    </div>
                                    <p class="vpn-mini mt-2 mb-1"><strong>config.json</strong></p>
                                    <div class="vpn-copy-actions">
                                        <button type="button" class="btn btn-sm btn-outline-light js-copy-text" data-copy-target="vpnOnboardingConfig">Copiar config</button>
                                    </div>
                                    <div class="vpn-code" id="vpnOnboardingConfig">{$vpn_onboarding_config_json|default:'{}'|escape:'html'}</div>
                                    <p class="vpn-mini mt-3 mb-1"><strong>Instalación base</strong></p>
                                    <div class="vpn-copy-actions">
                                        <button type="button" class="btn btn-sm btn-outline-light js-copy-text" data-copy-target="vpnOnboardingInstall">Copiar comando</button>
                                    </div>
                                    <div class="vpn-code" id="vpnOnboardingInstall">{$vpn_onboarding_install_script|default:''|escape:'html'}</div>
                                </div>
                            </details>
                            <p class="vpn-copy-feedback js-copy-feedback" style="display:none;"></p>
                            <p class="vpn-mini mt-3 mb-0">Después de esto entra a <strong>Gestion VPS</strong> para validar `pull`, `ack`, runtime e identidad SSH del nodo.</p>
                            {else}
                            <h4 class="text-white mb-3">Motor automático</h4>
                            <p class="vpn-mini mb-2">Mientras llenas el alta rápida, el panel prepara los valores internos que antes tenías que escribir a mano.</p>
                            <div class="vpn-preview-list">
                                <div class="vpn-preview-item">
                                    <strong>Clave interna</strong>
                                    <span data-preview="server-key">auto</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>Alias final</strong>
                                    <span data-preview="server-name">-</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>Detección de dirección</strong>
                                    <span data-preview="address-kind">Esperando datos</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>Host / IP interna</strong>
                                    <span data-preview="address-target">-</span>
                                </div>
                                <div class="vpn-preview-item">
                                    <strong>URL pública</strong>
                                    <span data-preview="public-base">Manual o avanzada</span>
                                </div>
                            </div>
                            <div class="vpn-code mt-3">Qué se hace por dentro
- genera server_key si no lo defines
- separa host o IP automáticamente
- conserva sync y visibilidad activos por defecto
- deja compatibilidad legacy lista en modo avanzado si la necesitas</div>
                            <div class="mt-3">
                                <span class="vpn-badge ok">Servidores actuales: {$vpn_servers|@count}</span>
                            </div>
                            {/if}
                        </div>
                    </div>
                    {else}
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">Control rápido VPS</h4>
                            <p class="vpn-mini mb-3">Aquí debería resolverse casi todo sin entrar a zonas técnicas: registrar nodo, completar SSH/token, instalar agente y validar sync.</p>
                            <div class="vpn-direct-grid">
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.total|default:0}</strong>
                                    <span>Nodos registrados</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.healthy|default:0}</strong>
                                    <span>Salud OK</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.attention|default:0}</strong>
                                    <span>Piden atención</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.offline|default:0}</strong>
                                    <span>Offline / bloqueados</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.automation_ready|default:0}</strong>
                                    <span>Auto listos</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.pending_setup|default:0}</strong>
                                    <span>Falta completar</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.agent_reporting|default:0}</strong>
                                    <span>Agent reportando</span>
                                </div>
                                <div class="vpn-direct-kpi">
                                    <strong>{$vpn_manage_summary.sync_on|default:0}</strong>
                                    <span>Sync activo</span>
                                </div>
                            </div>
                            <div class="vpn-direct-actions">
                                <a href="{$base_url}admin.php#vpn-create-main" target="_top" class="btn btn-primary">Agregar servidor</a>
                                <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}" style="margin:0;">
                                    <input type="hidden" name="run_vpn_reconcile" value="1">
                                    <button type="submit" class="btn btn-outline-light">Reconciliar ahora</button>
                                </form>
                            </div>
                            <div class="vpn-code mt-3">Ruta recomendada
1. Revisa qué nodos no están en `salud ok`
2. Completa SSH o token faltante
3. Usa `Activar nodo`
4. Confirma `online + agent ok + sync`</div>
                        </div>
                    </div>
                    {/if}
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">{if $vpn_admin_view == 'create-server'}Servidores actuales{else}Servidores registrados{/if}</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered vpn-table vpn-table-clean">
                                    <thead>
                                        <tr>
                                            <th>Servidor</th>
                                            <th>Salud</th>
                                            <th>Automatización</th>
                                            <th>Sync</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$vpn_servers item=server}
                                        <tr>
                                            <td>
                                                <div class="vpn-server-title">
                                                    <strong>{$server.server_name}</strong>
                                                    <span class="vpn-badge {if $server.status == 'active'}ok{elseif $server.status == 'maintenance'}warn{else}off{/if}">{$server.status}</span>
                                                    {if $server.legacy_category|default:'' != ''}<span class="vpn-badge warn">{$server.legacy_category}</span>{/if}
                                                </div>
                                                <span class="vpn-mini">destino: {$server.server_host|default:$server.server_ip}{if $server.server_port != ''}:{$server.server_port}{/if}</span>
                                                <br><span class="vpn-mini">key: {$server.server_key}</span>
                                                <br><span class="vpn-mini">routes: {$server.active_mapping_count|default:0}/{$server.mapping_total_count|default:0}</span>
                                                {if isset($server.identity_state.label)}
                                                <br><span class="vpn-badge {$server.identity_state.class}">{$server.identity_state.label}</span>
                                                {if $server.identity_state.note != ''}<br><span class="vpn-mini">{$server.identity_state.note}</span>{/if}
                                                {/if}
                                                {if isset($server.meta.legacy_bridge.server_id) && $server.meta.legacy_bridge.server_id != ''}
                                                <br><span class="vpn-mini">legacy row: {$server.meta.legacy_bridge.server_id} / {$server.meta.legacy_bridge.server_category|default:$server.legacy_category}</span>
                                                {elseif $server.legacy_category|default:'' != ''}
                                                <br><span class="vpn-mini">legacy: pendiente / {$server.legacy_category}</span>
                                                {/if}
                                                {if $server.runtime_hostname != '' || $server.runtime_fqdn != '' || $server.runtime_request_ip != ''}
                                                <br><span class="vpn-mini">runtime: {if $server.runtime_fqdn != ''}{$server.runtime_fqdn}{elseif $server.runtime_hostname != ''}{$server.runtime_hostname}{else}-{/if}{if $server.runtime_request_ip != ''} · {$server.runtime_request_ip}{/if}</span>
                                                {/if}
                                                {if $server.runtime_ssh_md5_summary != ''}
                                                <br><span class="vpn-mini">ssh: {$server.runtime_ssh_md5_summary}</span>
                                                {/if}
                                            </td>
                                            <td>
                                                <div class="vpn-cell-stack">
                                                    <div>
                                                        <span class="vpn-badge {if isset($server.health_state.class)}{$server.health_state.class}{else}warn{/if}">
                                                            {if isset($server.health_state.label)}{$server.health_state.label}{else}atencion{/if}
                                                        </span>
                                                        <span class="vpn-mini">{if isset($server.health_state.last_signal)}{$server.health_state.last_signal}{else}sin señal{/if}</span>
                                                    </div>
                                                    <div>
                                                        <span class="vpn-badge {if isset($server.health_state.online.class)}{$server.health_state.online.class}{else}off{/if}">
                                                            {if isset($server.health_state.online.label)}{$server.health_state.online.label}{else}offline{/if}
                                                        </span>
                                                        <span class="vpn-badge {if isset($server.health_state.agent.class)}{$server.health_state.agent.class}{else}off{/if}">
                                                            {if isset($server.health_state.agent.label)}{$server.health_state.agent.label}{else}agent off{/if}
                                                        </span>
                                                        <span class="vpn-badge {if isset($server.health_state.sync.class)}{$server.health_state.sync.class}{else}off{/if}">
                                                            {if isset($server.health_state.sync.label)}{$server.health_state.sync.label}{else}sync off{/if}
                                                        </span>
                                                    </div>
                                                    {if isset($server.health_state.note) && $server.health_state.note != ''}
                                                    <span class="vpn-mini">{$server.health_state.note}</span>
                                                    {/if}
                                                    {if $server.last_seen_at|default:'' != ''}<span class="vpn-mini">seen: {$server.last_seen_at}</span>{/if}
                                                    {if $server.last_sync_at|default:'' != ''}<span class="vpn-mini">pull: {$server.last_sync_at}</span>{/if}
                                                    {if $server.last_ack_at|default:'' != ''}<span class="vpn-mini">ack: {$server.last_ack_at}</span>{/if}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="vpn-cell-stack">
                                                    {if isset($server.control_access.ssh_user)}
                                                    <span class="vpn-mini">{$server.control_access.ssh_user}@{$server.control_access.target|default:'-'}</span>
                                                    {/if}
                                                    <div>
                                                        <span class="vpn-badge {if $server.control_access.has_password}ok{else}warn{/if}">{if $server.control_access.has_password}ssh ok{else}ssh falta{/if}</span>
                                                        <span class="vpn-badge {if $server.control_access.has_sync_token}ok{else}warn{/if}">{if $server.control_access.has_sync_token}token ok{else}token falta{/if}</span>
                                                        <span class="vpn-badge {if $server.control_access.ready_for_install}ok{else}off{/if}">{if $server.control_access.ready_for_install}auto listo{else}manual{/if}</span>
                                                    </div>
                                                    {if $server.control_access.last_test_at|default:'' != ''}
                                                    <span class="vpn-mini">test: {$server.control_access.last_test_status|default:'-'} / {$server.control_access.last_test_at}</span>
                                                    {/if}
                                                    {if $server.control_access.last_install_at|default:'' != ''}
                                                    <span class="vpn-mini">install: {$server.control_access.last_install_status|default:'-'} / {$server.control_access.last_install_at}</span>
                                                    {/if}
                                                    {if $server.control_access.last_activate_at|default:'' != ''}
                                                    <span class="vpn-mini">activate: {$server.control_access.last_activate_status|default:'-'} / {$server.control_access.last_activate_at}</span>
                                                    {/if}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="vpn-cell-stack">
                                                    <div>
                                                        <span class="vpn-badge {if isset($server.sync_state.class)}{$server.sync_state.class}{else}{if $server.sync_enabled == 1}ok{else}off{/if}{/if}">
                                                            {if isset($server.sync_state.label)}{$server.sync_state.label}{else}{if $server.sync_enabled == 1}on{else}off{/if}{/if}
                                                        </span>
                                                        <span class="vpn-mini">switch: {if $server.sync_enabled == 1}on{else}off{/if}</span>
                                                    </div>
                                                    <span class="vpn-mini">cursor: {$server.last_sync_cursor|default:0}</span>
                                                    <span class="vpn-mini">routes: {$server.active_mapping_count|default:0}</span>
                                                    {if $server.runtime_agent_version != ''}<span class="vpn-mini">agent: {$server.runtime_agent_version}</span>{/if}
                                                    {if isset($server.sync_state.note) && $server.sync_state.note != ''}<span class="vpn-mini">{$server.sync_state.note}</span>{/if}
                                                    {if isset($server.last_log.status) && $server.last_log.status != ''}<span class="vpn-mini">log: {$server.last_log.action_name|default:'sync'} / {$server.last_log.status}</span>{/if}
                                                    {if isset($server.last_log.details.reason) && $server.last_log.details.reason != ''}<span class="vpn-mini">reason: {$server.last_log.details.reason}</span>{/if}
                                                    {if $server.last_ack_at|default:'' != ''}<span class="vpn-mini">ack: {$server.last_ack_at}</span>{/if}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:flex;flex-direction:column;gap:6px;min-width:120px;">
                                                    <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}" style="margin:0;">
                                                        <input type="hidden" name="server_id" value="{$server.id}">
                                                        <button type="submit" name="vpn_activate_node" value="1" class="btn btn-sm btn-primary" title="{if $vpn_remote_exec_supported != 1}{$vpn_remote_exec_reason|escape:'html'}{elseif $server.control_access.ready_for_install != 1}Completa SSH y token para habilitar la activación automática.{else}Probar SSH, instalar agente y dejar el nodo arriba{/if}" {if $server.control_access.ready_for_install != 1 || $vpn_remote_exec_supported != 1}disabled{/if}>Activar nodo</button>
                                                    </form>
                                                    <a href="{$base_url}index.php?p=vpn-control&edit_server={$server.id}{$vpn_view_qs}" class="btn btn-sm btn-outline-primary">Editar</a>
                                                    <a href="{$base_url}index.php?p=vpn-control&onboard_server={$server.id}{$vpn_embed_qs}&vpn_view=create-server" class="btn btn-sm btn-outline-info">Abrir flujo</a>
                                                </div>
                                            </td>
                                        </tr>
                                        {foreachelse}
                                        <tr>
                                            <td colspan="5" class="text-center">No hay servidores registrados.</td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {if $vpn_admin_view != 'create-server'}
            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">Panel avanzado</h4>
                            <p class="vpn-mini mb-3">Todo lo técnico queda aquí plegado para que la vista principal de nodos siga limpia.</p>
                            <div class="vpn-manage-stack">
                                <details class="vpn-advanced"{if $vpn_method_form.id|default:0 > 0} open{/if}>
                                    <summary>Métodos lógicos</summary>
                                    <div class="vpn-advanced-body">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="vpn-card">
                                                    <div class="card-body">
                                                        <h4 class="text-white mb-3">Guardar método</h4>
                                                        <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}">
                                                            <input type="hidden" name="save_vpn_method" value="1">
                                                            <input type="hidden" name="method_id" value="{$vpn_method_form.id|default:0}">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Clave</label>
                                                                    <input type="text" name="method_key" class="form-control" value="{$vpn_method_form.method_key|default:''}" placeholder="premium">
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Nombre</label>
                                                                    <input type="text" name="method_name" class="form-control" value="{$vpn_method_form.method_name|default:''}" placeholder="Premium">
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-4">
                                                                    <label>Tipo</label>
                                                                    <input type="text" name="method_type" class="form-control" value="{$vpn_method_form.method_type|default:'custom'}" placeholder="premium">
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label>Legacy group</label>
                                                                    <input type="text" name="legacy_group" class="form-control" value="{$vpn_method_form.legacy_group|default:''}" placeholder="premium">
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label>Auth mode</label>
                                                                    <input type="text" name="auth_mode" class="form-control" value="{$vpn_method_form.auth_mode|default:'local'}" placeholder="local">
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-4">
                                                                    <label>Orden</label>
                                                                    <input type="number" name="sort_order" class="form-control" value="{$vpn_method_form.sort_order|default:100}">
                                                                </div>
                                                                <div class="form-group col-md-8">
                                                                    <label>Config JSON</label>
                                                                    <textarea name="config_json" class="form-control" rows="4">{$vpn_method_form.config_json|default:'{}'}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" id="method_active" name="is_active" value="1" {if $vpn_method_form.is_active|default:0 == 1}checked{/if}>
                                                                <label class="form-check-label" for="method_active">Método activo</label>
                                                            </div>
                                                            <div class="form-check mb-3">
                                                                <input class="form-check-input" type="checkbox" id="method_public" name="is_public" value="1" {if $vpn_method_form.is_public|default:0 == 1}checked{/if}>
                                                                <label class="form-check-label" for="method_public">Visible en la app</label>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Guardar método</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped table-bordered vpn-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Método</th>
                                                                <th>Tipo</th>
                                                                <th>Auth</th>
                                                                <th>Estado</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach from=$vpn_methods item=method}
                                                            <tr>
                                                                <td>
                                                                    <strong>{$method.method_name}</strong><br>
                                                                    <span class="vpn-mini">{$method.method_key}</span>
                                                                </td>
                                                                <td>{$method.method_type}</td>
                                                                <td>{$method.auth_mode}</td>
                                                                <td>
                                                                    <span class="vpn-badge {if $method.is_active == 1}ok{else}off{/if}">
                                                                        {if $method.is_active == 1}active{else}disabled{/if}
                                                                    </span>
                                                                </td>
                                                                <td><a href="{$base_url}index.php?p=vpn-control&edit_method={$method.id}{$vpn_view_qs}" class="btn btn-sm btn-outline-primary">Editar</a></td>
                                                            </tr>
                                                            {foreachelse}
                                                            <tr>
                                                                <td colspan="5" class="text-center">No hay métodos registrados.</td>
                                                            </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>

                                <details class="vpn-advanced"{if $vpn_map_form.id|default:0 > 0} open{/if}>
                                    <summary>Relaciones método ↔ VPS y despliegues</summary>
                                    <div class="vpn-advanced-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}">
                                                    <input type="hidden" name="save_vpn_mapping" value="1">
                                                    <input type="hidden" name="map_id" value="{$vpn_map_form.id|default:0}">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Método</label>
                                                            <select name="method_id" class="custom-select">
                                                                <option value="0">Selecciona método</option>
                                                                {foreach from=$vpn_methods item=method}
                                                                <option value="{$method.id}" {if $vpn_map_form.method_id|default:0 == $method.id}selected{/if}>{$method.method_name} ({$method.method_key})</option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Servidor</label>
                                                            <select name="server_id" class="custom-select">
                                                                <option value="0">Selecciona servidor</option>
                                                                {foreach from=$vpn_servers item=server}
                                                                <option value="{$server.id}" {if $vpn_map_form.server_id|default:0 == $server.id}selected{/if}>{$server.server_name} ({$server.server_key})</option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-3">
                                                            <label>Protocolo</label>
                                                            <select name="endpoint_protocol" class="custom-select">
                                                                <option value="https" {if $vpn_map_form.endpoint_protocol|default:'https' == 'https'}selected{/if}>https</option>
                                                                <option value="http" {if $vpn_map_form.endpoint_protocol|default:'' == 'http'}selected{/if}>http</option>
                                                                <option value="tcp" {if $vpn_map_form.endpoint_protocol|default:'' == 'tcp'}selected{/if}>tcp</option>
                                                                <option value="udp" {if $vpn_map_form.endpoint_protocol|default:'' == 'udp'}selected{/if}>udp</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-5">
                                                            <label>Host endpoint</label>
                                                            <input type="text" name="endpoint_host" class="form-control" value="{$vpn_map_form.endpoint_host|default:''}" placeholder="vpn1.tu-dominio.com">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Puerto</label>
                                                            <input type="number" name="endpoint_port" class="form-control" value="{$vpn_map_form.endpoint_port|default:443}">
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Path</label>
                                                            <input type="text" name="deploy_path" class="form-control" value="{$vpn_map_form.deploy_path|default:''}" placeholder="/connect">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>TLS SNI</label>
                                                            <input type="text" name="tls_sni" class="form-control" value="{$vpn_map_form.tls_sni|default:''}" placeholder="sni.tu-dominio.com">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Peso</label>
                                                            <input type="number" name="weight" class="form-control" value="{$vpn_map_form.weight|default:100}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Config JSON</label>
                                                        <textarea name="config_json" class="form-control" rows="4">{$vpn_map_form.config_json|default:'{}'}</textarea>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="map_active" name="is_active" value="1" {if $vpn_map_form.is_active|default:0 == 1}checked{/if}>
                                                        <label class="form-check-label" for="map_active">Relación activa</label>
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="map_default" name="is_default" value="1" {if $vpn_map_form.is_default|default:0 == 1}checked{/if}>
                                                        <label class="form-check-label" for="map_default">Nodo por defecto del método</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Guardar relación</button>
                                                </form>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped table-bordered vpn-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Método</th>
                                                                <th>Servidor</th>
                                                                <th>Endpoint</th>
                                                                <th>Peso</th>
                                                                <th>Estado</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach from=$vpn_deployments item=deploy}
                                                            <tr>
                                                                <td>
                                                                    <strong>{$deploy.method_name}</strong><br>
                                                                    <span class="vpn-mini">{$deploy.method_key}</span>
                                                                </td>
                                                                <td>
                                                                    <strong>{$deploy.server_name}</strong><br>
                                                                    <span class="vpn-mini">{$deploy.server_key}</span>
                                                                </td>
                                                                <td>
                                                                    {$deploy.endpoint_protocol}://{$deploy.endpoint_host|default:$deploy.server_host}:{$deploy.endpoint_port}{$deploy.deploy_path}
                                                                    {if $deploy.tls_sni != ''}<br><span class="vpn-mini">SNI: {$deploy.tls_sni}</span>{/if}
                                                                </td>
                                                                <td>{$deploy.weight}</td>
                                                                <td>
                                                                    <span class="vpn-badge {if $deploy.is_active == 1}ok{else}off{/if}">
                                                                        {if $deploy.is_active == 1}active{else}disabled{/if}
                                                                    </span>
                                                                    {if $deploy.is_default == 1}<span class="vpn-badge warn ml-1">default</span>{/if}
                                                                </td>
                                                                <td><a href="{$base_url}index.php?p=vpn-control&edit_map={$deploy.id}{$vpn_view_qs}" class="btn btn-sm btn-outline-primary">Editar</a></td>
                                                            </tr>
                                                            {foreachelse}
                                                            <tr>
                                                                <td colspan="6" class="text-center">No hay despliegues registrados.</td>
                                                            </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>

                                <details class="vpn-advanced">
                                    <summary>Sync incremental y catálogo de app</summary>
                                    <div class="vpn-advanced-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="vpn-card">
                                                    <div class="card-body">
                                                        <h4 class="text-white mb-3">Sync incremental</h4>
                                                        <p class="vpn-mini mb-2">El motor genera eventos desde <code>users</code> y los filtra por método asignado a cada VPS.</p>
                                                        <p class="mb-1"><strong>Pull:</strong> <span style="font-family:Consolas,monospace;">{$vpn_sync_pull_url}</span></p>
                                                        <p class="mb-1"><strong>Ack:</strong> <span style="font-family:Consolas,monospace;">{$vpn_sync_ack_url}</span></p>
                                                        <p class="mb-1"><strong>Último resumen:</strong></p>
                                                        <div class="vpn-code">{if isset($vpn_reconcile_last_summary.created)}Nuevos: {$vpn_reconcile_last_summary.created}
Actualizados: {$vpn_reconcile_last_summary.updated}
Eliminados: {$vpn_reconcile_last_summary.deleted}
Ejecución: {$vpn_reconcile_last_summary.ran_at|default:'-'}{else}Sin ejecuciones registradas{/if}</div>
                                                        <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_view_qs}" class="mt-3">
                                                            <input type="hidden" name="run_vpn_reconcile" value="1">
                                                            <button type="submit" class="btn btn-success">Ejecutar reconciliación ahora</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="vpn-card">
                                                    <div class="card-body">
                                                        <h4 class="text-white mb-3">Endpoint JSON para la app</h4>
                                                        <p class="mb-1"><strong>URL pública:</strong></p>
                                                        <div class="vpn-code">{$vpn_public_endpoint_read_url}</div>
                                                        <p class="mb-1 mt-3"><strong>Clave lógica:</strong> <span style="font-family:Consolas,monospace;">{$vpn_public_endpoint_key|default:'-'}</span></p>
                                                        <p class="vpn-mini mb-0">La app solo verá métodos y nodos marcados como públicos y activos.</p>
                                                        <p class="vpn-mini mt-3 mb-1"><strong>Vista previa JSON</strong></p>
                                                        <div class="vpn-code">{$vpn_public_catalog_json|default:'{}'|escape:'html'}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>

                                <details class="vpn-advanced">
                                    <summary>Logs de sincronización</summary>
                                    <div class="vpn-advanced-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered vpn-table">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Servidor</th>
                                                        <th>Acción</th>
                                                        <th>Estado</th>
                                                        <th>Detalle</th>
                                                        <th>Cursor</th>
                                                        <th>Eventos</th>
                                                        <th>IP</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {foreach from=$vpn_sync_logs item=log}
                                                    <tr>
                                                        <td>{$log.created_at}</td>
                                                        <td>{if $log.server_name != ''}{$log.server_name}{else}ID {$log.server_id}{/if}</td>
                                                        <td>{$log.action_name}</td>
                                                        <td><span class="vpn-badge {if $log.status == 'ok'}ok{elseif $log.status == 'warn'}warn{else}off{/if}">{$log.status}</span></td>
                                                        <td>{if isset($log.details.reason) && $log.details.reason != ''}{$log.details.reason}{elseif isset($log.details.message) && $log.details.message != ''}{$log.details.message}{else}-{/if}</td>
                                                        <td>{$log.cursor_from} → {$log.cursor_to}</td>
                                                        <td>{$log.events_count}</td>
                                                        <td>{$log.request_ip|default:'-'}</td>
                                                    </tr>
                                                    {foreachelse}
                                                    <tr>
                                                        <td colspan="8" class="text-center">Sin logs todavía.</td>
                                                    </tr>
                                                    {/foreach}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
        </div>
        {if $vpn_embed_admin != 1}{include file='apps/footer.tpl'}{/if}
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
(function () {
    var createForm = document.querySelector('.js-vpn-create-form');
    var successNotice = document.getElementById('vpn-success-notice');
    if (!createForm) { return; }

    var nameInput = createForm.querySelector('.js-vpn-server-name');
    var addressInput = createForm.querySelector('.js-vpn-server-address');
    var keyInput = createForm.querySelector('.js-vpn-server-key');
    var hostInput = createForm.querySelector('.js-vpn-server-host');
    var ipInput = createForm.querySelector('.js-vpn-server-ip');
    var portInput = createForm.querySelector('.js-vpn-server-port');
    var publicBaseInput = createForm.querySelector('.js-vpn-public-base');
    var groupInput = createForm.querySelector('.js-vpn-legacy-group');
    var previewRoot = createForm.closest('.row') || document;
    var copyButtons = Array.prototype.slice.call(document.querySelectorAll('.js-copy-text'));
    var copyFeedback = document.querySelector('.js-copy-feedback');

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-{2,}/g, '-');
    }

    function isIpv4(value) {
        return /^(25[0-5]|2[0-4]\d|1?\d?\d)(\.(25[0-5]|2[0-4]\d|1?\d?\d)){3}$/.test(value);
    }

    function isHostLike(value) {
        return /^[a-z0-9.-]+$/i.test(value) && value.indexOf('..') === -1 && value !== '';
    }

    function parseAddress(raw) {
        var value = String(raw || '').trim();
        var info = {
            raw: value,
            kind: 'empty',
            host: '',
            ip: '',
            port: '',
            scheme: '',
            publicBase: ''
        };
        if (value === '') { return info; }

        var schemeMatch = value.match(/^([a-z][a-z0-9+.-]*):\/\//i);
        if (schemeMatch) {
            info.scheme = schemeMatch[1].toLowerCase();
            value = value.replace(/^[a-z][a-z0-9+.-]*:\/\//i, '');
        }

        value = value.split('/')[0].trim();
        if (value === '') {
            info.kind = 'unknown';
            return info;
        }

        var hostPart = value;
        var portPart = '';
        var colonCount = (hostPart.match(/:/g) || []).length;
        if (hostPart.charAt(0) !== '[' && colonCount === 1) {
            var hostBits = hostPart.split(':');
            if (hostBits.length === 2 && /^\d+$/.test(hostBits[1])) {
                hostPart = hostBits[0].trim();
                portPart = hostBits[1].trim();
            }
        }

        if (isIpv4(hostPart)) {
            info.kind = 'ip';
            info.ip = hostPart;
        } else if (isHostLike(hostPart)) {
            info.kind = 'host';
            info.host = hostPart.toLowerCase();
        } else {
            info.kind = 'unknown';
        }

        info.port = portPart;
        if ((info.scheme === 'http' || info.scheme === 'https') && (info.host || info.ip)) {
            info.publicBase = info.scheme + '://' + (info.host || info.ip);
            if (info.port !== '' && !(
                (info.scheme === 'http' && info.port === '80')
                || (info.scheme === 'https' && info.port === '443')
            )) {
                info.publicBase += ':' + info.port;
            }
        }

        return info;
    }

    function setPreview(name, value) {
        var node = previewRoot.querySelector('[data-preview="' + name + '"]');
        if (!node) { return; }
        node.textContent = value;
    }

    function markManual(input) {
        if (!input) { return; }
        input.addEventListener('input', function () {
            if (document.activeElement === input) {
                input.setAttribute('data-manual', '1');
            }
            syncCreateForm();
        });
    }

    function syncCreateForm() {
        var addressInfo = parseAddress(addressInput ? addressInput.value : '');
        var aliasValue = nameInput ? String(nameInput.value || '').trim() : '';
        var groupValue = groupInput ? String(groupInput.value || '').trim() : '';
        var keySource = aliasValue || addressInfo.host || addressInfo.ip || groupValue || 'server';
        var keyValue = slugify(keySource);
        if (keyValue === '') {
            keyValue = 'server-auto';
        }

        if (keyInput && keyInput.getAttribute('data-manual') !== '1') {
            keyInput.value = keyValue;
        }
        if (hostInput && hostInput.getAttribute('data-manual') !== '1') {
            hostInput.value = addressInfo.host;
        }
        if (ipInput && ipInput.getAttribute('data-manual') !== '1') {
            ipInput.value = addressInfo.ip;
        }
        if (publicBaseInput && publicBaseInput.getAttribute('data-manual') !== '1' && addressInfo.publicBase !== '') {
            publicBaseInput.value = addressInfo.publicBase;
        }
        if (portInput && portInput.getAttribute('data-manual') !== '1' && addressInfo.port !== '') {
            portInput.value = addressInfo.port;
        }

        var addressKindLabel = 'Esperando datos';
        if (addressInfo.kind === 'ip') {
            addressKindLabel = 'IP directa';
        } else if (addressInfo.kind === 'host') {
            addressKindLabel = 'Hostname';
        } else if (addressInfo.kind === 'unknown') {
            addressKindLabel = 'Formato manual';
        }

        setPreview('server-key', keyInput ? keyInput.value || 'auto' : 'auto');
        setPreview('server-name', aliasValue || '-');
        setPreview('address-kind', addressKindLabel);
        setPreview('address-target', (hostInput && hostInput.value ? hostInput.value : '-') + ' / ' + (ipInput && ipInput.value ? ipInput.value : '-'));
        setPreview('public-base', publicBaseInput && publicBaseInput.value ? publicBaseInput.value : 'Manual o avanzada');
    }

    [nameInput, addressInput, keyInput, hostInput, ipInput, portInput, publicBaseInput].forEach(markManual);

    [keyInput, hostInput, ipInput, publicBaseInput].forEach(function (input) {
        if (input && String(input.value || '').trim() !== '') {
            input.setAttribute('data-manual', '1');
        }
    });

    Array.prototype.slice.call(createForm.querySelectorAll('[data-set-port]')).forEach(function (button) {
        button.addEventListener('click', function () {
            if (!portInput) { return; }
            portInput.value = button.getAttribute('data-set-port') || '';
            portInput.setAttribute('data-manual', '1');
            syncCreateForm();
        });
    });

    syncCreateForm();

    function showCopyFeedback(message, isError) {
        if (!copyFeedback) { return; }
        copyFeedback.textContent = message;
        copyFeedback.style.display = 'block';
        copyFeedback.style.color = isError ? '#ffb7c5' : '#9ff3ca';
        window.clearTimeout(showCopyFeedback._timer);
        showCopyFeedback._timer = window.setTimeout(function () {
            copyFeedback.style.display = 'none';
        }, 2200);
    }

    function fallbackCopyText(text) {
        var area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', 'readonly');
        area.style.position = 'fixed';
        area.style.opacity = '0';
        area.style.pointerEvents = 'none';
        document.body.appendChild(area);
        area.focus();
        area.select();
        var copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (err) {
            copied = false;
        }
        document.body.removeChild(area);
        return copied;
    }

    copyButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-copy-target') || '';
            if (targetId === '') { return; }
            var targetNode = document.getElementById(targetId);
            if (!targetNode) {
                showCopyFeedback('No se encontró el bloque a copiar.', true);
                return;
            }
            var text = String(targetNode.textContent || '').replace(/\n{3,}/g, '\n\n').trim();
            if (text === '') {
                showCopyFeedback('No hay contenido para copiar.', true);
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopyFeedback('Copiado al portapapeles.', false);
                }).catch(function () {
                    var copied = fallbackCopyText(text);
                    showCopyFeedback(copied ? 'Copiado al portapapeles.' : 'No se pudo copiar automáticamente.', !copied);
                });
                return;
            }

            var copied = fallbackCopyText(text);
            showCopyFeedback(copied ? 'Copiado al portapapeles.' : 'No se pudo copiar automáticamente.', !copied);
        });
    });

    if (successNotice) {
        window.addEventListener('load', function () {
            try {
                successNotice.scrollIntoView({ behavior: 'smooth', block: 'start' });
                successNotice.focus({ preventScroll: true });
            } catch (err) {
                successNotice.scrollIntoView(true);
            }
        });
    }
})();
{/literal}
</script>
<script>
{literal}
(function () {
    var tokenInputs = Array.prototype.slice.call(document.querySelectorAll('.js-vpn-sync-token'));
    if (!tokenInputs.length) { return; }

    function randomTokenPart(length) {
        var size = Math.max(8, Number(length || 16));
        var alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        var output = '';

        if (window.crypto && window.crypto.getRandomValues) {
            var bytes = new Uint8Array(size);
            window.crypto.getRandomValues(bytes);
            for (var i = 0; i < bytes.length; i += 1) {
                output += alphabet.charAt(bytes[i] % alphabet.length);
            }
            return output;
        }

        for (var j = 0; j < size; j += 1) {
            output += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
        }
        return output;
    }

    function tokenPrefixForForm(form) {
        var nameInput = form ? form.querySelector('input[name="server_name"]') : null;
        var raw = nameInput ? String(nameInput.value || '').trim().toUpperCase() : '';
        raw = raw.replace(/[^A-Z0-9]+/g, '_').replace(/^_+|_+$/g, '');
        if (raw === '') {
            raw = 'NODE';
        }
        if (raw.length > 14) {
            raw = raw.slice(0, 14);
        }
        return 'PGM_' + raw + '_';
    }

    function updateBadge(input) {
        var group = input ? input.closest('.form-group') : null;
        if (!group) { return; }
        var badge = group.querySelector('.js-token-status-badge');
        if (!badge) { return; }
        var hasValue = String(input.value || '').trim() !== '';
        var hasSavedToken = String(input.getAttribute('data-token-saved') || '0') === '1';
        var form = input.closest('form');
        var isCreateForm = !!(form && form.querySelector('input[name="server_id"][value="0"]'));
        badge.classList.remove('ok', 'warn');
        if (hasValue) {
            badge.classList.add('ok');
            badge.textContent = 'token listo para guardar';
            return;
        }
        if (hasSavedToken) {
            badge.classList.add('ok');
            badge.textContent = 'token actual guardado';
            return;
        }
        badge.classList.add('warn');
        badge.textContent = isCreateForm ? 'se generará al crear' : 'sin token usable';
    }

    tokenInputs.forEach(function (input) {
        var group = input.closest('.form-group');
        if (!group) { return; }

        var generateButton = group.querySelector('.js-generate-token');
        var clearButton = group.querySelector('.js-clear-token');

        if (generateButton) {
            generateButton.addEventListener('click', function () {
                var form = input.closest('form');
                input.value = tokenPrefixForForm(form) + randomTokenPart(24);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.focus();
                input.select();
                updateBadge(input);
            });
        }

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                updateBadge(input);
            });
        }

        input.addEventListener('input', function () {
            updateBadge(input);
        });

        updateBadge(input);
    });
})();
{/literal}
</script>
{if $vpn_embed_admin == 1}
<script>
(function () {
    var lastHeight = 0;

    function sendHeight() {
        var root = document.querySelector('.vpn-embed-shell') || document.querySelector('.page-content') || document.body;
        if (!root) { return; }
        var height = Math.max(
            Math.ceil(root.getBoundingClientRect ? root.getBoundingClientRect().height : 0),
            Math.ceil(root.scrollHeight || 0),
            Math.ceil(document.body ? (document.body.scrollHeight || 0) : 0),
            Math.ceil(document.documentElement ? (document.documentElement.scrollHeight || 0) : 0)
        );
        if (!isFinite(height) || height < 320) { return; }
        if (Math.abs(height - lastHeight) < 6) { return; }
        lastHeight = height;
        window.parent.postMessage({ type: 'vpn_embed_height', height: height }, '*');
    }

    window.addEventListener('load', function () {
        sendHeight();
        setTimeout(sendHeight, 80);
        setTimeout(sendHeight, 260);
        setTimeout(sendHeight, 900);
    });
    window.addEventListener('resize', sendHeight);
    if ('ResizeObserver' in window) {
        var ro = new ResizeObserver(sendHeight);
        var rootNode = document.querySelector('.vpn-embed-shell') || document.querySelector('.page-content') || document.body;
        if (rootNode) { ro.observe(rootNode); }
    }
})();
</script>
{/if}
</body>
</html>
