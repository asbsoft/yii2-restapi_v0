@echo off

set APP=../../../../../yii.bat
if exist %APP% goto START

set APP=../../../../yii.bat
if exist %APP% goto START

echo Yii console bootstrap file not found
goto DONE

:START
set CMD=up
if not '%1'=='' set CMD=%1
call %APP% migrate/%CMD% "--migrationPath=." %2 %3 %4 %5 %6 %7 %8 %9
:DONE

