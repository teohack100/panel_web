<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Checkout recarga</title>
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
        :root{
            --checkout-bg:#091221;
            --checkout-panel:#0d1930;
            --checkout-panel-soft:#163359;
            --checkout-border:#2d4f7c;
            --checkout-text:#eef5ff;
            --checkout-soft:#a6b8d7;
            --checkout-accent:#74beff;
            --checkout-accent-dark:#4a8fe4;
            --checkout-info:#4da1ff;
            --checkout-warning:#ffca2c;
            --checkout-success:#63a5ff;
            --checkout-danger:#91b7ea;
        }
        .checkout-stage{
            border:1px solid var(--checkout-border);
            border-radius:0;
            overflow:hidden;
            background:linear-gradient(180deg,#10203a 0%, #0a1426 100%);
            box-shadow:0 18px 36px rgba(6,14,28,.18);
            max-width:500px;
            margin:0 auto;
            text-rendering:optimizeLegibility;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }
        .checkout-hero{
            background:linear-gradient(180deg,#10315c 0%, #1a4a83 100%);
            color:#f4f9ff;
            padding:10px 16px 8px;
            border-bottom:1px solid rgba(135,179,233,.24);
        }
        .checkout-hero h2{
            margin:0;
            font-size:1.54rem;
            font-weight:700;
            letter-spacing:-0.025em;
            color:#f8fbff;
            text-shadow:0 0 14px rgba(190,227,255,.10);
        }
        .checkout-hero p{
            margin:3px 0 0;
            font-weight:600;
            opacity:1;
            font-size:.92rem;
            line-height:1.38;
            color:#e6f0ff;
            text-shadow:0 0 10px rgba(176,219,255,.06);
        }
        .checkout-layout{
            padding:0;
        }
        .checkout-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 16px;
            border-radius:4px;
            font-size:.92rem;
            font-weight:800;
            letter-spacing:.04em;
            text-transform:uppercase;
            border:1px solid rgba(255,255,255,.08);
        }
        .checkout-badge.pending{ background:rgba(255,202,44,.16); color:#ffdb69; }
        .checkout-badge.paid{ background:rgba(99,165,255,.14); color:#d8ebff; }
        .checkout-badge.expired{ background:rgba(112,166,237,.16); color:#d7eaff; }
        .checkout-badge.failed,
        .checkout-badge.cancelled{ background:rgba(112,166,237,.16); color:#d7eaff; }
        .checkout-status-line{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            min-height:42px;
            padding:0 12px;
            border-radius:0;
            border:1px solid rgba(124,164,219,.16);
            border-top:0;
            border-left:0;
            border-right:0;
            background:linear-gradient(90deg,#132646 0%, #1a345c 45%, #132646 100%);
            color:#dce8ff;
            margin:0;
            overflow:hidden;
            position:relative;
        }
        .checkout-status-line.waiting{
            background:linear-gradient(90deg,#132646 0%, #1d3963 50%, #132646 100%);
            box-shadow:
                inset 2px 0 0 var(--checkout-accent),
                inset 0 0 0 1px rgba(124,164,219,.08),
                0 0 14px rgba(80,146,229,.08);
        }
        .checkout-status-line.waiting::before{
            content:"";
            position:absolute;
            top:-10px;
            bottom:-10px;
            left:-22%;
            width:22%;
            background:radial-gradient(ellipse at center, rgba(222,244,255,.54) 0%, rgba(148,214,255,.24) 32%, rgba(107,187,255,.08) 52%, rgba(107,187,255,0) 74%);
            pointer-events:none;
            filter:blur(10px);
            opacity:.95;
            animation:checkoutWaitingSheen 2.8s ease-in-out infinite;
        }
        .checkout-status-line.waiting::after{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(180deg, rgba(255,255,255,.03) 0%, rgba(255,255,255,0) 32%, rgba(255,255,255,0) 100%);
            box-shadow:inset 0 0 18px rgba(109,188,255,.04);
            pointer-events:none;
        }
        .checkout-status-label{
            position:relative;
            z-index:2;
            font-size:1rem;
            font-weight:700;
            letter-spacing:.01em;
            color:#f7fbff;
            text-shadow:0 0 14px rgba(175,219,255,.16);
        }
        .checkout-status-countdown{
            position:relative;
            z-index:2;
            font-size:.98rem;
            font-weight:700;
            letter-spacing:.08em;
            color:#b2e0ff;
            font-variant-numeric:tabular-nums;
            font-family:Consolas, "SFMono-Regular", Monaco, "Roboto Mono", monospace;
            white-space:nowrap;
            text-shadow:0 0 14px rgba(172,223,255,.24);
            animation:checkoutCountdownPulse 1.6s ease-in-out infinite;
        }
        .checkout-body{
            background:rgba(255,255,255,.02);
            border:1px solid var(--checkout-border);
            border-radius:0;
            border-top:0;
            border-left:0;
            border-right:0;
            padding:6px 8px 8px;
            color:var(--checkout-text);
        }
        .checkout-subtitle{
            margin:10px 0 0;
            color:#d9e7ff;
            font-size:.96rem;
            line-height:1.45;
        }
        .checkout-pay-card{
            position:relative;
            margin-top:2px;
            background:linear-gradient(180deg,#0e1c33 0%, #0b1729 100%);
            border:1px solid rgba(255,255,255,.08);
            border-radius:0;
            padding:5px;
            box-shadow:0 8px 18px rgba(5,12,24,.14);
            max-width:342px;
            margin-left:auto;
            margin-right:auto;
            overflow:hidden;
        }
        .checkout-pay-card.is-pending::before{
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at 50% 26%, rgba(110,186,255,.12) 0%, rgba(110,186,255,0) 34%),
                radial-gradient(circle at 50% 62%, rgba(110,186,255,.05) 0%, rgba(110,186,255,0) 52%);
            opacity:.9;
            pointer-events:none;
            animation:checkoutQrAura 2.4s ease-in-out infinite;
        }
        .checkout-pay-layout{
            position:relative;
            z-index:1;
            display:block;
        }
        .checkout-qr-box{
            width:100%;
            max-width:252px;
            margin:0 auto;
            background:#fff;
            border-radius:3px;
            padding:1px;
            border:1px solid rgba(255,255,255,.16);
            box-shadow:0 6px 14px rgba(0,0,0,.1);
            position:relative;
        }
        .checkout-pay-card.is-pending .checkout-qr-box{
            border-color:rgba(164,216,255,.72);
            box-shadow:
                0 0 0 1px rgba(114,190,255,.18),
                0 0 18px rgba(114,190,255,.16),
                0 10px 22px rgba(0,0,0,.14);
            animation:checkoutQrGlow 2.1s ease-in-out infinite;
        }
        .checkout-pay-card.is-pending .checkout-qr-box::before{
            content:"";
            position:absolute;
            inset:-1px;
            border:1px solid rgba(190,231,255,.18);
            pointer-events:none;
            opacity:.8;
        }
        .checkout-qr-box img{
            width:100%;
            max-width:248px;
            height:auto;
            display:block;
            margin:0 auto;
            border-radius:1px;
        }
        .checkout-pay-info{
            text-align:center;
            margin-top:5px;
        }
        .checkout-amount-box{
            position:relative;
            width:100%;
            max-width:252px;
            margin:0 auto;
            background:linear-gradient(180deg,#eef6ff 0%, #d7e8ff 100%);
            color:#10335f;
            border-radius:4px;
            padding:7px 8px 6px;
            box-shadow:0 8px 14px rgba(39,89,157,.1);
            border:1px solid rgba(103,148,212,.26);
            overflow:hidden;
        }
        .checkout-pay-card.is-pending .checkout-amount-box{
            border-color:rgba(118,181,255,.42);
            box-shadow:
                0 0 0 1px rgba(113,177,255,.12),
                0 0 16px rgba(113,177,255,.12),
                0 8px 14px rgba(39,89,157,.12);
            animation:checkoutAmountGlow 2.3s ease-in-out infinite;
        }
        .checkout-pay-card.is-pending .checkout-amount-box::before{
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at 50% 8%, rgba(255,255,255,.42) 0%, rgba(255,255,255,.14) 18%, rgba(255,255,255,0) 46%),
                linear-gradient(180deg, rgba(167,215,255,.12) 0%, rgba(167,215,255,0) 56%);
            opacity:.92;
            pointer-events:none;
        }
        .checkout-pay-card.is-pending .checkout-amount-box::after{
            content:"";
            position:absolute;
            top:0;
            bottom:0;
            width:84px;
            left:-28px;
            background:linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.26) 50%, rgba(255,255,255,0) 100%);
            transform:skewX(-16deg);
            filter:blur(1px);
            opacity:.65;
            animation:checkoutAmountSweep 2.8s linear infinite;
            pointer-events:none;
        }
        .checkout-amount-box .label{
            position:relative;
            z-index:1;
            display:block;
            font-size:1rem;
            font-weight:700;
            margin-bottom:4px;
            opacity:1;
            color:#294f7d;
            text-shadow:0 1px 0 rgba(255,255,255,.42);
        }
        .checkout-amount-line{
            position:relative;
            z-index:1;
            display:flex;
            align-items:flex-end;
            justify-content:center;
            gap:6px;
            flex-wrap:wrap;
            font-size:2.12rem;
            font-weight:700;
            line-height:1;
            color:#123764;
            text-shadow:0 1px 0 rgba(255,255,255,.30);
        }
        .checkout-amount-line .value{
            display:block;
        }
        .checkout-amount-line .sub{
            display:block;
            font-size:.96rem;
            font-weight:600;
            opacity:1;
            line-height:1.2;
            padding-bottom:3px;
            color:#2d5688;
        }
        .checkout-meta{
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:4px;
            margin-top:5px;
        }
        .checkout-meta-item{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:24px;
            padding:0 8px;
            border-radius:4px;
            background:linear-gradient(90deg,rgba(255,255,255,.04) 0%, rgba(56,88,132,.14) 100%);
            border:1px solid rgba(255,255,255,.05);
            color:#dbe7ff;
            font-size:.88rem;
            font-weight:700;
            text-align:center;
            width:100%;
            max-width:252px;
            text-shadow:0 0 12px rgba(172,224,255,.10);
        }
        .checkout-success-box,
        .checkout-expired-box{
            margin-top:10px;
            border-radius:4px;
            padding:18px 16px;
            text-align:center;
            border:1px solid var(--checkout-border);
        }
        .checkout-success-box{
            background:linear-gradient(180deg,rgba(67,126,207,.16) 0%, rgba(16,40,77,.38) 100%);
        }
        .checkout-expired-box{
            background:linear-gradient(180deg,rgba(74,143,228,.14) 0%, rgba(13,33,60,.28) 100%);
        }
        .checkout-icon-mark{
            width:64px;
            height:64px;
            border-radius:4px;
            margin:0 auto 12px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:2rem;
            font-weight:900;
            background:#fff;
        }
        .checkout-success-box .checkout-icon-mark{
            color:var(--checkout-success);
            box-shadow:0 18px 36px rgba(74,143,228,.22);
        }
        .checkout-expired-box .checkout-icon-mark{
            color:var(--checkout-danger);
            box-shadow:0 18px 36px rgba(74,143,228,.18);
        }
        .checkout-success-box h3,
        .checkout-expired-box h3{
            margin:0 0 6px;
            color:#fff;
            font-weight:900;
            font-size:1.6rem;
            letter-spacing:-0.03em;
        }
        .checkout-success-box p,
        .checkout-expired-box p{
            margin:0;
            color:var(--checkout-soft);
            font-size:.94rem;
        }
        .checkout-inline-note{
            margin-top:4px;
            color:var(--checkout-soft);
            font-size:.72rem;
            line-height:1.3;
        }
        .checkout-note-card{
            display:block;
            width:calc(100% + 16px);
            max-width:none;
            margin:10px -8px -8px;
            padding:9px 12px;
            border-top:1px solid rgba(98,149,214,.28);
            border-left:0;
            border-right:0;
            border-bottom:0;
            border-radius:0;
            background:linear-gradient(180deg, rgba(24,48,84,.92) 0%, rgba(13,28,50,.96) 100%);
            box-shadow:inset 3px 0 0 #4da1ff;
            color:#d8e9ff;
            text-align:center;
            box-shadow:
                inset 3px 0 0 #4da1ff,
                inset 0 1px 0 rgba(255,255,255,.03);
        }
        .checkout-note-text{
            display:block;
            color:#f2f8ff;
            font-size:.92rem;
            line-height:1.25;
            font-weight:600;
            white-space:normal;
            text-shadow:0 0 12px rgba(190,226,255,.08);
        }
        .checkout-actions{
            display:flex;
            flex-wrap:wrap;
            justify-content:center;
            gap:10px;
            margin-top:12px;
        }
        .checkout-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:46px;
            padding:0 18px;
            border-radius:4px;
            font-weight:800;
            font-size:.96rem;
            text-decoration:none !important;
            border:1px solid transparent;
            transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
        }
        .checkout-btn:hover{
            transform:translateY(-1px);
        }
        .checkout-btn-primary{
            background:linear-gradient(180deg,#4c8df5 0%, #316bd0 100%);
            color:#fff !important;
            box-shadow:0 16px 32px rgba(48,107,208,.25);
        }
        .checkout-btn-soft{
            background:rgba(255,255,255,.04);
            border-color:rgba(255,255,255,.1);
            color:#dce9ff !important;
        }
        .checkout-btn-success{
            background:linear-gradient(180deg,#63a5ff 0%, #3378d1 100%);
            color:#ffffff !important;
            box-shadow:0 16px 32px rgba(48,107,208,.25);
        }
        .checkout-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 12px;
            border-radius:4px;
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
            color:#dce8ff;
            font-size:.92rem;
            font-weight:700;
        }
        @keyframes checkoutCountdownPulse{
            0%,100%{
                opacity:1;
                text-shadow:0 0 8px rgba(143,208,255,.22);
            }
            50%{
                opacity:.92;
                text-shadow:0 0 16px rgba(174,224,255,.36);
            }
        }
        @keyframes checkoutWaitingSheen{
            0%{
                transform:translateX(0);
                opacity:0;
            }
            18%{
                opacity:.9;
            }
            100%{
                transform:translateX(560%);
                opacity:0;
            }
        }
        @keyframes checkoutQrGlow{
            0%,100%{
                box-shadow:
                    0 0 0 1px rgba(114,190,255,.16),
                    0 0 14px rgba(114,190,255,.12),
                    0 10px 22px rgba(0,0,0,.14);
            }
            50%{
                box-shadow:
                    0 0 0 1px rgba(166,220,255,.26),
                    0 0 24px rgba(114,190,255,.22),
                    0 12px 24px rgba(0,0,0,.16);
            }
        }
        @keyframes checkoutQrAura{
            0%,100%{
                opacity:.72;
                transform:scale(1);
            }
            50%{
                opacity:1;
                transform:scale(1.02);
            }
        }
        @keyframes checkoutAmountGlow{
            0%,100%{
                box-shadow:
                    0 0 0 1px rgba(113,177,255,.10),
                    0 0 12px rgba(113,177,255,.08),
                    0 8px 14px rgba(39,89,157,.1);
            }
            50%{
                box-shadow:
                    0 0 0 1px rgba(156,210,255,.16),
                    0 0 18px rgba(113,177,255,.16),
                    0 8px 16px rgba(39,89,157,.14);
            }
        }
        @keyframes checkoutAmountSweep{
            0%{ transform:translateX(-120%) skewX(-16deg); }
            100%{ transform:translateX(320%) skewX(-16deg); }
        }
        .checkout-qr-payload{
            white-space:pre-wrap;
            word-break:break-word;
            color:#f0f5ff;
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.08);
            padding:16px;
            border-radius:4px;
            text-align:left;
        }
        .checkout-instructions-card{
            width:100%;
            max-width:262px;
            margin:0 auto;
            background:linear-gradient(180deg,#17345c 0%, #0f2442 100%);
            border:1px solid rgba(110,164,236,.24);
            border-radius:4px;
            padding:14px 14px 12px;
            box-shadow:0 8px 18px rgba(8,18,33,.18);
            text-align:left;
        }
        .checkout-instructions-title{
            display:block;
            margin:0 0 8px;
            color:#f4f9ff;
            font-size:.9rem;
            font-weight:800;
            letter-spacing:.01em;
        }
        .checkout-instructions-copy{
            margin:0;
            white-space:pre-wrap;
            word-break:break-word;
            color:#d7e7ff;
            font-size:.82rem;
            line-height:1.45;
            font-weight:700;
        }
        @media (max-width: 1199.98px){
        }
        @media (max-width: 767.98px){
            .checkout-layout{
                padding:0;
            }
            .checkout-hero{
                padding:12px;
            }
            .checkout-hero h2{
                font-size:1.28rem;
            }
            .checkout-hero p{
                font-size:.88rem;
            }
            .checkout-body{
                padding:5px 6px 6px;
            }
            .checkout-status-line{
                padding:0 10px;
                min-height:40px;
            }
            .checkout-status-label,
            .checkout-status-countdown{
                font-size:.86rem;
            }
            .checkout-note-card{
                max-width:100%;
            }
            .checkout-note-text{
                white-space:normal;
            }
            .checkout-actions{
                flex-direction:column;
            }
            .checkout-btn{
                width:100%;
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
                                <li class="breadcrumb-item active">Checkout</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Checkout de recarga</h4>
                    </div>
                </div>
            </div>

            {if $finance_notice != ''}
            <div class="alert alert-info">{$finance_notice}</div>
            {/if}

            <div class="checkout-stage">
                <div class="checkout-hero">
                    <h2>{$finance_recharge.hero_title}</h2>
                    <p>{$finance_recharge.hero_subtitle}</p>
                </div>

                <div class="checkout-layout">
                    {if $finance_recharge.show_pending_countdown == 1}
                    <div class="checkout-status-line waiting" data-expires="{$finance_recharge.expires_at_unix|default:0}">
                        <span class="checkout-status-label">{$finance_recharge.pending_label}</span>
                        <span class="checkout-status-countdown" data-role="checkout-countdown">{$finance_recharge.countdown_text|default:'00:00:00'}</span>
                    </div>
                    {/if}
                    <div class="checkout-body">
                        {if $finance_recharge.status_code != 'PENDING' && $finance_recharge.status_code != 'PAID' && $finance_recharge.status_code != 'EXPIRED'}
                        <span class="checkout-badge {$finance_recharge.status_code|lower}">{$finance_recharge.status}</span>
                        {/if}

                        {if $finance_recharge.status_code == 'PAID'}
                            <div class="checkout-success-box">
                                <div class="checkout-icon-mark">✓</div>
                                <h3>Pago completado</h3>
                                <p>El saldo ya fue acreditado a tu monedero.</p>
                            </div>
                            <div class="checkout-meta">
                                <span class="checkout-meta-item">Créditos: {$finance_recharge.credits_to_add}</span>
                                <span class="checkout-meta-item">Pagado: {$finance_recharge.paid_at}</span>
                            </div>
                        {elseif $finance_recharge.status_code == 'EXPIRED'}
                            <div class="checkout-expired-box">
                                <div class="checkout-icon-mark">!</div>
                                <h3>{$finance_recharge.expired_title}</h3>
                                <p>La vigencia terminó. Esta ventana se cerrará automáticamente.</p>
                            </div>
                            <div class="checkout-meta">
                                <span class="checkout-meta-item">Expiró: {$finance_recharge.expires_at}</span>
                            </div>
                        {elseif $finance_recharge.status_code == 'FAILED'}
                            <div class="checkout-expired-box">
                                <div class="checkout-icon-mark">!</div>
                                <h3>Pago no validado</h3>
                                <p>El proveedor informó un fallo en la operación.</p>
                            </div>
                        {elseif $finance_recharge.status_code == 'CANCELLED'}
                            <div class="checkout-expired-box">
                                <div class="checkout-icon-mark">!</div>
                                <h3>Pago cancelado</h3>
                                <p>Esta operación fue cancelada.</p>
                            </div>
                        {else}
                            <div class="checkout-pay-card is-pending">
                                <div class="checkout-pay-layout">
                                    {if $finance_recharge.show_manual_instructions == 1}
                                        <div class="checkout-instructions-card">
                                            <span class="checkout-instructions-title">Instrucciones del pago</span>
                                            {if $finance_recharge.qr_payload != ''}
                                                <pre class="checkout-instructions-copy">{$finance_recharge.qr_payload|escape:'html'}</pre>
                                            {else}
                                                <div class="checkout-instructions-copy">Este método no tiene instrucciones configuradas todavía.</div>
                                            {/if}
                                        </div>
                                    {else}
                                        <div class="checkout-qr-box">
                                            {if $finance_recharge.qr_image_url != ''}
                                                <img src="{$finance_recharge.qr_image_url}" alt="QR de pago">
                                            {elseif $finance_recharge.qr_payload != ''}
                                                <pre class="checkout-qr-payload mb-0">{$finance_recharge.qr_payload}</pre>
                                            {else}
                                                <p class="text-warning mb-0">Este método aún no devolvió QR automático. Revisa la configuración del proveedor.</p>
                                            {/if}
                                        </div>
                                    {/if}
                                    <div class="checkout-pay-info">
                                        <div class="checkout-amount-box">
                                            <span class="label">Monto a pagar</span>
                                            <div class="checkout-amount-line">
                                                <span class="value">{$finance_recharge.total_bob} Bs</span>
                                                <span class="sub">${$finance_recharge.total_usd} USD</span>
                                            </div>
                                        </div>
                                        <div class="checkout-meta">
                                            <span class="checkout-meta-item">Créditos: {$finance_recharge.credits_to_add}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-note-card">
                                <span class="checkout-note-text">{$finance_recharge.pending_note}</span>
                            </div>
                        {/if}

                        {if $finance_recharge.status_code != 'PENDING' && $finance_recharge.status_code != 'EXPIRED'}
                        <div class="checkout-actions">
                            <a class="checkout-btn checkout-btn-soft" href="{$base_url}index.php?p=finance-history">Ver historial</a>
                            {if $finance_recharge.status_code == 'PAID'}
                                <a class="checkout-btn checkout-btn-success" href="{$base_url}index.php?p=finance-add">Recargar otra vez</a>
                            {else}
                                <a class="checkout-btn checkout-btn-primary" href="{$base_url}index.php?p=finance-add">Nueva recarga</a>
                            {/if}
                        </div>
                        {/if}

                        {if $finance_is_superadmin == 1 && $finance_recharge.status_code != 'PAID'}
                        <form method="post" class="mt-3 text-center">
                            <input type="hidden" name="mark_paid" value="1">
                            <button type="submit" class="btn btn-success">Marcar pagado (admin)</button>
                        </form>
                        {/if}

                        {if $finance_is_superadmin == 1 && $finance_recharge.qr_image_url == '' && $finance_recharge.provider_response != ''}
                        <div class="mt-4">
                            <h6 class="text-warning mb-2">Debug proveedor</h6>
                            <pre class="text-left text-light mb-0" style="white-space:pre-wrap;background:#0f1d32;border:1px solid #2f4a74;border-radius:8px;padding:10px;max-height:220px;overflow:auto;">{$finance_recharge.provider_response}</pre>
                        </div>
                        {/if}
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
{if $finance_recharge.status_code == 'PENDING'}
<script>
{literal}
(function () {
    var pollUrl = window.location.pathname + window.location.search.replace(/([?&])ajax=1(&|$)/, '$1').replace(/[?&]$/, '');
    pollUrl += (pollUrl.indexOf('?') === -1 ? '?' : '&') + 'ajax=1';
    var statusLine = document.querySelector('.checkout-status-line[data-expires]');
    var countdownNode = document.querySelector('[data-role="checkout-countdown"]');
    var expiresAt = statusLine ? parseInt(statusLine.getAttribute('data-expires') || '0', 10) : 0;

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function renderCountdown() {
        if (!countdownNode || !expiresAt) {
            return;
        }
        var now = Math.floor(Date.now() / 1000);
        var remaining = expiresAt - now;
        if (remaining <= 0) {
            countdownNode.textContent = '00:00:00';
            window.location.reload();
            return;
        }
        var hours = Math.floor(remaining / 3600);
        var minutes = Math.floor((remaining % 3600) / 60);
        var seconds = remaining % 60;
        countdownNode.textContent = pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
    }

    function pollCheckout() {
        fetch(pollUrl, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (!data || !data.ok) {
                return;
            }
            if (data.expires_at_unix) {
                expiresAt = parseInt(data.expires_at_unix, 10) || expiresAt;
            }
            if (countdownNode && data.countdown_text) {
                countdownNode.textContent = data.countdown_text;
            }
            if (String(data.status_code || '').toUpperCase() !== 'PENDING') {
                window.location.reload();
            }
        })
        .catch(function () {
        })
        .finally(function () {
            setTimeout(pollCheckout, 10000);
        });
    }

    renderCountdown();
    setInterval(renderCountdown, 1000);
    setTimeout(pollCheckout, 10000);
})();
{/literal}
</script>
{/if}
{if $finance_recharge.status_code == 'EXPIRED'}
<script>
setTimeout(function () {
    window.location.href = '{$base_url}index.php?p=finance-add';
}, 5000);
</script>
{/if}
</body>
</html>
