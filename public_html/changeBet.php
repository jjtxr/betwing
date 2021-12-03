<?php
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}

	$matchId = $_POST['matchId'];
	$steamId = $_POST['steamid'];

	// check if match is already started
	$dateNow = date('d.m.Y, G:i');

	$sql = "SELECT date FROM matches WHERE id='".$matchId."'";
	$result = $db->query($sql);

	if ($result->rowCount() > 0) 
	{
		while($row = $result->fetch()) 
		{
			$dateMatch = $row['date'];
		}
	} 
	
	if($dateNow > $dateMatch)
	{
		echo 'match is already started';
		exit;
	}
	elseif($dateNow < $dateMatch)
	{
		// select actual data
		$sql3 = "SELECT betOn FROM bets WHERE matchId='".$matchId."' AND userId='".$steamId."'";
		$result3 = $db->query($sql3);

		if ($result3->rowCount() > 0) 
		{
			while($row3 = $result3->fetch()) 
			{
				$type = $row3['betOn'];
				if($type=='team1')
				{
					$typeNew = 'team2';
				}
				if($type=='team2')
				{
					$typeNew = 'team1';
				}
			}
		} 

		$sql2 = "UPDATE bets SET betOn='".$typeNew."' WHERE userId='".$steamId."' AND matchId='".$matchId."'";
		$db->query($sql2);
		echo 'changed from '.$type.' to '.$typeNew.' in match '.$matchId.' for user '.$steamId;

	}
?>