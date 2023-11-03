@echo off
REM Copy all pointed files, start the program and copy all files backwards

SET PROGRAM="C:\Program Files (x86)\PokerStars\PokerStars.exe"
REM SET PROGRAM="C:\Program Files (x86)\PokerStars\PokerStarsUpdate.exe"
SET FILE1_SHARE="C:\Users\ilya.gulevskiy\Google Disc\arch\Poker Stars\notes.taras1242.xml"
SET FILE1_SYS="C:\Users\ilya.gulevskiy\AppData\Local\PokerStars\notes.taras1242.xml"
SET DIR1_SHARE="C:\Users\ilya.gulevskiy\Google Disc\arch\Poker Stars\HandHistory\taras1242"
SET DIR1_SYS="C:\Users\ilya.gulevskiy\AppData\Local\PokerStars\HandHistory\taras1242"

echo Copy files from share point to system...
IF EXIST %FILE1_SHARE% COPY %FILE1_SHARE% %FILE1_SYS%
IF EXIST %DIR1_SYS% del %DIR1_SYS%\*.* /F /Q
IF EXIST %DIR1_SHARE% xcopy /s/Q/Y %DIR1_SHARE% %DIR1_SYS%

start /wait "PokerStars" %PROGRAM%

echo Copy files from system point to share...
IF EXIST %FILE1_SYS% COPY %FILE1_SYS% %FILE1_SHARE%
IF EXIST %DIR1_SHARE% del %DIR1_SHARE%\*.* /F /Q
IF EXIST %DIR1_SYS% xcopy /s/Q/Y %DIR1_SYS% %DIR1_SHARE%

REM pause
exit