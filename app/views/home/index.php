<?php include_once '/../header.php'; ?>

<?php include_once '/../menu.php'; ?>

<?php 
	if(!empty($data["reportType"])){
		include '/../reports/' . $data["reportType"] . '.php';
	}
?>

<?php include_once '/../footer.php'; ?>