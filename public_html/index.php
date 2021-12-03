<?php
if (!isset($_GET['page'])) {
	header('Location: /main');
	exit();
}

ini_set('display_errors','Off');
try {
	$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (PDOException $e) {
	exit($e->getMessage());
}

if (isset($_COOKIE['hash'])) {
	$sql = $db->query("SELECT * FROM `users` WHERE `hash` = " . $db->quote($_COOKIE['hash']));
	if ($sql->rowCount() != 0) {
		$row = $sql->fetch();
		$user = $row;
	}
}

$API_KEY = '3028A88A5B059CB438E7A8DE950239AC';
$GOOGLE_KEY = '6LfP4BUUAAAAAELZfW6QliFwom2GdfU3_EXNaWRa';
$min = 150;
$ip = '79.137.32.226';
$referal_summa = 20;

switch ($_GET['page']) {
	case 'main':
		$page = getTemplate('main.php', array('user'=>$user));
		echo $page;
		break;
		
	case 'matches':
		$page = getTemplate('matches.php', array('user'=>$user));
		echo $page;
		break;
		
	case 'match':
		$page = getTemplate('match.php', array('user'=>$user,'gid'=>$_GET['id']));
		echo $page;
		break;
		
	case 'bets':
		$page = getTemplate('bets.php', array('user'=>$user));
		echo $page;
		break;
		
	case 'ref':
		$page = getTemplate('ref.php', array('user'=>$user));
		echo $page;
		break;
	
	case 'about':
		$page = getTemplate('about.php', array('user'=>$user));
		echo $page;
		break;

	case 'deposit':
		$page = getTemplate('deposit.php', array('user'=>$user));
		echo $page;
		break;

	case 'tos':
		$page = getTemplate('tos.php', array('user'=>$user));
		echo $page;
		break;
	
	case 'rekt':
		$page = getTemplate('banned.php', array('user'=>$user));
		echo $page;
		break;

	case 'support':
		$page = getTemplate('support.php', array('user'=>$user));
		echo $page;
		break;

	case 'support_new':
		if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the support.')));
		$tid = $_POST['tid'];
		$title = $_POST['title'];
		$body = $_POST['reply'];
		$close = $_POST['close'];
		$cat = $_POST['cat'];
		$flag = $_POST['flag'];
		$lmao = $_POST['lmao'];
		if($tid == 0) {
			if((strlen($title) < 0) || (strlen($title) > 256)) exit(json_encode(array('success'=>false, 'error'=>'Title < 0 or > 256.')));
			if(($cat < 0) || ($cat > 4)) exit(json_encode(array('success'=>false, 'error'=>'Department cannot be left blank.')));
			if((strlen($body) < 0) || (strlen($body) > 2056)) exit(json_encode(array('success'=>false, 'error'=>'Description cannot be left blank.')));
			$sql = $db->query('SELECT COUNT(`id`) FROM `tickets` WHERE `user` = '.$db->quote($user['steamid']).' AND `status` = 0');
			$row = $sql->fetch();
			$count = $row['COUNT(`id`)'];
			if($count != 0) exit(json_encode(array('success'=>false, 'error'=>'You already have a pending support ticket.')));
			$db->exec('INSERT INTO `tickets` SET `time` = '.$db->quote(time()).', `user` = '.$db->quote($user['steamid']).', `cat` = '.$db->quote($cat).', `title` = '.$db->quote($title));
			$id = $db->lastInsertId();
			$db->exec('INSERT INTO `messages` SET `ticket` = '.$db->quote($id).', `message` = '.$db->quote($body).', `user` = '.$db->quote($user['steamid']).', `time` = '.$db->quote(time()));
			exit(json_encode(array('success'=>true,'msg'=>'Thank you - your ticket has been submitted ('.$id.')')));
		} else {
			$sql = $db->query('SELECT * FROM `tickets` WHERE `id` = '.$db->quote($tid).' AND `user` = '.$db->quote($user['steamid']));
			if($sql->rowCount() > 0) {
				$row = $sql->fetch();
				if($close == 1) {
					$db->exec('UPDATE `tickets` SET `status` = 1 WHERE `id` = '.$db->quote($tid));
					exit(json_encode(array('success'=>true,'msg'=>'[CLOSED]')));
				}
				$db->exec('INSERT INTO `messages` SET `ticket` = '.$db->quote($tid).', `message` = '.$db->quote($body).', `user` = '.$db->quote($user['steamid']).', `time` = '.$db->quote(time()));
				exit(json_encode(array('success'=>true,'msg'=>'Response added.')));
			}
		}
		break;

	case 'faq':
		$page = getTemplate('faq.php', array('user'=>$user));
		echo $page;
		break;

	case 'withdraw':
		$sql = $db->query('SELECT `id` FROM `bots`');
		$ids = array();
		while ($row = $sql->fetch()) {
			$ids[] = $row['id'];
		}
		$page = getTemplate('withdraw.php', array('user'=>$user,'bots'=>$ids));
		echo $page;
		break;

	case 'login':
		header('Location: http://79.137.32.226/login/');
		exit();
		break;

	case 'get_inv':
	if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the deposit.')));
		$prices = file_get_contents('prices.txt');
		$prices = json_decode($prices, true);
		$inv = curl('https://steamcommunity.com/profiles/'.$user['steamid'].'/inventory/json/730/2/');
		$inv = json_decode($inv, true);
		if($inv['success'] != 1) {
			exit(json_encode(array('error'=>'Your profile is private. Please <a href="http://steamcommunity.com/my/edit/settings" target="_blank">set your inventory to public</a> and <a href="javascript:loadLeft(\'nocache\')">try again</a>.')));
		}
		$items = array();
		foreach ($inv['rgInventory'] as $key => $value) {
			$id = $value['classid'].'_'.$value['instanceid'];
			$trade = $inv['rgDescriptions'][$id]['tradable'];
			if(!$trade) continue;
			$name = $inv['rgDescriptions'][$id]['market_hash_name'];
			$price = $prices['response']['items'][$name]['value']*10;
			$img = 'http://steamcommunity-a.akamaihd.net/economy/image/'.$inv['rgDescriptions'][$id]['icon_url'];
			if((preg_match('/(Souvenir)/', $name)) || ($price < $min)) {
				$price = 0;
				$reject = 'Junk';
			} else {
				$reject = 'unknown item';
			}
			$items[] = array(
				'assetid' => $value['id'],
				'bt_price' => "0.00",
				'img' => $img,
				'name' => $name,
				'price' => $price,
				'reject' => $reject,
				'sa_price' => $price,
				'steamid' => $user['steamid']);
		}

		$array = array(
			'error' => 'none',
			'fromcache' => false,
			'items' => $items,
			'success' => true);
		if(isset($_COOKIE['tid'])) {
			$sql = $db->query('SELECT * FROM `trades` WHERE `id` = '.$db->quote($_COOKIE['tid']).' AND `status` = 0');
			if($sql->rowCount() != 0) {
				$row = $sql->fetch();
				$array['code'] = $row['code'];
				$array['amount'] = $row['summa'];
				$array['tid'] = $row['id'];
				$array['bot'] = "Bot #".$row['bot_id'];
			} else {
				setcookie("tid", "", time() - 3600, '/');
			}
		}
		exit(json_encode($array));
		break;

	case 'deposit_js':
		if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the deposit.')));
		if($_COOKIE['tid']) {
			exit(json_encode(array('success'=>false, 'error'=>'You isset active tradeoffer.')));
		}
		$sql = $db->query('SELECT `id`,`name` FROM `bots` ORDER BY rand() LIMIT 1');
		$row = $sql->fetch();
		$bot = $row['id'];
		$partner = extract_partner($_GET['tradeurl']);
		$token = extract_token($_GET['tradeurl']);
		setcookie('tradeurl', $_GET['tradeurl'], time() + 3600 * 24 * 7, '/');
		$checksum = intval($_GET['checksum']);
		$prices = file_get_contents('prices.txt');
		$prices = json_decode($prices, true);
		$out = curl('http://'.$ip.':'.(3000+$bot).'/sendTrade/?assetids='.$_GET['assetids'].'&partner='.$partner.'&token='.$token.'&checksum='.$_GET['checksum'].'&steamid='.$user['steamid']);
		$out = json_decode($out, true);
		$out['bot'] = $row['name'];
		if($out['success'] == true) {
			$s = 0;
			foreach ($out['items'] as $key => $value) {
				$db->exec('INSERT INTO `items` SET `trade` = '.$db->quote($out['tid']).', `market_hash_name` = '.$db->quote($value['market_hash_name']).', `img` = '.$db->quote($value['icon_url']).', `botid` = '.$db->quote($bot).', `time` = '.$db->quote(time()));
				$s += $prices['response']['items'][$value['market_hash_name']]['value']*10;
			}
			$db->exec('INSERT INTO `trades` SET `id` = '.$db->quote($out['tid']).', `bot_id` = '.$db->quote($bot).', `code` = '.$db->quote($out['code']).', `status` = 0, `user` = '.$db->quote($user['steamid']).', `summa` = '.$db->quote($s).', `time` = '.$db->quote(time()));
			$out['amount'] = $s;
			setcookie('tid', $out['tid'], time() + 3600 * 24 * 7, '/');
		}
		exit(json_encode($out));
		break;

	case 'confirm':
	if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the confirm.')));
		$tid = (int)$_GET['tid'];
		$sql = $db->query('SELECT * FROM `trades` WHERE `id` = '.$db->quote($tid));
		$row = $sql->fetch();
		$out = curl('http://'.$ip.':'.(3000+$row['bot_id']).'/checkTrade?tid='.$row['id']);
		$out = json_decode($out, true);
		if(($out['success'] == true) && ($out['action'] == 'accept') && ($row['status'] != 1)) {
			if($row['summa'] > 0) $db->exec('UPDATE `users` SET `balance` = `balance` + '.$row['summa'].' WHERE `steamid` = '.$db->quote($user['steamid']));
			if($row['summa'] > 0) $db->exec('UPDATE `items` SET `status` = 1 WHERE `trade` = '.$db->quote($row['id']));
			if($row['summa'] > 0) $db->exec('UPDATE `trades` SET `status` = 1 WHERE `id` = '.$db->quote($row['id']));
			setcookie("tid", "", time() - 3600, '/');
		} elseif(($out['success'] == true) && ($out['action'] == 'cross')) {
			setcookie("tid", "", time() - 3600, '/');
			$db->exec('DELETE FROM `items` WHERE `trade` = '.$db->quote($row['id']));
			$db->exec('DELETE FROM `trades` WHERE `id` = '.$db->quote($row['id']));
		} else {
			exit(json_encode(array('success'=>false, 'error'=>'Trade is in procces or the coins are already credited')));
		}
		exit(json_encode($out));
		break;

	case 'get_bank_safe':
		if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the widthdraw.')));
		//if(($user['steamid'] != "76561198092088938") || ($user['steamid'] != "76561198025678566")) exit();
		$g = curl('https://www.google.com/recaptcha/api/siteverify?secret='.$GOOGLE_KEY.'&response='.$_GET['g-recaptcha-response']);
		$g = json_decode($g, true);
		if($g['success'] == true) {
			$array = array('balance'=>$user['balance'],'error'=>'none','items'=>array(),'success'=>true);
			$sql = $db->query('SELECT * FROM `items` WHERE `status` = 1');
			$prices = file_get_contents('prices.txt');
			$prices = json_decode($prices, true);
			while ($row = $sql->fetch()) {
				$array['items'][] = array('botid'=>$row['botid'],'img'=>'http://steamcommunity-a.akamaihd.net/economy/image/'.$row['img'],'name'=>$row['market_hash_name'],'assetid'=>$row['id'],'price'=>$prices['response']['items'][$row['market_hash_name']]['value']*10,'reject'=>'unknown items');
			}
			exit(json_encode($array));
		}
		break;

	case 'withdraw_js':
		if(!$user) exit(json_encode(array('success'=>false, 'error'=>'You must login to access the widthdraw.')));
		$items = array();
		$assetids = explode(',', $_GET['assetids']);
		$sum = 0;
		$prices = file_get_contents('prices.txt');
		$prices = json_decode($prices, true);
		$norm_itms = '';
		foreach ($assetids as $key) {
			if($key == "") continue;
			$sql = $db->query('SELECT * FROM `items` WHERE `id` = '.$db->quote($key));
			$row = $sql->fetch();
			$items[$row['botid']] = $row['market_hash_name'];
			$sum += $prices['response']['items'][$row['market_hash_name']]['value']*10;
			$norm_itms = $norm_itms.$row['market_hash_name'].',';
		}
		$out = array('success'=>false,'error'=>'');
		if(count($items) > 1) {
			$out = array('success'=>false,'error'=>'You choose more bots');
		} elseif($user['balance'] < $sum) {
			$out = array('success'=>false,'error'=>'You dont have coins!');
		} else {
			reset($items);
			$bot = key($items);
			$s = $db->query('SELECT `name` FROM `bots` WHERE `id` = '.$db->quote($bot));
			$r = $s->fetch();
			$db->exec('UPDATE `users` SET `balance` = `balance` - '.$sum.' WHERE `steamid` = '.$user['steamid']);
			$partner = extract_partner($_GET['tradeurl']);
			$token = extract_token($_GET['tradeurl']);
			$out = curl('http://'.$ip.':'.(3000+$bot).'/sendTradeMe/?names='.urlencode($norm_itms).'&partner='.$partner.'&token='.$token.'&checksum='.$_GET['checksum'].'&steamid='.$user['steamid']);
			$out = json_decode($out, true);
			if($out['success'] == false) {
				$db->exec('UPDATE `users` SET `balance` = `balance` + '.$sum.' WHERE `steamid` = '.$user['steamid']);
			} else {
				foreach ($assetids as $key) {
					$db->exec('DELETE FROM `items` WHERE `id` = '.$db->quote($key));
				}
				$out['bot'] = $r['name'];
				$db->exec('INSERT INTO `trades` SET `id` = '.$db->quote($out['tid']).', `bot_id` = '.$db->quote($bot).', `code` = '.$db->quote($out['code']).', `status` = 2, `user` = '.$db->quote($user['steamid']).', `summa` = '.'-'.$db->quote($_GET['checksum']).', `time` = '.$db->quote(time()));
			}
		}
		exit(json_encode($out));
		break;

	case 'exit':
		setcookie("hash", "", time() - 3600, '/');
		header('Location: /main');
		exit();
		break;
	
	case 'lost':
		$page = getTemplate('404.php', array('user'=>$user));
		echo $page;
		break;
		
	case 'no':
		$page = getTemplate('401.php', array('user'=>$user));
		echo $page;
		break;
		
		/* ADMIN PANEL */
	case 'admin':
		$page = getTemplate('admin_index.php', array('user'=>$user));
		echo $page;
		break;
	
	default:
		header('Location: ./lost');
		exit();
		break;
}

function getTemplate($name, $in = null) {
	extract($in);
	ob_start();
	include "template/" . $name;
	$text = ob_get_clean();
	return $text;
}

function curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function extract_token($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $queryString);
    return isset($queryString['token']) ? $queryString['token'] : false;
}

function extract_partner($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $queryString);
    return isset($queryString['partner']) ? $queryString['partner'] : false;
}