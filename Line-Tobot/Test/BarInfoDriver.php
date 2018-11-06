<?php
require_once('./BarInfo.php');

$input = strtolower("Bar:tokyo-south");
#if (preg_match("/^bar:/", $input)) {
#	$barlist = json_decode(getBarInfoByArea(ltrim($input, 'bar:')), true);
#	foreach ($barlist as $bar){
#    		print $bar['name'] . "(" .  $bar['url'] . ")";
#	}
#}
if (preg_match("/^bar:/", $input)) {
        $barlist = json_decode(getBarInfoByArea(ltrim($input, 'bar:')), true);
        $info = "";
        foreach ($barlist as $bar){
                $info .= $bar['name'];
                $info .= "(";
                $info .= $bar['url'];
                $info .= ")<br/>";
        }
}
print $info


?>

