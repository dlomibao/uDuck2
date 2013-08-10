<?php
/**tests to make sure uD_Admin is behaving as expected**/

require_once "uD_Admin.php";
//test to make sure config loads correctly
echo DB_NAME;

//test creation
$cms= new uDuck_Admin();

echo "<br>";
echo $cms->blowfishCrypt("hello", 10);
echo "<br>";
$resetstring=$cms->resetstring("derek@lomibao.net");
echo $resetstring."<br>";

echo "test reset<br>";
if($cms->resetpass("derek@lomibao.net", $resetstring, "qwerty")){
	echo "success<br>";
}else{echo "fail<br>";}
?>