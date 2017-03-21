<?php include_once '/../header.php'; ?>

<div class="container">
	<div class="row signinrow">
		<div class="col-md-4 col-md-offset-4">
			
			<form class="form-signin" action = '/csm/public/login' method = "POST">
			    <h2 class="form-signin-heading">Please sign in</h2>
			    <?php  
			    	if(isset($_COOKIE['username'])) 
			    	{
    					echo "<input type=\"text\" name=\"username\" value=".$_COOKIE['username']." class='form-control' required autofocus>";
    				}
    				else
    				{
    					echo "<input type=\"text\" name=\"username\" placeholder='Username' class='form-control' required autofocus>";
    				}

    				if(isset($_COOKIE['password'])) 
			    	{
    					echo "<input type=\"password\" name=\"password\" value=".$_COOKIE['password']." class='form-control' required>";
    				}
    				else
    				{
    					echo "<input type=\"password\" name=\"password\" placeholder='Password' class='form-control' required>";
    				}

    				echo '<div class="checkbox"><label>';
    				if(isset($_COOKIE['remember'])) 
			    	{
    					echo "<input type=\"checkbox\" name=\"rememberMe\" checked = 'checked'>";
    				}
    				else
    				{
    					echo "<input type=\"checkbox\" name=\"rememberMe\">";
    				}



    				echo 'Remember me</label></div>';
			    ?>

			    
			    <?= $data['error']; ?>
			    <input type='submit' class="btn btn-lg btn-primary btn-block" value='Submit' name="submit">
			</form>
		</div>
	</div>
</div> <!-- /container -->

