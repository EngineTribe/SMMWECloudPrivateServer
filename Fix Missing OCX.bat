@echo off
color f0
title OCX Installation
set ocxfile=MSWINSCK.OCX
set ocxfile2=RICHTX32.OCX
echo.
echo. SMMWE Cloud Private Server - OCX Installation
echo. By YidaozhanYa
echo. File1: %ocxfile%
echo. File2: %ocxfile2%
echo. -----------------------------------------------
:chkadmin
>nul 2>&1 "%SYSTEMROOT%\system32\cacls.exe" "%SYSTEMROOT%\system32\config\system"
if '%errorlevel%' EQU '0' (
goto INSTALLATION
) else (
    echo. Ejecute el script como Administrador.
    echo. Please run the script as Administrator.
    echo. 请以管理员权限运行本脚本。
    pause>nul
    exit
)
:INSTALLATION
echo. Installing...
echo. %ocxfile%
copy /y "%~dp0%ocxfile%" "%WINDIR%\SysWOW64\%ocxfile%"
echo. %ocxfile2%
copy /y "%~dp0%ocxfile2%" "%WINDIR%\SysWOW64\%ocxfile2%"
echo. -----------------------------------------------
echo. Registering...
regsvr32 "%WINDIR%\SysWOW64\%ocxfile%"
regsvr32 "%WINDIR%\SysWOW64\%ocxfile2%"
echo. -----------------------------------------------
echo. Instalacion completa!
echo. Installation Completed!
echo. 控件安装完成！
pause>nul