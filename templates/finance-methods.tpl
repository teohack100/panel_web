<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Metodos de pago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Panel de finanzas" name="description" />
    <meta content="PROGRAMMIT" name="author" />

    <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    <style>
    {literal}
        html, body.finance-embed-admin { min-height: 0 !important; height: auto !important; background: #122543 !important; background-image: none !important; }
        body.finance-embed-admin { margin: 0; overflow-x: hidden; overflow-y: hidden; }
        .finance-embed-shell { min-height: 0 !important; height: auto !important; background: #122543 !important; }
        .finance-embed-content { margin-left: 0 !important; padding: 0 !important; min-height: 0 !important; height: auto !important; background: #122543 !important; }
        .finance-embed-content .container-fluid { padding: 0 !important; margin: 0 !important; min-height: 0 !important; background: #122543 !important; }
        .finance-embed-content .page-title-box { display: none !important; }

        .pay-shell { color: #d9e8ff; margin: 0; background: #122543 !important; }
        .pay-card { border: 1px solid #29456f; border-radius: 12px; overflow: hidden; background: linear-gradient(180deg, #192f50 0%, #152949 100%); box-shadow: 0 12px 30px rgba(7,16,34,.25); height: auto !important; min-height: 0 !important; }
        .pay-tabs { list-style: none; margin: 0; padding: 0 12px; display: flex; border-bottom: 1px solid #2b4972; }
        .pay-tabs .nav-link { display: inline-block; padding: 14px 16px; font-weight: 700; color: #95b3dc; text-decoration: none; border-bottom: 2px solid transparent; }
        .pay-tabs .nav-link.active { color: #54adff; border-bottom-color: #54adff; }
        .pay-body { padding: 14px; height: auto !important; min-height: 0 !important; }
        body.finance-embed-admin .pay-card { border: 0; border-radius: 0; background: linear-gradient(180deg, #1a3356 0%, #132643 100%) !important; box-shadow: none; }
        body.finance-embed-admin .pay-tabs { display: none; }
        body.finance-embed-admin .pay-body { padding: 14px 14px 12px; background: transparent !important; }
        body.finance-embed-admin .pay-shell { padding: 10px 10px 8px !important; height: auto !important; min-height: 0 !important; }
        body.finance-embed-admin .pay-card { border-radius: 10px; }
        body.finance-embed-admin .pay-table-wrap { background: #122340 !important; }
        body.finance-embed-admin .pay-table,
        body.finance-embed-admin .pay-table tbody,
        body.finance-embed-admin .pay-table tr,
        body.finance-embed-admin .pay-table td {
            background: #122340 !important;
        }
        .pay-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
        .pay-title { margin: 0; font-size: 1.05rem; font-weight: 700; color: #eff6ff; }
        .pay-sub { margin: 0; color: #95b3dc; font-size: .9rem; }

        .pay-btn { border: 1px solid #38639d; border-radius: 8px; padding: 9px 13px; color: #e8f2ff; background: #1d3b64; font-weight: 700; text-decoration: none; cursor: pointer; }
        .pay-btn:hover { color: #fff; background: #255089; text-decoration: none; }
        .pay-btn-primary { background: linear-gradient(90deg,#2f7fe2,#4ca0ff); border-color: #4f98ef; }
        .pay-btn-danger { color: #ffc1c1; border-color: #7a4154; background: rgba(120,35,52,.2); }

        .pay-grid { display: grid; grid-template-columns: repeat(12,minmax(0,1fr)); gap: 10px; }
        .c3 { grid-column: span 3; } .c4 { grid-column: span 4; } .c6 { grid-column: span 6; } .c8 { grid-column: span 8; } .c12 { grid-column: span 12; }
        .pay-label { display: block; margin-bottom: 5px; color: #bdd3f2; font-size: .82rem; font-weight: 700; }
        .pay-help { display: block; margin-top: 4px; color: #86a4cc; font-size: .79rem; }
        .pay-note { margin: 0 0 10px; padding: 10px 12px; border: 1px solid #355682; border-radius: 10px; background: rgba(16,34,59,.72); color: #c8dbf8; font-size: .84rem; line-height: 1.45; }
        .pay-note strong { color: #f4f8ff; }
        .pay-note code { color: #8ed0ff; background: rgba(10,24,43,.8); padding: 1px 5px; border-radius: 6px; }
        .pay-input, .pay-select, .pay-textarea { width: 100%; border: 1px solid #2f4d78; border-radius: 8px; background: #10213b; color: #e8f2ff; font-size: .9rem; padding: 9px 10px; }
        .pay-textarea { resize: vertical; min-height: 72px; }

        .pay-provider-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap: 10px; }
        .pay-provider { border: 1px solid #2b466f; border-radius: 10px; background: #122541; padding: 12px; }
        .pay-chip { display: inline-block; border: 1px solid #395d8f; border-radius: 999px; padding: 2px 9px; color: #b5d0f5; font-size: .74rem; }

        .pay-table-wrap { border: 1px solid #2a466f; border-radius: 10px; overflow-x: auto !important; overflow-y: hidden !important; background: #122340; height: auto !important; min-height: 0 !important; max-height: none !important; }
        .pay-table { width: 100%; min-width: 920px; border-collapse: collapse; }
        .pay-table thead th { position: sticky; top: 0; z-index: 1; background: #173055; color: #b7cde9; border-bottom: 1px solid #30517f; padding: 10px; font-size: .74rem; text-transform: uppercase; letter-spacing: .03em; }
        .pay-table tbody td { border-bottom: 1px solid #244268; padding: 10px; color: #e1efff; font-size: .89rem; white-space: nowrap; }
        .pay-table .meta { color: #8fb1dc; font-size: .79rem; }
        .pay-method-cell { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .pay-method-thumb { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 12px; border: 1px solid #2f4d78; background: linear-gradient(180deg, #163154 0%, #10223d 100%); overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 1px 0 rgba(255,255,255,.06); }
        .pay-method-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pay-method-thumb-icon { color: #6fb3ff; font-size: 1.15rem; line-height: 1; }
        .pay-badge { display: inline-block; padding: 2px 9px; border-radius: 999px; border: 1px solid; font-size: .72rem; font-weight: 700; }
        .pay-ok { color: #7ff2ba; border-color: #1f6a4a; background: rgba(30,109,74,.24); }
        .pay-warn { color: #ffd773; border-color: #7a5f1c; background: rgba(127,96,20,.24); }
        .pay-off { color: #abc6e8; border-color: #466182; background: rgba(65,93,132,.22); }

        .pay-modal { position: fixed; inset: 0; background: rgba(5,12,24,.78); z-index: 3000; display: none; align-items: flex-start; justify-content: center; overflow-y: auto; padding: 24px 10px; }
        .pay-modal.open { display: flex; }
        .pay-modal-card { width: min(920px,100%); border: 1px solid #2e4c76; border-radius: 12px; background: linear-gradient(180deg,#172d4e,#132440); overflow: hidden; max-height: none; }
        .pay-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-bottom: 1px solid #2a466f; background: #163055; }
        .pay-modal-title { margin: 0; color: #f2f8ff; font-size: 1rem; font-weight: 700; }
        .pay-modal-close { border: 0; background: transparent; color: #a6c2e6; font-size: 1.45rem; cursor: pointer; }
        .pay-modal-body { padding: 14px; max-height: none; overflow: visible; }
        .pay-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 14px; border-top: 1px solid #28466f; background: rgba(11,25,44,.45); }
        .pay-box { border: 1px solid #2d496f; border-radius: 10px; background: rgba(14,33,59,.65); padding: 10px; margin-top: 10px; }
        .pay-box-title { margin: 0 0 8px; color: #d5e5ff; font-size: .86rem; font-weight: 700; }
        .pay-icon-preview { width: 84px; height: 84px; border-radius: 16px; border: 1px solid #2f4d78; background: linear-gradient(180deg, #163154 0%, #10223d 100%); overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 1px 0 rgba(255,255,255,.06); }
        .pay-icon-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pay-icon-preview-empty { color: #6fb3ff; font-size: 1.65rem; line-height: 1; }
        .pay-preview { margin-top: 12px; border: 1px solid #2b466f; border-radius: 10px; padding: 12px 14px; background: rgba(12,28,49,.76); color: #d9ebff; }
        .pay-preview strong { color: #ffffff; }
        .pay-preview-ok { border-color: #1f7a57; background: rgba(14,64,43,.28); }
        .pay-preview-error { border-color: #7d4253; background: rgba(84,25,40,.26); }
        .pay-preview-title { font-weight: 700; margin-bottom: 6px; color: #eff7ff; }
        .pay-preview-meta { color: #aecdF1; font-size: .83rem; line-height: 1.5; }
        .pay-preview-qr-wrap { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(142, 181, 233, .18); }
        .pay-preview-qr-title { color: #eff7ff; font-weight: 700; margin-bottom: 8px; }
        .pay-preview-qr-img { display: inline-block; max-width: 260px; width: 100%; background: #fff; border-radius: 12px; border: 1px solid #33547f; padding: 10px; }
        .pay-preview-qr-img img { display: block; width: 100%; height: auto; }
        .pay-preview-qr-text { width: 100%; min-height: 92px; border: 1px solid #34547d; border-radius: 10px; background: #0c1c31; color: #d9ebff; font-size: .82rem; padding: 10px; resize: vertical; }
        .hide { display: none !important; }

        @media (max-width: 980px) { .c3,.c4,.c6,.c8 { grid-column: span 12; } }
    {/literal}
    </style>
</head>
<body{if $finance_embed_admin == 1} class="finance-embed-admin"{/if}>
{if $finance_embed_admin != 1}{include file='apps/topnav.tpl'}{/if}
<div class="{if $finance_embed_admin == 1}finance-embed-shell{else}page-wrapper{/if}">
{if $finance_embed_admin != 1}{include file='apps/sidenavi.tpl'}{/if}
    <div class="{if $finance_embed_admin == 1}page-content finance-embed-content{else}page-content{/if}">
        <div class="container-fluid pay-shell">
            {if $finance_embed_admin != 1}
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="float-right">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Finanzas</a></li>
                                <li class="breadcrumb-item active">Metodos de pago</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Configuracion de metodos de pago</h4>
                    </div>
                </div>
            </div>
            {/if}

            {if $finance_method_error != ''}<div class="alert alert-danger">{$finance_method_error}</div>{/if}
            {if $finance_method_success != ''}<div class="alert alert-success">{$finance_method_success}</div>{/if}
            {if $finance_methods_locked == 1}
            <div class="alert alert-warning">
                Edicion bloqueada en este subdominio (<strong>{$finance_current_host}</strong>).<br>
                Admin central: <a href="https://{$finance_master_host}/index.php?p=finance-methods" target="_blank">https://{$finance_master_host}/index.php?p=finance-methods</a>
            </div>
            {/if}

            <section class="pay-card">
                {if $finance_embed_admin != 1}
                <ul class="pay-tabs">
                    <li><a class="nav-link {if $finance_active_tab=='general'}active{/if}" href="{$base_url}index.php?p=finance-methods&tab=general{$finance_embed_qs}">General</a></li>
                    <li><a class="nav-link {if $finance_active_tab=='providers'}active{/if}" href="{$base_url}index.php?p=finance-methods&tab=providers{$finance_embed_qs}">Proveedores API</a></li>
                    <li><a class="nav-link {if $finance_active_tab=='methods'}active{/if}" href="{$base_url}index.php?p=finance-methods&tab=methods{$finance_embed_qs}">Metodos de pago</a></li>
                </ul>
                {/if}
                <div class="pay-body">
                    {if $finance_active_tab=='general'}
                    <div class="pay-toolbar">
                        <div>
                            <h2 class="pay-title">Configuracion General</h2>
                            <p class="pay-sub">Tipo de cambio global, precio por credito y host principal.</p>
                        </div>
                    </div>
                    <form method="post" action="{$base_url}index.php?p=finance-methods&tab=general{$finance_embed_qs}">
                        <input type="hidden" name="save_general" value="1">
                        <fieldset {if $finance_methods_locked == 1}disabled{/if}>
                            <div class="pay-grid">
                                <div class="c4">
                                    <label class="pay-label">Tipo de cambio global (1 USD = ? Bs)</label>
                                    <input class="pay-input" type="number" step="0.0001" min="0.0001" name="default_rate_bob" value="{$finance_default_rate_bob}">
                                </div>
                                <div class="c4">
                                    <label class="pay-label">Precio por credito (USD)</label>
                                    <input class="pay-input" type="number" step="0.0001" min="0.0001" name="credit_price_usd" value="{$finance_credit_price_usd}">
                                    <small class="pay-help">Ej: 1.5000 = 1 credito cuesta $1.5</small>
                                </div>
                                <div class="c4" style="align-self:end;">
                                    <label><input type="checkbox" name="apply_rate_all" value="1"> Aplicar tipo de cambio a todos los metodos</label>
                                </div>
                                <div class="c8">
                                    <label class="pay-label">Dominio admin principal</label>
                                    <input class="pay-input" type="text" name="finance_master_host" value="{$finance_master_host}" placeholder="panel.programmit.com">
                                </div>
                            </div>
                            <div style="margin-top:12px;"><button type="submit" class="pay-btn pay-btn-primary">Guardar configuracion</button></div>
                        </fieldset>
                    </form>
                    {/if}

                    {if $finance_active_tab=='providers'}
                    <div class="pay-toolbar">
                        <div>
                            <h2 class="pay-title">Proveedores API</h2>
                            <p class="pay-sub">Catalogo de proveedores disponibles y metodos asociados.</p>
                        </div>
                    </div>
                    <div class="pay-provider-grid">
                        {foreach from=$finance_provider_rows item=pr}
                        <article class="pay-provider">
                            <h3 style="margin:0 0 8px; color:#eff6ff; font-size:.95rem;">{$pr.name}</h3>
                            <span class="pay-chip">key: {$pr.key}</span>
                            <p style="margin:8px 0 0; color:#95b3dc;">Metodos configurados: <strong>{$pr.total_methods}</strong></p>
                        </article>
                        {/foreach}
                    </div>
                    {/if}

                    {if $finance_active_tab=='methods'}
                    <div class="pay-toolbar">
                        <div>
                            <h2 class="pay-title">Metodos de pago</h2>
                            <p class="pay-sub">Flujo estilo tienda: tabla + modal para crear/editar.</p>
                        </div>
                        <div>
                            <button type="button" class="pay-btn pay-btn-primary" id="openMethodModalBtn" {if $finance_methods_locked == 1}disabled{/if}>+ Agregar metodo</button>
                        </div>
                    </div>

                    <div class="pay-table-wrap">
                        <table class="pay-table">
                            <thead>
                                <tr>
                                    <th>Metodo</th>
                                    <th>Nombre</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Rate</th>
                                    <th>Nuevos usuarios</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if $finance_method_rows|@count > 0}
                                {foreach from=$finance_method_rows item=r}
                                <tr>
                                    <td>
                                        <div class="pay-method-cell">
                                            <div class="pay-method-thumb">
                                                {if isset($r.settings.icon_url) && $r.settings.icon_url|trim != ''}
                                                <img src="{$r.settings.icon_url|escape:'html'}" alt="{$r.method_name|escape:'html'}">
                                                {else}
                                                <span class="pay-method-thumb-icon"><i class="fa fa-image"></i></span>
                                                {/if}
                                            </div>
                                            <div>
                                                <div>{$r.method_key}</div>
                                                <div class="meta">{$r.provider_key}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><div>{$r.method_name}</div><div class="meta">ID #{$r.id}</div></td>
                                    <td>${$r.min_amount|string_format:"%.2f"}</td>
                                    <td>${$r.max_amount|string_format:"%.2f"}</td>
                                    <td>{$r.rate_bob|string_format:"%.4f"}</td>
                                    <td>{if $r.allow_new_users == 1}<span class="pay-badge pay-ok">Permitido</span>{else}<span class="pay-badge pay-warn">Restringido</span>{/if}</td>
                                    <td>{if $r.is_active == 1}<span class="pay-badge pay-ok">Activo</span>{else}<span class="pay-badge pay-off">Inactivo</span>{/if}</td>
                                    <td>
                                        <a class="pay-btn" href="{$base_url}index.php?p=finance-methods&tab=methods&edit={$r.id}{$finance_embed_qs}">Editar</a>
                                        <form method="post" action="{$base_url}index.php?p=finance-methods&tab=methods{$finance_embed_qs}" style="display:inline-block; margin:0 0 0 6px;">
                                            <input type="hidden" name="delete_method" value="1">
                                            <input type="hidden" name="delete_id" value="{$r.id}">
                                            <button type="submit" class="pay-btn pay-btn-danger" onclick="return confirm('Eliminar metodo?')" {if $finance_methods_locked == 1}disabled{/if}>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                {/foreach}
                                {else}
                                <tr><td colspan="8" style="text-align:center; padding:36px; color:#8aa7cf;">No hay metodos de pago configurados.</td></tr>
                                {/if}
                            </tbody>
                        </table>
                    </div>
                    {/if}
                </div>
            </section>
        </div>
        {if $finance_embed_admin != 1}{include file='apps/footer.tpl'}{/if}
    </div>
</div>
{if $finance_active_tab=='methods'}
<div id="methodModal" class="pay-modal" aria-hidden="true">
    <div class="pay-modal-card">
        <div class="pay-modal-head">
            <h3 class="pay-modal-title" id="methodModalTitle">Agregar metodo de pago</h3>
            <button type="button" class="pay-modal-close" id="closeMethodModalBtn">&times;</button>
        </div>
        <form id="paymentMethodForm" method="post" enctype="multipart/form-data" action="{$base_url}index.php?p=finance-methods&tab=methods{$finance_embed_qs}">
            <div class="pay-modal-body">
                <input type="hidden" name="save_method" value="1">
                <input type="hidden" name="method_id" id="method_id" value="0">
                <input type="hidden" name="preview_method" id="preview_method" value="0">

                    <div class="pay-grid">
                        <div class="c6">
                        <label class="pay-label">Plantilla de metodo (recomendada)</label>
                        <select class="pay-select" id="method_preset" name="method_preset" {if $finance_methods_locked == 1}disabled{/if}>
                            <option value="">-- Selecciona un preset --</option>
                            {foreach from=$finance_method_presets item=ps}
                            <option value="{$ps.method_key}">{$ps.method_name}</option>
                            {/foreach}
                        </select>
                        <small class="pay-help">Usa una plantilla para cargar la estructura base y luego ajusta solo lo necesario.</small>
                    </div>
                    <div class="c6">
                        <label class="pay-label">Proveedor base</label>
                        <select class="pay-select" id="provider_key" name="provider_key" required {if $finance_methods_locked == 1}disabled{/if}>
                            {foreach from=$finance_provider_options item=opt}
                            <option value="{$opt.key}">{$opt.name}</option>
                            {/foreach}
                        </select>
                        <small class="pay-help">Para <strong>QR Bolivia Auto</strong>, este valor se sincroniza automaticamente con el proveedor QR seleccionado abajo.</small>
                    </div>

                    <div class="c6">
                        <label class="pay-label">Clave del metodo</label>
                        <input class="pay-input" type="text" id="method_key" name="method_key" required placeholder="qr_bolivia_auto" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>
                    <div class="c6">
                        <label class="pay-label">Nombre del metodo</label>
                        <input class="pay-input" type="text" id="method_name" name="method_name" required placeholder="Nombre visible" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>

                    <div class="c12">
                        <label class="pay-label">Descripcion</label>
                        <textarea class="pay-textarea" id="description" name="description" rows="2" {if $finance_methods_locked == 1}disabled{/if}></textarea>
                    </div>

                    <div class="c8">
                        <label class="pay-label">URL del icono / imagen</label>
                        <input class="pay-input" type="text" id="icon_url" name="icon_url" placeholder="https://... o logo/metodos/bnb.png" {if $finance_methods_locked == 1}disabled{/if}>
                        <small class="pay-help">Se muestra en <strong>Agregar saldo</strong> y en la lista de metodos. Puedes usar una URL publica o una ruta local publicada.</small>
                    </div>
                    <div class="c4">
                        <label class="pay-label">Vista previa del icono</label>
                        <div class="pay-icon-preview" id="icon_preview_box">
                            <img id="icon_preview_img" src="" alt="Vista previa del icono" class="hide">
                            <div id="icon_preview_empty" class="pay-icon-preview-empty"><i class="fa fa-image"></i></div>
                        </div>
                        <label style="display:block; margin-top:10px;"><input type="checkbox" id="remove_icon" name="remove_icon" value="1" {if $finance_methods_locked == 1}disabled{/if}> Quitar icono actual</label>
                    </div>
                    <div class="c12">
                        <label class="pay-label">Subir imagen del icono</label>
                        <input class="pay-input" type="file" id="icon_file" name="icon_file" accept=".png,.jpg,.jpeg,.webp,.gif" {if $finance_methods_locked == 1}disabled{/if}>
                        <small class="pay-help">La forma rapida: elige la imagen aqui y el panel la guardara automaticamente en <code>logo/metodos/</code>.</small>
                    </div>

                    <div class="c4">
                        <label class="pay-label">Monto minimo</label>
                        <input class="pay-input" type="number" id="min_amount" name="min_amount" step="0.01" min="0" value="1" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>
                    <div class="c4">
                        <label class="pay-label">Monto maximo</label>
                        <input class="pay-input" type="number" id="max_amount" name="max_amount" step="0.01" min="0" value="1000" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>
                    <div class="c4">
                        <label class="pay-label">Tipo de cambio USD->BOB</label>
                        <input class="pay-input" type="number" id="rate_bob" name="rate_bob" step="0.0001" min="0.0001" value="{$finance_default_rate_bob}" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>

                    <div class="c6">
                        <label class="pay-label">Precio por credito (USD)</label>
                        <input class="pay-input" type="number" id="credit_price_usd" name="credit_price_usd" step="0.0001" min="0.0001" placeholder="Vacio = usar global" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>
                    <div class="c6">
                        <label class="pay-label">Orden de visualizacion</label>
                        <input class="pay-input" type="number" id="display_order" name="display_order" value="100" {if $finance_methods_locked == 1}disabled{/if}>
                    </div>

                    <div class="c12">
                        <label><input type="checkbox" id="allow_new_users" name="allow_new_users" value="1" checked {if $finance_methods_locked == 1}disabled{/if}> Permitir nuevos usuarios</label>
                        <label style="margin-left:12px;"><input type="checkbox" id="is_active" name="is_active" value="1" checked {if $finance_methods_locked == 1}disabled{/if}> Activo</label>
                        <label style="margin-left:12px;"><input type="checkbox" id="processing_fee" name="processing_fee" value="1" {if $finance_methods_locked == 1}disabled{/if}> Habilitar comision de procesamiento</label>
                    </div>

                    <div class="c12" id="processing_fee_fields" style="display:none;">
                        <div class="pay-grid">
                            <div class="c6">
                                <label class="pay-label">Monto fijo</label>
                                <input class="pay-input" type="number" id="fee_fixed" name="fee_fixed" step="0.01" min="0" value="0" {if $finance_methods_locked == 1}disabled{/if}>
                            </div>
                            <div class="c6">
                                <label class="pay-label">Porcentaje (%)</label>
                                <input class="pay-input" type="number" id="fee_percent" name="fee_percent" step="0.01" min="0" max="100" value="0" {if $finance_methods_locked == 1}disabled{/if}>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="qr_box" class="pay-box">
                    <h4 class="pay-box-title">Patron QR Bolivia Automatico</h4>
                    <p class="pay-note">
                        <strong>Uso recomendado:</strong> si eliges <code>BNB</code> o <code>VeriPagos</code>, llena solo el bloque especifico del proveedor.
                        El bloque generico de API se ocultara porque no interviene en ese flujo.
                    </p>
                    <div class="pay-grid">
                        <div class="c6">
                            <label class="pay-label">Proveedor QR Bolivia</label>
                            <select class="pay-select" id="qr_provider" name="qr_provider" {if $finance_methods_locked == 1}disabled{/if}>
                                <option value="veripagos">VeriPagos</option>
                                <option value="bnb">Banco Nacional de Bolivia (BNB)</option>
                            </select>
                        </div>
                        <div class="c3">
                            <label class="pay-label">Modo</label>
                            <select class="pay-select" id="qb_mode" name="qb_mode" {if $finance_methods_locked == 1}disabled{/if}>
                                <option value="test">Prueba (TEST)</option>
                                <option value="prod">Produccion</option>
                            </select>
                            <small class="pay-help">En BNB, el ambiente TEST usa normalmente <code>test.bnb.com.bo</code>.</small>
                        </div>
                        <div class="c3">
                            <label class="pay-label">Vigencia QR (min)</label>
                            <input class="pay-input" type="number" id="qb_expiry_minutes" name="qb_expiry_minutes" min="1" max="1440" value="15" {if $finance_methods_locked == 1}disabled{/if}>
                            <small class="pay-help">Para BNB el cobro simple suele vencer al cierre del dia en hora Bolivia.</small>
                        </div>
                    </div>

                    <div id="qr_veripagos_box" class="pay-box">
                        <h5 class="pay-box-title">Config VeriPagos</h5>
                        <div class="pay-grid">
                            <div class="c6"><label class="pay-label">Usuario VeriPagos</label><input class="pay-input" type="text" id="vp_username" name="vp_username" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c6"><label class="pay-label">Contrasena VeriPagos</label><input class="pay-input" type="password" id="vp_password" name="vp_password" placeholder="Dejar vacio para conservar" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c6"><label class="pay-label">Secret Key</label><input class="pay-input" type="text" id="vp_secret_key" name="vp_secret_key" placeholder="Dejar vacio para conservar" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c6"><label class="pay-label">URL Base VeriPagos</label><input class="pay-input" type="text" id="vp_base_url" name="vp_base_url" placeholder="https://..." {if $finance_methods_locked == 1}disabled{/if}></div>
                        </div>
                    </div>

                    <div id="qr_bnb_box" class="pay-box hide">
                        <h5 class="pay-box-title">Config BNB</h5>
                        <p class="pay-note">
                            <strong>BNB QR Simple:</strong> usa las credenciales del comercio entregadas por el banco.
                            <code>BNB Account ID</code> y <code>BNB Authorization ID</code> son los campos clave.
                            Si editas un metodo existente, puedes dejar vacio el <code>Authorization ID</code> para conservar el actual.
                        </p>
                        <div class="pay-grid">
                            <div class="c6">
                                <label class="pay-label">BNB Account ID</label>
                                <input class="pay-input" type="text" id="bnb_account_id" name="bnb_account_id" {if $finance_methods_locked == 1}disabled{/if}>
                                <small class="pay-help">Identificador del comercio entregado por el banco.</small>
                            </div>
                            <div class="c6">
                                <label class="pay-label">BNB Authorization ID</label>
                                <input class="pay-input" type="password" id="bnb_authorization_id" name="bnb_authorization_id" placeholder="Dejar vacio para conservar" {if $finance_methods_locked == 1}disabled{/if}>
                                <small class="pay-help">Autorizacion del comercio. El banco puede exigir actualizarla en la primera integracion.</small>
                            </div>
                            <div class="c4">
                                <label class="pay-label">Cuenta destino BNB</label>
                                <select class="pay-select" id="bnb_destination_account_id" name="bnb_destination_account_id" {if $finance_methods_locked == 1}disabled{/if}>
                                    <option value="1">1 - Cuenta nacional (BOB)</option>
                                    <option value="2">2 - Cuenta extranjera</option>
                                </select>
                                <small class="pay-help">La documentacion BNB define <strong>1</strong> para cuenta nacional y <strong>2</strong> para moneda extranjera.</small>
                            </div>
                            <div class="c4">
                                <label class="pay-label">Moneda</label>
                                <select class="pay-select" id="bnb_currency" name="bnb_currency" {if $finance_methods_locked == 1}disabled{/if}>
                                    <option value="BOB">BOB</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                            <div class="c12"><label class="pay-label">URL Token BNB</label><input class="pay-input" type="text" id="bnb_token_url" name="bnb_token_url" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c12"><label class="pay-label">URL Generacion QR BNB</label><input class="pay-input" type="text" id="bnb_qr_url" name="bnb_qr_url" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c12"><label class="pay-label">URL Estado QR BNB</label><input class="pay-input" type="text" id="bnb_status_url" name="bnb_status_url" {if $finance_methods_locked == 1}disabled{/if}></div>
                            <div class="c12"><label class="pay-label">URL Cancelacion QR BNB</label><input class="pay-input" type="text" id="bnb_cancel_url" name="bnb_cancel_url" {if $finance_methods_locked == 1}disabled{/if}></div>
                        </div>
                    </div>
                </div>

                <div id="api_box" class="pay-box">
                    <h4 class="pay-box-title">Credenciales y mapeo de API</h4>
                    <p class="pay-note">
                        Este bloque se usa solo para proveedores genericos o integraciones personalizadas.
                        Si el metodo trabaja con <strong>BNB</strong> o <strong>VeriPagos</strong>, normalmente no necesitas llenarlo.
                    </p>
                    <div class="pay-grid">
                        <div class="c12"><label class="pay-label">URL de creacion</label><input class="pay-input" type="text" id="create_url" name="create_url" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c4"><label class="pay-label">Tipo de autenticacion</label><select class="pay-select" id="auth_type" name="auth_type" {if $finance_methods_locked == 1}disabled{/if}><option value="none">none</option><option value="bearer">bearer (token)</option><option value="basic">basic (user/pass)</option><option value="apikey">apikey (X-API-KEY)</option></select></div>
                        <div class="c4"><label class="pay-label">API Key</label><input class="pay-input" type="text" id="api_key" name="api_key" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c4"><label class="pay-label">Secret / Firma</label><input class="pay-input" type="text" id="secret" name="secret" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c6"><label class="pay-label">Usuario API</label><input class="pay-input" type="text" id="api_user" name="api_user" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c6"><label class="pay-label">Password API</label><input class="pay-input" type="password" id="api_password" name="api_password" placeholder="Dejar vacio para conservar" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c12"><label class="pay-label">Encabezados extra</label><textarea class="pay-textarea" id="extra_headers" name="extra_headers" rows="2" {if $finance_methods_locked == 1}disabled{/if}></textarea></div>
                        <div class="c6"><label class="pay-label">Ruta TXN ID</label><input class="pay-input" type="text" id="txn_path" name="txn_path" placeholder="data.id" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c6"><label class="pay-label">Ruta QR imagen</label><input class="pay-input" type="text" id="qr_image_path" name="qr_image_path" placeholder="data.qr_image" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c6"><label class="pay-label">Ruta QR payload</label><input class="pay-input" type="text" id="qr_payload_path" name="qr_payload_path" placeholder="data.qr_text" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c6"><label class="pay-label">Ruta expiracion</label><input class="pay-input" type="text" id="expires_path" name="expires_path" placeholder="data.expires_at" {if $finance_methods_locked == 1}disabled{/if}></div>
                        <div class="c12"><label class="pay-label">Instrucciones</label><textarea class="pay-textarea" id="instructions" name="instructions" rows="3" placeholder="Instrucciones para el usuario" {if $finance_methods_locked == 1}disabled{/if}></textarea></div>
                    </div>
                </div>

                <div id="methodPreviewResult" class="pay-preview hide" aria-live="polite"></div>
            </div>
            <div class="pay-modal-foot">
                <button type="button" class="pay-btn" id="previewMethodBtn" {if $finance_methods_locked == 1}disabled{/if}>Probar credenciales</button>
                <button type="button" class="pay-btn" id="cancelMethodModalBtn">Cancelar</button>
                <button type="submit" class="pay-btn pay-btn-primary" {if $finance_methods_locked == 1}disabled{/if}>Guardar</button>
            </div>
        </form>
    </div>
</div>
{/if}

<script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
<script src="{$base_url}firenet/assets/js/bootstrap.bundle.min.js"></script>
<script src="{$base_url}firenet/assets/js/metisMenu.min.js"></script>
<script src="{$base_url}firenet/assets/js/waves.min.js"></script>
<script src="{$base_url}firenet/assets/js/jquery.slimscroll.min.js"></script>
<script src="{$base_url}firenet/assets/js/app.js"></script>
{if $finance_embed_admin == 1}
<script>
(function () {
    if (window.parent === window) { return; }
    var lastHeight = 0;
    function calcMethodsHeight() {
        var body = document.querySelector('.pay-body');
        var toolbar = document.querySelector('.pay-toolbar');
        var tableWrap = document.querySelector('.pay-table-wrap');
        if (!body || !tableWrap) { return 0; }

        var bodyStyle = window.getComputedStyle ? window.getComputedStyle(body) : null;
        var padTop = bodyStyle ? (parseFloat(bodyStyle.paddingTop) || 0) : 0;
        var padBottom = bodyStyle ? (parseFloat(bodyStyle.paddingBottom) || 0) : 0;
        var toolbarH = toolbar && toolbar.getBoundingClientRect ? toolbar.getBoundingClientRect().height : 0;

        var thead = tableWrap.querySelector('thead');
        var headH = thead && thead.getBoundingClientRect ? thead.getBoundingClientRect().height : 0;
        var rows = tableWrap.querySelectorAll('tbody tr');
        var rowsH = 0;
        for (var i = 0; i < rows.length; i++) {
            rowsH += rows[i].getBoundingClientRect ? rows[i].getBoundingClientRect().height : 0;
        }
        if (rows.length === 0) { rowsH = 56; }

        var scrollbarH = Math.max(0, (tableWrap.offsetHeight || 0) - (tableWrap.clientHeight || 0));
        var cardExtra = 30; // border + breathing space

        return Math.ceil(padTop + padBottom + toolbarH + headH + rowsH + scrollbarH + cardExtra);
    }

    function sendHeight() {
        var card = document.querySelector('.pay-card');
        var shell = document.querySelector('.pay-shell');
        var root = card || shell;
        if (!root) { return; }
        var methodsHeight = calcMethodsHeight();
        var rootRectHeight = Math.ceil(root.getBoundingClientRect ? root.getBoundingClientRect().height : 0);
        var rootScrollHeight = Math.ceil(root.scrollHeight || 0);
        var bodyScrollHeight = Math.ceil(document.body ? (document.body.scrollHeight || 0) : 0);
        var docScrollHeight = Math.ceil(document.documentElement ? (document.documentElement.scrollHeight || 0) : 0);
        var height = Math.max(methodsHeight, rootRectHeight, rootScrollHeight, bodyScrollHeight, docScrollHeight);
        if (!isFinite(height) || height < 320) { return; }
        if (Math.abs(height - lastHeight) < 6) { return; }
        lastHeight = height;
        window.parent.postMessage({ type: 'finance_embed_height', height: height }, '*');
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
        var rootNode = document.querySelector('.pay-card') || document.querySelector('.pay-shell') || document.body;
        if (rootNode) { ro.observe(rootNode); }
    }
})();
</script>
{/if}
{if $finance_active_tab=='methods'}
<script>
(function () {
    var modal = document.getElementById('methodModal');
    var openBtn = document.getElementById('openMethodModalBtn');
    var closeBtn = document.getElementById('closeMethodModalBtn');
    var cancelBtn = document.getElementById('cancelMethodModalBtn');
    var form = document.getElementById('paymentMethodForm');
    if (!modal || !form) return;

    var formBaseAction = '{$base_url}index.php?p=finance-methods&tab=methods{$finance_embed_qs}';
    var modalTitle = document.getElementById('methodModalTitle');
    var presetSelect = document.getElementById('method_preset');
    var providerSelect = document.getElementById('provider_key');
    var methodKeyInput = document.getElementById('method_key');
    var methodNameInput = document.getElementById('method_name');
    var qrProvider = document.getElementById('qr_provider');
    var processingFeeInput = document.getElementById('processing_fee');
    var iconUrlInput = document.getElementById('icon_url');
    var removeIconInput = document.getElementById('remove_icon');
    var iconFileInput = document.getElementById('icon_file');
    var iconPreviewImg = document.getElementById('icon_preview_img');
    var iconPreviewEmpty = document.getElementById('icon_preview_empty');
    var previewInput = document.getElementById('preview_method');
    var previewBtn = document.getElementById('previewMethodBtn');
    var previewResultBox = document.getElementById('methodPreviewResult');
    var iconPreviewObjectUrl = null;

    var PRESETS = {};
    {foreach from=$finance_method_presets item=ps}
    PRESETS['{$ps.method_key|escape:'javascript'}'] = {
        method_key: '{$ps.method_key|escape:'javascript'}',
        provider_key: '{$ps.provider_key|escape:'javascript'}',
        method_name: '{$ps.method_name|escape:'javascript'}'
    };
    {/foreach}

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function hidePreview() {
        if (!previewResultBox) return;
        previewResultBox.classList.add('hide');
        previewResultBox.classList.remove('pay-preview-ok');
        previewResultBox.classList.remove('pay-preview-error');
        previewResultBox.innerHTML = '';
    }

    function showPreview(data) {
        if (!previewResultBox) return;
        var ok = !!(data && data.ok);
        var message = data && data.message ? String(data.message) : (ok ? 'Prueba completada.' : 'No se pudo probar el metodo.');
        var html = '';

        previewResultBox.classList.remove('hide');
        previewResultBox.classList.toggle('pay-preview-ok', ok);
        previewResultBox.classList.toggle('pay-preview-error', !ok);

        html += '<div class="pay-preview-title">' + (ok ? 'Prueba correcta' : 'Prueba fallida') + '</div>';
        html += '<div>' + escapeHtml(message) + '</div>';

        if (data && (data.txn_id || data.expires_at)) {
            html += '<div class="pay-preview-meta">';
            if (data.txn_id) {
                html += '<div><strong>TXN:</strong> ' + escapeHtml(data.txn_id) + '</div>';
            }
            if (data.expires_at) {
                html += '<div><strong>Expira:</strong> ' + escapeHtml(data.expires_at) + '</div>';
            }
            html += '</div>';
        }

        if (data && Number(data.has_qr || 0) === 1) {
            html += '<div class="pay-preview-qr-wrap">';
            html += '<div class="pay-preview-qr-title">Vista previa del QR devuelto</div>';
            if (data.qr_image_url) {
                html += '<div class="pay-preview-qr-img"><img src="' + escapeHtml(data.qr_image_url) + '" alt="QR de prueba"></div>';
            } else if (data.qr_payload) {
                html += '<textarea class="pay-preview-qr-text" readonly>' + escapeHtml(data.qr_payload) + '</textarea>';
            }
            html += '</div>';
        }

        previewResultBox.innerHTML = html;
    }

    function setVal(id, value) {
        var el = document.getElementById(id);
        if (el) el.value = value == null ? '' : String(value);
    }
    function setCheck(id, value) {
        var el = document.getElementById(id);
        if (el) el.checked = !!value;
    }

    function toggleFee() {
        var box = document.getElementById('processing_fee_fields');
        if (!box || !processingFeeInput) return;
        box.style.display = processingFeeInput.checked ? 'block' : 'none';
    }

    function toggleQrProvider() {
        var vBox = document.getElementById('qr_veripagos_box');
        var bBox = document.getElementById('qr_bnb_box');
        if (!vBox || !bBox || !qrProvider) return;
        if ((qrProvider.value || 'veripagos').toLowerCase() === 'bnb') {
            vBox.classList.add('hide');
            bBox.classList.remove('hide');
        } else {
            vBox.classList.remove('hide');
            bBox.classList.add('hide');
        }
    }

    function updateIconPreview() {
        if (!iconPreviewImg || !iconPreviewEmpty) return;
        if (iconPreviewObjectUrl) {
            URL.revokeObjectURL(iconPreviewObjectUrl);
            iconPreviewObjectUrl = null;
        }
        if (iconFileInput && iconFileInput.files && iconFileInput.files[0] && (!removeIconInput || !removeIconInput.checked)) {
            iconPreviewObjectUrl = URL.createObjectURL(iconFileInput.files[0]);
            iconPreviewImg.src = iconPreviewObjectUrl;
            iconPreviewImg.classList.remove('hide');
            iconPreviewEmpty.classList.add('hide');
            return;
        }
        var iconValue = iconUrlInput ? String(iconUrlInput.value || '').trim() : '';
        if (removeIconInput && removeIconInput.checked) {
            iconValue = '';
        }
        if (iconValue !== '') {
            iconPreviewImg.src = iconValue;
            iconPreviewImg.classList.remove('hide');
            iconPreviewEmpty.classList.add('hide');
        } else {
            iconPreviewImg.src = '';
            iconPreviewImg.classList.add('hide');
            iconPreviewEmpty.classList.remove('hide');
        }
    }

    function isQrBoliviaMethod() {
        var provider = ((providerSelect && providerSelect.value) || '').toLowerCase();
        var methodKey = ((methodKeyInput && methodKeyInput.value) || '').toLowerCase();
        var presetKey = ((presetSelect && presetSelect.value) || '').toLowerCase();
        return provider === 'veripagos_qr' || provider === 'bnb_qr' || provider === 'custom_qr' || methodKey === 'qr_bolivia_auto' || methodKey === 'veripagos' || presetKey === 'qr_bolivia_auto' || presetKey === 'veripagos';
    }

    function syncProviderForQr() {
        if (!providerSelect || !qrProvider || !isQrBoliviaMethod()) return;
        var providerValue = (qrProvider.value || 'veripagos').toLowerCase();
        if (providerValue === 'bnb') {
            providerSelect.value = 'bnb_qr';
        } else if (providerValue === 'veripagos') {
            providerSelect.value = 'veripagos_qr';
        }
    }

    function toggleSections() {
        var provider = ((providerSelect && providerSelect.value) || '').toLowerCase();
        var methodKey = ((methodKeyInput && methodKeyInput.value) || '').toLowerCase();
        var qrProviderValue = ((qrProvider && qrProvider.value) || 'veripagos').toLowerCase();
        var isQr = isQrBoliviaMethod();
        var qrBox = document.getElementById('qr_box');
        var apiBox = document.getElementById('api_box');
        syncProviderForQr();
        if (qrBox) qrBox.classList.toggle('hide', !isQr);
        if (apiBox) {
            var showGenericApi = provider !== 'manual' && !(isQr && (qrProviderValue === 'bnb' || qrProviderValue === 'veripagos'));
            apiBox.classList.toggle('hide', !showGenericApi);
        }
        toggleQrProvider();
    }

    function resetForm() {
        form.reset();
        form.action = formBaseAction;
        setVal('method_id', '0');
        setVal('preview_method', '0');
        if (modalTitle) modalTitle.textContent = 'Agregar metodo de pago';
        hidePreview();

        setVal('method_preset', '');
        setVal('method_key', '');
        setVal('method_name', '');
        setVal('description', '');
        setVal('min_amount', '1');
        setVal('max_amount', '1000');
        setVal('rate_bob', '{$finance_default_rate_bob}');
        setVal('credit_price_usd', '');
        setVal('display_order', '100');
        setVal('icon_url', '');
        if (iconFileInput) {
            iconFileInput.value = '';
        }
        setCheck('allow_new_users', true);
        setCheck('is_active', true);
        setCheck('remove_icon', false);
        setCheck('processing_fee', false);
        setVal('fee_fixed', '0');
        setVal('fee_percent', '0');

        setVal('create_url', '');
        setVal('auth_type', 'bearer');
        setVal('api_key', '');
        setVal('api_user', '');
        setVal('api_password', '');
        setVal('secret', '');
        setVal('txn_path', '');
        setVal('qr_image_path', '');
        setVal('qr_payload_path', '');
        setVal('expires_path', '');
        setVal('extra_headers', '');
        setVal('instructions', '');

        setVal('qr_provider', 'veripagos');
        setVal('qb_mode', 'test');
        setVal('qb_expiry_minutes', '15');
        setVal('vp_username', '');
        setVal('vp_password', '');
        setVal('vp_secret_key', '');
        setVal('vp_base_url', '');
        setVal('bnb_account_id', '');
        setVal('bnb_authorization_id', '');
        setVal('bnb_destination_account_id', '1');
        setVal('bnb_currency', 'BOB');
        setVal('bnb_token_url', 'http://test.bnb.com.bo/ClientAuthentication.API/api/v1/auth/token');
        setVal('bnb_qr_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRWithImageAsync');
        setVal('bnb_status_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRStatusAsync');
        setVal('bnb_cancel_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/CancelQRByIdAsync');

        toggleFee();
        toggleSections();
        updateIconPreview();
    }

    function openModal() {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        hidePreview();
    }

    function openEdit(payload) {
        resetForm();
        var data = payload || {};
        var settings = data.settings || {};
        var id = Number(data.id || 0);
        if (id <= 0) {
            openModal();
            return;
        }

        if (modalTitle) modalTitle.textContent = 'Editar metodo de pago';
        form.action = formBaseAction + '&edit=' + encodeURIComponent(String(id));
        setVal('method_id', String(id));

        setVal('method_key', data.method_key || '');
        setVal('provider_key', data.provider_key || '');
        setVal('method_name', data.method_name || '');
        setVal('description', settings.description || '');
        setVal('icon_url', settings.icon_url || '');
        setVal('min_amount', data.min_amount || '1');
        setVal('max_amount', data.max_amount || '1000');
        setVal('rate_bob', data.rate_bob || '{$finance_default_rate_bob}');
        setVal('credit_price_usd', settings.credit_price_usd || '');
        setVal('display_order', data.display_order || '100');
        setCheck('remove_icon', false);
        setCheck('allow_new_users', Number(data.allow_new_users || 0) === 1);
        setCheck('is_active', Number(data.is_active || 0) === 1);
        setCheck('processing_fee', Number(settings.processing_fee || 0) === 1);
        setVal('fee_fixed', data.fee_fixed || settings.fee_fixed || '0');
        setVal('fee_percent', data.fee_percent || settings.fee_percent || '0');

        setVal('create_url', settings.create_url || '');
        setVal('auth_type', settings.auth_type || 'bearer');
        setVal('api_key', settings.api_key || '');
        setVal('api_user', settings.api_user || '');
        setVal('secret', settings.secret || '');
        setVal('txn_path', settings.txn_path || '');
        setVal('qr_image_path', settings.qr_image_path || '');
        setVal('qr_payload_path', settings.qr_payload_path || '');
        setVal('expires_path', settings.expires_path || '');
        setVal('extra_headers', settings.extra_headers || '');
        setVal('instructions', settings.instructions || settings.instruction || '');

        setVal('qr_provider', settings.qr_provider || settings.qb_provider || (String(data.provider_key || '').toLowerCase() === 'bnb_qr' ? 'bnb' : 'veripagos'));
        setVal('qb_mode', settings.qb_mode || 'test');
        setVal('qb_expiry_minutes', settings.qb_expiry_minutes || '15');
        setVal('vp_username', settings.vp_username || settings.api_user || '');
        setVal('vp_base_url', settings.vp_base_url || '');
        setVal('bnb_account_id', settings.bnb_account_id || '');
        setVal('bnb_destination_account_id', settings.bnb_destination_account_id || '1');
        setVal('bnb_currency', settings.bnb_currency || 'BOB');
        setVal('bnb_token_url', settings.bnb_token_url || 'http://test.bnb.com.bo/ClientAuthentication.API/api/v1/auth/token');
        setVal('bnb_qr_url', settings.bnb_qr_url || 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRWithImageAsync');
        setVal('bnb_status_url', settings.bnb_status_url || 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRStatusAsync');
        setVal('bnb_cancel_url', settings.bnb_cancel_url || 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/CancelQRByIdAsync');

        var presetKey = String(data.method_key || '').toLowerCase();
        setVal('method_preset', PRESETS[presetKey] ? presetKey : '');

        toggleFee();
        toggleSections();
        updateIconPreview();
        openModal();
    }

    if (presetSelect) {
        presetSelect.addEventListener('change', function () {
            var key = String(presetSelect.value || '').toLowerCase();
            var preset = PRESETS[key];
        if (!preset) return;
        if (methodKeyInput && (!methodKeyInput.value || methodKeyInput.value.trim() === '' || methodKeyInput.value === key)) {
            methodKeyInput.value = preset.method_key;
        }
        if (providerSelect) {
            providerSelect.value = preset.provider_key;
        }
        if (methodNameInput && (!methodNameInput.value || methodNameInput.value.trim() === '')) {
            methodNameInput.value = preset.method_name;
        }
        toggleSections();
    });
    }

    if (providerSelect) providerSelect.addEventListener('change', toggleSections);
    if (methodKeyInput) methodKeyInput.addEventListener('input', toggleSections);
    if (qrProvider) qrProvider.addEventListener('change', toggleSections);
    if (processingFeeInput) processingFeeInput.addEventListener('change', toggleFee);
    if (iconUrlInput) {
        iconUrlInput.addEventListener('input', function () {
            if (removeIconInput && String(iconUrlInput.value || '').trim() !== '') {
                removeIconInput.checked = false;
            }
            updateIconPreview();
        });
    }
    if (iconFileInput) {
        iconFileInput.addEventListener('change', function () {
            if (removeIconInput && iconFileInput.files && iconFileInput.files.length > 0) {
                removeIconInput.checked = false;
            }
            updateIconPreview();
        });
    }
    if (removeIconInput) removeIconInput.addEventListener('change', updateIconPreview);

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
        if (previewBtn.disabled) return;
        hidePreview();

        var submitLabel = previewBtn.textContent;
        previewBtn.disabled = true;
        previewBtn.textContent = 'Probando...';
        if (previewInput) {
            previewInput.value = '1';
        }

        var formData = new FormData(form);
        formData.set('preview_method', '1');

            fetch(formBaseAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function (response) {
                return response.text().then(function (text) {
                    var data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        data = {
                            ok: false,
                            message: 'La prueba no devolvio JSON valido.'
                        };
                    }
                    if (!response.ok && (!data || data.ok)) {
                        data = data || {};
                        data.ok = false;
                        data.message = data.message || ('Respuesta HTTP ' + response.status);
                    }
                    return data;
                });
            })
            .then(function (data) {
                showPreview(data || {});
            })
            .catch(function () {
                showPreview({
                    ok: false,
                    message: 'No se pudo conectar con el proveedor para la prueba.'
                });
            })
            .finally(function () {
                if (previewInput) {
                    previewInput.value = '0';
                }
                previewBtn.disabled = false;
                previewBtn.textContent = submitLabel;
            });
        });
    }

    if (openBtn) openBtn.addEventListener('click', function () { resetForm(); openModal(); });
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (ev) { if (ev.target === modal) closeModal(); });
    document.addEventListener('keydown', function (ev) { if (ev.key === 'Escape' && modal.classList.contains('open')) closeModal(); });

    toggleFee();
    toggleSections();
    updateIconPreview();

    var editPayload = {$finance_edit_method_json nofilter};
    if (editPayload && typeof editPayload === 'object' && Number(editPayload.id || 0) > 0) {
        openEdit(editPayload);
    }
})();
</script>
{/if}
</body>
</html>
