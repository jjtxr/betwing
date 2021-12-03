<?php 
	require_once 'header.php'; 
	
	include 'template/php/match.php';
	include 'template/php/match-over.php';
?>

	<!-- MAIN CONTENT -->
	<div class="mainContent">
		<div class="row" style="padding:15px">
			<div class="col-md-6">
				<h1>
					<i class="fa fa-arrow-up"></i>
					Upcoming matches
				</h1>
				<?php 
					match('1', '2 hours from now', 'Fnatic', 'Navi', 'fnatic', 'navi', '50%', '50%', 'test info');
					//$date1 = date('d.m.Y').', '.date('G:i');
					
					$sql = "SELECT * FROM matches WHERE winner='' ORDER BY date ASC";
					$result = $db->query($sql);

					if ($result->rowCount() > 0) 
					{
						while($row = $result->fetch()) 
						{
							$date = $row['date'].' CET';
							$dateReal = $row['date'];

							// bets data, team 1
							$sql4 = "SELECT * FROM bets WHERE matchId='".$row['id']."' AND betOn='team1'";
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
							$sql5 = "SELECT * FROM bets WHERE matchId='".$row['id']."' AND betOn='team2'";
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

							if($betOnTeam1users == $betOnTeam2users)
							{
								$team1Percent = 50;
								$team2Percent = 50;
							}
							else
							{
								$betTotal = $betOnTeam1users + $betOnTeam2users;
								$team1Percent = floor($betOnTeam1users / $betTotal * 100);
								$team2Percent = ceil($betOnTeam2users / $betTotal * 100);
							}

							$dateNow = date('d.m.Y, G:i');
							$live = '';
							if($dateNow >= $dateReal){$live='<strong style="color:green">LIVE</strong>';} 

							match($row['id'], $date.' '.$live, $row['team1'], $row['team2'], $row['img1'], $row['img2'], $team1Percent.'%', $team2Percent.'%', $row['message']);
						}
					} 
					else 
					{
						echo '<h3>No matches found</h3>';
					}
				?>
				
			</div>

			<div class="col-md-6">
				<h1>
					<i class="fa fa-arrow-down"></i>
					Past matches
				</h1>
				
				<?php 					
					$sql2 = "SELECT * FROM matches WHERE winner!='' ORDER BY date DESC LIMIT 10";
					$result = $db->query($sql2);

					if ($result->rowCount() > 0) 
					{
						while($row2 = $result->fetch()) 
						{
							$date = $row2['date'].' CET';

							// bets data, team 1
							$sql4 = "SELECT * FROM bets WHERE matchId='".$row2['id']."' AND betOn='team1'";
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
							$sql5 = "SELECT * FROM bets WHERE matchId='".$row2['id']."' AND betOn='team2'";
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

							matchOver($row2['id'], $date, $row2['team1'], $row2['team2'], $row2['img1'], $row2['img2'], $team1Percent.'%', $team2Percent.'%', $row2['message'], $row2['winner']);
						}
					} 
					else 
					{
						echo '<h3>No matches found</h3>';
					}
				?>
			</div>
		</div>
	</div>
	<!-- BODY END -->
	
<?php require_once 'footer.php'; ?>