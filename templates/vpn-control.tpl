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
        .vpn-embed-content .container-fluid { padding: 0 !important; margin: 0 !important; min-height: 0 !important; background: #122543 !important; }
        .vpn-embed-content .page-title-box { display: none !important; }
        body.vpn-embed-admin .vpn-embed-shell { padding: 10px 10px 8px !important; }
        body.vpn-embed-admin .vpn-card,
        body.vpn-embed-admin .card {
            box-shadow: none;
            border-radius: 10px;
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
        .vpn-badge.ok { background: rgba(46, 204, 113, .16); color: #8df0b3; }
        .vpn-badge.warn { background: rgba(241, 196, 15, .16); color: #ffe08a; }
        .vpn-badge.off { background: rgba(231, 76, 60, .16); color: #ff9f98; }
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
            <div class="alert alert-success">{$vpn_success}</div>
            {/if}
            {if $vpn_generated_token != ''}
            <div class="alert alert-warning">
                Token generado o actualizado para el agente:
                <strong style="font-family:Consolas,monospace;">{$vpn_generated_token}</strong>
            </div>
            {/if}

            <div class="row">
                <div class="col-lg-6">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">1. Servidores VPS</h4>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_embed_qs}">
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
                                        <input type="number" name="server_port" class="form-control" value="{$vpn_server_form.server_port|default:443}">
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
                                    <input type="text" name="sync_token" class="form-control" value="" placeholder="Deja vacio para conservar o generar uno nuevo">
                                    <small class="vpn-mini">El valor plano solo se muestra una vez tras guardar.</small>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Payload público JSON</label>
                                        <textarea name="public_payload_json" class="form-control" rows="4">{$vpn_server_form.public_payload_json|default:'{}'}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Meta JSON interno</label>
                                        <textarea name="meta_json" class="form-control" rows="4">{$vpn_server_form.meta_json|default:'{}'}</textarea>
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
                                <button type="submit" class="btn btn-primary">Guardar servidor</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">2. Métodos lógicos</h4>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_embed_qs}">
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
            </div>

            <div class="row mt-2">
                <div class="col-lg-6">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">3. Relación método ↔ VPS</h4>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_embed_qs}">
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
                    </div>
                </div>

                <div class="col-lg-6">
                        <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">4. Sync incremental</h4>
                            <p class="vpn-mini mb-2">El motor genera eventos a partir del estado actual de <code>users</code> y los filtra por método asignado a cada VPS.</p>
                            <p class="mb-1"><strong>Pull:</strong> <span style="font-family:Consolas,monospace;">{$vpn_sync_pull_url}</span></p>
                            <p class="mb-1"><strong>Ack:</strong> <span style="font-family:Consolas,monospace;">{$vpn_sync_ack_url}</span></p>
                            <p class="mb-1"><strong>Último resumen:</strong></p>
                            <div class="vpn-code">{if isset($vpn_reconcile_last_summary.created)}Nuevos: {$vpn_reconcile_last_summary.created}
Actualizados: {$vpn_reconcile_last_summary.updated}
Eliminados: {$vpn_reconcile_last_summary.deleted}
Ejecución: {$vpn_reconcile_last_summary.ran_at|default:'-'}{else}Sin ejecuciones registradas{/if}</div>
                            <form method="post" action="{$base_url}index.php?p=vpn-control{$vpn_embed_qs}" class="mt-3">
                                <input type="hidden" name="run_vpn_reconcile" value="1">
                                <button type="submit" class="btn btn-success">Ejecutar reconciliación ahora</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Servidores registrados</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered vpn-table">
                                    <thead>
                                        <tr>
                                            <th>Servidor</th>
                                            <th>Key</th>
                                            <th>Legacy</th>
                                            <th>Estado</th>
                                            <th>Sync</th>
                                            <th>Cursor</th>
                                            <th>Último visto</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$vpn_servers item=server}
                                        <tr>
                                            <td>
                                                <strong>{$server.server_name}</strong><br>
                                                <span class="vpn-mini">config: {$server.server_host|default:$server.server_ip}{if $server.server_port != ''}:{$server.server_port}{/if}</span>
                                                {if isset($server.identity_state.label)}
                                                <br><span class="vpn-badge {$server.identity_state.class}">{$server.identity_state.label}</span>
                                                {if $server.identity_state.note != ''}<br><span class="vpn-mini">{$server.identity_state.note}</span>{/if}
                                                {/if}
                                                {if $server.runtime_hostname != '' || $server.runtime_fqdn != '' || $server.runtime_request_ip != ''}
                                                <br><span class="vpn-mini">runtime: {if $server.runtime_fqdn != ''}{$server.runtime_fqdn}{elseif $server.runtime_hostname != ''}{$server.runtime_hostname}{else}-{/if}{if $server.runtime_request_ip != ''} · {$server.runtime_request_ip}{/if}</span>
                                                {/if}
                                                {if $server.runtime_ssh_md5_summary != ''}
                                                <br><span class="vpn-mini">ssh: {$server.runtime_ssh_md5_summary}</span>
                                                {/if}
                                            </td>
                                            <td>{$server.server_key}</td>
                                            <td>{$server.legacy_category|default:'-'}</td>
                                            <td><span class="vpn-badge {if $server.status == 'active'}ok{elseif $server.status == 'maintenance'}warn{else}off{/if}">{$server.status}</span></td>
                                            <td>
                                                <span class="vpn-badge {if isset($server.sync_state.class)}{$server.sync_state.class}{else}{if $server.sync_enabled == 1}ok{else}off{/if}{/if}">
                                                    {if isset($server.sync_state.label)}{$server.sync_state.label}{else}{if $server.sync_enabled == 1}on{else}off{/if}{/if}
                                                </span>
                                                <br><span class="vpn-mini">switch: {if $server.sync_enabled == 1}on{else}off{/if}</span>
                                                {if isset($server.sync_state.note) && $server.sync_state.note != ''}<br><span class="vpn-mini">{$server.sync_state.note}</span>{/if}
                                                {if $server.last_ack_at|default:'' != ''}<br><span class="vpn-mini">ack: {$server.last_ack_at}</span>{/if}
                                            </td>
                                            <td>
                                                {$server.last_sync_cursor|default:0}
                                                {if $server.runtime_agent_version != ''}<br><span class="vpn-mini">agent: {$server.runtime_agent_version}</span>{/if}
                                            </td>
                                            <td>
                                                {$server.last_seen_at|default:'-'}
                                                {if $server.last_sync_at|default:'' != ''}<br><span class="vpn-mini">pull: {$server.last_sync_at}</span>{/if}
                                            </td>
                                            <td><a href="{$base_url}index.php?p=vpn-control&edit_server={$server.id}{$vpn_embed_qs}" class="btn btn-sm btn-outline-primary">Editar</a></td>
                                        </tr>
                                        {foreachelse}
                                        <tr>
                                            <td colspan="8" class="text-center">No hay servidores registrados.</td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Métodos lógicos</h4>
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
                                            <td><a href="{$base_url}index.php?p=vpn-control&edit_method={$method.id}{$vpn_embed_qs}" class="btn btn-sm btn-outline-primary">Editar</a></td>
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

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Despliegues método ↔ VPS</h4>
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
                                            <td><a href="{$base_url}index.php?p=vpn-control&edit_map={$deploy.id}{$vpn_embed_qs}" class="btn btn-sm btn-outline-primary">Editar</a></td>
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
            </div>

            <div class="row mt-2">
                <div class="col-lg-6">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">5. Endpoint JSON para la app</h4>
                            <p class="mb-1"><strong>URL pública:</strong></p>
                            <div class="vpn-code">{$vpn_public_endpoint_read_url}</div>
                            <p class="mb-1 mt-3"><strong>Clave lógica:</strong> <span style="font-family:Consolas,monospace;">{$vpn_public_endpoint_key|default:'-'}</span></p>
                            <p class="vpn-mini mb-0">La app solo verá métodos y nodos marcados como públicos y activos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card vpn-card">
                        <div class="card-body">
                            <h4 class="text-white mb-3">Vista previa JSON</h4>
                            <div class="vpn-code">{$vpn_public_catalog_json|default:'{}'|escape:'html'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Logs de sincronización</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered vpn-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Servidor</th>
                                            <th>Acción</th>
                                            <th>Estado</th>
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
                                            <td>{$log.cursor_from} → {$log.cursor_to}</td>
                                            <td>{$log.events_count}</td>
                                            <td>{$log.request_ip|default:'-'}</td>
                                        </tr>
                                        {foreachelse}
                                        <tr>
                                            <td colspan="7" class="text-center">Sin logs todavía.</td>
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
        {if $vpn_embed_admin != 1}{include file='apps/footer.tpl'}{/if}
    </div>
</div>

<script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
<script src="{$base_url}firenet/assets/js/bootstrap.bundle.min.js"></script>
<script src="{$base_url}firenet/assets/js/metisMenu.min.js"></script>
<script src="{$base_url}firenet/assets/js/waves.min.js"></script>
<script src="{$base_url}firenet/assets/js/jquery.slimscroll.min.js"></script>
<script src="{$base_url}firenet/assets/js/app.js"></script>
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
