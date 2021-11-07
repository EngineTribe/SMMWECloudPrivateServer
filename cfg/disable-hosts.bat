cd /d "%temp%"
echo. Recovering Hosts...
copy /y "%WINDIR%\System32\drivers\etc\hosts" "%temp%\hosts.tmp"
findstr /v "127.0.0.1 smmwe.online" "%temp%\hosts.tmp">"%WINDIR%\System32\drivers\etc\hosts"
del "%temp%\hosts.tmp"
exit
