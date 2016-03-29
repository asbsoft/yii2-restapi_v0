@echo off
set APP=../../../../..
set CMD=up
if not '%1'=='' set CMD=%1
call %APP%/yii.bat migrate/%CMD% --migrationPath=.
