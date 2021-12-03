<?php
	if(!$user || !isset($_GET['id']))
	{
		header('Location: /main');
		exit();
	}
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}
	
	$sql = "SELECT * FROM tickets WHERE id='".$_GET['id']."'";
	$result = $db->query($sql);

	if ($result->rowCount() > 0) 
	{
		while($row = $result->fetch()) 
		{
			if($row['status']=='open'){$status='<span style="color:green">OPEN</span>';}
			if($row['status']=='closed'){$status='<span style="color:red">CLOSED</span>';}

			$steamInfo = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=3028A88A5B059CB438E7A8DE950239AC&steamids='.$row['reviewedBy']);
			$steamInfoDecoded = json_decode($steamInfo, true);
			$reviewedby = @$steamInfoDecoded['response']['players'][0]['personaname'];

			echo '<h2>Ticket details #'.$row['id'].'</h2>';
			echo '<h4><b>Subject: </b>'.$row['subject'].'</h4>';
			echo '<h4><b>Date: </b>'.$row['date'].'</h4>';
			echo '<h4><b>Message: </b>'.$row['message'].'</h4>';
			echo '<h4><b>Status: </b>'.$status.'</h4>';
			echo '<h4><b>Reviewed by: </b>'.$reviewedby.'</h4>';
			echo '<h4><b>Answer: </b><br>'.$row['answer'].'</h4>';	
		}
	} 
	else
	{
		echo "0 results";
	}
?>