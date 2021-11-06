@echo off
%1 mshta vbscript:CreateObject("Shell.Application").ShellExecute("cmd.exe","/c %~s0 ::","","runas",1)(window.close)&&exit
cd /d "%~dp0"
echo. Writing hosts...
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%WINDIR%\System32\drivers\etc\hosts.bak"
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%~dp0hosts.tmp"
echo.127.0.0.1 smmwe.online>>"%~dp0hosts.tmp"
copy /y "%~dp0hosts.tmp" "%WINDIR%\System32\drivers\etc\hosts"
del "%~dp0hosts.tmp"
ipconfig /flushdns
exit