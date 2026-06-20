# UI Stable Bundle

Este bundle congela la combinacion buena del UI del panel para no volver a mezclar archivos de horas distintas.

Archivos que deben restaurarse juntos:

- `templates/apps/sidenavi.tpl`
- `templates/apps/topnav.tpl`
- `firenet/assets/js/app.js`

Origen del estado estable:

- `sidenavi.tpl`: `credit_history_menu_label_20260312_140633`
- `topnav.tpl`: `menu_restore_exact_20260312_152209`
- `app.js`: `menu_restore_exact_20260312_152209` + limpieza segura del legado

Limpieza segura aplicada:

- `app.js` ya no intenta activar el menu legacy global si existe el sidebar premium actual
- `admin-hub.tpl` ya no depende de `.main-icon-menu` y se ajusta al sidebar premium

No mezclar:

- sidebars de `menu_exact_restore_*`
- sidebars de `pm-premium-sidenav`
- topnav/header de otras horas

Uso local:

```powershell
powershell -ExecutionPolicy Bypass -File ops\ui-bundle\verify_ui_bundle.ps1
powershell -ExecutionPolicy Bypass -File ops\ui-bundle\restore_ui_bundle.ps1
```

Si se restaura el bundle, limpiar tambien cache de Smarty (`templates_c`) para `sidenavi` y `topnav`.
