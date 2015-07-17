<?php
include "../../../wp-config.php";
$type = $_GET['type'];
if ($type == 'post') {
	echo wptao_ensign();
} 

?>