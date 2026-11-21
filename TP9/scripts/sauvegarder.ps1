$ErrorActionPreference = "Stop"

$mysqldump = "C:\Program Files\MySQL\MySQL Server 26.7\bin\mysqldump.exe"
$backupDir = Join-Path $PSScriptRoot "..\preuves"
$backupDir = [System.IO.Path]::GetFullPath($backupDir)

if (-not (Test-Path $mysqldump)) {
    throw "mysqldump introuvable : $mysqldump"
}

New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$date = Get-Date -Format "yyyyMMdd_HHmmss"
$fichier = Join-Path $backupDir "projet_sil_tp9_$date.sql"

Write-Host "Sauvegarde de la base projet_sil_tp9..."
& $mysqldump --user=root --password --single-transaction --routines --triggers --databases projet_sil_tp9 "--result-file=$fichier"

if ($LASTEXITCODE -ne 0) {
    throw "La sauvegarde a échoué."
}

Write-Host "Sauvegarde terminée : $fichier"
