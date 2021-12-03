<?php require_once 'header.php'; ?>

<?php

	if (!$user) {
		header('Location: /no');
		exit();
	}

	$sql = "SELECT totalBet, commision FROM code WHERE referredBy='".$user['steamid']."'";
	$result = $db->query($sql);

	if ($result->rowCount() > 0) 
	{
		while($row = $result->fetch()) 
		{
			$referredUsers = $result->rowCount();
			@$totalEarnings += $row['commision'];
		}				
	}

	$sql2 = "SELECT steamid FROM code WHERE referredBy='".$user['steamid']."' AND hasDeposit='1'";
	$result2 = $db->query($sql2);

	if ($result2->rowCount() > 0) 
	{
		while($row2 = $result2->fetch()) 
		{
			$depositors = $result2->rowCount();
		}				
	}
	else
	{
		$depositors = 0;
	}

	$sql4 = "SELECT code, available FROM code WHERE steamid='".$user['steamid']."'";
	$result4 = $db->query($sql4);

	if ($result4->rowCount() > 0) 
	{
		while($row4 = $result4->fetch()) 
		{
			$promo = $row4['code'];
			$available = $row4['available'];
		}				
	}

	?>
	<!-- MAIN CONTENT -->
	<div class="main">
		<div class="row" style="padding:15px">
			<div class="col-md-10 col-md-offset-1">

				<div id="setPromoCodeResult" style="display:none"></div>
				<div id="collectEarningsResult" style="display:none"></div>

				<div class="input-group" style="margin-bottom:25px">
					<input type="text" placeholder="Update your refferal code..." class="form-control" id="code2" required>
					<input type="hidden" value="<?php echo $user['steamid'] ?>" id="steamid2">
					<div class="input-group-btn">
						<input type="submit" class="btn btn-primary" value="Update" onclick="setPromoCode();" />
					</div>
				</div>

				<table class="table table-bordered"> 
					<tbody> 
						<tr> 
							<td>Promo code</td> 
							<td><?php echo $promo; ?></td> 
						</tr> 
						<tr> 
							<td>Referred users</td> 
							<td><?php echo $referredUsers; ?></td> 
						</tr> 
						<tr> 
							<td>Depositors</td> 
							<td><?php echo $depositors; ?></td> 
						</tr> 
						<tr> 
							<td>Total earnings</td> 
							<td><?php echo $totalEarnings; ?></td> 
						</tr> 
						<tr> 
							<td>Available now</td> 
							<td><?php echo $available; ?></td> 
						</tr> 
					</tbody> 
				</table>

				<button class="btn btn-success btn-block" onclick="collectEarnings();">Collect Earnings</button>

				<table class="table table-bordered" style="margin-top:25px;"> 
					<thead> 
						<tr> 
							<th>Steam ID</th> 
							<th>Joined</th> 
							<th>Total bet</th> 
							<th>Commision</th> 
						</tr> 
					</thead> 
					<tbody> 
						<?php
							$sql2 = "SELECT steamid, date, totalBet, commision FROM code WHERE referredBy='".$user['steamid']."'";
							$result2 = $db->query($sql2);

							if ($result2->rowCount() > 0) 
							{
								while($row2 = $result2->fetch()) 
								{
									echo '<tr><td>'.$row2['steamid'].'</td>';
									echo '<td>'.$row2['date'].'</td>';
									echo '<td>'.$row2['totalbet'].'</td>';
									echo '<td>'.$row2['commision'].'</td></tr>';
								}				
							}
						?>
					</tbody> 
				</table>

			</div>
		</div>
	</div>
	
	<!-- jQuery -->
	<script src="/style/js/setPromoCode.js"></script>
	<script src="/style/js/collectEarnings.js"></script>
<?php require_once 'footer.php'; ?>
