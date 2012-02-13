<?php
require_once 'my_global.php';

$param = $_POST['select_name'];
if (isset($param)) {
	get_materials_content($param);
}

?>