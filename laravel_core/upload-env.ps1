$envVars = Get-Content .env | Where-Object { $_ -match "^[A-Za-z0-9_]+=" }
foreach ($line in $envVars) {
    $parts = $line -split '=', 2
    $key = $parts[0].Trim()
    $val = $parts[1].Trim()
    if ($val -match '^"(.*)"$') { $val = $matches[1] } elseif ($val -match "^'(.*)'$") { $val = $matches[1] }
    if ([string]::IsNullOrWhiteSpace($val)) { continue }

    echo "Adding $key..."
    vercel env rm $key production -y | Out-Null
    [IO.File]::WriteAllText("temp.txt", $val)
    cmd.exe /c "vercel env add $key production < temp.txt"
}
Remove-Item temp.txt
