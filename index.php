<?php
session_start();
require_once("function.php");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A journalist football blog.">
    <title>BlogYourOpinion - Homepage</title>
    <link rel="stylesheet" href="css/bulma.css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
  </head>
	<body>
		<?php
			loadNav();
		?>

		<section class="section">
      <div id="notify"></div>
			<div style="text-align:center" id>
				<a href="https://www.legaseriea.it/it">
					<img src="images/logo_main_default.webp" width="78" height="78" title="serieA website" ></a>
				</a>
			</div>
			<?php
			if(isset($_SESSION["teamId"])){
					echo '<h1 class="title is-2" style="text-align:center">Your team</h1>';
					$sfmt = $db->prepare("SELECT * FROM category");
					$sfmt->execute([]);
					while($row = $sfmt->fetch()){

						$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE team.id=? AND category.categoryName=? AND articles.approvated=1 ORDER BY articles.date DESC LIMIT 5");
						$stmt->execute([$_SESSION["teamId"], $row["categoryName"]]);
						loadArticleMini($stmt,$row["categoryName"]);
					}
					echo '<h1 class="title is-2" style="text-align:center">Serie A<br><a href="article_navigation.php" style="font-size:12pt">Tutte le notizie...</a></h1>';
					$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName,  team.id as teamId, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 ORDER BY articles.date DESC LIMIT 5");
					$stmt->execute([]);
					loadArticleMini($stmt,"");
			}
			else{
				echo '<h1 class="title is-2" style="text-align:center">Serie A<br><a href="article_navigation.php" style="font-size:12pt">Tutte le notizie...</a></h1>';
				$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 AND articles.limited=0 ORDER BY articles.date DESC LIMIT 5");
				$stmt->execute([]);
				loadArticleMini($stmt,"");
			}

			?>
			<div class="columns is-desktop">
				<div class="column is-full-mobile is-full-tablet is-full-desktop is-one-third-widescreen is-one-third-fullhd" >
						<?php
						echo "<h1 class=\"title is-2\" style='text-align:center'>Standing</h1>";
							$stmt = $db->prepare("SELECT timestamp,response FROM standing ORDER BY timestamp DESC LIMIT 1");
							$stmt->execute([]);
							$row = $stmt->fetch();
							if(timeDiff($row["timestamp"])<5){
								$response_data=json_decode($row["response"]);
							}
							else{
								$response_data = request("https://api-football-v1.p.rapidapi.com/v3/standings?league=135&season=2025");
								$timestamp= date('Y-m-d H:i:s');
								$stmt = $db->prepare("INSERT INTO standing(timestamp,response) VALUES (?,?)");
								$stmt->execute([$timestamp,json_encode($response_data)]);
							}
							echo"<div class='table-container'><table class=\"table\">";
							echo "<th><th>#P</th><th>Logo</th><th>Team</th><th>Points</th><th>Games played</th><th>Goals for</th><th>Goal against</th><th>GD</th></tr>";
							for($i=0; $i<count($response_data->response[0]->league->standings[0]);$i++){
									$teamName=$response_data->response[0]->league->standings[0][$i]->team->name;
									$rank=$response_data->response[0]->league->standings[0][$i]->rank;
									$matchsPlayed=$response_data->response[0]->league->standings[0][$i]->all->played;
									$point=$response_data->response[0]->league->standings[0][$i]->point;
									$goalsFor=$response_data->response[0]->league->standings[0][$i]->all->goals->for;
									$goalsAgainst=$response_data->response[0]->league->standings[0][$i]->all->goals->against;
									$logo=$response_data->response[0]->league->standings[0][$i]->team->logo;
									$team_id=$response_data->response[0]->league->standings[0][$i]->team->id;
									$points=$response_data->response[0]->league->standings[0][$i]->points;
									$goal_diff=($goalsFor)-($goalsAgainst);
                  if($i<4)
  									if($_SESSION["teamId"]==$team_id)
  										echo "<tr class='yourteam'><td class='champions'></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\" style='color:white;'>$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
  									else
  										echo "<tr><td class='champions'></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\">$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
                  else if($i==4)
                    if($_SESSION["teamId"]==$team_id)
  										echo "<tr class='yourteam'><td class='europa'></td><td><b>$rank</b></td><td><image src=\"$logo\" alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\" style='color:white;'>$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
  									else
  										echo "<tr><td class='europa'></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\">$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
                  else if($i>4&&$i<17)
                    if($_SESSION["teamId"]==$team_id)
  										echo "<tr class='yourteam'><td></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\" style='color:white;'>$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
  									else
  										echo "<tr><td></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\">$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
                  else
                    if($_SESSION["teamId"]==$team_id)
                      echo "<tr class='yourteam'><td class='playout'></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\" style='color:white;'>$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
                    else
                      echo "<tr><td class='playout'></td><td><b>$rank</b></td><td><image src=\"$logo\" loading='lazy'alt='team-logo' style=\"width:50px\"></td><td><a href=\"./team.php?id=$team_id&action=article\">$teamName</a></td><td><b>$points</b></td><td>$matchsPlayed</td><td>$goalsFor</td><td>$goalsAgainst</td><td>$goal_diff</td></tr>";
                }
							echo"</table></div>";
						?>
					</div>
				<div class="column is-full-mobile is-full-tablet is-full-desktop is-one-third-widescreen is-one-third-fullhd">
						<?php
						echo "<h1 class=\"title is-2\" style='text-align:center'>Current turn</h1>";
							$stmt = $db->prepare("SELECT timestamp,response FROM current ORDER BY timestamp DESC LIMIT 1");
							$stmt->execute([]);
							$row = $stmt->fetch();
							if(timeDiff($row["timestamp"])<24){
								$response_data=json_decode($row["response"]);
							}
							else{
								$response_data = request("https://api-football-v1.p.rapidapi.com/v3/fixtures/rounds?league=135&season=2025&current=true");
								$timestamp= date('Y-m-d H:i:s');
								$stmt = $db->prepare("INSERT INTO current(timestamp,response) VALUES (?,?)");
								$stmt->execute([$timestamp,json_encode($response_data)]);

							}
							$round=$response_data->response[0];
							$stmt = $db->prepare("SELECT timestamp,response FROM turns ORDER BY timestamp DESC LIMIT 1");
							$stmt->execute([]);
							$row = $stmt->fetch();

							if(timeDiff($row["timestamp"])<0.16){
								$response=json_decode($row["response"]);
							}
							else{
								$rou = str_replace(" ", "%20", $round);
								$response = request("https://api-football-v1.p.rapidapi.com/v3/fixtures?league=135&season=2025&round=$rou");
								$timestamp= date('Y-m-d H:i:s');
								$stmt = $db->prepare("INSERT INTO turns(timestamp,response) VALUES (?,?)");
								$stmt->execute([$timestamp,json_encode($response)]);
							}

								echo"<table class=\"table\">";
								$round=preg_replace('/\D/', '', $round);
								echo"<tr class='matches'><td colspan='6'><h1 class=\"title is-4\">Turns $round</h1></td></tr>";
							for($i=0; $i<count($response->response);$i++){
									$id=$response->response[$i]->fixture->id;
									$date= str_replace(" ", "<br>" ,date("d-m-Y H:i", strtotime($response->response[$i]->fixture->date)));
									$status=$response->response[$i]->fixture->status->long;
									$elapsed=$response->response[$i]->fixture->status->elapsed;
									$homeTeamId=$response->response[$i]->fixture->teams->home->id;
									$homeTeam=$response->response[$i]->teams->home->name;
									$homeTeamLogo=$response->response[$i]->teams->home->logo;
									$homeTeamGoal=$response->response[$i]->goals->home;
									$awayTeamId=$response->response[$i]->teams->away->id;
									$awayTeam=$response->response[$i]->teams->away->name;
									$awayTeamLogo=$response->response[$i]->teams->away->logo;
									$awayTeamGoal=$response->response[$i]->goals->away;
									if($_SESSION["teamId"]!=null && ($_SESSION["teamId"]==$homeTeamId || $_SESSION["teamId"]==$awayTeamId)){
										if($status=="Match Finished"||$status=="Not Started"||$status=="Time to be defined"||$status=="Match Postponed")
											echo "<tr class='matches' style='background-color:#3273dc; color:white;'><td><img style='width:50px' src='$homeTeamLogo' alt='team-logo' loading='lazy'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article' style='color:white;'>$homeTeam</a></h1></td><td>$date<br>$status<br><h1 class=\"title is-5\" style='color:white'>$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id' style='color:white'>Details</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article' style='color:white;'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo' alt='team-logo' loading='lazy'></td></tr>";
										else
											echo "<tr class='matches' style='background-color:#3273dc; color:white;'><td><img style='width:50px' src='$homeTeamLogo' alt='team-logo' loading='lazy'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article' style='color:white;'>$homeTeam</a></h1></td><td>$date<br><p style='background-color:red'>$status</p>$elapsed<h1 class=\"title is-5\">$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id'>Dettagli</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article' style='color:white;'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo' alt='team-logo' loading='lazy'></td></tr>";
									}
									else{
										if($status=="Match Finished"||$status=="Not Started"||$status=="Time to be defined"||$status=="Match Postponed")
											echo "<tr class='matches'><td><img style='width:50px' src='$homeTeamLogo' alt='team-logo' loading='lazy'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article'>$homeTeam</a></h1></td><td>$date<br>$status<br><h1 class=\"title is-5\">$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id'>Details</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo' alt='team-logo' loading='lazy'></td></tr>";
										else
											echo "<tr class='matches'><td><img style='width:50px' src='$homeTeamLogo' alt='team-logo' loading='lazy'></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$homeTeamId&action=article'>$homeTeam</a></h1></td><td>$date<br><p style='background-color:red'>$status</p>$elapsed<h1 class=\"title is-5\">$homeTeamGoal-$awayTeamGoal</h1><a href='./match.php?id=$id'>Dettagli</a></td><td><h1 class=\"title is-5\"><a href='./team.php?id=$awayTeamId&action=article'>$awayTeam</a></h1></td><td><img style='width:50px' src='$awayTeamLogo' loading='lazy'></td></tr>";
									}
								}
								echo "</table>";
						?>
				</div>
				<div class="coulum is-full-mobile is-full-tablet is-full-desktop is-one-third-widescreen is-one-third-fullhd">
					<?php
					$stmt = $db->prepare("SELECT timestamp,response FROM topscorer ORDER BY timestamp DESC LIMIT 1");
					$stmt->execute([]);
					$row = $stmt->fetch();
					if(timeDiff($row["timestamp"])<24){
						$response_data=json_decode($row["response"]);
					}
					else{
						$response_data = request("https://api-football-v1.p.rapidapi.com/v3/players/topscorers?league=135&season=2025");
						$timestamp= date('Y-m-d H:i:s');
						$stmt = $db->prepare("INSERT INTO topscorer(timestamp,response) VALUES (?,?)");
						$stmt->execute([$timestamp,json_encode($response_data)]);
					}
					echo "<h1 class=\"title is-2\" style='text-align:center'>Top scorer</h1>";
					echo"<div class='table-container'><table class=\"table\">";
					echo "<tr><th>Player</th><th>Goal</th><th>Total shot</th><th>Shot on goal</th><th>Assist</th><th>Appearances</th></tr>";
					for($i=0; $i<count($response_data->response); $i++){
						$name=$response_data->response[$i]->player->name;
						$played=$response_data->response[$i]->statistics[0]->games->appearences;
						$goal=$response_data->response[$i]->statistics[0]->goals->total;
						$assist=$response_data->response[$i]->statistics[0]->goals->assists;
            $shotOnGoal=$response_data->response[$i]->statistics[0]->shots->on;
            $total=$response_data->response[$i]->statistics[0]->shots->total;
						echo "<tr><td><b>$name</b></td><td>$goal</td><td>$total</td><td>$shotOnGoal</td><td>$assist</td><td>$played</td></tr>";
					}
					echo"</table></div>";
					?>
				</div>
			</div>
		</section>
	</body>
</html>
