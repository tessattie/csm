<div class="row">
	<div class="changepw"></div>
	<form action = "/csm/public/account/changePassword" method = "POST" class="form-inline" id='setpassform'>
		<div class="form-group">
			<label>Change password : </label>
		</div>
		<div class="form-group">
		    <input type="password" class="form-control oldpass" placeholder="Old password" name = "oldpass" required>
		  </div>
		<div class="form-group">
		  <input type="password" class="form-control newpass" placeholder="New password" name="newpass" required>
		</div>
		<div class="form-group">
		  <input type="password" class="form-control newpass2" placeholder="Confirm new password" name="newpass2" required>
		</div>
    	<input type="submit" class="btn btn-default setpass" value="Set password" name="submit">		
	</form>
	<div class="errorDiv"></div>
</div>