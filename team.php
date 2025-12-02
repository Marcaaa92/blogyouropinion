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
								echo '<h1 class="title is-3 " style="text-align:center"><img style="width:60px" src="'.$teamLogo.'">'.$teamName.'<img style="width:60px" src="'.$teamLogo.'"></h1>';
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
													$response = request("https://api-football-v1.p.rapidapi.com/v3/fixtures?league=135&season=2025&team=$id");
													$timestamp= date('Y-m-d H:i:s');
													$stmt = $db->prepare("INSERT INTO matchbyteam(timestamp,response,teamId) VALUES (?,?,?)");
													$stmt->execute([$timestamp,json_encode($response),$id]);
												}
											}
											else{
												$response = request("https://api-football-v1.p.rapidapi.com/v3/fixtures?league=135&season=2025&team=$id");
												$timestamp= date('Y-m-d H:i:s');
												$stmt = $db->prepare("INSERT INTO matchbyteam(timestamp,response,teamId) VALUES (?,?,?)");
												$stmt->execute([$timestamp,json_encode($response),$id]);
											}

											echo"<div class=\"columns is-mobile is-centered\"><div class=\"is-half is-offset-one-quarter\"><table class=\"table\">";
											$round="";
											for($i=0; $i<count($response->response);$i++){
											$round=$response->response[$i]->league->round;
											if($round==$oldround){
											}
											else{
											$oldround=$round;
											$round=preg_replace('/\D/', '', $round);
											echo"
											<tr class='matches'><td colspan='6'><h1 class=\"title is-4\">Round $round</h1></td></tr>";
											}
												$id=$response->response[$i]->fixture->id;
												$elapsed=$response->response[$i]->fixture->status->elapsed;
												$date= str_replace(" ", "<br>" ,date("d-m-Y H:i", strtotime($response->response[$i]->fixture->date)));
												$status=$response->response[$i]->status->long;
												$homeTeamId=$response->response[$i]->teams->home->id;
												$homeTeam=$response->response[$i]->teams->home->name;
												$homeTeamGoal=$response->response[$i]->score->fulltime->home;
												$homeTeamLogo=$response->response[$i]->teams->home->logo;
												$awayTeamId=$response->response[$i]->teams->away->id;
												$awayTeam=$response->response[$i]->teams->away->name;
												$awayTeamGoal=$response->response[$i]->score->fulltime->away;
												$awayTeamLogo=$response->response[$i]->teams->away->logo;
												$score=$response->response[$i]->score->fulltime->home."-".$response->response[$i]->score->fulltime->away;

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

// Variabile per salvare tutti i dati dei giocatori
$allPlayersResponse = null;

if ($stmt->rowCount() == 1){
    $row = $stmt->fetch();
    
    // Controlla la scadenza della cache (24*7 ore)
    if (timeDiff($row["timestamp"]) < 24 * 7){
        $allPlayersResponse = json_decode($row["response"]);
    }
}

// Se i dati non sono stati caricati dalla cache, o la cache non esiste
if ($allPlayersResponse === null) {
    
    // --- 1. PRIMA CHIAMATA (Pagina 1) ---
    $decodedResponse1 = request("https://api-football-v1.p.rapidapi.com/v3/players?season=2025&league=135&team=$id&page=1");

    
    // Assumiamo che $decodedResponse1 sia un oggetto PHP valido
    $allPlayersResponse = $decodedResponse1;
    
    // Verifica se esiste una pagina 2
    $totalPages = $decodedResponse1->paging->total ?? 1;
    
    if ($totalPages > 1) {
        // --- 2. SECONDA CHIAMATA (Pagina 2) ---
        $decodedResponse2 = request("https://api-football-v1.p.rapidapi.com/v3/players?season=2025&league=135&team=$id&page=2");

        // --- 3. UNIONE DEI RISULTATI ---
        if (isset($decodedResponse2->response) && is_array($decodedResponse2->response)) {
            // Unisce l'array 'response' della Pagina 1 con l'array 'response' della Pagina 2
            $mergedResponses = array_merge(
                $decodedResponse1->response, 
                $decodedResponse2->response
            );
            
            // Sostituisce l'array 'response' nell'oggetto della Pagina 1 con l'array unito
            $allPlayersResponse->response = $mergedResponses;
            
            // Aggiorna anche il conteggio dei risultati totali e la paginazione
            $allPlayersResponse->results = count($mergedResponses);
            $allPlayersResponse->paging->total = 1; // Resettiamo a 1 la paginazione se tutti i dati sono qui
        }
    }
    
    // --- 4. SALVATAGGIO NEL DB (dopo aver unito tutti i dati) ---
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $db->prepare("INSERT INTO playersbyteam(timestamp,response,teamId) VALUES (?,?,?)");
    $stmt->execute([$timestamp, json_encode($allPlayersResponse), $id]);
}

// La variabile $response (o meglio $allPlayersResponse) contiene ora i dati uniti
$response = $allPlayersResponse;

echo '<h1 class="title is-3 " style="text-align:center">Here are the players who have played at least one match in Serie A</h1>';
echo '<div id="table_div" class="table"></div>';
								}
								else{
									echo '<h1 class="title is-4 " style="text-align:center">You have no selectet any action or team name</h1>';
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
				$s = "";
				// Assicurati che $response->response sia un array prima di ciclare
				if (isset($response->response) && is_array($response->response)) {
					for($i = 0; $i < count($response->response); $i++){
						$playerData = $response->response[$i]->player;
						$statsData = $response->response[$i]->statistics[0];
						
						// --- Dati del Giocatore ---
						// addslashes e gestione null per stringhe
						$playerName = addslashes($playerData->name);
						$nationality = addslashes($playerData->nationality);
						
						// Per Google Charts, i valori null per stringhe sono accettati come stringhe vuote
						// Per i valori nulli che Google Charts deve trattare come null (se il campo lo permette) o 0
						$height = $playerData->height ?? ''; 
						$weight = $playerData->weight ?? ''; 

						// --- Dati Statistiche (Numerici) ---
						// Utilizzo di ?? 0 per convertire 'null' in 0, essenziale per i campi number di Google Charts
						$age = $playerData->age ?? 0;
						$goals = $statsData->goals->total ?? 0;
						$assists = $statsData->goals->assists ?? 0;
						$appearences = $statsData->games->appearences ?? 0;
						$minutes_played = $statsData->games->minutes ?? 0;
						$lineups = $statsData->games->lineups ?? 0;

						// Costruzione della riga JavaScript
						// NOTA: I valori stringa devono essere racchiusi tra apici singoli ('...')
						// I valori numerici non devono avere apici
						$s.= "['$playerName', $age, '$nationality', '$height', '$weight', $goals, $assists, $appearences, $minutes_played, $lineups],";
					}
					// Rimuove l'ultima virgola
					echo rtrim($s, ',');
				}
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
