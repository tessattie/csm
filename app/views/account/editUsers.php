<table class="table table-bordered">
		<thead>
			<tr><th colspan="7">Users</th></tr>
			<tr><th>Last name</th><th>First name</th><th>Username</th><th>Email</th><th>Access</th><th>Vendors</th><th>Actions</th></tr>
			<form method = "POST" action = "/csm/public/account/index">
				<tr><th><input type="text" class="form-control" name="lastname" placeholder="Last name" required></th>
					<th><input type="text" class="form-control" name="firstname" placeholder="First name" required></th>
					<th><input type="text" class="form-control" name="username" placeholder="Username" required></th>
					<th><input type="email" class="form-control" name="email" placeholder="Email" required></th>
					<th>
						<select class= "form-control" name="role">
							<option value = "1">Admin</option>
							<option value = "4">Level 0</option>
							<option value = "2">Level 1</option>
							<option value = "3" selected>Level 2</option>
							<option value = "10">Level 5</option>
						</select>
					</th>
					<th><input type="text" class="form-control" name="vendors" placeholder="Vendors"></th>
					<th><input type='submit' class="btn btn-default" value='Submit' name="submit"></th>
				</tr>
			</form>
		</thead>
		<tbody>
			<?php  
				$count = count($data['users']);
				for($i=0;$i<$count;$i++)
				{
					echo "<tr>";
					echo "<td>" . strtoupper($data['users'][$i]['lastname']) . "</td>";
					echo "<td>" . $data['users'][$i]['firstname'] . "</td>";
					echo "<td>" . $data['users'][$i]['username'] . "</td>";
					echo "<td>" . $data['users'][$i]['email'] . "</td>";
					echo "<td>" . $data['users'][$i]['role'] . "</td>";
					if(!empty($data['users'][$i]['vendors'])){
						echo "<td>" . $data['users'][$i]['vendors'] . "</td>";
					}else{
						echo "<td>ALL</td>";
					}
					echo "<td><a href='/csm/public/account/delete/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Delete'></a>
							  <a href='/csm/public/account/reset/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Reset'></a>
							  <a href='/csm/public/account/edit/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Edit'></a></td></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>