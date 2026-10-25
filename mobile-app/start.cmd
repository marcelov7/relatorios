@echo off
REM Atalho para rodar o Expo sem depender do npm.ps1 do PowerShell
REM Uso: start.cmd [--android | --ios | --web | --tunnel]
node node_modules/@expo/cli/build/bin/cli start %*
