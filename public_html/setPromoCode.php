<?php
	try {
		$db = new PDO('mysql:host=localhost;dbname=csgo_betcenter', 'root', 'v8Fz0jkjE5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		exit($e->getMessage());
	}

	if(empty($_GET['code']))
	{
		echo '
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-triangle"></i> 
				<a href="#" class="close" onclick="promoCodeFadeOut();">&times;</a>
				Please type your promo code.
			</div>';
		exit;
	}

	if(isset($_GET['code']))
	{
		$code = strtoupper($_GET['code']);

		$sql = "SELECT steamid, code FROM users WHERE code='".$code."' AND steamid!='".$_GET['steamid']."'";
		$result = $db->query($sql);

		if (@$result->rowCount() > 0) 
		{
			echo '
				<div class="alert alert-danger">
					<i class="fa fa-exclamation-triangle"></i>
					<a href="#" class="close" onclick="promoCodeFadeOut();">&times;</a> 
					This code is already in use. Please type another one.
				</div>
				';
		}
		else
		{
			$codeL = strlen($code);
			if($codeL <= 4)
			{
				echo '
					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> 
						<a href="#" class="close" onclick="promoCodeFadeOut();">&times;</a>
						Your promo code has to be atleast 5 characters.
					</div>
					';
			}
			else
			{
				if(!preg_match('/\s/',$code))
				{
					if(!preg_match('/[^A-Za-z0-9]/', $code)) // '/[^a-z\d]/i' should also work.
					{
						$sql2 = "UPDATE users SET code='".$code."' WHERE steamid='".$_GET['steamid']."'";
						$db->query($sql2);
						echo '
							<div class="alert alert-success">
								<i class="fa fa-check"></i> 
								<a href="#" class="close" onclick="promoCodeFadeOut();">&times;</a>
								Your promo code has been changed to <strong>'.$code.'</strong>.
							</div>
							';
					}
					elseif (preg_match('/[^A-Za-z0-9]/', $code))
					{
						echo "
							<div class='alert alert-danger'>
								<i class='fa fa-exclamation-triangle'></i> 
								<a href='#' class='close' onclick='promoCodeFadeOut();'>&times;</a>
								Your promo code can contain only letters and numbers.
							</div>
							";
					}
				elseif(preg_match('/\s/',$code))
				{
					echo "
						<div class='alert alert-danger'>
							<i class='fa fa-exclamation-triangle'></i> 
							<a href='#' class='close' onclick='promoCodeFadeOut();'>&times;</a>
							Your promo code can't contain spaces.
						</div>
						";
					}
				}
			}
		}

	}
?>