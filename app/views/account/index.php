<?php include_once '/../header.php'; ?>

<?php include_once '/../menu.php'; ?>

<div class="error"><?php echo $data['error']; ?></div>

<?php  
	include "changePassword.php";

	if($data["menu"] == "menuAdmin")
	{
		include "editUsers.php";
	}
?>

<?php include_once '/../footer.php'; ?>