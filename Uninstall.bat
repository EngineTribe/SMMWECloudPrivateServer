@echo off
color f0
title SMMWE Cloud Private Server Uninstallation
set server-ver=1.0.3
echo.
echo. SMMWE Cloud Private Server - Version %server-ver%
echo. By YidaozhanYa
echo. -----------------------------------------------
if exist "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Version.txt" (
goto :chkcaddy
) else (
echo. You haven't installed the program yet.
)
:chkcaddy
tasklist|find /i "caddy.exe">nul
if %errorlevel%==0 (
    echo. Please exit the server first.
    pause>nul
    exit
) else (
    goto chkadmin
)
:chkadmin
>nul 2>&1 "%SYSTEMROOT%\system32\cacls.exe" "%SYSTEMROOT%\system32\config\system"
if '%errorlevel%' EQU '0' (
goto UNINSTALLATION
) else (
    echo. Please run the script as Administrator.
    pause>nul
    exit
)
:UNINSTALLATION
echo. Recovering Hosts...
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%~dp0hosts.tmp"
findstr /v "127.0.0.1 smmwe.online" "%~dp0hosts.tmp">"%WINDIR%\System32\drivers\etc\hosts"
del "%~dp0hosts.tmp"
echo. -----------------------------------------------
echo. Removing SSL cert...
"%~dp0certmgr.exe" -del -c "%~dp0smmwe_cloud.crt" -r localMachine -s trustedpublisher
"%~dp0certmgr.exe" -del -c "%~dp0smmwe_cloud.crt" -s root
echo. -----------------------------------------------
echo. Removing configuration folder...
rd /s /q "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer"
echo. -----------------------------------------------
echo. Unnstallation Completed!
pause>nul