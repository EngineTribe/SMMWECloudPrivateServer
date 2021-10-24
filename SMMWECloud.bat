@echo off
color f0
title SMMWE Cloud Private Server
if "%~1"=="-exit" (shift) else (call <nul %0 -exit %* && goto :eof)
set server-ver=1.0.3
echo. SMMWE Cloud Private Server - Version %server-ver%
echo. By YidaozhanYa
echo. -----------------------------------------------
if exist "%USERPROFILE%\AppData\Local\SMM_WE_PrivateServer\Version.txt" (
goto :chkcaddy
) else (
echo. You haven't installed the program yet.
echo. Please install first.
)
:chkcaddy
tasklist|find /i "caddy.exe">nul
if %errorlevel%==0 (
    echo. Can't run multiple servers.
    pause>nul
    exit
) else (
    goto chkport
)
:chkport
netstat -a | find "127.0.0.1:443" >nul 2>nul
if %errorlevel%==0 (
    echo. Your port 443 is being used by other program,
    echo. it may be a web server, or VMWare's VM server.
    echo. Please close the program that occupies the port, then try again.
    pause>nul
    exit
) else (
    goto chkport2
)
:chkport2
netstat -a | find "127.0.0.1:80" >nul 2>nul
if %errorlevel%==0 (
    echo. Your port 80 is being used by other program,
    echo. it may be a web server, or a SSH client.
    echo. Please close the program that occupies the port, then try again.
    pause>nul
    exit
) else (
    goto main
)
:main
echo. Press Ctrl+C to exit.
echo. -----------------------------------------------
Caddy.exe