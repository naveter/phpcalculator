<?php
/**
 * Попытка запустить программу и дождаться её завершения
 */

echo "Program is running\n";

//copy( "C:\Users\ilya.gulevskiy\tmp\user.class.php", "C:\Users\ilya.gulevskiy\Google Диск\tmp\user.class.php" );
//rename('C:\Users\ilya.gulevskiy\tmp\user.class.php', 'C:\Users\ilya.gulevskiy\Google Диск\tmp\user.class.php');

//exit();

$res = system("C:\Users\ilya.gulevskiy\prg\CoolReader3-qt-win32-3.0.56-42\cr3.exe", $return_var);
 
 print "Program is closed\n";
 print_r($res);
 
