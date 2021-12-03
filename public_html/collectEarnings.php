<?php
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}

	$sql = "SELECT available, balance FROM users WHERE steamid='".$_GET['steamid']."'";
	$result = $db->query($sql);

	if (@$result->rowCount() > 0) 
	{
		while($row = $result->fetch()) 
		{
			$availableNow = $row['available'];
			$balance = $row['balance'];
		}
	}

	if($availableNow <= 0)
	{
		echo 
		'
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-triangle"></i> 
				<a href="#" class="close" onclick="collectEarningsFadeOut();">&times;</a>
				You need to have atleast 1 available coin.
			</div>
		';
	}
	elseif($availableNow >= 0)
	{
		$balanceNew = $balance + $availableNow;
		$sql2 = "UPDATE users SET balance='".$balanceNew."', available='0' WHERE steamid='".$_GET['steamid']."'";
		$db->query($sql2);
		echo 
			'
				<div class="alert alert-success">
					<i class="fa fa-check"></i> 
					<a href="#" class="close" onclick="collectEarningsFadeOut();">&times;</a>
					'.$availableNow.' coins has been added to your account. Your current balance is '.$balanceNew.'.
				</div>
			';
	}
?>