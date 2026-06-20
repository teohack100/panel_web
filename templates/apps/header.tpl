        <meta charset="utf-8" />
        <title>Metrica - Responsive Bootstrap 4 Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        {assign var=pm_header_favicon value="`$base_url`logo/favicon2.png?v=2"}
        {if isset($panel_favicon_url) && $panel_favicon_url neq ''}
            {assign var=pm_header_favicon value=$panel_favicon_url}
        {/if}
        <link rel="icon" type="image/png" href="{$pm_header_favicon}">
        <link rel="shortcut icon" type="image/png" href="{$pm_header_favicon}">

        <link href="../assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

        <!-- App css -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
