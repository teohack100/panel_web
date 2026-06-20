param(
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\..")).Path
)

$bundleRoot = Join-Path $PSScriptRoot "files"
$backupRoot = Join-Path $PSScriptRoot "local-backups"
$stamp = Get-Date -Format "yyyyMMdd_HHmmss"

$targets = @(
    @{ Relative = "templates\apps\sidenavi.tpl"; Bundle = "templates\apps\sidenavi.tpl" },
    @{ Relative = "templates\apps\topnav.tpl"; Bundle = "templates\apps\topnav.tpl" },
    @{ Relative = "firenet\assets\js\app.js"; Bundle = "firenet\assets\js\app.js" }
)

New-Item -ItemType Directory -Force -Path $backupRoot | Out-Null

foreach ($item in $targets) {
    $target = Join-Path $ProjectRoot $item.Relative
    $bundle = Join-Path $bundleRoot $item.Bundle
    $backup = Join-Path $backupRoot ($stamp + "\\" + $item.Relative)

    New-Item -ItemType Directory -Force -Path (Split-Path $backup -Parent) | Out-Null
    Copy-Item $target $backup -Force
    Copy-Item $bundle $target -Force
}

$templatesCache = Join-Path $ProjectRoot "templates_c"
if (Test-Path $templatesCache) {
    Get-ChildItem -Path $templatesCache -File -Recurse |
        Where-Object { $_.Name -like "*sidenavi*" -or $_.Name -like "*topnav*" } |
        Remove-Item -Force
}

Write-Host "Stable UI bundle restored."
Write-Host "Backup saved in: $backupRoot\\$stamp"
