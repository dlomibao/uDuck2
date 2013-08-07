<?php
/**tests to make sure uD_Admin is behaving as expected**/

require_once "uD_Admin.php";
//test to make sure config loads correctly
echo DB_NAME;

//test creation
$cms= new uDuck_Admin();

echo "<br>";
echo $cms->blowfishCrypt("hello", 10);
?>