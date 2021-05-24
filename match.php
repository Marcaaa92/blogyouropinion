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
		<title>BlogYourOpinion-Match center</title>
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
		<style>
			body{
			font-size: 12px;
			}
			table{
			margin-left: auto;
			margin-right: auto;
			}
			td, tr{
			text-align:center;
			}
		</style>

	</head>
	<body>
		<?php
			loadNav();
		?>
		<section class="section is-four-fifth">
			<div class=\"columns is-mobile is-centered\">
				<div class=\"column is-half is-offset-one-quarter\">
							<?php
							$match=$_GET["id"];
							$response = request("https://api-football-v1.p.rapidapi.com/v2/fixtures/id/$match?timezone=Europe%2FRome");
								$date= str_replace(" ", "<br>", date("d-m-Y H:i", strtotime(substr($response->api->fixtures[0]->event_date, 0, 16))));
								$elapsed=$response->api->fixtures[0]->elapsed;
								$status=$response->api->fixtures[0]->status;
								$refree=$response->api->fixtures[0]->referee;
								$venue=$response->api->fixtures[0]->venue;
								$homeTeamId=$response->api->fixtures[0]->homeTeam->team_id;
								$homeTeam=$response->api->fixtures[0]->homeTeam->team_name;
								$homeTeamLogo=$response->api->fixtures[0]->homeTeam->logo;
								$awayTeamId=$response->api->fixtures[0]->awayTeam->team_id;
								$awayTeam=$response->api->fixtures[0]->awayTeam->team_name;
								$awayTeamLogo=$response->api->fixtures[0]->awayTeam->logo;
								$homeTeamScore=$response->api->fixtures[0]->goalsHomeTeam;
								$awayTeamScore=$response->api->fixtures[0]->goalsAwayTeam;
							echo"<title>$nome $homeTeam - $awayTeam </title>";
							if($status=="Match Finished"||$status=="Not Started"||$status=="Time to be defined"){
							echo"<table class=\"table\">
							<tr><td></td><td><img src=\"$homeTeamLogo\"><h1 class=\"title is-2\"><a href='./team.php?id=$homeTeamId&action=article&name=$homeTeam&logo=$homeTeamLogo'>$homeTeam</a></h1></td><td><h1 class=\"title is-2\">$homeTeamScore</h1></td><td><h1 class=\"title is-5\">$date<br>referee: $refree<br>Stadium: $venue</h1><h1 class=\"title is-4\">$status</h1></td><td><h1 class=\"title is-2\">$awayTeamScore</h1></td><td><img src=\"$awayTeamLogo\"><h1 class=\"title is-2\"><a href=\"team.php?id=$awayTeamId&action=article\">$awayTeam</a></h1></td><td></td></tr>";
							}
							else{
							echo"<table class=\"table\">
							<tr><td></td><td><img src=\"$homeTeamLogo\"><h1 class=\"title is-2\"><a href='./team.php?id=$homeTeamId&action=article&name=$homeTeam&logo=$homeTeamLogo'>$homeTeam</a></h1></td><td><h1 class=\"title is-2\">$homeTeamScore</h1></td><td><h1 class=\"title is-5\">$date<br>referee: $refree<br>Stadium: $venue</h1><h1 style='background-color:red'class=\"title is-4\">LIVE-$status</h1><h1 class=\"title is-4\">$elapsed'</h1></td><td><h1 class=\"title is-2\">$awayTeamScore</h1></td><td><img src=\"$awayTeamLogo\"><h1 class=\"title is-2\"><a href='./team.php?id=$awayTeamId&action=article&name=$awayTeam&logo=$awayTeamLogo'>$awayTeam</a></h1></td><td></td></tr>";

							}
							if($status=="Not Started"){
								echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">Game not yet started</h1></td><td></td><td><p></p></td><td></td></tr>";
							}
							else{

							$response = request("https://api-football-v1.p.rapidapi.com/v2/events/$match");

							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Events</h1></td><td></td><td><p></p></td><td></td></tr>";
							for($i=0; $i<count($response->api->events); $i++){
							$elapsed=$response->api->events[$i]->elapsed;
							$teamName=$response->api->events[$i]->teamName;
							$player=$response->api->events[$i]->player;
							$type=$response->api->events[$i]->type;
							$detail=$response->api->events[$i]->detail;
							$assist=$response->api->events[$i]->assist;

							if($teamName==$homeTeam){
								if($type=="Goal"){
									if($assist==null){
									if($detail=="Penalty")
									echo "<tr><td><img src=\"./images/gol.jpg\"></td><td><p>Penalty: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									else if ($detail=="Missed Penalty")
									echo "<tr><td><img src=\"./images/gol.jpg\"></td><td><p>Missed penalty: $player (R)</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									else if($detail=="Own Goal")
									echo "<tr><td><img src=\"./images/gol.jpg\"></td><td><p>Autogol: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									else
									echo "<tr><td><img src=\"./images/gol.jpg\"></td><td><p>Goal: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									}
									else
									echo "<tr><td><img src=\"./images/gol.jpg\"></td><td><p>Goal: $player | Assist: $assist</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
								}
								else if($type=="Card"){
									if($detail=="Yellow Card")
									echo "<tr><td style='background-color:yellow'></td><td><p>$player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									else if($detail==""){}
									else
									echo "<tr><td style='background-color:red'></td><td><p>$player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";

								}
								else if($type=="subst"){
									echo "<tr><td><img src=\"./images/sost.png\"></td><td><p><font color='red'>$assist</font> | <font color='green'>$player</font></p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
								}
							}
							else{
								if($type=="Goal"){
									if($assist==null){
									if($detail=="Penalty")
									echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Penalty: $player (R)</p></td><td><img src=\"./images/gol.jpg\"></td></tr>";
									else if($detail=="Missed Penalty")
									echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Missed penalty: $player</p></td><td><img src=\"./images/gol.jpg\"></td></tr>";
									else if($detail=="Own Goal")
									echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Autogol: $player</p></td><td><img src=\"./images/gol.jpg\"></td></tr>";
									else
									echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Goal: $player</p></td><td><img src=\"./images/gol.jpg\"></td></tr>";
									}
									else
									echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Goal: $player | Assist: $assist</p></td><td><img src=\"./images/gol.jpg\"></td></tr>";
								}
								else if($type=="Card"){
								if($detail=="Yellow Card")
									echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>$player</p></td><td style='background-color:yellow'></td></tr>";
									else if($detail==""){}
									else
									echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>$player</p></td><td style='background-color:red'></td></tr>";
								}
								else if($type=="subst"){
								echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p><font color='red'>$assist</font> | <font color='green'>$player</font></p></td><td><img src=\"./images/sost.png\"></td></tr>";
								}
							}
							}
							$response = request("https://api-football-v1.p.rapidapi.com/v2/statistics/fixture/$match");

							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Stats</h1></td><td></td><td><p></p></td><td></td></tr>";
							$statName=array("Shot on goal", "Shots off Goal", "Total shot", "Blocked shot","Shots insidebox","Shots outsidebox", "Fouls","Corner Kicks",
							"Offsides","Ball Possession", "Yellow card", "Red card","Goalkeeper saves", "Total passes","Passes accurate","Passes %");
							$j=0;
							foreach($response->api->statistics as $stat){
								$home=$stat->home;
								$away=$stat->away;
							echo "<tr><td></td><td></td><td>$home</td><td>{$statName[$j]}</td><td>$away</td><td></td><td></td></tr>";
							$j++;
							}
							$response = request("https://api-football-v1.p.rapidapi.com/v2/lineups/$match");

							$homeCoach=$response->api->lineUps->$homeTeam->coach;
							$homeFormation=$response->api->lineUps->$homeTeam->formation;
							$awayCoach=$response->api->lineUps->$awayTeam->coach;
							$awayFormation=$response->api->lineUps->$awayTeam->formation;
							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Lineups</h1></td><td></td><td><p></p></td><td></td></tr>";
							echo "<tr><td></td><td></td><td>$homeCoach</td><td></td><td>$awayCoach</td><td></td><td></td></tr>";
							echo "<tr><td></td><td></td><td>$homeFormation</td><td></td><td>$awayFormation</td><td></td><td></td></tr>";
							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">11 initial</h1></td><td></td><td><p></p></td><td></td></tr>";
							for($i=0; $i<11; $i++){
							$homePlayer=$response->api->lineUps->$homeTeam->startXI[$i]->player;
							$homeNumber=$response->api->lineUps->$homeTeam->startXI[$i]->number;
							$awayPlayer=$response->api->lineUps->$awayTeam->startXI[$i]->player;
							$awayNumber=$response->api->lineUps->$awayTeam->startXI[$i]->number;
							echo "<tr><td></td><td>$homeNumber</td><td>$homePlayer</td><td></td><td>$awayPlayer</td><td>$awayNumber</td><td></td></tr>";
							}

							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">Reserves</h1></td><td></td><td><p></p></td><td></td></tr>";
							for($i=0; $i<13; $i++){
							$homePlayer=$response->api->lineUps->$homeTeam->substitutes[$i]->player;
							$homeNumber=$response->api->lineUps->$homeTeam->substitutes[$i]->number;
							$awayPlayer=$response->api->lineUps->$awayTeam->substitutes[$i]->player;
							$awayNumber=$response->api->lineUps->$awayTeam->substitutes[$i]->number;
							echo "<tr><td></td><td>$homeNumber</td><td>$homePlayer</td><td></td><td>$awayPlayer</td><td>$awayNumber</td><td></td></tr>";
							}
							echo "</table>";
							}
							?>
						</div>
				</div>
		</section>
	</body>
</html>
