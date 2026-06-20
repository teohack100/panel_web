<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{$siteTitle} - Acceso no disponible</title>
    <link rel="icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">
    <link rel="shortcut icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
{literal}
        :root{
            --bg0:#173154;
            --bg1:#262f73;
            --card:#1f2f4f;
            --line:rgba(109,156,235,.34);
            --text:#ecf3ff;
            --muted:#bed1ef;
            --ok:#44d27a;
            --warn:#f3b019;
            --btn:#338dff;
            --btn2:#1f2c4e;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            min-height:100vh;
            font-family:'Nunito Sans',sans-serif;
            color:var(--text);
            background:
                radial-gradient(980px 520px at -20% -10%, rgba(75,152,255,.2), transparent 62%),
                radial-gradient(900px 560px at 120% 110%, rgba(111,67,255,.18), transparent 60%),
                linear-gradient(150deg, var(--bg0) 0%, var(--bg1) 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px 16px;
        }

        .lock-card{
            width:100%;
            max-width:560px;
            border:1px solid var(--line);
            border-radius:10px;
            background:linear-gradient(180deg,#21385e 0%, var(--card) 100%);
            box-shadow:0 24px 56px rgba(6,14,35,.48);
            padding:30px 28px;
        }

        .lock-icon{
            width:64px;
            height:64px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(255,86,114,.15);
            color:#ff5672;
            margin:0 auto 14px;
            font-size:1.7rem;
        }

        h1{
            margin:0;
            font-family:'Montserrat',sans-serif;
            font-size:2rem;
            line-height:1.1;
            text-align:center;
        }

        .subtitle{
            margin:10px auto 0;
            max-width:460px;
            text-align:center;
            color:var(--muted);
            font-size:1.04rem;
            line-height:1.5;
            font-weight:700;
        }

        .meta{
            margin:22px 0 16px;
            padding:12px 14px;
            border:1px solid var(--line);
            border-radius:8px;
            background:rgba(7,20,45,.24);
            display:flex;
            gap:18px;
            flex-wrap:wrap;
            justify-content:center;
        }

        .meta-item{
            font-size:.98rem;
            font-weight:700;
            color:#dce9ff;
        }

        .meta-item b{
            color:#ffffff;
        }

        .benefits{
            margin:0;
            padding:0;
            list-style:none;
            display:grid;
            gap:8px;
        }

        .benefits li{
            display:flex;
            align-items:center;
            gap:10px;
            color:#dce9ff;
            font-weight:700;
        }

        .benefits i{
            color:var(--ok);
            width:18px;
            text-align:center;
        }

        .actions{
            margin-top:24px;
            display:grid;
            gap:10px;
        }

        .btn{
            border:0;
            border-radius:8px;
            padding:12px 14px;
            text-align:center;
            text-decoration:none;
            font-weight:800;
            font-size:1rem;
            transition:transform .14s ease,opacity .14s ease;
        }

        .btn:hover{transform:translateY(-1px)}

        .btn-main{
            color:#fff;
            background:linear-gradient(14deg,#338dff 36%,#56c2ff 100%);
            box-shadow:0 7px 20px rgba(51,141,255,.35);
        }

        .btn-soft{
            color:#b9d6ff;
            background:rgba(7,20,45,.25);
            border:1px solid rgba(68,185,255,.45);
        }

        .note{
            margin-top:12px;
            text-align:center;
            color:#a7c0e7;
            font-size:.92rem;
            font-weight:700;
        }

        .crown{
            color:var(--warn);
            margin-right:6px;
        }

        @media (max-width:560px){
            .lock-card{padding:24px 18px}
            h1{font-size:1.7rem}
        }
{/literal}
    </style>
</head>
<body>
    <div class="lock-card">
        <div class="lock-icon"><i class="fas fa-lock"></i></div>
        <h1>{$lock_title}</h1>
        <p class="subtitle">{$lock_subtitle}</p>

        <div class="meta">
            <div class="meta-item"><span class="crown"><i class="fas fa-user"></i></span>Cuenta: <b>{$lock_user}</b></div>
            <div class="meta-item"><span class="crown"><i class="fas fa-coins"></i></span>Creditos: <b>{$lock_credits}</b></div>
            <div class="meta-item"><span class="crown"><i class="fas fa-database"></i></span>Estado DB: <b>{$lock_reason}</b></div>
        </div>

        <ul class="benefits">
            <li><i class="fas fa-check-circle"></i>Activacion de panel de gestion de clientes</li>
            <li><i class="fas fa-check-circle"></i>Permiso para crear, editar y renovar usuarios</li>
            <li><i class="fas fa-check-circle"></i>Acceso completo segun tu rol asignado</li>
        </ul>

        <div class="actions">
            <a class="btn btn-main" href="{$base_url}index.php?p=finance-add">Agregar saldo ahora</a>
            <a class="btn btn-soft" href="{$base_url}index.php?p=support">Ver planes disponibles</a>
            <a class="btn btn-soft" href="{$base_url}index.php?p=logout">Cerrar sesion</a>
        </div>

        <div class="note">Una vez activado tu plan, el acceso se habilita automaticamente.</div>
    </div>
</body>
</html>
