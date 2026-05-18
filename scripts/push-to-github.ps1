# Push Zionite Charity to GitHub (user: BOYIKA06)
$ErrorActionPreference = "Stop"
$root = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $root

$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

$repo = "Mbayamvula/zionite-charity"
Write-Host "Target: https://github.com/$repo" -ForegroundColor Cyan

# Check gh auth
$ghAuthed = $false
try {
    gh auth status 2>$null | Out-Null
    if ($LASTEXITCODE -eq 0) { $ghAuthed = $true }
} catch {}

if ($ghAuthed) {
    Write-Host "Creating repo and pushing via GitHub CLI..."
    gh repo create zionite-charity --public --source=. --remote=origin --push
    exit $LASTEXITCODE
}

Write-Host ""
Write-Host "GitHub CLI not logged in. Using git push..." -ForegroundColor Yellow
Write-Host "Create the repo first: https://github.com/new" -ForegroundColor Yellow
Write-Host "  - Name: zionite-charity" -ForegroundColor Yellow
Write-Host "  - Do NOT add README" -ForegroundColor Yellow
Write-Host ""
Read-Host "Press Enter after you created the empty repo on GitHub"

git branch -M main
git remote remove origin 2>$null
git remote add origin "https://github.com/$repo.git"
git push -u origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "Success! https://github.com/$repo" -ForegroundColor Green
}
