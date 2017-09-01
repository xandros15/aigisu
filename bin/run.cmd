@setlocal

set BIN_PATH=%~dp0

php -S localhost:5555 -t "%BIN_PATH%../web"

@endlocal
