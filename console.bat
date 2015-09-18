@echo off

rem -------------------------------------------------------------
rem  Windows console bootstrap
rem -------------------------------------------------------------
@setlocal

set P=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%P%console.php" %*

@endlocal
