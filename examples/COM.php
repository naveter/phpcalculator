
<?php
// starting word
$word = new COM("word.application") or die("Unable to instantiate Word");
echo "Loaded Word, version {$word->Version}\n";

//bring it to front
$word->Visible = 1;

//open an empty document
$word->Documents->Add();

//do some weird stuff
$word->Selection->TypeText("This is a test...");
$word->Documents[1]->SaveAs("Useless test.doc");

//closing word
$word->Quit();

//free the object
$word = null;


// ����� ���������� �������� � �������
function run_in_bg($cmd, $winStyle = 0, $waitOnReturn = false)  
{  
    $WshShell = new COM("WScript.Shell");  
    $oExec = $WshShell->Run($cmd, $winStyle, $waitOnReturn);  
    $WshShell = null;  
      
    return $oExec;  
} 

print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
print run_in_bg("php client.win.php");
