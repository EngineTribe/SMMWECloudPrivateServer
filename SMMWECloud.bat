@echo off
color f0
title SMMWE Cloud Private Server
if "%~1"=="-exit" (shift) else (call <nul %0 -exit %* && goto :eof)
echo. SMMWE Cloud Private Server - version 1.0.2
echo. By YidaozhanYa
echo. -----------------------------------------------
echo. Press Ctrl+C to exit.
echo. -----------------------------------------------
Caddy.exe