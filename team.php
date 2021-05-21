<?php
session_start();
require_once("function.php");
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>BlogYourOpinion - Team</title>
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
	</head>
	<body>
		<?php
			loadNav();
		?>
		<section class="section is-four-fifth">
			<div class="columns is-desktop">
				<div class="column">

							<?php
								$id=$_GET["id"];
								$stmt = $db->prepare("SELECT * FROM team WHERE id=?");
								$stmt->execute([$id]);
								$row = $stmt->fetch();
								$teamName=$row["teamName"];
								$teamLogo=$row["logo"];
								echo '<h2 class="title is-3 " style="text-align:center"><img style="width:60px" src="'.$teamLogo.'">'.$teamName.'<img style="width:60px" src="'.$teamLogo.'"></h2>';
								if($_GET["action"]=="article"&&isset($_GET["id"])){
									echo'
									<div class="tabs is-centered">
									  <ul>
										<li class="is-active"><a href="team.php?action=article&id='.$id.'">Articles</a></li>
										<li><a href="team.php?action=match&id='.$id.'">Matches</a></li>
										<li><a href="team.php?action=player&id='.$id.'">Player</a></li>
									  </ul>
									</div>
									';
										if(isset($_SESSION["id"])){
											$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId,   articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 AND team.id=?  ORDER BY articles.date DESC");
											$stmt->execute([$id]);
											loadArticleMiniNavigation($stmt);
										}
										else{
											$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 AND team.id=? AND articles.limited=0 ORDER BY articles.date DESC");
											$stmt->execute([$id]);
											loadArticleMiniNavigation($stmt);
										}
								}
								else if($_GET["action"]=="match"&&isset($_GET["id"])){
									echo'
									<div class="tabs is-centered">
									  <ul>
										<li><a href="team.php?action=article&id='.$id.'">Articles</a></li>
										<li class="is-active"><a href="team.php?action=match&id='.$id.'">Matches</a></li>
										<li><a href="team.php?action=player&id='.$id.'">Player</a></li>
									  </ul>
									</div>
									';


											$stmt = $db->prepare("SELECT timestamp,response FROM matchbyteam WHERE teamId=? ORDER BY timestamp DESC LIMIT 1");
											$stmt->execute([$id]);
											if ($stmt->rowCount() == 1){
												$row = $stmt->fetch();
												if(timeDiff($row["timestamp"])<0.16){
													$response=json_decode($row["response"]);
												}
												else{
													$response = request("https://api-football-v1.p.rapidapi.com/v2/fixtures/team/$id/2857?timezone=Europe%2FRome");
													$timestamp= date('Y-m-d H:i:s');
													$stmt = $db->prepare("INSERT INTO matchbyteam(timestamp,response,teamId) VALUES (?,?,?)");
													$stmt->execute([$timestamp,json_encode($response),$id]);
												}
											}
											else{
												$response = request("https://api-football-v1.p.rapidapi.com/v2/fixtures/team/$id/2857?timezone=Europe%2FRome");
												$timestamp= date('Y-m-d H:i:s');
												$stmt = $db->prepare("INSERT INTO matchbyteam(timestamp,response,teamId) VALUES (?,?,?)");
												$stmt->execute([$timestamp,json_encode($response),$id]);
											}

											echo"<div class=\"columns is-mobile is-centered\"><div class=\"is-half is-offset-one-quarter\"><table class=\"table\">";
											$round="";
											for($i=0; $i<count($response->api->fixtures);$i++){
											$round=$response->api->fixtures[$i]->round;
											if($round==$oldround){
											}
											else{
											$oldround=$round;
											$round=preg_replace('/\D/', '', $round);
											echo"
											<tr class='matches'><td colspan='6'><h1 class=\"title is-4\">Round $round</h1></td></tr>";
											}
												$id=$response->api->fixtures[$i]->fixture_id;
												$elapsed=$response->api->fixtures[$i]->elapsed;
												$date= str_replace(" ", "<br>" ,date("d-m-Y H:i", strtotime($response->api->fixtures[$i]->event_date)));
												$status=$response->api->fixtures[$i]->status;
												$homeTeamId=$response->api->fixtures[$i]->homeTeam->team_id;
												$homeTeam=$response->api->fixtures[$i]->homeTeam->team_name;
												$homeTeamGoal=$response->api->fixtures[$i]->goalsHomeTeam;
												$homeTeamLogo=$response->api->fixtures[$i]->homeTeam->logo;
												$awayTeamId=$response->api->fixtures[$i]->awayTeam->team_id;
												$awayTeam=$response->api->fixtures[$i]->awayTeam->team_name;
												$awayTeamGoal=$response->api->fixtures[$i]->goalsAwayTeam;
												$awayTeamLogo=$response->api->fixtures[$i]->awayTeam->logo;
												$score=$response->api->fixtures[$i]->score->fulltime;

												if($status=="Match Finished"||$status=="Not Started"||$status=="Time to be defined"||$status=="Match Postponed")
													echo "<tr class='matches'><td><img style='width:50px' src='$homeTeamLogo'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article'>$homeTeam</a></h1></td><td>$date<br>$status<br><h1 class=\"title is-5\">$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id'>Details</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo'></td></tr>";
												else
													echo "<tr class='matches'><td><img style='width:50px' src='$homeTeamLogo'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article'>$homeTeam</a></h1></td><td>$date<br><p style='background-color:red'>$status</p>$elapsed<h1 class=\"title is-5\">$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id'>Dettagli</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo'></td></tr>";
												}
											echo "</table>
											</div></div>";
								}
								else if($_GET["action"]=="player"&&isset($_GET["id"])){
									echo'
									<div class="tabs is-centered">
									  <ul>
										<li><a href="team.php?action=article&id='.$id.'">Articles</a></li>
										<li><a href="team.php?action=match&id='.$id.'">Matches</a></li>
										<li class="is-active"><a href="team.php?action=player&id='.$id.'">Player</a></li>
									  </ul>
									</div>
									';
									$stmt = $db->prepare("SELECT timestamp,response FROM playersbyteam WHERE teamId=? ORDER BY timestamp DESC LIMIT 1");
									$stmt->execute([$id]);
									if ($stmt->rowCount() == 1){
										$row = $stmt->fetch();
										if(timeDiff($row["timestamp"])<24*7){
											$response=json_decode($row["response"]);
										}
										else{
											$response = request("https://api-football-v1.p.rapidapi.com/v2/players/team/$id/2020-2021");
											$timestamp= date('Y-m-d H:i:s');
											$stmt = $db->prepare("INSERT INTO playersbyteam(timestamp,response,teamId) VALUES (?,?,?)");
											$stmt->execute([$timestamp,json_encode($response),$id]);
										}
									}
									else{
										$response = request("https://api-football-v1.p.rapidapi.com/v2/players/team/$id/2020-2021");
										$timestamp= date('Y-m-d H:i:s');
										$stmt = $db->prepare("INSERT INTO playersbyteam(timestamp,response,teamId) VALUES (?,?,?)");
										$stmt->execute([$timestamp,json_encode($response),$id]);
									}
									echo '<h2 class="title is-3 " style="text-align:center">Here are the players who have played at least one match in Serie A</h2>';
									echo '<div id="table_div" class="table"></div>';
								}
								else{
									echo '<h2 class="title is-3 " style="text-align:center">You have no selectet any action or team name</h2>';
								}
							?>
				</div>
			</div>
		</section>
	</body>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
	google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(drawTable);

		function drawTable() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Player name');
			data.addColumn('string', 'Position');
			data.addColumn('number', 'Age');
			data.addColumn('string', 'Nationality');
			data.addColumn('string', 'Height');
			data.addColumn('string', 'Weight');
			data.addColumn('number', 'Goals');
			data.addColumn('number', 'Assists');
			data.addColumn('number', 'Appearences');
			data.addColumn('number', 'Minutes played');
			data.addColumn('number', 'In lineups');
			data.addRows([
				<?php
				$s="";
					for($i=0; $i<count($response->api->players); $i++){
						if($response->api->players[$i]->league=="Serie A"&&$response->api->players[$i]->games->appearences>0){
							$playerName=addslashes ($response->api->players[$i]->player_name);
							$position=$response->api->players[$i]->position;
							$age=$response->api->players[$i]->age;
							$nationality=addslashes ($response->api->players[$i]->nationality);
							$height=$response->api->players[$i]->height;
							$weight=$response->api->players[$i]->weight;
							$goals=$response->api->players[$i]->goals->total;
							$assists=$response->api->players[$i]->goals->assists;
							$appearences=$response->api->players[$i]->games->appearences;
							$minutes_played=$response->api->players[$i]->games->minutes_played;
							$lineups=$response->api->players[$i]->games->lineups;

						$s.= "['$playerName', '$position' , $age, '$nationality','$height', '$weight',$goals,$assists, $appearences , $minutes_played , $lineups],";
					}
				}
				echo substr($s, 0, -1);
				?>
			]);

			var table = new google.visualization.Table(document.getElementById('table_div'));
			 var cssClassNames = {
				'headerRow': 'table',
				'tableRow': 'table',
				'oddTableRow': 'table',
				'selectedTableRow': 'table',
				'hoverTableRow': 'table',
				'headerCell': 'table',
				'tableCell': 'table',
				'rowNumberCell': 'table'};
			table.draw(data, {showRowNumber: true, width: '100%', height: '100%','cssClassNames':cssClassNames});
		}
	</script>
</html>
