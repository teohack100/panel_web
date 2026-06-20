<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>{$siteTitle} - Agregar saldo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Panel de finanzas" name="description" />
    <meta content="PROGRAMMIT" name="author" />

    {assign var=pm_finance_add_favicon value="`$base_url`firenet/assets/images/v.png"}
    {if isset($panel_favicon_url) && $panel_favicon_url neq ''}
        {assign var=pm_finance_add_favicon value=$panel_favicon_url}
    {/if}
    <link rel="shortcut icon" href="{$pm_finance_add_favicon}">
    <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />
    {include file='css/custom_css.tpl'}
    {include file='apps/panel-theme.tpl'}
    <style>
    {literal}
        /* Finance Add: scoped theme tokens */
        .wallet-shell{
            --wallet-bg-1:color-mix(in srgb, var(--pm-brand-bg-secondary) 72%, var(--pm-brand-primary) 28%);
            --wallet-bg-2:var(--pm-brand-bg-main);
            --wallet-border:color-mix(in srgb, var(--pm-brand-primary) 34%, white 16%);
            --wallet-soft:var(--pm-brand-text-secondary);
            --wallet-white:var(--pm-brand-text-primary);
            --wallet-accent:var(--pm-brand-primary);
            --wallet-accent-strong:color-mix(in srgb, var(--pm-brand-primary) 78%, var(--pm-brand-bg-main) 22%);
            --wallet-accent-soft:color-mix(in srgb, var(--pm-brand-primary) 64%, white 36%);
            --wallet-surface:color-mix(in srgb, white 94%, var(--pm-brand-primary) 6%);
            --wallet-surface-border:color-mix(in srgb, var(--pm-brand-primary) 18%, white 82%);
            --wallet-shadow-soft:0 18px 34px rgba(18,48,92,.08);
            --wallet-card-top:color-mix(in srgb, white 92%, var(--pm-brand-primary) 8%);
            --wallet-card-bottom:color-mix(in srgb, white 84%, var(--pm-brand-primary) 16%);
            --wallet-card-edge:color-mix(in srgb, var(--pm-brand-primary) 26%, white 74%);
            --wallet-ink:color-mix(in srgb, var(--pm-brand-primary) 32%, #112b4d 68%);
            --wallet-muted-ink:color-mix(in srgb, var(--pm-brand-primary) 18%, #54769d 82%);
            --wallet-dark-top:color-mix(in srgb, var(--pm-brand-primary) 38%, #11233d 62%);
            --wallet-dark-bottom:color-mix(in srgb, var(--pm-brand-primary) 24%, #13253f 76%);
            --wallet-dark-edge:color-mix(in srgb, var(--pm-brand-primary) 42%, #2a4162 58%);
            --wallet-dark-text:#edf5ff;
            max-width:1180px;
        }

        /* Finance Add: layout shell */
        .wallet-shell{
            position:relative;
        }

        /* Finance Add: main card */
        .wallet-card{
            border:1px solid var(--wallet-card-edge);
            border-radius:2px !important;
            background:linear-gradient(180deg,var(--wallet-card-top) 0%,var(--wallet-card-bottom) 100%);
            color:var(--wallet-ink);
            box-shadow:0 6px 14px rgba(8,24,48,.08);
            overflow:hidden;
            margin-top:24px;
            position:relative;
            background-clip:padding-box;
        }
        .wallet-card-body{
            border-radius:0 !important;
            padding:28px 26px 22px;
            background:
                radial-gradient(circle at top right, color-mix(in srgb, var(--pm-brand-primary) 10%, transparent 90%) 0%, rgba(90,153,232,0) 24%),
                linear-gradient(180deg,var(--wallet-card-top) 0%,var(--wallet-card-bottom) 100%);
        }

        /* Finance Add: admin state */
        .wallet-admin-panel{
            border:1px solid rgba(83,129,191,.34);
            border-radius:4px;
            background:
                radial-gradient(circle at top right, rgba(108,174,255,.14) 0%, rgba(108,174,255,0) 34%),
                linear-gradient(180deg, rgba(15,37,65,.88) 0%, rgba(11,27,48,.92) 100%);
            padding:26px;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.05), 0 20px 34px rgba(7,20,38,.16);
        }
        .wallet-admin-tag{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 12px;
            border:1px solid rgba(145,203,255,.22);
            border-radius:4px;
            background:rgba(14,31,55,.7);
            color:#dceaff;
            font-size:.78rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .wallet-admin-tag i{
            color:var(--wallet-accent-soft);
        }
        .wallet-admin-panel h5{
            margin:18px 0 10px;
            color:#ffffff;
            font-size:1.5rem;
            font-weight:900;
        }
        .wallet-admin-panel p{
            margin:0;
            max-width:820px;
            color:#a9c1e3;
            font-size:1rem;
            line-height:1.75;
        }
        .wallet-admin-meta{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            margin-top:20px;
        }
        .wallet-admin-meta span{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:9px 12px;
            border:1px solid rgba(79,120,176,.3);
            border-radius:4px;
            background:rgba(12,26,46,.65);
            color:#d6e7fe;
            font-size:.9rem;
            font-weight:700;
        }
        .wallet-admin-meta strong{
            color:#ffffff;
            font-weight:900;
        }
        .wallet-admin-actions{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            margin-top:24px;
        }
        .wallet-admin-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:180px;
            padding:12px 18px;
            border-radius:4px;
            background:linear-gradient(180deg,var(--wallet-accent) 0%,var(--wallet-accent-strong) 100%);
            color:#ffffff;
            font-size:.96rem;
            font-weight:900;
            text-decoration:none;
            transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
            box-shadow:0 12px 24px rgba(28,82,157,.20);
        }
        .wallet-admin-btn:hover{
            color:#ffffff;
            text-decoration:none;
            transform:translateY(-1px);
            box-shadow:0 14px 28px rgba(26,78,150,.24);
        }
        .wallet-admin-btn.is-muted{
            background:transparent;
            color:#e8f2ff;
            border:1px solid rgba(120,171,235,.34);
            box-shadow:none;
        }
        .wallet-admin-btn.is-muted:hover{
            color:#ffffff;
            border-color:rgba(108,174,255,.36);
            box-shadow:none;
        }

        /* Finance Add: headings and amount input */
        .wallet-title-block{
            margin-top:-7px;
            margin-bottom:15px;
        }
        .wallet-title-block h4{
            margin:0;
            color:var(--wallet-ink);
            font-size:1.72rem;
            line-height:1.05;
            font-weight:900;
        }
        .wallet-title-block p{
            margin:8px 0 0;
            color:var(--wallet-muted-ink);
            font-size:.98rem;
            display:block;
        }
        .wallet-label{
            color:var(--wallet-ink);
            font-weight:800;
            margin-bottom:10px;
            display:block;
            font-size:1.06rem;
        }
        .wallet-amount-field{
            position:relative;
        }
        .wallet-amount-wrap{
            display:flex;
            align-items:center;
            border:1px solid var(--wallet-dark-edge);
            border-radius:4px;
            background:linear-gradient(180deg,var(--wallet-dark-top) 0%,var(--wallet-dark-bottom) 100%);
            overflow:hidden;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.03);
        }
        .wallet-amount-prefix{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:54px;
            color:#b7d4ff;
            font-size:1.02rem;
            font-weight:700;
            border-right:1px solid color-mix(in srgb, var(--pm-brand-primary) 30%, #23446c 70%);
        }
        .wallet-amount-input{
            border:0;
            background:transparent;
            color:#f2f7ff;
            width:100%;
            padding:12px 14px;
            font-size:1.12rem;
            font-weight:600;
            outline:0;
        }
        .wallet-amount-input::placeholder{
            color:#b0c5e2;
            font-weight:600;
            opacity:1;
        }
        .wallet-amount-input::-webkit-outer-spin-button,
        .wallet-amount-input::-webkit-inner-spin-button{
            -webkit-appearance:none;
            margin:0;
        }
        .wallet-amount-input[type=number]{
            -moz-appearance:textfield;
        }

        /* Finance Add: amount suggestions */
        .quick-amounts{
            position:absolute;
            top:calc(100% + 4px);
            left:0;
            min-width:112px;
            max-width:128px;
            display:none;
            flex-direction:column;
            gap:0;
            padding:4px;
            border:0;
            border-radius:6px;
            background:linear-gradient(180deg, rgba(236,244,253,.72) 0%, rgba(216,231,248,.78) 100%);
            box-shadow:
                0 10px 22px rgba(23,49,83,.10),
                inset 0 0 0 1px rgba(255,255,255,.22);
            backdrop-filter:blur(10px) saturate(1.04);
            -webkit-backdrop-filter:blur(10px) saturate(1.04);
            z-index:20;
        }
        .quick-amounts.is-visible{
            display:flex;
        }
        .quick-amount-btn{
            border:0;
            background:transparent;
            color:#2c4c75;
            border-radius:4px;
            font-weight:500;
            font-size:.88rem;
            padding:7px 9px;
            line-height:1;
            text-align:left;
            width:100%;
            cursor:pointer;
            transition:.16s ease;
        }
        .quick-amount-btn + .quick-amount-btn{
            border-top:0;
        }
        .quick-amount-btn:hover{
            background:rgba(64,123,210,.08);
            color:#16385f;
        }

        /* Finance Add: method picker */
        .method-strip{
            display:flex;
            align-items:center;
            gap:12px;
            overflow-x:auto;
            padding:4px 2px 10px;
            -webkit-overflow-scrolling:touch;
            scrollbar-width:none;
            scroll-snap-type:x proximity;
            perspective:1200px;
        }
        .method-strip-shell{
            position:relative;
            padding:0;
            border:0;
            background:none;
            box-shadow:none;
        }
        .method-strip-shell::before{
            display:none;
        }
        .method-strip::-webkit-scrollbar{
            display:none;
        }
        .method-chip-wrap{
            position:relative;
            display:flex;
            flex:0 0 auto;
            scroll-snap-align:start;
        }
        .method-chip{
            --tilt-x:0deg;
            --tilt-y:0deg;
            --pointer-x:18%;
            --pointer-y:18%;
            border:0;
            border-radius:0;
            background:linear-gradient(180deg,#152c4c 0%,#102340 100%);
            background-size:140% 140%;
            background-position:50% 0%;
            color:#e6f0ff;
            width:68px;
            height:68px;
            padding:0;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            overflow:hidden;
            isolation:isolate;
            will-change:transform, box-shadow, background-position;
            transition:
                transform .28s cubic-bezier(.22,1,.36,1),
                box-shadow .28s ease,
                background-position .32s ease,
                filter .28s ease;
            transform:
                translateY(0)
                scale(1)
                rotateX(var(--tilt-x))
                rotateY(var(--tilt-y));
            transform-style:preserve-3d;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                inset 0 -1px 0 rgba(0,0,0,.16),
                0 8px 20px rgba(7,17,35,.18);
        }
        .method-chip::before{
            content:"";
            position:absolute;
            inset:-32%;
            background:radial-gradient(circle at var(--pointer-x) var(--pointer-y), rgba(129,197,255,.32), transparent 42%);
            opacity:0;
            transform:scale(.72);
            transition:opacity .26s ease, transform .34s ease;
            pointer-events:none;
            z-index:0;
            mix-blend-mode:screen;
        }
        .method-chip::after{
            content:"";
            position:absolute;
            top:-22%;
            left:-90%;
            width:56%;
            height:150%;
            background:linear-gradient(90deg, rgba(255,255,255,0), rgba(255,255,255,.24), rgba(255,255,255,0));
            transform:skewX(-18deg);
            opacity:0;
            pointer-events:none;
            z-index:1;
        }
        .method-chip:hover{
            background:linear-gradient(180deg,#193455 0%,#133053 100%);
            background-position:50% 100%;
            transform:
                translateY(0)
                scale(1.065)
                rotateX(var(--tilt-x))
                rotateY(var(--tilt-y));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.1),
                inset 0 -1px 0 rgba(0,0,0,.2),
                0 16px 34px rgba(7,17,35,.34),
                0 0 0 1px rgba(120,180,255,.22),
                0 0 22px rgba(79,152,255,.14);
            filter:saturate(1.08);
        }
        .method-chip:hover::before{
            opacity:1;
            transform:scale(1);
        }
        .method-chip:hover::after{
            opacity:1;
            animation:methodChipSheen .78s cubic-bezier(.22,1,.36,1) forwards;
        }
        .method-chip:active{
            transform:
                translateY(0)
                scale(1.01)
                rotateX(calc(var(--tilt-x) * .55))
                rotateY(calc(var(--tilt-y) * .55));
        }
        .method-chip.active{
            background:linear-gradient(180deg,#22426a 0%,#17375f 100%);
            box-shadow:
                inset 0 0 0 2px rgba(142,196,255,.34),
                inset 0 1px 0 rgba(255,255,255,.08),
                0 12px 26px rgba(59,120,215,.22),
                0 0 18px rgba(79,152,255,.12);
        }
        .method-chip.active::before{
            opacity:1;
            transform:scale(1);
        }
        .method-chip.active .method-icon{
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.12),
                inset 0 0 0 1px rgba(255,255,255,.06);
        }
        .method-icon{
            width:100%;
            height:100%;
            border-radius:0;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#ffffff;
            font-size:1.35rem;
            flex:0 0 100%;
            background:#335a91;
            overflow:hidden;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.08);
            position:relative;
            z-index:2;
            transition:filter .24s ease, box-shadow .24s ease, transform .24s cubic-bezier(.22,1,.36,1);
            transform:translateZ(12px);
        }
        .method-brand-logo{
            position:relative;
            width:100%;
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            color:#fff;
            text-align:center;
            letter-spacing:.01em;
        }
        .method-brand-logo::before{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,0));
            pointer-events:none;
        }
        .method-brand-logo span,
        .method-brand-logo i,
        .method-brand-logo b,
        .method-brand-logo small{
            position:relative;
            z-index:1;
        }
        .method-brand-logo b{
            display:block;
            font-size:1.08rem;
            line-height:1;
            font-weight:900;
        }
        .method-brand-logo small{
            display:block;
            margin-top:2px;
            font-size:.48rem;
            line-height:1;
            font-weight:800;
            letter-spacing:.11em;
            text-transform:uppercase;
            opacity:.9;
        }
        .method-brand-logo.brand-veripagos{
            background:
                radial-gradient(circle at 18% 20%, rgba(255,206,96,.32), transparent 32%),
                linear-gradient(135deg,#071019 0%,#0a1d2f 100%);
        }
        .method-brand-logo.brand-veripagos i{
            font-size:1.8rem;
            opacity:.95;
        }
        .method-brand-logo.brand-veripagos .brand-badge{
            position:absolute;
            right:5px;
            bottom:5px;
            background:#ffcc33;
            color:#101a24;
            padding:2px 4px;
            font-size:.42rem;
            font-weight:900;
            line-height:1;
        }
        .method-brand-logo.brand-manual{
            background:linear-gradient(135deg,#4aa6ff 0%,#67bbff 100%);
        }
        .method-brand-logo.brand-manual i{
            font-size:1.45rem;
        }
        .method-brand-logo.brand-paypal{
            background:linear-gradient(135deg,#ffffff 0%,#eef6ff 100%);
            color:#11457a;
        }
        .method-brand-logo.brand-paypal b{
            font-size:.94rem;
            letter-spacing:-.02em;
        }
        .method-brand-logo.brand-binance{
            background:linear-gradient(135deg,#ffdb38 0%,#f4bf18 100%);
            color:#0d1520;
        }
        .method-brand-logo.brand-binance i{
            font-size:1.4rem;
        }
        .method-brand-logo.brand-yape{
            background:linear-gradient(135deg,#6b0ab7 0%,#a00fd9 100%);
        }
        .method-brand-logo.brand-yape b{
            font-size:.82rem;
            font-style:italic;
            letter-spacing:-.03em;
        }
        .method-brand-logo.brand-mercadopago{
            background:linear-gradient(135deg,#41a7ff 0%,#77c1ff 100%);
            color:#07213a;
        }
        .method-brand-logo.brand-mercadopago b{
            font-size:.86rem;
        }
        .method-brand-logo.brand-stripe{
            background:linear-gradient(135deg,#5145ff 0%,#7c67ff 100%);
        }
        .method-brand-logo.brand-stripe b{
            font-size:1.18rem;
        }
        .method-brand-logo.brand-bnb{
            background:linear-gradient(135deg,#ffffff 0%,#eff5ff 100%);
            color:#17375d;
        }
        .method-brand-logo.brand-bnb i{
            font-size:1.5rem;
        }
        .method-brand-logo.brand-bnb .brand-badge{
            position:absolute;
            right:5px;
            bottom:5px;
            background:#ffd24b;
            color:#152331;
            padding:2px 4px;
            font-size:.42rem;
            font-weight:900;
            line-height:1;
        }
        .method-brand-logo.brand-cryptomus{
            background:linear-gradient(135deg,#05080d 0%,#121722 100%);
        }
        .method-brand-logo.brand-cryptomus b{
            font-size:.8rem;
            letter-spacing:.06em;
        }
        .method-brand-logo.brand-card{
            background:linear-gradient(135deg,#21476f 0%,#35679c 100%);
        }
        .method-brand-logo.brand-card i{
            font-size:1.42rem;
        }
        .method-chip:hover .method-icon{
            filter:brightness(1.04) saturate(1.08);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.1),
                inset 0 0 0 1px rgba(255,255,255,.04);
            transform:translateZ(18px);
        }
        .method-icon img{
            display:block;
            width:100%;
            height:100%;
            object-fit:fill;
            border-radius:0;
            background:transparent;
            padding:0;
            transition:filter .24s ease;
        }
        .method-chip:hover .method-icon img{
            filter:contrast(1.03) saturate(1.05);
        }
        .method-chip:focus-visible{
            outline:none;
            transform:
                translateY(0)
                scale(1.05)
                rotateX(var(--tilt-x))
                rotateY(var(--tilt-y));
            box-shadow:
                inset 0 0 0 2px rgba(145,203,255,.36),
                0 14px 30px rgba(7,17,35,.3),
                0 0 18px rgba(79,152,255,.16);
        }
        .method-tooltip{
            position:absolute;
            left:calc(100% + 7px);
            top:calc(50% + 15px);
            transform:translateY(-50%) scale(.96);
            background:linear-gradient(180deg, rgba(9,18,31,.96) 0%, rgba(5,12,23,.98) 100%);
            border:1px solid rgba(118,170,234,.28);
            border-radius:7px;
            color:#ffffff;
            white-space:nowrap;
            font-size:.58rem;
            font-weight:800;
            letter-spacing:.015em;
            text-transform:none;
            padding:4px 7px;
            line-height:1.05;
            opacity:0;
            pointer-events:none;
            z-index:8;
            transition:opacity .18s ease, transform .18s ease;
            box-shadow:
                0 8px 18px rgba(3,10,20,.26),
                0 0 0 1px rgba(94,151,228,.05);
            display:block;
            visibility:hidden;
            transform-origin:left center;
            backdrop-filter:blur(10px);
        }
        .method-tooltip::before{
            display:none;
        }
        .method-chip.active + .method-tooltip{
            border-color:rgba(171,221,255,.36);
            box-shadow:
                0 10px 20px rgba(3,10,20,.3),
                0 0 0 1px rgba(114,180,255,.08),
                0 0 10px rgba(74,146,255,.1);
        }
        .method-chip.active + .method-tooltip::before{
            border-left-color:rgba(171,221,255,.48);
            border-top-color:rgba(171,221,255,.48);
        }
        .method-chip-wrap:hover .method-tooltip,
        .method-chip-wrap:focus-within .method-tooltip{
            opacity:1;
            visibility:visible;
            transform:translateY(-50%) scale(1);
        }
        .method-selected-bar{
            margin-top:12px;
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:10px;
            flex-wrap:wrap;
            display:none;
        }
        .method-selected-pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 12px;
            border:1px solid rgba(100,151,219,.26);
            border-radius:4px;
            background:rgba(14,31,55,.74);
            color:#dbe9fb;
            font-size:.8rem;
            font-weight:700;
            line-height:1;
            min-height:0;
        }
        .method-selected-icon{
            width:28px;
            height:28px;
            flex:0 0 28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            background:#163154;
            box-shadow:inset 0 0 0 1px rgba(255,255,255,.05);
        }
        .method-selected-icon img{
            display:block;
            width:100%;
            height:100%;
            object-fit:fill;
        }
        .method-selected-icon .method-brand-logo{
            width:100%;
            height:100%;
        }
        .method-selected-label{
            display:flex;
            align-items:center;
            min-height:22px;
        }
        .method-selected-meta{
            color:#bfd5f1;
            font-size:.76rem;
            font-weight:600;
            line-height:1.25;
        }
        .method-icon.is-qr{
            background:linear-gradient(135deg,#285ea7 0%,#3b89ef 100%);
        }
        .method-icon.is-manual{
            background:linear-gradient(135deg,#3f72b8 0%,#4db0ff 100%);
        }
        .method-icon.is-card{
            background:linear-gradient(135deg,#2f578f 0%,#3d6cac 100%);
        }
        .method-empty{
            border:1px dashed #3e679d;
            border-radius:4px;
            color:#aac5e7;
            width:100%;
            padding:10px 12px;
            font-size:.88rem;
        }
        @keyframes methodChipSheen{
            0%{
                left:-90%;
                opacity:0;
            }
            18%{
                opacity:1;
            }
            100%{
                left:135%;
                opacity:0;
            }
        }

        /* Finance Add: method details, terms and actions */
        .wallet-select{
            background:linear-gradient(180deg,var(--wallet-dark-top) 0%,var(--wallet-dark-bottom) 100%);
            border:1px solid var(--wallet-dark-edge);
            color:#eef5ff;
            border-radius:4px;
            padding:14px 14px;
            width:100%;
            font-size:1.02rem;
            font-weight:800;
            transition:border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .wallet-select:focus{
            outline:0;
            border-color:#70a6ed;
            box-shadow:0 0 0 2px rgba(112,166,237,.2);
        }
        .wallet-select.has-selection{
            border-color:rgba(108,174,255,.78);
            box-shadow:0 0 0 1px rgba(108,174,255,.16), 0 10px 24px rgba(6,14,24,.16);
            background:linear-gradient(180deg, color-mix(in srgb, var(--pm-brand-primary) 44%, #12253f 56%) 0%, color-mix(in srgb, var(--pm-brand-primary) 28%, #142742 72%) 100%);
        }
        .wallet-select option{
            color:#0f223b;
        }
        .wallet-info{
            border:1px solid rgba(73,117,176,.42);
            border-left:3px solid color-mix(in srgb, var(--pm-brand-primary) 76%, white 24%);
            border-radius:4px;
            background:linear-gradient(180deg, color-mix(in srgb, var(--pm-brand-primary) 34%, #23384f 66%) 0%, color-mix(in srgb, var(--pm-brand-primary) 18%, #28384c 82%) 100%);
            color:#dce9fa;
            font-size:.92rem;
            font-weight:700;
            line-height:1.4;
            padding:12px 14px;
            margin-top:2px;
            box-shadow:0 10px 18px rgba(8,20,36,.16);
        }
        .wallet-info strong{
            color:#ffffff;
        }
        .wallet-terms{
            border:1px solid rgba(73,117,176,.34);
            background:linear-gradient(180deg, color-mix(in srgb, var(--pm-brand-primary) 24%, #edf4fc 76%) 0%, color-mix(in srgb, var(--pm-brand-primary) 16%, #e6eef8 84%) 100%);
            box-shadow:0 12px 20px rgba(7,17,31,.16);
            margin-top:14px;
            padding:12px 14px;
        }
        .wallet-check{
            display:flex;
            align-items:flex-start;
            gap:7px;
            margin:0;
            color:var(--wallet-ink);
            font-weight:800;
            cursor:pointer;
            line-height:1.18;
        }
        .wallet-check input{
            transform:translateY(1px);
            accent-color:var(--wallet-accent);
            width:14px;
            height:14px;
            flex:0 0 14px;
        }
        .wallet-terms-text{
            margin:6px 0 0 21px;
            color:#496c95;
            font-size:.88rem;
            font-weight:700;
            line-height:1.36;
            max-width:700px;
        }
        .wallet-action-row{
            margin-top:14px;
            display:flex;
            gap:14px;
            justify-content:flex-end;
            align-items:flex-end;
            flex-wrap:wrap;
        }
        .wallet-summary{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            display:none;
        }
        .wallet-summary-item{
            border:1px solid #325480;
            border-radius:4px;
            background:#132b48;
            padding:9px 12px;
            min-width:126px;
        }
        .wallet-summary-item span{
            display:block;
            color:#9db9df;
            font-size:.74rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.05em;
        }
        .wallet-summary-item strong{
            display:block;
            margin-top:4px;
            color:#ffffff;
            font-size:1rem;
            font-weight:900;
            line-height:1;
        }
        .wallet-btn-primary{
            border:0;
            border-radius:4px;
            background:linear-gradient(180deg,color-mix(in srgb, var(--pm-brand-primary) 88%, white 12%) 0%, color-mix(in srgb, var(--pm-brand-primary) 72%, #18355d 28%) 100%);
            color:#ffffff;
            min-width:210px;
            padding:12px 20px;
            font-weight:900;
            font-size:1.12rem;
            letter-spacing:.01em;
            transition:.16s ease;
            box-shadow:0 12px 24px rgba(26,78,150,.24);
        }
        .wallet-btn-primary:hover{
            filter:brightness(1.06);
            transform:translateY(-1px);
        }
        .wallet-shell .alert-success{
            color:#e8f2ff;
            background:linear-gradient(180deg,rgba(30,82,154,.22) 0%,rgba(19,51,96,.22) 100%);
            border-color:rgba(93,145,214,.34);
        }
        .wallet-btn-primary:disabled{
            opacity:.5;
            cursor:not-allowed;
            transform:none;
        }
        @media (max-width: 991.98px){
            .wallet-title-block h4{
                font-size:1.62rem;
            }
        }
        @media (max-width: 767.98px){
            .wallet-summary{
                width:100%;
            }
            .wallet-summary-item{
                flex:1 1 calc(50% - 10px);
                min-width:0;
            }
            .wallet-btn-primary{
                width:100%;
                min-width:0;
            }
            .wallet-terms-text{
                margin-left:0;
            }
            .method-chip{
                width:62px;
                height:62px;
            }
            .wallet-title-block h4{
                font-size:1.45rem;
            }
            .method-selected-bar{
                align-items:flex-start;
            }
        }
        @media (prefers-reduced-motion: reduce){
            .method-chip,
            .method-chip::before,
            .method-chip::after,
            .method-icon,
            .method-icon img{
                transition:none !important;
                animation:none !important;
            }
            .method-chip:hover,
            .method-chip:active{
                transform:none;
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
        <div class="container-fluid wallet-shell">
            {if $finance_error != ''}
            <div class="alert alert-danger">{$finance_error}</div>
            {/if}
            {if $finance_success != ''}
            <div class="alert alert-success">{$finance_success}</div>
            {/if}

            <div class="row">
                <div class="col-12">
                    <div class="wallet-card">
                        <div class="wallet-card-body">
                            {if $finance_is_superadmin == 1}
                            <div class="wallet-title-block">
                                <h4>Cuenta administrativa</h4>
                            </div>
                            <div class="wallet-admin-panel">
                                <div class="wallet-admin-tag"><i class="fas fa-shield-alt"></i> Acceso total</div>
                                <h5>Esta cuenta opera con creditos ilimitados</h5>
                                <p>
                                    La recarga de saldo aplica a usuarios con creditos limitados. Mantengo este flujo intacto para los otros roles del sistema,
                                    pero en tu sesion administrativa no corresponde generar una recarga nueva.
                                </p>
                                <div class="wallet-admin-meta">
                                    <span><strong>Saldo:</strong> ilimitado</span>
                                </div>
                                <div class="wallet-admin-actions">
                                    <a class="wallet-admin-btn is-muted" href="{$base_url}index.php?p=finance-history">Ver recargas</a>
                                    <a class="wallet-admin-btn" href="{$base_url}">Volver al panel</a>
                                </div>
                            </div>
                            {else}
                            <div class="wallet-title-block">
                                <h4>Monto a agregar (USD)</h4>
                            </div>
                            <form method="post" action="{$base_url}index.php?p=finance-add" id="financeAddForm" autocomplete="off">
                                <input type="hidden" name="finance_submit" value="1">

                                <div class="form-group mb-3">
                                    <div class="wallet-amount-field">
                                    <div class="wallet-amount-wrap">
                                        <span class="wallet-amount-prefix">$</span>
                                        <input type="number" step="0.01" min="0.01" name="amount_usd" id="amount_usd" class="wallet-amount-input" value="" placeholder="Ingresa el monto" required>
                                    </div>
                                    <div class="quick-amounts" id="quickAmountSuggestions">
                                        {foreach from=$finance_amount_suggestions item=suggested_amount}
                                        <button type="button" class="quick-amount-btn" data-quick-amount="{$suggested_amount}">${$suggested_amount|escape:'html'}</button>
                                        {/foreach}
                                    </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="wallet-label">Metodo de pago</label>
                                    <div class="method-strip-shell">
                                    <div class="method-strip" id="methodStrip">
                                        {if $finance_methods|@count > 0}
                                            {foreach from=$finance_methods item=m}
                                                {assign var=method_icon value='fas fa-credit-card'}
                                                {assign var=method_icon_class value='is-card'}
                                                {assign var=method_brand_class value='brand-card'}
                                                {assign var=method_brand_html value="<i class='fas fa-credit-card'></i>"}
                                                {if $m.provider_key == 'veripagos_qr' || $m.provider_key == 'bnb_qr'}
                                                    {assign var=method_icon value='fas fa-qrcode'}
                                                    {assign var=method_icon_class value='is-qr'}
                                                {/if}
                                                {if $m.provider_key == 'veripagos_qr' || $m.method_key == 'qr_bolivia_auto' || $m.method_key == 'veripagos'}
                                                    {assign var=method_brand_class value='brand-veripagos'}
                                                    {assign var=method_brand_html value="<i class='fas fa-qrcode'></i><span class='brand-badge'>VP</span>"}
                                                {elseif $m.provider_key == 'bnb_qr'}
                                                    {assign var=method_brand_class value='brand-bnb'}
                                                    {assign var=method_brand_html value="<i class='fas fa-qrcode'></i><span class='brand-badge'>BNB</span>"}
                                                {elseif $m.provider_key == 'manual'}
                                                    {assign var=method_icon value='fas fa-university'}
                                                    {assign var=method_icon_class value='is-manual'}
                                                    {assign var=method_brand_class value='brand-manual'}
                                                    {assign var=method_brand_html value="<i class='fas fa-university'></i>"}
                                                {elseif $m.provider_key == 'paypal' || $m.method_key == 'paypal'}
                                                    {assign var=method_brand_class value='brand-paypal'}
                                                    {assign var=method_brand_html value="<b>PayPal</b>"}
                                                {elseif $m.provider_key == 'binance_pay' || $m.provider_key == 'binance_gateway' || $m.provider_key == 'binance_usdt' || $m.method_key == 'binance_pay' || $m.method_key == 'binance_gateway' || $m.method_key == 'binance_usdt'}
                                                    {assign var=method_brand_class value='brand-binance'}
                                                    {assign var=method_brand_html value="<i class='fas fa-coins'></i>"}
                                                {elseif $m.provider_key == 'yape' || $m.method_key == 'yape'}
                                                    {assign var=method_brand_class value='brand-yape'}
                                                    {assign var=method_brand_html value="<b>yape</b>"}
                                                {elseif $m.provider_key == 'mercadopago' || $m.method_key == 'mercadopago'}
                                                    {assign var=method_brand_class value='brand-mercadopago'}
                                                    {assign var=method_brand_html value="<b>MP</b><small>Pago</small>"}
                                                {elseif $m.provider_key == 'stripe' || $m.method_key == 'stripe'}
                                                    {assign var=method_brand_class value='brand-stripe'}
                                                    {assign var=method_brand_html value="<b>S</b>"}
                                                {elseif $m.provider_key == 'cryptomus' || $m.method_key == 'cryptomus'}
                                                    {assign var=method_brand_class value='brand-cryptomus'}
                                                    {assign var=method_brand_html value="<b>CM</b>"}
                                                {/if}
                                                <div class="method-chip-wrap">
                                                    <button
                                                        type="button"
                                                        class="method-chip"
                                                        data-method-id="{$m.id}"
                                                        aria-label="{$m.method_name|escape:'html'}">
                                                        <span class="method-icon {$method_icon_class}">
                                                            {if $m.icon_url != ''}
                                                            <img src="{$m.icon_url|escape:'html'}" alt="{$m.method_name|escape:'html'}">
                                                            {else}
                                                            <span class="method-brand-logo {$method_brand_class}">{$method_brand_html nofilter}</span>
                                                            {/if}
                                                        </span>
                                                    </button>
                                                    <span class="method-tooltip">{$m.method_name}</span>
                                                </div>
                                            {/foreach}
                                        {else}
                                            <div class="method-empty">No hay metodos de pago activos. Configuralos en el panel administrativo.</div>
                                        {/if}
                                    </div>
                                    <div class="method-selected-bar">
                                        <div class="method-selected-pill" id="selectedMethodPill">
                                            <span class="method-selected-icon" id="selectedMethodIcon"></span>
                                            <span class="method-selected-label" id="selectedMethodLabel">Sin metodo seleccionado</span>
                                        </div>
                                        <div class="method-selected-meta" id="selectedMethodMeta">Selecciona un icono para ver el metodo activo.</div>
                                    </div>
                                    </div>

                                    <select
                                        name="method_id"
                                        id="method_id"
                                        class="wallet-select mt-2"
                                        required
                                        {if $finance_methods|@count == 0}disabled{/if}>
                                        <option value="">-- Selecciona un metodo de pago --</option>
                                        {foreach from=$finance_methods item=m}
                                        <option
                                            value="{$m.id}"
                                            data-min="{$m.min_amount}"
                                            data-max="{$m.max_amount}"
                                            data-fixed="{$m.fee_fixed}"
                                            data-percent="{$m.fee_percent}"
                                            data-rate="{$m.rate_bob}"
                                            data-credit-price="{$m.credit_price_usd}"
                                            data-kind="{if $m.provider_key == 'manual'}Transferencia manual{elseif $m.provider_key == 'veripagos_qr' || $m.provider_key == 'bnb_qr' || $m.method_key == 'qr_bolivia_auto' || $m.method_key == 'veripagos'}QR automatico{elseif $m.provider_key == 'paypal' || $m.method_key == 'paypal'}PayPal{elseif $m.provider_key == 'binance_pay' || $m.provider_key == 'binance_gateway' || $m.provider_key == 'binance_usdt' || $m.method_key == 'binance_pay' || $m.method_key == 'binance_gateway' || $m.method_key == 'binance_usdt'}Binance Pay{elseif $m.provider_key == 'yape' || $m.method_key == 'yape'}Yape{elseif $m.provider_key == 'mercadopago' || $m.method_key == 'mercadopago'}MercadoPago{elseif $m.provider_key == 'stripe' || $m.method_key == 'stripe'}Stripe{else}Metodo de pago{/if}">
                                            {$m.method_name}
                                        </option>
                                        {/foreach}
                                    </select>
                                </div>

                                <div id="method_hint" class="wallet-info">
                                    <strong>Informacion:</strong> selecciona un metodo para ver costos, limites y creditos estimados.
                                </div>

                                <div class="wallet-terms">
                                    <label class="wallet-check" for="accept_terms">
                                        <input type="checkbox" id="accept_terms" name="accept_terms" value="1" required>
                                        <span>Acepto los Terminos y Condiciones de recarga.</span>
                                    </label>
                                    <p class="wallet-terms-text">
                                        Reconozco que los fondos agregados son no reembolsables y se usaran dentro de la plataforma.
                                        Cualquier intento de fraude, contracargo o suplantacion puede suspender la cuenta de forma permanente.
                                    </p>
                                </div>

                                <div class="wallet-action-row">
                                    <div class="wallet-summary">
                                        <div class="wallet-summary-item">
                                            <span>Total USD</span>
                                            <strong id="summary_total_usd">$0.00</strong>
                                        </div>
                                        <div class="wallet-summary-item">
                                            <span>Total Bs</span>
                                            <strong id="summary_total_bob">Bs 0.00</strong>
                                        </div>
                                        <div class="wallet-summary-item">
                                            <span>Creditos</span>
                                            <strong id="summary_credits">0</strong>
                                        </div>
                                    </div>
                                    <button
                                        type="submit"
                                        id="finance_submit_btn"
                                        class="wallet-btn-primary"
                                        {if $finance_methods|@count == 0}disabled{/if}>
                                        Continuar al pago
                                    </button>
                                </div>
                            </form>
                            {/if}
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
<script>
(function(){
    var methodSelect = document.getElementById('method_id');
    var amountInput = document.getElementById('amount_usd');
    var hint = document.getElementById('method_hint');
    var quickBtns = document.querySelectorAll('[data-quick-amount]');
    var quickAmountSuggestions = document.getElementById('quickAmountSuggestions');
    var methodChips = document.querySelectorAll('.method-chip[data-method-id]');
    var summaryUsd = document.getElementById('summary_total_usd');
    var summaryBob = document.getElementById('summary_total_bob');
    var summaryCredits = document.getElementById('summary_credits');
    var selectedMethodPill = document.getElementById('selectedMethodPill');
    var selectedMethodIcon = document.getElementById('selectedMethodIcon');
    var selectedMethodLabel = document.getElementById('selectedMethodLabel');
    var selectedMethodMeta = document.getElementById('selectedMethodMeta');
    var reduceMotion = false;

    if (window.matchMedia) {
        reduceMotion = !!window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    if(!methodSelect || !amountInput || !hint){
        return;
    }

    function toNum(value, fallback){
        var n = parseFloat(value);
        return (isNaN(n) || !isFinite(n)) ? fallback : n;
    }

    function money(value){
        return toNum(value, 0).toFixed(2);
    }

    function normalizeAmountText(value){
        return String(value || '').replace(/[^0-9.]/g, '');
    }

    function setActiveChip(methodId){
        methodChips.forEach(function(chip){
            var id = String(chip.getAttribute('data-method-id') || '');
            chip.classList.toggle('active', id === String(methodId || ''));
        });
    }

    function resetChipTilt(chip){
        if(!chip){ return; }
        chip.style.setProperty('--tilt-x', '0deg');
        chip.style.setProperty('--tilt-y', '0deg');
        chip.style.setProperty('--pointer-x', '18%');
        chip.style.setProperty('--pointer-y', '18%');
    }

    function setSelectedMethodVisual(methodId, label){
        var methodIdStr = String(methodId || '');
        var sourceChip = null;
        methodChips.forEach(function(chip){
            if (String(chip.getAttribute('data-method-id') || '') === methodIdStr) {
                sourceChip = chip;
            }
        });

        if (selectedMethodLabel) {
            selectedMethodLabel.textContent = label || 'Sin metodo seleccionado';
        } else if (selectedMethodPill) {
            selectedMethodPill.textContent = label || 'Sin metodo seleccionado';
        }

        if (!selectedMethodIcon) {
            return;
        }

        if (!sourceChip) {
            selectedMethodIcon.innerHTML = '';
            return;
        }

        var iconNode = sourceChip.querySelector('.method-icon');
        selectedMethodIcon.innerHTML = iconNode ? iconNode.innerHTML : '';
    }

    function updateChipTilt(chip, evt){
        if(!chip || !evt || reduceMotion){ return; }
        var rect = chip.getBoundingClientRect();
        if(!rect.width || !rect.height){ return; }

        var px = (evt.clientX - rect.left) / rect.width;
        var py = (evt.clientY - rect.top) / rect.height;

        if(!isFinite(px) || !isFinite(py)){ return; }

        if(px < 0){ px = 0; } else if(px > 1){ px = 1; }
        if(py < 0){ py = 0; } else if(py > 1){ py = 1; }

        var rotateY = (px - 0.5) * 10;
        var rotateX = (0.5 - py) * 10;

        chip.style.setProperty('--tilt-x', rotateX.toFixed(2) + 'deg');
        chip.style.setProperty('--tilt-y', rotateY.toFixed(2) + 'deg');
        chip.style.setProperty('--pointer-x', (px * 100).toFixed(2) + '%');
        chip.style.setProperty('--pointer-y', (py * 100).toFixed(2) + '%');
    }

    function updatePanel(){
        var amount = toNum(amountInput.value, 0);
        if(amount < 0){ amount = 0; }

        var opt = methodSelect.options[methodSelect.selectedIndex];
        if(!opt || !opt.value){
            hint.innerHTML = 'Elige tu método preferido para ver tipo de cambio y total a pagar.';
            methodSelect.classList.remove('has-selection');
            summaryUsd.textContent = '$' + money(amount);
            summaryBob.textContent = 'Bs 0.00';
            summaryCredits.textContent = '0';
            setSelectedMethodVisual('', 'Sin metodo seleccionado');
            if (selectedMethodMeta) { selectedMethodMeta.textContent = 'Selecciona un icono para ver el metodo activo.'; }
            setActiveChip('');
            return;
        }

        var methodName = (opt.textContent || opt.innerText || '').trim();
        var min = toNum(opt.getAttribute('data-min'), 0);
        var max = toNum(opt.getAttribute('data-max'), 0);
        var feeFixed = toNum(opt.getAttribute('data-fixed'), 0);
        var feePercent = toNum(opt.getAttribute('data-percent'), 0);
        var rate = toNum(opt.getAttribute('data-rate'), 0);
        var creditPrice = toNum(opt.getAttribute('data-credit-price'), 1);
        var methodKind = String(opt.getAttribute('data-kind') || 'Metodo de pago').trim();
        if(creditPrice <= 0){ creditPrice = 1; }

        var fee = feeFixed + ((feePercent / 100) * amount);
        var totalUsd = amount + fee;
        var totalBob = totalUsd * rate;
        var credits = Math.floor((amount + 0.000001) / creditPrice);
        if(credits < 0){ credits = 0; }

        var inRange = true;
        if(amount < min){ inRange = false; }
        if(max > 0 && amount > max){ inRange = false; }

        hint.innerHTML =
            '<span style="color:#ffffff; font-weight:800;">Tipo de cambio:</span> ' + money(rate) + ' Bs | 1 USD' +
            '<br><span style="color:#ffffff; font-weight:800;">Total a pagar:</span> $' + money(totalUsd) + ' | Bs ' + money(totalBob) +
            (inRange ? '' : '<br><span style="color:#ffd781; font-weight:800;">Monto fuera de rango permitido.</span>');

        methodSelect.classList.add('has-selection');
        summaryUsd.textContent = '$' + money(totalUsd);
        summaryBob.textContent = 'Bs ' + money(totalBob);
        summaryCredits.textContent = String(credits);
        setSelectedMethodVisual(opt.value, methodName);
        if (selectedMethodMeta) {
            selectedMethodMeta.textContent = methodKind + ' | $' + money(min) + ' - $' + money(max) + ' USD';
        }
        setActiveChip(opt.value);
    }

    methodChips.forEach(function(chip){
        chip.addEventListener('click', function(){
            var id = String(chip.getAttribute('data-method-id') || '');
            if(!id){ return; }
            methodSelect.value = id;
            updatePanel();
        });
        chip.addEventListener('pointermove', function(evt){
            updateChipTilt(chip, evt);
        });
        chip.addEventListener('pointerleave', function(){
            resetChipTilt(chip);
        });
        chip.addEventListener('blur', function(){
            resetChipTilt(chip);
        });
    });

    quickBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var val = toNum(btn.getAttribute('data-quick-amount'), 0);
            if(val <= 0){ return; }
            amountInput.value = val.toFixed(2);
            updatePanel();
            hideQuickAmountSuggestions();
        });
    });

    function hideQuickAmountSuggestions(){
        if(quickAmountSuggestions){
            quickAmountSuggestions.classList.remove('is-visible');
        }
    }

    function filterQuickAmountSuggestions(){
        if(!quickAmountSuggestions || !quickBtns.length){
            return;
        }

        var raw = normalizeAmountText(amountInput.value);
        var visibleCount = 0;

        quickBtns.forEach(function(btn){
            var amountText = normalizeAmountText(btn.getAttribute('data-quick-amount'));
            var show = raw === '' ? true : amountText.indexOf(raw) === 0;
            btn.style.display = show ? '' : 'none';
            if(show){
                visibleCount += 1;
            }
        });

        if(visibleCount > 0){
            quickAmountSuggestions.classList.add('is-visible');
        } else {
            quickAmountSuggestions.classList.remove('is-visible');
        }
    }

    amountInput.addEventListener('focus', filterQuickAmountSuggestions);
    amountInput.addEventListener('click', filterQuickAmountSuggestions);
    amountInput.addEventListener('input', filterQuickAmountSuggestions);
    amountInput.addEventListener('blur', function(){
        window.setTimeout(hideQuickAmountSuggestions, 140);
    });

    methodSelect.addEventListener('change', updatePanel);
    amountInput.addEventListener('input', function(){
        updatePanel();
        filterQuickAmountSuggestions();
    });

    updatePanel();
})();
</script>
</body>
</html>
