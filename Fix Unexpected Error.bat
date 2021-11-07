@echo off
color f0
title OCX Installation
set ocxfile=MSWINSCK.OCX
set ocxfile2=RICHTX32.OCX
echo.
echo. SMMWE Cloud Private Server - OCX Uninstallation
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
echo. Unregistering...
regsvr32 /U "%WINDIR%\SysWOW64\%ocxfile%"
regsvr32 /U "%WINDIR%\SysWOW64\%ocxfile2%"
echo. -----------------------------------------------
echo. Uninstalling...
echo. %ocxfile%
del "%WINDIR%\SysWOW64\%ocxfile%"
echo. %ocxfile2%
del "%WINDIR%\SysWOW64\%ocxfile2%"
echo. -----------------------------------------------
echo. Uninstalacion completa!
echo. Uninstallation Completed!
echo. 控件安全卸载完成！
pause>nul