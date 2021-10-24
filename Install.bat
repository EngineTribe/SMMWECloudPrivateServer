@echo off
color f0
title SMMWE Cloud Private Server Installation
echo.
echo. SMMWE Cloud Private Server - version 1.0.2
echo. By YidaozhanYa
echo. -----------------------------------------------
>nul 2>&1 "%SYSTEMROOT%\system32\cacls.exe" "%SYSTEMROOT%\system32\config\system"
if '%errorlevel%' EQU '0' (
goto INSTALLATION
) else (
    echo. Please run the script as Administrator.
    pause>nul
    exit
)
:INSTALLATION
echo. Writing hosts...
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%WINDIR%\System32\drivers\etc\hosts.bak"
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%~dp0hosts.tmp"
echo.127.0.0.1 smmwe.online>>"%~dp0hosts.tmp"
copy /y "%~dp0hosts.tmp" "%WINDIR%\System32\drivers\etc\hosts"
del "%~dp0hosts.tmp"
echo. -----------------------------------------------
echo. Installing SSL cert...
echo. PLEASE CLICK "YES"
"%~dp0certmgr.exe" -add -c "%~dp0smmwe_cloud.crt" -r localMachine -s trustedpublisher
"%~dp0certmgr.exe" -add -c "%~dp0smmwe_cloud.crt" -s root
echo. -----------------------------------------------
echo. Installation Completed!
pause>nul