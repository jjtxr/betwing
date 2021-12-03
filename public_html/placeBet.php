<?php
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}

	$steamId = $_POST['steamid'];
	$matchId = $_POST['matchid'];
	$betOn = $_POST['betOn2'];
	$betOnName = $_POST['betOn'];
	$amount = $_POST['amount'];

	if(isset($steamId) && isset($matchId) && isset($betOn) && isset($amount))
	{
		$sql3 = "SELECT balance, referredBy, totalBet, commision FROM users WHERE steamid='".$steamId."'";
		$result3 = $db->query($sql3);

		if ($result3->rowCount() > 0) 
		{
			while($row3 = $result3->fetch()) 
			{
				$balance = $row3['balance'];
				$newBalance = $balance - $amount;
				$referredBy = $row3['referredBy'];
				$totalBet = $row3['totalBet'];
				$totalBetNew = $totalBet + $amount;
				$commision = $row3['commision'];
				$commisionTest = floor($amount * 0.03);
				$commisionNew = $commision + $commisionTest;

				$sql6 = "SELECT available FROM users WHERE steamid='".$referredBy."'";
				$result6 = $db->query($sql6);
				if ($result6->rowCount() > 0) 
				{
					while($row6 = $result6->fetch()) 
					{
						$referredByBalance = $row6['available'];
					}
				}
			}
		}

		$sql = "SELECT id FROM bets WHERE matchId='".$matchId."' AND userId='".$steamId."'";
		$result = $db->query($sql);

		if ($result->rowCount() > 0) 
		{
			echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <a href="#" class="close" onclick="placeBetFadeOut();">&times;</a>You have already placed a bet on this match. Check <a href="bets.php" class="alert-link">your bets</a>.</div>';
		} 
		else 
		{
			if($balance<=0)
			{
				echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <a href="#" class="close" onclick="placeBetFadeOut();">&times;</a>You dont have enough coins. You can <a href="deposit.php" class="alert-link">deposit some skins</a> to get coins.</div>';
				exit;
			}
			else
			{
				$sql2 = 'INSERT INTO bets (matchId, betAmount, betOn, userId) VALUES ("'.$matchId.'", "'.$amount.'", "'.$betOn.'", "'.$steamId.'")';

				if ($db->query($sql2) === TRUE) 
				{
					$sql5 = "UPDATE users SET balance='".$newBalance."', totalBet='".$totalBetNew."', commision='".$commisionNew."' WHERE steamid='".$steamId."'";
					$db->query($sql5);
					$amountRef = $amount * 0.03;
					$referredByBalanceNew = floor($referredByBalance + $amountRef);
					$sql7 = "UPDATE users SET available='".$referredByBalanceNew."' WHERE steamid='".$referredBy."'";
					$db->query($sql7);
					echo '<div class="alert alert-success"><i class="fa fa-check"></i> <a href="#" class="close" onclick="placeBetFadeOut();">&times;</a>You have successfully placed '.$amount.' coins on team '.$betOnName.'! Go to <a href="bets.php" class="alert-link">your bets</a> to manage it.</div>';
				} 
				else 
				{
					echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <a href="#" class="close" onclick="placeBetFadeOut();">&times;</a>Database error. Try refreshing site or contact administrator.</div>';
					echo $db->error.' / '.$sql2;
				}
			}
		}
	}
	else
	{
		echo 'error...';
	}
?>