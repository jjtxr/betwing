<?php 
	require_once 'header.php'; 
	
	$sql2 = "SELECT id FROM tickets WHERE status='open'";
	$result2 = $db->query($sql2);
	$tickets = $result2->rowCount();
?>

<div class="main">
	<div class="a-parent">
		<div class="col-md-4 text-center a-select">
			<i class="fa fa-users fa-4x"></i>
			<h1>Users</h1>
			<small>Managing users - ban, set balance, block etc.</small>
			<a href="users.php" class="btn btn-default">See more</a>
		</div>
		<div class="col-md-4 text-center a-select">
			<i class="fa fa-gamepad fa-4x"></i>
			<h1>Matches</h1>
			<small>Managing matches - add, end, edit etc.</small>
			<a href="matches.php" class="btn btn-default">See more</a>
		</div>
		<div class="col-md-4 text-center a-select">
			<i class="fa fa-bar-chart fa-4x"></i>
			<h1>Tickets (<span style="color:green"><?php echo $tickets; ?></span>)</h1>
			<small>Users tickets</small>
			<a href="tickets.php" class="btn btn-default">See more</a>
		</div>
	</div>
	</div>
<?php require_once 'footer.php'; ?>