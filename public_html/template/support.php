<?php require_once 'header.php'; ?>

<!-- MAIN CONTENT -->
	<div class="main">
		<div class="row" style="padding:15px">
			<div id="inlineAlert" class="alert alert-warning" style="font-weight:bold">
				<i class="fa fa-exclamation-circle"></i><b> Misuse of our support will lead to consequences.</b>
			</div>
			<div class="col-md-6">
				<h1>Submit new ticket</h1>

				<div>
				<?php 
					if (!$user) 
					{
						header('Location: /no');
						exit();
					}
				?>
				</div>
				
				<form action="/support" method="POST">
					<input type="text" class="form-control" name="subject" placeholder="Subject..." style="margin-top:10px" required>
					<input type="hidden" name="steamid4" value="<?php echo $user['steamid']; ?>">
					<textarea name="message" class="form-control" placeholder="Your message..." style="margin-top:10px" required></textarea>
					<input type="submit" class="btn btn-default" name="submit" style="margin-top:10px" value="Submit" />
				</form>

				<?php
					if(isset($_POST['submit']))
					{
						$subject = $_POST['subject'];
						$steamid = $_POST['steamid4'];
						$message = $_POST['message'];
						$date = date('d.m.Y, G:i');

						$sql = "INSERT INTO tickets (userId, subject, message, status, date) VALUES ('".$steamid."', '".$subject."', '".$message."', 'open', '".$date."')";
						#echo $sql;

						$db->query($sql);
						echo '<div class="alert alert-success" role="alert"><i class="fa fa-check"></i> Your message was sent to our team. We will answer as soon as possible.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}
				?>

			</div>

			<div class="col-md-6">
				<h1>Your tickets</h1>
				<?php

					$sql = "SELECT id, subject, message, status, answer, date, reviewedBy FROM tickets WHERE userId='".$user['steamid']."'";
					$result = $db->query($sql);

					if ($result->rowCount() > 0) 
					{
						echo '<table class="table table-bordered text-center">';
						echo '<thead>';
						echo '<tr><td>Subject</td><td>Status</td><td>Reviewed by</td></tr>';
						echo '</thead>';
						echo '<tbody>';
						while($row = $result->fetch()) 
						{
							$info = $row;
							if($row['status']=='open'){$status='<span style="color:green">OPEN</span>';}
							if($row['status']=='closed'){$status='<span style="color:red">CLOSED</span>';}

							$answer = $row['answer'];
							if(empty($answer)){$answer='No answer yet. Please be patient.';}

							$steamInfo = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=2A19C2EA73F803C304ED6DEE6DEA4408&steamids='.$row['reviewedBy']);
							$steamInfoDecoded = json_decode($steamInfo, true);
							$reviewedby = @$steamInfoDecoded['response']['players'][0]['personaname'];

							echo 
							'
								<tr>
									<td><a href="#" onclick="loadTicket('.$row['id'].');">'.$row['subject'].'</a></td>
									<td>'.$status.'</td>
									<td><a target="_blank" href="http://steamcommunity.com/profiles/'.$row['reviewedBy'].'">'.$reviewedby.'</a></td>
								</tr>
							';
						}
						echo '</tbody>';
						echo '</table>';
					} 
					else 
					{
						echo 'You didnt submit any ticket.';
					}	
				?>
			</div>
			<div class="col-md-12 text-center" id="contactDetails"></div>
		</div>
	</div>
	
	<!-- jQuery -->
	<script src="/style/js/loadTicket.js"></script>

<?php require_once 'footer.php'; ?>
