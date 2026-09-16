$files = @('admin','app','applicant','auth')

# token name -> new hex value (MoCU navy palette)
$map = @{
  '--sand-50' = '#F5F8FC'; '--sand-100' = '#ECF1F6'; '--sand-200' = '#DCE5EE';
  '--coffee-900' = '#052B3D'; '--coffee-800' = '#07364F'; '--coffee-700' = '#0A4260';
  '--coffee-500' = '#285B78'; '--coffee-300' = '#5E88A3';
  '--terracotta-600' = '#0066CC'; '--terracotta-500' = '#1B80E0'; '--terracotta-100' = '#E4F0FA';
  '--acacia-600' = '#2E7D6B'; '--acacia-500' = '#3E8F7C'; '--acacia-100' = '#E1EFEA';
  '--gold-500' = '#E8B82F'; '--gold-100' = '#F9EFD2';
  '--ink' = '#052C3F'; '--ink-soft' = '#40637A'; '--line' = '#C9D6E2';
}

foreach ($f in $files) {
  $p = "resources/views/layouts/$f.blade.php"
  $c = Get-Content $p -Raw
  $orig = $c
  foreach ($k in $map.Keys) {
    $rx = "(?i)($([regex]::Escape($k)):#)[0-9A-Fa-f]{6}"
    $c = [regex]::Replace($c, $rx, "`${1}$($map[$k])")
  }
  # add MoCU brand tokens after --coffee-900 group
  if ($c -notmatch '--primary-dark:') {
    $c = [regex]::Replace($c, '(--coffee-900:#[0-9A-Fa-f]{6};)', "`${1}--primary:#0A4260;--primary-dark:#07364F;--primary-light:#285B78;--secondary:#0066CC;--accent:#F9CC41;--accent-dark:#E8B82F;")
  }
  if ($c -ne $orig) { Set-Content $p $c -NoNewline; $st='CHANGED' } else { $st='UNCHANGED' }
  echo "$f [$st]"
}
