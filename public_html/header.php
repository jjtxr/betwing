<?php
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(1);
	
	if (!isset($_GET['page'])) {
		header('Location: /main');
		exit();
	}
	
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}
	
	if($user['banned'] == 1 && $_GET['page'] != "rekt")
	{		
		header('Location: /rekt');
		exit();	
	}
	
	$preR = $db->query("SELECT `preRegister` FROM `users` WHERE `preRegister` = '1'");
	$preRT = $preR->rowCount();

	$result2 = $db->query("SELECT `id` FROM `matches` WHERE `winner` = ''");
	$upcomingMatches = $result2->rowCount();
	
	date_default_timezone_set("Europe/Lisbon");
	$today = getdate(); 
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BetWing - eSports Betting</title>
	<meta name="description" content="">

	<meta name="author" content="Web Domus Italia">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href="http://fonts.googleapis.com/css?family=Exo:300,400,600,700" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Aldrich" rel="stylesheet" type="text/css">
	
	<? 
		if($_GET['page'] == 'deposit' || $_GET['page'] == 'withdraw') 
			echo '<link href="style/css/mineNew.css?v=5" rel="stylesheet">'; 
	?>
	
	<? 
		if($_GET['page'] == 'matches') 
			echo '<link href="style/style.css" rel="stylesheet">'; 
	?>
	
	<link rel="stylesheet" type="text/css" href="source/bootstrap-3.3.6-dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="source/font-awesome-4.5.0/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="style/mystyle.css">
	
	<script src="/style/js/jquery-1.11.1.min.js"></script>
</head>

<body>
<!-- MAIN HEADER -->
<div class="topContent">
	<!-- STATS BAR -->
	<div class="header">
			<ul class="socialIcon">
				<li><a href="#" class="fbc"><i class="fa fa-facebook"></i></a></li>
				<li><a href="#" class="twc"><i class="fa fa-twitter"></i></a></li>
				<li><a href="#" class="stc"><i class="fa fa-steam"></i></a></li>
			</ul>
			<ul class="stats">
				<li> <i class="fa fa-clock-o"></i> <span id="txt"><?php echo $today['hours'].":".$today['minutes'].":".$today['seconds']; ?></span> &middot; <span id="userCount">0</span> Online <i class="fa fa-users"></i></li>
			</ul>
			<ul class="logst">
				
			</ul>
	</div>
	<ul class="nav2">
		<li><a href="#"><i class="fa fa-gamepad"></i> Matches <span class="badge"><? echo $upcomingMatches; ?></span></a>  </li>
		<li><a href="#"><i class="fa fa-gift"></i> Pre-Registration <span class="badge"><? echo $preRT; ?>/1000</span></a> </li>
		<li><a href="#"><i class="fa fa-book"></i> Terms Of Service</a></li>
		<li><a href="#"><i class="fa fa-exclamation-circle"></i> Rules</a></li>
		<li><a href="#"><i class="fa fa-question-circle"></i> FAQ</a></li>
		<li><a href="#"><i class="fa fa-support"></i> Support</a></li>
		<? if($user): ?>
				<li class="dropdown stlog">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <img class="rounded" src="<?=$user['avatar']?>" width="24px" height="24px"> <b><?=$user['name']?></b> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-toggle="modal" data-target="#pointsModal"><i class="fa fa-user fa-fw"></i> Profile</a></li>
						<li><a href="#" data-toggle="modal" data-target="#promoModal"><i class="fa fa-ticket fa-fw"></i> Redeem</a></li>
						<li><a href="bets.php"><i class="fa fa-line-chart fa-fw"></i> Bet History</a></li>
						<li><a href="/offers"><i class="fa fa-history fa-fw"></i> Trade history</a></li>
						<li><a href="#" data-toggle="modal" data-target="#settingsModal"><i class="fa fa-cog fa-fw"></i> Settings</a></li>
                        <li class="divider"></li>
						<li><a href="/exit"><i class="fa fa-power-off fa-fw"></i> Logout</a></li>
					</ul>
				</li>
				<? else: ?>
				<li class="stlog"><a href="#"><i class="fa fa-steam"></i> Login </a> </li>
			<? endif; ?>
	</ul>
</div>

<!-- MAIN CONTENT -->
<div class="mainContent">