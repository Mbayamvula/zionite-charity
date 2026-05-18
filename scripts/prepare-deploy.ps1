# Zionite Charity — Create deployment ZIP for upload to hosting
$ErrorActionPreference = "Stop"
$root = Resolve-Path (Join-Path $PSScriptRoot "..")

$outDir = Join-Path $root "deploy-package"
$zipPath = Join-Path $root "zionite-charity-deploy.zip"

Write-Host "Preparing deployment package from: $root"

if (Test-Path $outDir) { Remove-Item $outDir -Recurse -Force }
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }
New-Item -ItemType Directory -Path $outDir -Force | Out-Null

function ShouldExclude([string]$relPath) {
    $relPath = $relPath -replace '/', '\'
    if ($relPath -like 'deploy-package*') { return $true }
    if ($relPath -like '.git*') { return $true }
    if ($relPath -like 'storage\security\*.json') { return $true }
    if ($relPath -like 'storage\security\*.log') { return $true }
    if ($relPath -eq 'includes\env.php') { return $true }
    if ($relPath -eq 'zionite-charity-deploy.zip') { return $true }
    if ($relPath -like 'scripts\*') { return $true }
    return $false
}

Get-ChildItem -Path $root -Recurse -File | ForEach-Object {
    $rel = $_.FullName.Substring($root.Path.Length + 1)
    if (ShouldExclude $rel) { return }

    $dest = Join-Path $outDir $rel
    $destParent = Split-Path $dest -Parent
    if (-not (Test-Path $destParent)) {
        New-Item -ItemType Directory -Path $destParent -Force | Out-Null
    }
    Copy-Item $_.FullName $dest -Force
}

# Production env (from template — edit on server)
$envDest = Join-Path $outDir "includes\env.php"
if (-not (Test-Path (Split-Path $envDest -Parent))) {
    New-Item -ItemType Directory -Path (Split-Path $envDest -Parent) -Force | Out-Null
}
Copy-Item (Join-Path $root "includes\env.example.php") $envDest -Force

@('uploads\projects', 'uploads\reports', 'storage\security') | ForEach-Object {
    $p = Join-Path $outDir $_
    New-Item -ItemType Directory -Path $p -Force | Out-Null
    $ht = Join-Path $root "$_\.htaccess"
    if (Test-Path $ht) { Copy-Item $ht (Join-Path $p ".htaccess") -Force }
}

Compress-Archive -Path (Join-Path $outDir "*") -DestinationPath $zipPath -Force

$sizeMb = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host ""
Write-Host "Done! ZIP: $zipPath ($sizeMb MB)"
Write-Host "Folder: $outDir"
