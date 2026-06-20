<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:46
  from "C:\xampp\htdocs\panel_web\templates\index.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b2134a7cb8d6_26232027',
  'file_dependency' => 
  array (
    'd5c3a61e857bbecacfb33566c6361731e1e8527d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\index.tpl',
      1 => 1772758800,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:js/lenz_js.tpl' => 1,
  ),
),false)) {
function content_69b2134a7cb8d6_26232027 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php $_smarty_tpl->tpl_vars['pm_index_favicon'] = new Smarty_Variable(((string)$_smarty_tpl->tpl_vars['base_url']->value)."logo/favicon2.png?v=2", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_index_favicon', 0);?>
    <?php if (isset($_smarty_tpl->tpl_vars['panel_favicon_url']->value) && $_smarty_tpl->tpl_vars['panel_favicon_url']->value != '') {?>
        <?php $_smarty_tpl->tpl_vars['pm_index_favicon'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_favicon_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_index_favicon', 0);?>
    <?php }?>
    <link rel="shortcut icon" type="image/png" href="<?php echo $_smarty_tpl->tpl_vars['pm_index_favicon']->value;?>
">
    <title><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 - Página principal</title>
    <!-- OpenGraph SEO meta tags -->
    <meta content="Defeat hackers and spies with best-in-class encryption and leakproofing" name="description">
    <meta content="Defeat hackers and spies with best-in-class encryption and leakproofing"
        name="keywords">
    <meta content="images/banner.jpg" property="og:image">
    <meta content="Defeat hackers and spies with best-in-class encryption and leakproofing" property="og:title">
    <meta content="website" property="og:type">
    <link rel="icon" type="image/png" href="<?php echo $_smarty_tpl->tpl_vars['pm_index_favicon']->value;?>
">
    <link rel="shortcut icon" type="image/png" href="<?php echo $_smarty_tpl->tpl_vars['pm_index_favicon']->value;?>
">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&family=Montserrat:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/css/flipclock.css">
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/css/owl.theme.default.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/css/light.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --bg: #111527;
            --bg-soft: #1d2138;
            --card: #202c49;
            --text: #e2ebfb;
            --muted: #75849d;
            --primary: #4dbad6;
            --secondary: #348cf4;
            --accent: #7b48e8;
            --danger: #7b48e8;
            --border: #2d3b5c;
            --glow-cyan: rgba(77, 186, 214, 0.24);
            --glow-indigo: rgba(123, 72, 232, 0.2);
            --panel-grad: linear-gradient(155deg, rgba(20, 34, 67, 0.95) 0%, rgba(29, 53, 95, 0.93) 52%, rgba(47, 37, 96, 0.93) 100%);
            --panel-border: rgba(80, 188, 255, 0.24);
        }

        body.dark {
            font-family: 'Inter', 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            color: var(--text);
            position: relative;
            overflow-x: hidden;
            background: radial-gradient(circle at top left, rgba(77, 186, 214, 0.22), transparent 34%),
                radial-gradient(circle at top right, rgba(123, 72, 232, 0.16), transparent 36%),
                linear-gradient(180deg, #1a2040 0%, #141b36 34%, #111527 100%),
                var(--bg) !important;
        }

        body.dark::before,
        body.dark::after {
            content: "";
            position: fixed;
            pointer-events: none;
            z-index: 0;
            filter: blur(64px);
            opacity: 0.56;
            border-radius: 999px;
            animation: ambient-float 12s ease-in-out infinite alternate;
        }

        body.dark::before {
            width: 260px;
            height: 260px;
            left: -80px;
            top: 18%;
            background: radial-gradient(circle, var(--glow-cyan) 0%, rgba(0, 229, 255, 0) 72%);
        }

        body.dark::after {
            width: 220px;
            height: 220px;
            right: -70px;
            top: 42%;
            background: radial-gradient(circle, var(--glow-indigo) 0%, rgba(123, 72, 232, 0) 72%);
            animation-duration: 15s;
            animation-name: ambient-float-reverse;
        }

        body.dark > * {
            position: relative;
            z-index: 1;
        }

        @keyframes ambient-float {
            0% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            100% {
                transform: translate3d(16px, -14px, 0) scale(1.06);
            }
        }

        @keyframes ambient-float-reverse {
            0% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            100% {
                transform: translate3d(-14px, 12px, 0) scale(1.05);
            }
        }

        h1,
        h2,
        h3,
        h4,
        .section-title h3,
        .section-title h4 {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            letter-spacing: 0.1px;
        }

        p,
        a,
        li,
        button,
        small {
            font-family: 'Inter', 'Poppins', sans-serif;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .navbar.navbar-dark {
            background: linear-gradient(155deg, #101a36 0%, #1a2c55 46%, #2d2758 100%) !important;
            border-bottom: 1px solid rgba(77, 186, 214, 0.24);
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        .navbar-dark .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-right: 0;
        }

        .navbar-brand-text {
            font-family: 'Montserrat', 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.55rem;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            line-height: 1;
            background: none;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff;
            text-shadow: none;
        }

        .navbar-dark .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.36) !important;
        }

        .navbar-dark .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(236,241,252,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='3' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
            background-size: 24px 24px;
            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.2));
        }

        @media (max-width: 991.98px) {
            #navbarSupportedContent.collapsing {
                transition: height 0.1s ease !important;
            }
        }

        .site-header {
            position: relative;
            isolation: isolate;
        }

        .site-header::before {
            content: none;
        }

        .site-header > * {
            position: relative;
            z-index: 1;
        }

        .site-header .hero {
            background: var(--panel-grad) !important;
        }

        #features,
        #help-center {
            scroll-margin-top: 68px;
        }

        .section.feature-highlight,
        .help-center-section,
        footer.section.section-separated.py-lg {
            background: radial-gradient(120% 130% at 50% 0%, rgba(77, 186, 214, 0.14), rgba(77, 186, 214, 0) 58%),
                linear-gradient(165deg, #121526 0%, #1a2242 50%, #121629 100%) !important;
        }

        .section.section-highlight {
            background: radial-gradient(120% 130% at 50% 0%, rgba(77, 186, 214, 0.14), rgba(77, 186, 214, 0) 58%),
                var(--panel-grad) !important;
        }

        .section.section-highlight.discount-fusion {
            position: relative;
            overflow: hidden;
            padding-top: 0.8rem !important;
            margin-top: 0;
            background:
                radial-gradient(120% 140% at 12% -18%, rgba(77, 186, 214, 0.14), rgba(77, 186, 214, 0) 56%),
                radial-gradient(120% 140% at 92% 116%, rgba(123, 72, 232, 0.14), rgba(123, 72, 232, 0) 58%),
                linear-gradient(165deg, #121526 0%, #1a2242 50%, #121629 100%) !important;
            border-top: none !important;
            border-bottom: none !important;
        }

        .discount-fusion::before {
            content: none;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 0;
            pointer-events: none;
            background: none;
        }

        .discount-fusion::after {
            content: none;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 0;
            pointer-events: none;
            background: none;
        }

        .discount-fusion .section-title {
            margin-top: 1.6rem;
        }

        .help-center-section,
        footer.section.section-separated.py-lg {
            border-top: 1px solid rgba(77, 186, 214, 0.14);
            border-bottom: 1px solid rgba(77, 186, 214, 0.12);
        }

        .help-center-section.is-hidden {
            display: none !important;
        }

        .hero {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
            padding: 8px 18px 20px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 14px 36px rgba(8, 12, 24, 0.44), 0 0 34px rgba(77, 186, 214, 0.22), 0 0 30px rgba(123, 72, 232, 0.2);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 18% -12%, rgba(77, 186, 214, 0.32), rgba(77, 186, 214, 0) 54%);
            z-index: 0;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 88% 12%, rgba(123, 72, 232, 0.28), rgba(123, 72, 232, 0) 46%);
            z-index: 0;
        }

        .hero h1,
        .hero p,
        .hero .hero-actions,
        .section-title h3,
        .section-title h4,
        .featured-item-title,
        .feature-item p,
        .footer__info--text,
        .copyright__text {
            color: var(--text) !important;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: clamp(2rem, 5.5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.16;
            margin-bottom: 6px;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(8, 12, 24, 0.35);
        }

        .hero .title-accent {
            display: inline-block;
            background: linear-gradient(90deg, #7a5dff 0%, #4ab8ff 55%, #7fd3ff 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent !important;
            text-shadow: none;
        }

        .hero .lead {
            max-width: 760px;
            margin-bottom: 0.8rem;
            line-height: 1.45;
            color: #f2f7ff !important;
            text-shadow: none;
        }

        .landing-nav-link {
            color: var(--text) !important;
            font-weight: 700 !important;
            opacity: 1 !important;
            letter-spacing: 0.2px;
        }

        .landing-nav-link:hover {
            color: var(--primary) !important;
            text-shadow: 0 0 10px rgba(77, 186, 214, 0.42);
        }

        .hero-actions,
        .download-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .btn-action-danger,
        .btn-action-cyan {
            border-radius: 10px;
            padding: 11px 18px;
            border: 1px solid transparent;
            font-weight: 700;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
        }

        .btn-action-danger {
            background: linear-gradient(90deg, rgba(77, 186, 214, 0.96), rgba(52, 140, 244, 0.96));
            color: #fff !important;
            border-color: rgba(77, 186, 214, 0.9);
            box-shadow: 0 0 0 1px rgba(77, 186, 214, 0.24), 0 0 16px rgba(77, 186, 214, 0.28);
        }

        .btn-action-danger:hover {
            color: #fff !important;
            box-shadow: 0 0 0 1px rgba(77, 186, 214, 0.35), 0 0 20px rgba(77, 186, 214, 0.45);
            transform: translateY(-1px);
        }

        .btn-action-cyan {
            background: rgba(123, 72, 232, 0.1);
            color: var(--text) !important;
            border-color: rgba(123, 72, 232, 0.58);
            box-shadow: 0 0 0 1px rgba(123, 72, 232, 0.2), 0 0 14px rgba(123, 72, 232, 0.18);
        }

        .btn-action-cyan:hover {
            background: rgba(123, 72, 232, 0.2);
            color: var(--text) !important;
            box-shadow: 0 0 0 1px rgba(123, 72, 232, 0.35), 0 0 18px rgba(123, 72, 232, 0.35);
            transform: translateY(-1px);
        }

        .btn.btn-outline-secondary {
            color: var(--text) !important;
            border-color: rgba(52, 140, 244, 0.78) !important;
            background-color: rgba(10, 20, 44, 0.2) !important;
            background-image: none !important;
            box-shadow: 0 0 0 1px rgba(52, 140, 244, 0.3), 0 8px 22px rgba(52, 140, 244, 0.18), 0 0 10px rgba(52, 140, 244, 0.12);
            transition: color 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
        }

        .btn.btn-outline-secondary:hover {
            border-color: rgba(52, 140, 244, 0.95) !important;
            background-color: #5d72ff !important;
            background-image: linear-gradient(90deg, #6b62ff 0%, #33bfff 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 1px rgba(52, 140, 244, 0.45), 0 10px 26px rgba(52, 140, 244, 0.32), 0 0 22px rgba(64, 161, 255, 0.3);
        }

        .btn.btn-outline-secondary:focus,
        .btn.btn-outline-secondary:not(:disabled):not(.disabled):active {
            background-color: #5d72ff !important;
            background-image: linear-gradient(90deg, #6b62ff 0%, #33bfff 100%) !important;
            color: #ffffff !important;
        }

        .hero .btn.btn-outline-secondary {
            position: relative;
            z-index: 3;
        }

        .graph img {
            opacity: 0.88;
            filter: saturate(1.06) brightness(0.98) contrast(1.02);
        }

        .graph,
        .graph img {
            pointer-events: none;
        }

        .feature-highlight .row > [class*="col-"] {
            display: flex;
            margin-bottom: 24px;
        }

        .feature-item {
            background: var(--panel-grad);
            border: 1px solid var(--panel-border);
            border-radius: 14px;
            padding: 22px 18px;
            min-height: 220px;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 24px rgba(11, 16, 32, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-item::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 22% -24%, rgba(77, 186, 214, 0.2), rgba(77, 186, 214, 0) 56%);
        }

        .feature-item::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 88% 118%, rgba(123, 72, 232, 0.16), rgba(123, 72, 232, 0) 48%);
        }

        .feature-item:hover {
            transform: translateY(-1px);
            border-color: rgba(77, 186, 214, 0.4);
            box-shadow: 0 12px 28px rgba(11, 16, 32, 0.56), 0 0 18px rgba(77, 186, 214, 0.14);
        }

        .featured-item-icon {
            width: 86px;
            height: 72px;
            max-width: none;
            object-fit: contain;
            margin: 0 auto 12px;
            display: block;
        }

        .featured-item-title {
            min-height: 3.8em;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            text-align: center;
            line-height: 1.25;
        }

        .feature-item p {
            margin-top: 0;
            text-align: left;
            line-height: 1.55;
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: var(--primary);
            background: rgba(0, 229, 255, 0.1);
            border: 1px solid rgba(77, 186, 214, 0.5);
            box-shadow: 0 0 10px rgba(77, 186, 214, 0.22);
            font-size: 19px;
        }

        .help-center-section .section-title h3 {
            font-family: 'Inter', 'Poppins', sans-serif;
            font-weight: 400 !important;
            letter-spacing: 0;
            text-shadow: none !important;
            -webkit-font-smoothing: antialiased;
        }

        .download-note {
            color: var(--muted);
            margin-top: 12px;
            margin-bottom: 0;
        }

        footer.section.section-separated.py-lg .footer__info--text,
        footer.section.section-separated.py-lg .copyright__text,
        footer.section.section-separated.py-lg .copyright__text small {
            color: var(--muted) !important;
        }

        .footer-minimal {
            background: linear-gradient(165deg, #121526 0%, #1a2242 50%, #121629 100%) !important;
            padding: 2.4rem 0 2.8rem !important;
            margin-top: -1px;
            position: relative;
            overflow: hidden;
        }

        footer.section.section-separated.py-lg.footer-minimal {
            border-top: none !important;
        }

        .footer-minimal::before {
            content: none;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 0;
            pointer-events: none;
            background: none;
        }

        .footer-minimal .container {
            max-width: 760px;
        }

        .footer-minimal-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
        }

        .footer-minimal-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .footer-minimal-brand img {
            height: 28px;
            width: auto;
        }

        .footer-minimal-title {
            font-family: 'Montserrat', 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: 0.6px;
            color: #ffffff;
            line-height: 1;
        }

        .footer-minimal-text {
            margin: 0;
            max-width: 560px;
            font-size: 1.02rem;
            line-height: 1.6;
        }

        .footer-minimal .copyright__text {
            margin-top: 8px;
        }

        .footer-minimal .footer__info--text,
        .footer-minimal .copyright__text,
        .footer-minimal .copyright__text small {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        footer.section.section-separated.py-lg.footer-minimal .footer__info--text,
        footer.section.section-separated.py-lg.footer-minimal .copyright__text,
        footer.section.section-separated.py-lg.footer-minimal .copyright__text small {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-item {
            background: var(--panel-grad);
            border: 1px solid var(--panel-border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(11, 16, 32, 0.34);
            position: relative;
        }

        .faq-item::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(77, 186, 214, 0), rgba(77, 186, 214, 0.6), rgba(77, 186, 214, 0));
            opacity: 0.65;
            pointer-events: none;
        }

        .faq-trigger {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--text);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            font-size: 21px;
            line-height: 1.25;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
        }

        .faq-trigger:hover {
            background: linear-gradient(90deg, rgba(77, 186, 214, 0.12), rgba(123, 72, 232, 0.1));
            box-shadow: inset 0 0 0 1px rgba(77, 186, 214, 0.24), 0 0 16px rgba(77, 186, 214, 0.09);
        }

        .faq-plus {
            color: var(--primary);
            font-size: 40px;
            line-height: 1;
            font-weight: 300;
            text-shadow: 0 0 6px rgba(77, 186, 214, 0.34);
            transition: transform 0.2s ease;
        }

        .faq-trigger[aria-expanded="true"] .faq-plus {
            transform: rotate(45deg);
        }

        .faq-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.28s ease;
            border-top: 1px solid rgba(77, 186, 214, 0.14);
            background: rgba(24, 33, 56, 0.92);
        }

        .faq-panel-inner {
            padding: 14px 22px 22px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
            font-weight: 400;
        }

        .faq-panel-inner p,
        .faq-panel-inner ul {
            margin-bottom: 12px;
        }

        .faq-panel-inner p:last-child,
        .faq-panel-inner ul:last-child {
            margin-bottom: 0;
        }

        .faq-panel-inner ul {
            padding-left: 20px;
        }

        .faq-panel-inner li {
            margin-bottom: 4px;
        }

        @media (max-width: 992px) {
            .hero {
                padding: 8px 16px 16px;
            }

            .faq-trigger {
                font-size: 19px;
                padding: 16px 18px;
            }

            .faq-plus {
                font-size: 34px;
            }

            .faq-panel-inner {
                padding: 12px 18px 16px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: clamp(1.7rem, 8.4vw, 2.3rem);
            }

            .navbar-brand-text {
                font-size: 1.2rem;
                letter-spacing: 0.7px;
            }

            .featured-item-icon {
                width: 72px;
                height: 60px;
            }

            .featured-item-title {
                min-height: 0;
            }

            .faq-trigger {
                font-size: 18px;
            }

            .faq-plus {
                font-size: 28px;
            }

            .faq-panel-inner {
                font-size: 15px;
            }
        }
    </style>
</head>

<body class="dark" style="background: #05060A;">


    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #05060A;">
        <a class="navbar-brand" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
">
            <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/images/vicath2.png"
     style="height:40px;width:auto;"
     alt="logo">
            <span class="navbar-brand-text">PROGRAMMIT</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item active">
                    <a class="nav-link landing-nav-link" href="#features">Características</a>
                </li>
             <!--   <li class="nav-item">
                    <a class="nav-link" href="#pricing">Teams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#feedbacks">Feedbacks</a>
                </li> -->
                <li class="nav-item">
                    
                    <a class="nav-link landing-nav-link" href="#help-center">Centro de Ayuda</a>
                </li>
                <!--li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Dropdown
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </li-->
            </ul>
        </div>
    </nav>

    <div class="site-header  d-flex flex-column align-items-center justify-content-between mb-0">
        <div class="hero" style="background: #05060A;">
            <h1><span class="title-accent">PROGRAMMIT VPN</span> es la VPN número 1 en América Latina</h1>
            <p class="lead mt-3 mx-auto">Es la red privada virtual más rápida y escalable, lo que permite la protección en tiempo real
                y seguridad en cualquier parte del mundo.</p>
            <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value > 0) {?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=dashboard" class="btn btn-outline-secondary mt-3">INICIAR SESION
                <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/right-arrow.svg" alt="arrow">
            </a>
            <?php } else { ?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=login" class="btn btn-outline-secondary mt-3">INICIAR SESION
                <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/right-arrow.svg" alt="arrow">
            </a>
            <?php }?>
        </div>

        <div class="graph">
            <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/graph-dark.svg" alt="graph" class="img-fluid">
        </div>

        <div class="text-center">
            <!--h4 class="centered-text section-highlight-text ">Built for enterprise use, Coin offers banks and
                payment providers a reliable, on-demand option to source liquidity
                for cross-border payments.</h4>
            <a href="#" class="btn btn-outline-secondary">Subscribe</a-->
        </div>

    </div>
    
   <!-- <section class="section section-highlight" id="news">
        <div class="container">

            <div class="section-title max-title mb-5">
                <h3>Descarga de la APLICACIÓN aquí</h3>
            </div>

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-10">
                    <div class="testimonials mt-lg">
                        <div class="news-slider owl-carousel owl-theme">
                                <?php
$_from = $_smarty_tpl->tpl_vars['downloads']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_i_0_saved_item = isset($_smarty_tpl->tpl_vars['i']) ? $_smarty_tpl->tpl_vars['i'] : false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['i']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
$__foreach_i_0_saved_local_item = $_smarty_tpl->tpl_vars['i'];
?> <?php echo $_smarty_tpl->tpl_vars['i']->value;?>
 <?php
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_local_item;
}
if ($__foreach_i_0_saved_item) {
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_item;
}
?> 
                        </div>

                    </div>
                </div>
            </div>


        </div>
        <!-- // container -->
    </section>
    <!-- // section -->

    <section class="section feature-highlight" id="features">
        <div class="container">
            <div class="section-title mini-title">
                <h4>Elige <?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
</h4>
            </div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="feature-item">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/feature-one.svg" alt="icon" class="featured-item-icon">
                        <h4 class="featured-item-title">Potente protección en línea</h4>
                        <p>Derrota a piratas informáticos y espías con el mejor cifrado y protección contra fugas de su clase.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-item">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/feature-two.svg" alt="icon" class="featured-item-icon">
                        <h4 class="featured-item-title">Internet sin fronteras</h4>
                        <p>Acceda a cualquier contenido, sin importar su ubicación. Dile adiós a los geobloques.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-item">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/feature-three.svg" alt="icon" class="featured-item-icon">
                        <h4 class="featured-item-title">Soporte de chat en vivo las 24 horas</h4>
                        <p>Los seres humanos reales están disponibles las 24 horas, los 7 días de la semana por correo electrónico y chat de Facebook para ayudarlo con la configuración y la resolución de problemas.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- // container -->
    </section>

    <section class="section section-highlight discount-fusion">
        <div class="container">


            <div class="section-title max-title">
                <h3>Descuento de cuenta VPN</h3>
            </div>
            <p class="lead p-3 centered-text text-center mx-auto"><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 ofrece un descuento único para la cuenta de VPN.</p>

            <div class="text-center">
                <div class="countdown-clock" data-datetime="December 31, 2020 00:50:00"></div>
            </div>

            <div class="text-center mt-5">
                <div class="text-muted my-3">
                    Oferta limitada: después de que finalice la venta, el precio de las cuentas VPN volverá a su precio original.
                </div>
            </div>
        </div>
        <!-- // container -->
    </section>
    <!-- // section -->


    <!--section class="section" id="feedbacks">
        <div class="container">

            <div class="section-title max-title mb-5">
                <h3>Our Happy Customers</h3>
            </div>

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-10">
                    <div class="testimonials mt-lg">
                        <div class="testimonial-slider owl-carousel owl-theme">
                            <div class="slider__item">
                                <div class="user-thumbnail">
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/user-thumb.jpg" alt="user" class="img-fluid">
                                </div>
                                <blockquote class="blockquote text-center">

                                    <h6 class="text-light text-uppercase">
                                        Helen Wade • United States
                                    </h6>

                                    <h4 class="mb-0"> Just wanted to say that I am very happy with all the services you
                                        have provided regarding
                                        the miners.
                                    </h4>

                                </blockquote>
                            </div>
                            <div class="slider__item">
                                <div class="user-thumbnail">
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/user-thumb.jpg" alt="user" class="img-fluid">
                                </div>
                                <blockquote class="blockquote text-center">

                                    <h6 class="text-light text-uppercase">
                                        Helen Wade • United States
                                    </h6>

                                    <h4 class="mb-0">I am very happy with all the services you have provided regarding
                                        the miners. Just wanted
                                        to say that.
                                    </h4>

                                </blockquote>
                            </div>
                            <div class="slider__item">
                                <div class="user-thumbnail">
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/user-thumb.jpg" alt="user" class="img-fluid">
                                </div>
                                <blockquote class="blockquote text-center">

                                    <h6 class="text-light text-uppercase">
                                        Helen Wade • United States
                                    </h6>

                                    <h4 class="mb-0"> Just wanted to say that I am very happy with all the services you
                                        have provided regarding
                                        the miners.
                                    </h4>

                                </blockquote>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </section-->
    <!-- // section -->


<!--    <section class="section" id="pricing">
        <div class="container">

            <div class="section-title max-title mb-5">
                <h3>Our Team Members</h3>
            </div>

            <div class="row text-center">
                <div class="col-12 col-md-12 col-lg-4 mb-5">
                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/team_1.jpg" alt="team" class="img-fluid mb-4">
                    <a href="https://www.facebook.com/105693651095251">
                    <h4 class="mb-0">Zel Magisa</h4>
                    <small class="text-light text-uppercase">ADMIN &amp; CEO</small>
                </div>
                <div class="col-12 col-md-12 col-lg-4 mb-5">
                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/team_4.jpg" alt="team" class="img-fluid mb-4">
                    <a href="https://www.facebook.com/Vicath-VPN-Official-116511013588943/">
                    <h4 class="mb-0">Fatima Sheena Acarab </h4>
                    <small class="text-light text-uppercase">ADMIN</small>
                </div>
                <div class="col-12 col-md-12 col-lg-4 mb-5">
                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/team_3.jpg" alt="team" class="img-fluid mb-4">
                    <a href="https://www.facebook.com/Vicath-VPN-Vic-102768634978393/">
                    <h4 class="mb-0">Nainah Vidal Jacob</h4>
                    <small class="text-light text-uppercase">ADMIN</small>
                    </div>
                <div class="col-12 col-md-12 col-lg-4 mb-5">
                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/images/team_2.jpg" alt="team" class="img-fluid mb-4">
                    <a href="https://www.facebook.com/moinajomar/">
                    <h4 class="mb-0">Jomar Ostonal Moina</h4>
                    <small class="text-light text-uppercase">ADMIN</small>
                </div>
            </div>

        </div>
        <!-- // container -->
    </section>
    <!-- // section -->

    <section class="section help-center-section is-hidden" id="help-center">
        <div class="container">
            <div class="section-title max-title mb-5">
                <h3>Centro de Ayuda</h3>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Qué es PROGRAMMMIT VPN?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>PROGRAMMMIT VPN es una aplicación que te permite navegar de forma privada, segura y rápida, conectándote a servidores en distintas regiones del mundo.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Cómo descargo PROGRAMMMIT VPN?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Puedes descargar la app desde el enlace oficial disponible en esta página. Si no lo ves, solicítalo por soporte.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Cómo activo mi cuenta o Token ID?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Ingresa tu Token ID en la aplicación y presiona Conectar. Si tu token está activo, la VPN se conectará automáticamente.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Qué hago si no conecta o aparece “Desconectado”?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Prueba lo siguiente:</p>
                            <ul>
                                <li>Revisa tu internet (WiFi o datos).</li>
                                <li>Presiona Actualizar.</li>
                                <li>Cambia de servidor.</li>
                            </ul>
                            <p>Si continúa, contacta soporte con tu Token ID.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Qué significa “Offline” en el panel o servidor?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Significa que el servidor o cuenta no está disponible temporalmente. Puede ser por mantenimiento, vencimiento o actualización.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Cómo renuevo mi suscripción?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Las renovaciones se realizan a través del reseller o administrador asignado. Si no sabes quién es, contacta soporte con tu Token ID.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Puedo usar la VPN en varios dispositivos?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Depende del plan configurado. En la mayoría de casos, 1 Token = 1 dispositivo para mayor estabilidad.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>Olvidé mi acceso al panel, ¿qué hago?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Contacta soporte enviando:</p>
                            <ul>
                                <li>Usuario o correo registrado.</li>
                                <li>Nombre del reseller (si aplica).</li>
                                <li>Captura del error (si aparece).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Cómo adquirir un panel para revender?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Si deseas un panel para administrar clientes y suscripciones, comunícate con soporte para conocer planes y activación.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-trigger" type="button" aria-expanded="false">
                        <span>¿Dónde puedo contactar soporte oficial?</span>
                        <span class="faq-plus" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-panel">
                        <div class="faq-panel-inner">
                            <p>Puedes comunicarte por Telegram, WhatsApp o Email. Usa solo canales oficiales para evitar estafas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="section section-separated py-lg footer-minimal">
        <div class="container">
            <div class="footer-minimal-inner">
                <div class="footer-minimal-brand">
                    <img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/images/vicath2.png" alt="logo-large">
                    <span class="footer-minimal-title"><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
</span>
                </div>
                <p class="footer__info--text footer-minimal-text"><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 es el activo digital más rápido y escalable, que permite pagos globales en tiempo real en cualquier lugar del mundo.</p>
                <p class="copyright__text mb-0"><small>Copyright ©
                        <?php echo '<script'; ?>
>
                            document.write(new Date().getFullYear());
                        <?php echo '</script'; ?>
>. Todos los Derechos Reservados. <?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 LLC</small></p>
            </div>
        </div>
        <!-- // container -->
    </footer>
    <!-- // section -->


    <!-- JavaScript -->
    <!-- jQuery & Bootstrap -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/js/jquery-3.3.1.slim.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/js/bootstrap.bundle.min.js"><?php echo '</script'; ?>
>
    <!-- Plugin JS -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/js/plugins/flipclock.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/js/plugins/chart.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
/firenet/lenzpogi/js/plugins/owl.carousel.min.js"><?php echo '</script'; ?>
>
    <!-- Custom JS -->
    <?php echo '<script'; ?>
 src="https://use.fontawesome.com/09c05be15f.js"><?php echo '</script'; ?>
>
    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/lenz_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php echo '<script'; ?>
>
        document.addEventListener('DOMContentLoaded', function () {
            var triggers = document.querySelectorAll('.faq-trigger');
            triggers.forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    var isOpen = trigger.getAttribute('aria-expanded') === 'true';
                    trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    var panel = trigger.nextElementSibling;
                    if (!panel) {
                        return;
                    }
                    panel.style.maxHeight = isOpen ? '0px' : panel.scrollHeight + 'px';
                });
            });

            var helpCenterSection = document.getElementById('help-center');
            var helpCenterLinks = document.querySelectorAll('a[href="#help-center"]');

            var scrollToHelpCenter = function () {
                if (!helpCenterSection) {
                    return;
                }
                var navbar = document.querySelector('.navbar.navbar-dark');
                var navbarHeight = navbar ? Math.ceil(navbar.getBoundingClientRect().height) : 0;
                var headingTarget = helpCenterSection.querySelector('.section-title') || helpCenterSection;
                var extraOffset = 18;
                var targetTop = headingTarget.getBoundingClientRect().top + window.pageYOffset - navbarHeight - extraOffset;
                window.scrollTo({
                    top: Math.max(targetTop, 0),
                    behavior: 'smooth'
                });
            };

            var showHelpCenter = function (shouldScroll) {
                if (!helpCenterSection) {
                    return;
                }
                helpCenterSection.classList.remove('is-hidden');
                if (shouldScroll) {
                    window.scrollBy({ top: -1, left: 0, behavior: 'auto' });
                    requestAnimationFrame(function () {
                        scrollToHelpCenter();
                    });
                }
            };

            if (window.location.hash === '#help-center') {
                showHelpCenter(false);
            }

            helpCenterLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    showHelpCenter(true);
                });
            });

            var navbarCollapse = document.getElementById('navbarSupportedContent');
            var navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarCollapse && navbarToggler) {
                var closeNavbarCollapse = function () {
                    if (!navbarCollapse.classList.contains('show')) {
                        return;
                    }
                    if (window.jQuery) {
                        window.jQuery(navbarCollapse).collapse('hide');
                    } else {
                        navbarCollapse.classList.remove('show');
                    }
                };

                document.addEventListener('click', function (event) {
                    var clickedInsideMenu = navbarCollapse.contains(event.target);
                    var clickedToggler = navbarToggler.contains(event.target);
                    if (!clickedInsideMenu && !clickedToggler && navbarCollapse.classList.contains('show')) {
                        closeNavbarCollapse();
                    }
                });

                navbarCollapse.addEventListener('click', function (event) {
                    var navLink = event.target.closest('a.nav-link');
                    if (!navLink) {
                        return;
                    }
                    if (navLink.classList.contains('dropdown-toggle')) {
                        return;
                    }
                    if (window.innerWidth < 992) {
                        closeNavbarCollapse();
                    }
                });
            }

            window.addEventListener('resize', function () {
                document.querySelectorAll('.faq-trigger[aria-expanded="true"]').forEach(function (trigger) {
                    var panel = trigger.nextElementSibling;
                    if (panel) {
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    }
                });
            });
        });
    <?php echo '</script'; ?>
>
</body>

</html>
<?php }
}
