param(
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\..")).Path
)

$bundleRoot = Join-Path $PSScriptRoot "files"
$manifestPath = Join-Path $PSScriptRoot "manifest.json"
$manifest = Get-Content $manifestPath -Raw | ConvertFrom-Json

$targets = @(
    "templates\apps\sidenavi.tpl",
    "templates\apps\topnav.tpl",
    "firenet\assets\js\app.js"
)

$hasMismatch = $false

foreach ($relative in $targets) {
    $jsonKey = $relative.Replace("\", "/")
    $expected = ($manifest.hashes_md5.PSObject.Properties | Where-Object { $_.Name -eq $jsonKey } | Select-Object -First 1 -ExpandProperty Value)
    $target = Join-Path $ProjectRoot $relative
    $bundle = Join-Path $bundleRoot $relative

    $targetHash = (Get-FileHash $target -Algorithm MD5).Hash.ToLower()
    $bundleHash = (Get-FileHash $bundle -Algorithm MD5).Hash.ToLower()
    $ok = ($targetHash -eq $expected -and $bundleHash -eq $expected)

    [pscustomobject]@{
        File = $relative
        Expected = $expected
        Target = $targetHash
        Bundle = $bundleHash
        Match = $ok
    }

    if (-not $ok) {
        $hasMismatch = $true
    }
}

if ($hasMismatch) {
    exit 1
}
