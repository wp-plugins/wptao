<?php
include "../../../wp-config.php";
$type = !empty($_GET['type']) ? $_GET['type'] : '';
if ($type == 'sign') {
	echo wptao_ensign('', $_GET['link']);
} 

?>