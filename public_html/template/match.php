<?php require_once 'header.php'; ?>
	
	<script src="style/js/placeBet.js"></script>
	<script src="style/js/changeBet.js"></script>
	<script src="style/js/selectTeam.js"></script>
	
	<!-- MAIN CONTENT -->
	<div class="main">
		<div class="row" style="padding:15px">
			<div class="col-md-6">

				<?php 
					$dateNow = date('d.m.Y, G:i');

					//$date1 = date('d.m.Y').', '.date('G:i');
					// user data
					$sql2 = "SELECT balance, banned FROM users WHERE steamid='".$user['steamid']."'";
					$result2 = $db->query($sql2);

					if ($result2->rowCount() > 0) 
					{
						while($row2 = $result2->fetch()) 
						{
							$balance = $row2['balance']; 
							$banned = $row2['banned']; 
						}
					}

					#$winnerS1 = '';
					#$winnerS2 = '';

					// match data
					$sql = "SELECT * FROM matches WHERE id='".$_GET['id']."'";
					$result = $db->query($sql);

					if ($result->rowCount() > 0) 
					{
						while($row = $result->fetch()) 
						{
							$stream = $row['stream'];
							$date = $row['date'].' CET';

							$match = $row;

							if(empty($row['winner']))
							{
								$allowBet = 1;
							}
							else
							{
								$allowBet = 0;
							}

							$winner = $row['winner'];
						}
					} 
					else 
					{
						echo '<h3>Match not found...</h3>';
						exit;
					}

					// bets data, user
					$sql7 = "SELECT * FROM bets WHERE matchId='".$_GET['id']."' AND userId='".$user['steamid']."'";
					$result7 = $db->query($sql7);

					if ($result7->rowCount() > 0) 
					{
						while($row7 = $result7->fetch()) 
						{
							$alreadyBet = 1;
							$alreadyBetOn = $row7['betOn'];
							$alreadyBetAmount = $row7['betAmount'];
						}
					}
					elseif ($result7->rowCount() <= 0)
					{
						$alreadyBet = 0;
					}

					// bets data, team 1
					$sql4 = "SELECT * FROM bets WHERE matchId='".$_GET['id']."' AND betOn='team1'";
					$result4 = $db->query($sql4);

					if ($result4->rowCount() > 0) 
					{
						while($row4 = $result4->fetch()) 
						{
							$betOnTeam1users = $result4->rowCount();
						}
					}
					elseif ($result4->rowCount() <= 0)
					{
						$betOnTeam1users = 0;
					}

					// bets data, team 2
					$sql5 = "SELECT * FROM bets WHERE matchId='".$_GET['id']."' AND betOn='team2'";
					$result5 = $db->query($sql5);

					if ($result5->rowCount() > 0) 
					{
						while($row5 = $result5->fetch()) 
						{
							$betOnTeam2users = $result5->rowCount();
						}
					}
					elseif ($result5->rowCount() <= 0)
					{
						$betOnTeam2users = 0;
					}

					if($betOnTeam1users==0)
					{
						$team1Percent = '0';
						$team2Percent = '100';
					}
					if($betOnTeam2users==0)
					{
						$team2Percent = '0';
						$team1Percent = '100';
					}
					if($betOnTeam1users==0 && $betOnTeam2users==0)
					{
						$team1Percent = '0';
						$team2Percent = '0';
					}

					if($betOnTeam1users >= 1 && $betOnTeam2users >= 1)
					{
						$betTotal = $betOnTeam1users + $betOnTeam2users;
						$team1Percent = floor($betOnTeam1users / $betTotal * 100);
						$team2Percent = ceil($betOnTeam2users / $betTotal * 100);
					}

					#echo $betOnTeam1users.' / '.$betOnTeam2users.'<br>';
					#echo $team1Percent.'% / '.$team2Percent.'%';

					$value1 = '0.'.$team2Percent;
					$value2 = '0.'.$team1Percent;

					$team1Value = 0;
					$team2Value = 0;

					if($team1Percent > $team2Percent)
					{
						$team1Value = 1.5;
						$team2Value = 3;
					}
					elseif($team1Percent < $team2Percent)
					{
						$team1Value = 3;
						$team2Value = 1.5;
					}

					if($team1Percent==50)
					{
						$team1Value = 2;
						$team2Value = 2;
					}

					if($team2Percent > $team1Percent)
					{
						$team1Value = 3;
						$team2Value = 1.5;
					}
					elseif($team2Percent < $team1Percent)
					{
						$team1Value = 1.5;
						$team2Value = 3;
					}

					$valueFor1team1 = round(1 * $value1 * $team1Value, 2);
					$valueFor1team2 = round(1 * $value2 * $team2Value, 2);

					$valueFor1team1new = round(1 * $value1 * $team1Value, 2);
					$valueFor1team2new = round(1 * $value2 * $team2Value, 2);

					$defaultBetAmount1 = 1;
					$defaultBetAmount2 = 1;

				?>

				<?php

					if($allowBet==1)
					{
						$selectedName1 = ' ';
						$selectedName2 = '';
						$yourType1 = '';
						$yourType2 = '';
				?>

				<?php

					if($alreadyBet==1)
					{
						$defaultBetAmount1 = $alreadyBetAmount;
						$defaultBetAmount2 = $alreadyBetAmount;

						$valueFor1team1new = round($defaultBetAmount1 * $valueFor1team1, 2);
						$valueFor1team2new = round($defaultBetAmount2 * $valueFor1team2, 2);


						if($alreadyBetOn=='team1')
						{
							$selectedName1 = 'selectedName ';
							$selectedName2 = '';
							$yourType1 = '(your type)';
							$yourType2 = '';
						}
						elseif($alreadyBetOn=='team2')
						{
							$selectedName1 = '';
							$selectedName2 = 'selectedName';
							$yourType1 = '';
							$yourType2 = '(your type)';
						}
					}

				?>

				<div class="match-container">
					<span><?php echo $date; ?> <?php if($dateNow >= $date){echo '<strong style="color:green">LIVE</strong>';} ?></span>
					<span class="match-info"><?php echo $match['message']; ?></span>

					<div class="match">

						<p><?php echo $yourType1; ?></p>

						<span class="match-box" onclick="selectTeam(1);">
							<span class="match-team <?php echo $selectedName1; ?>" id="matchTeam1"><?php echo $match['team1']; ?></span>
							<span class="match-percent"><?php echo $team1Percent; ?>%</span>
						</span>

						<img src="style/img/teams/<?php echo $match['img1']; ?>.png" onclick="selectTeam(1);" class="match-logo" />

						<span class="match-vs">vs</span>

						<img src="style/img/teams/<?php echo $match['img2']; ?>.png" onclick="selectTeam(2);" class="match-logo" />

						<span class="match-box" onclick="selectTeam(2);">
							<span class="match-team <?php echo $selectedName2; ?>" id="matchTeam2"><?php echo $match['team2']; ?></span>
							<span class="match-percent"><?php echo $team2Percent; ?>%</span>
						</span>

						<p><?php echo $yourType2; ?></p>
					
					</div>
				</div>

				<div class="progress">
					<div class="progress-bar progress-bar-success" style="width:<?php echo $team1Percent; ?>%"></div>
					<div class="progress-bar" style="width:<?php echo $team2Percent; ?>%"></div>
				</div>

				<p class="potentialReward">Potential reward:</p>
				<div class="rewardLeft">
					<p><span id="valueFor1team1"><?php echo $valueFor1team1; ?></span> for 1</p>
					<p><span id="betValuemultiplier1"><?php echo $valueFor1team1new; ?></span> for <span id="betValueDisplay1"><?php echo $defaultBetAmount1; ?></span></p>
				</div>
				<div class="rewardRight">
					<p><span id="valueFor1team2"><?php echo $valueFor1team2; ?></span> for 1</p>
					<p><span id="betValuemultiplier2"><?php echo $valueFor1team2new; ?></span> for <span id="betValueDisplay2"><?php echo $defaultBetAmount2; ?></span></p>
				</div>

				<?php
					if($alreadyBet==0 && $dateNow < $date && (isset($steamprofile)))
					{
				?>

				<div class="input-group" style="width:100%">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
							<span class="glyphicon glyphicon-minus"></span>
						</button>
					</span>
					<input type="text" name="quant[1]" id="betValueInput" onchange="updateValue();" onkeydown="updateValue();" onkeyup="updateValue();" onkeypress="updateValue();" class="form-control input-number" value="1" min="1" max="<?php echo $balance; ?>">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="quant[1]">
							<span class="glyphicon glyphicon-plus"></span>
						</button>
					</span>
				</div>

				<button class="btn btn-primary btn-block" id="placeBetButton" style="margin-top:15px" onclick="placeBet();">Place bet</button>

				<?php
					}
					elseif(!$user)
					{
						echo '<h2 style="margin-top:50px">Please log in in order to place bets.</h2>';
					}
					}
					elseif($allowBet==0)
					{
						if($match['winner']=='team1')
						{
							$class1 = 'selectedName';
							$class2 = '';
							$winnerS1 = '(winner)';
							$winnerS2 = '';
						}
						elseif($match['winner']=='team2')
						{
							$class2 = 'selectedName';
							$class1 = '';
							$winnerS2 = '(winner)';
							$winnerS1 = '';
						}
						elseif($match['winner']=='none')
						{
							$class2 = '';
							$class1 = '';
							$winnerS2 = '';
							$winnerS1 = '';
						}
				?>

				<div class="match-container">
					<span><?php echo $date; ?></span>
					<span class="match-info"><?php echo $match['message']; ?></span>

					<div class="match">

						<?php

							if(@$alreadyBetOn=='team1')
							{
								$alreadyBetOnS1 = '(your type)';
								$alreadyBetOnS2 = '';
							}
							elseif(@$alreadyBetOn=='team2')
							{
								$alreadyBetOnS2 = '(your type)';
								$alreadyBetOnS1 = '';
							}

						?>

						<p><?php echo @$alreadyBetOnS1; ?></p>
						<p><?php echo $winnerS1; ?></p>

						<span class="match-box">
							<span class="match-team <?php echo $class1; ?>" id="matchTeam1"><?php echo $match['team1']; ?></span>
							<span class="match-percent"><?php echo $team1Percent; ?>%</span>
						</span>

						<img src="style/img/teams/<?php echo $match['img1']; ?>.png" class="match-logo" />

						<span class="match-vs">vs</span>

						<img src="style/img/teams/<?php echo $match['img2']; ?>.png" class="match-logo" />

						<span class="match-box">
							<span class="match-team <?php echo $class2; ?>" id="matchTeam2"><?php echo $match['team2']; ?></span>
							<span class="match-percent"><?php echo $team2Percent; ?>%</span>
						</span>

						<p><?php echo $winnerS2; ?></p>
						<p><?php echo @$alreadyBetOnS2; ?></p>
					
					</div>
				</div>

				<p class="potentialReward">Potential reward:</p>
				<div class="rewardLeft">
					<p><span id="valueFor1team1"><?php echo $valueFor1team1; ?></span> for 1</p>
					<p><span id="betValuemultiplier1"><?php echo $valueFor1team1; ?></span> for <span id="betValueDisplay1">1</span></p>
				</div>
				<div class="rewardRight">
					<p><span id="valueFor1team2"><?php echo $valueFor1team2; ?></span> for 1</p>
					<p><span id="betValuemultiplier2"><?php echo $valueFor1team2; ?></span> for <span id="betValueDisplay2">1</span></p>
				</div>

				<?php
					}
					if($dateNow >= $date)
					{
						echo '<h2 style="margin-top:50px;">Stream:</h2>';
						if($stream)
						{
							echo '<div class="stream-container">
									<iframe
										src="http://player.twitch.tv/?channel='.$stream.'&muted=true"
										height="270"
										width="480"
										frameborder="0"
										scrolling="no"
										allowfullscreen="true">
									</iframe>
									</div>';
						}
						else
						{
							echo '<small>Seems like there is not stream for this match...</small>';
						}
					}
				?>



			</div>

			<div class="col-md-6">
				<?php

					if($alreadyBet==1)
					{
						echo '<div class="alert alert-info" role="alert">You have already placed a bet on this match. Go to <a href="bets.php" class="alert-link">your bets</a> to manage it.</div>';
					}

				?>
				<div id="placeBetAlerts"></div>
				<h1>How to place a bet?</h1>
				<small>Just select a team by clicking on their name or logo, type amount and press "Place bet".</small>

				<h1>Where will be my winnings?</h1>
				<small>Your winnings will be added instantly to your balance as soon as match will finish.</small>

				<h1>Can I see my bet history?</h1>
				<small>Sure. Click on your nickname and select "My bets", on right side you will see your bet history.</small>

				<h1>Where can I get more details about the match?</h1>
				<small>We recommend <a href="http://hltv.org/" target="_blank">hltv.org</a> for match informations.</small>
			</div>
				
		</div>
	</div>
	<!-- BODY END -->
	
<script type="text/javascript">
	matchId = '<?php echo $_GET["id"]; ?>';
</script>

<?php require_once 'footer.php'; ?>