$projectPhp = "C:\wamp64\bin\php\php8.3.14\php.exe"

if (-not (Test-Path $projectPhp)) {
    Write-Error "PHP not found at $projectPhp"
    return
}

Set-Alias -Name php -Value $projectPhp -Scope Global

Write-Host "PHP set to 8.3.14 for this session." -ForegroundColor Green
& $projectPhp -v
