@echo off
color f0
title SMMWE Cloud Private Server Installation
set server-ver=1.0.3
echo.
echo. SMMWE Cloud Private Server - Version %server-ver%
echo. By YidaozhanYa
echo. -----------------------------------------------
:chkinstall
if exist "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Version.txt" (
for /f "delims=" %%i in (%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Version.txt) do (set server-ver-installed=%%i)&(goto :next)
:next
echo. Server version: %server-ver%
echo. Installed version: %server-ver-installed%
if "%server-ver-installed%"=="%server-ver%" (
echo. You have already installed the Private Server.
pause>nul
exit
)
)
:chkver
ver | find "10.0" >nul
if %errorlevel%==0 (
    goto chkcaddy
) else (
ver | find "11.0" >nul
if %errorlevel%==0 (
    goto chkcaddy
) else (
    echo. This program only supports Windows 10/11.
    echo, Please upgrade your system first.
    pause>nul
    exit
)
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
echo. Saving installation information...
mkdir "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer"
mkdir "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Cache"
echo %server-ver%>"%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Version.txt"
echo. -----------------------------------------------
echo. Installation Completed!
pause>nul