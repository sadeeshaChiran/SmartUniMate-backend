$ErrorActionPreference = "Stop"
New-Item -ItemType Directory -Force -Path ".bin"
Write-Host "Downloading PHP..."
Invoke-WebRequest -Uri "https://windows.php.net/downloads/releases/php-8.3.31-nts-Win32-vs16-x64.zip" -OutFile ".bin\php.zip"
Write-Host "Extracting PHP..."
Expand-Archive -Path ".bin\php.zip" -DestinationPath ".bin\php" -Force
Write-Host "Configuring PHP..."
Copy-Item ".bin\php\php.ini-development" ".bin\php\php.ini"
Add-Content ".bin\php\php.ini" "`nextension_dir = `"ext`"`n"
Add-Content ".bin\php\php.ini" "extension=curl`n"
Add-Content ".bin\php\php.ini" "extension=ffi`n"
Add-Content ".bin\php\php.ini" "extension=fileinfo`n"
Add-Content ".bin\php\php.ini" "extension=mbstring`n"
Add-Content ".bin\php\php.ini" "extension=openssl`n"
Add-Content ".bin\php\php.ini" "extension=pdo_sqlite`n"
Add-Content ".bin\php\php.ini" "extension=sqlite3`n"

Write-Host "Downloading Composer..."
Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile ".bin\composer.phar"

Write-Host "Testing PHP..."
& ".\.bin\php\php.exe" -v
