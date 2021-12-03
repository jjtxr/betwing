<?php 
	require_once 'header.php'; 
	
	if (!$user) {
		header('Location: /no');
		exit();
	}
?>


<!-- MAIN CONTENT -->
<div class="main">
	<div class="row" style="padding:15px">
		<div class="col-md-6">
			<h1>Actual bets</h1>
			<?php

				$sql = "SELECT * FROM bets WHERE userId='".$user['steamid']."'";
				$result = $db->query($sql);

				$number = $result->rowCount();
				$i = 1;

				if ($result->rowCount() > 0) 
				{
					while($row = $result->fetch()) 
					{
						$matchId = $row['matchId'];
						$betValue = $row['betAmount'];
						$teamPick = $row['betOn'];
						$sql2 = "SELECT * FROM matches WHERE id='".$matchId."' AND winner=''";
						$result2 = $db->query($sql2);

						if ($result2->rowCount() > 0) 
						{
							while($row2 = $result2->fetch()) 
							{
								// calculating percents
								$sql4 = "SELECT * FROM bets WHERE matchId='".$matchId."' AND betOn='team1'";
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
								$sql5 = "SELECT * FROM bets WHERE matchId='".$matchId."' AND betOn='team2'";
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

								//calculating values
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

								if($teamPick=='team1')
								{
									$actualValue = $betValue * $valueFor1team1;
								}
								elseif($teamPick=='team2')
								{
									$actualValue = $betValue * $valueFor1team2;
								}

								$dateNow = date('d.m.Y, G:i');
								$dateMatch = $row2['date'];

								if($teamPick=='team1')
								{
									$selectedName1 = 'selectedName ';
									$selectedName2 = '';
									$yourType1 = '(your type)';
									$yourType2 = '';
								}
								elseif($teamPick='team2')
								{
									$selectedName1 = '';
									$selectedName2 = 'selectedName';
									$yourType1 = '';
									$yourType2 = '(your type)';
								}

								$displayAllowChangeBet = '';

								if($dateNow < $dateMatch)
								{
									$displayAllowChangeBet = '<a href="#" onclick="changeBet('.$matchId.', '.$user['steamid'].');">Change bet</a>';
								}
								elseif($dateNow > $dateMatch)
								{
									$displayAllowChangeBet = '<strong style="color:green">LIVE</strong>';
								}

								echo
								'
									<div class="match-container">
										<span>'.$row2['date'].' CET</span>
										<span class="change-bet">'.$displayAllowChangeBet.'</span>

										<a href="match.php?id='.$row2['id'].'">
											<div class="match">

												<p>'.$yourType1.'</p>

												<span class="match-box">
													<span class="match-team '.$selectedName1.'">'.$row2['team1'].'</span>
													<span class="match-percent">'.$team1Percent.'%</span>
												</span>

												<img src="img/teams/'.$row2['img1'].'.png" class="match-logo" />

												<span class="match-vs">vs</span>

												<img src="img/teams/'.$row2['img2'].'.png" class="match-logo" />

												<span class="match-box">
													<span class="match-team '.$selectedName2.'">'.$row2['team2'].'</span>
													<span class="match-percent">'.$team2Percent.'%</span>
												</span>

												<p>'.$yourType2.'</p>

											</div>
										</a>
									</div>	
									<p><strong>Bet value: </strong>'.$betValue.' coins</p>
									<p><strong>Potential reward: </strong> '.$actualValue.' coins</p>
								';
								if ($i < $number)
								{
									echo '<hr>';
								}

								$i++;

							}
						}

					}				
				}
				else
				{
					echo 'There is no bets... What about <a href="/main">placing some</a>?';
				}

		?>
		</div>
		<div class="col-md-6">
			<h1>Bet history</h1>
			<table class="table table-condensed text-center">
				<tbody> 

			<?php

				$sql2 = "SELECT matchId, betOn, betAmount FROM bets WHERE userId='".$user['steamid']."' ORDER BY matchId DESC";
				$result2 = $db->query($sql2);

				if ($result2->rowCount() > 0) 
				{
					while($row2 = $result2->fetch()) 
					{
						$sql3 = "SELECT date, id, winner, team1, team2 FROM matches WHERE id='".$row2['matchId']."' AND winner!=''";
						$result3 = $db->query($sql3);

						if ($result3->rowCount() > 0) 
						{
							while($row3 = $result3->fetch()) 
							{
								if($row2['betOn']==$row3['winner']){$class='won';}
								elseif($row2['betOn']!==$row3['winner']){$class='lost';}
								if($row3['winner']=='none'){$class='draw';}

								// bet data
								$sql4 = "SELECT * FROM bets WHERE matchId='".$row2['matchId']."' AND betOn='team1'";
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
								$sql5 = "SELECT * FROM bets WHERE matchId='".$row2['matchId']."' AND betOn='team2'";
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

								// calculating values
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

								$sql6 = "SELECT * FROM bets WHERE matchId='".$row2['matchId']."' AND userId='".$user['steamid']."'";
								$result6 = $db->query($sql6);

								if ($result6->rowCount() > 0) 
								{
									while($row6 = $result6->fetch()) 
									{
										$winner = $row6['betOn'];
									}
								}

								if($winner=='team1')
								{
									$valueFor1 = round(1 * $value1 * $team1Value, 2);
								}
								elseif($winner=='team2')
								{
									$valueFor1 = round(1 * $value2 * $team2Value, 2);
								}

								$winAmount = $row2['betAmount'] * $valueFor1;

								if($class=='lost'){$msg='<td><span style="color:red">-'.$row2['betAmount'].'</span></td>';}
								if($class=='draw'){$msg='<td><span style="color:#333">'.$row2['betAmount'].'</span></td>';}
								if($class=='won'){$msg='<td><span style="color:green">+'.$winAmount.'</span></td>';}
								echo 
								'
									<tr>
										<td class="'.$class.'"></td>
										<td><a href="match.php?id='.$row3['id'].'">'.$row3['team1'].' vs '.$row3['team2'].'</a></td>
										<td>'.$row3['date'].'</td>
										'.$msg.'
									</tr>
								';
							}
						}
					}
				}

				else
				{
					echo 'Nothing here...';
				}

			?>
				</tbody> 
			</table>
		</div>
	</div>
</div>

<?php require_once 'footer.php'; ?>
