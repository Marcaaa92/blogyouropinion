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
							// La funzione request() ora DEVE restituire l'oggetto JSON decodificato
							$response = request("https://api-football-v1.p.rapidapi.com/v3/fixtures?id=$match");
							
							// Otteniamo il primo (e unico) elemento della risposta
							$matchData = $response->response[0];

                            // Verifiche per evitare Fatal Errors se il match non esiste
                            if (empty($matchData)) {
                                echo "<h1 class=\"title is-3\" style=\"text-align:center\">Partita non trovata o dati non disponibili.</h1>";
                                exit();
                            }
							
							// --- 1. ASSEGNAZIONE DATI PRINCIPALI (Fixture, Teams, Goals) ---
							$date = str_replace(" ", "<br>", date("d-m-Y H:i", strtotime(substr($matchData->fixture->date, 0, 16))));
							$elapsed = $matchData->fixture->status->elapsed;
							$status = $matchData->fixture->status->long;
							$refree = $matchData->fixture->referee;
							// Venue name può essere null, usiamo Null Coalesce
							$venue = $matchData->fixture->venue->name ?? "N/A";
							
							// Dati Squadra Casa
							$homeTeamId = $matchData->teams->home->id;
							$homeTeam = $matchData->teams->home->name;
							$homeTeamLogo = $matchData->teams->home->logo;
							$homeTeamScore = $matchData->goals->home;

							// Dati Squadra Ospite
							$awayTeamId = $matchData->teams->away->id;
							$awayTeam = $matchData->teams->away->name;
							$awayTeamLogo = $matchData->teams->away->logo;
							$awayTeamScore = $matchData->goals->away;
							
							echo"<title>$homeTeam - $awayTeam </title>";
							
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
                            
							// --- 2. GESTIONE DEGLI EVENTI (Ora usiamo i dati dal $matchData) ---
                            $events = $matchData->events;

							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Events</h1></td><td></td><td><p></p></td><td></td></tr>";
							
							// Iteriamo sugli eventi
							foreach($events as $event){
								// Usiamo un alias per l'oggetto, per semplicità
								$elapsed = $event->time->elapsed;
								$teamName = $event->team->name;
								// I campi player e assist sono oggetti, dobbiamo accedere a 'name'
								$player = $event->player->name ?? null; 
								$type = $event->type;
								$detail = $event->detail;
								$assist = $event->assist->name ?? null; // Usa Null Coalesce
                                
                                // Controllo che l'evento non sia un 'Goal' senza dettagli 
                                if($type === "Card" && empty($detail)){
                                    continue;
                                }

								if($teamName == $homeTeam){
									if($type=="Goal"){
										if($assist==null){
											if($detail=="Penalty")
												echo "<tr><td><img src=\"./images/gol.webp\"></td><td><p>Penalty: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
											else if ($detail=="Missed Penalty")
												echo "<tr><td><img src=\"./images/gol.webp\"></td><td><p>Missed penalty: $player (R)</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
											else if($detail=="Own Goal")
												echo "<tr><td><img src=\"./images/gol.webp\"></td><td><p>Autogol: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
											else
												echo "<tr><td><img src=\"./images/gol.webp\"></td><td><p>Goal: $player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
										}
										else
											echo "<tr><td><img src=\"./images/gol.webp\"></td><td><p>Goal: $player | Assist: $assist</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									}
									else if($type=="Card"){
										if($detail=="Yellow Card")
											echo "<tr><td style='background-color:yellow'></td><td><p>$player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
										else
											echo "<tr><td style='background-color:red'></td><td><p>$player</p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";

									}
									else if($type=="subst"){
                                        // Nelle sostituzioni v3, 'player' è chi entra, 'assist' è chi esce (se presente)
										echo "<tr><td><img src=\"./images/sost.webp\"></td><td><p><font color='red'>$assist</font> | <font color='green'>$player</font></p></td><td></td><td><p>$elapsed</p></td><td></td><td><p></p></td><td></td></tr>";
									}
								}
								else{ // Away Team
									if($type=="Goal"){
										if($assist==null){
											if($detail=="Penalty")
												echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Penalty: $player</p></td><td><img src=\"./images/gol.webp\"></td></tr>";
											else if($detail=="Missed Penalty")
												echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Missed penalty: $player</p></td><td><img src=\"./images/gol.webp\"></td></tr>";
											else if($detail=="Own Goal")
												echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Autogol: $player</p></td><td><img src=\"./images/gol.webp\"></td></tr>";
											else
												echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Goal: $player</p></td><td><img src=\"./images/gol.webp\"></td></tr>";
										}
										else
											echo "<tr><td></td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>Goal: $player | Assist: $assist</p></td><td><img src=\"./images/gol.webp\"></td></tr>";
									}
									else if($type=="Card"){
										if($detail=="Yellow Card")
											echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>$player</p></td><td style='background-color:yellow'></td></tr>";
										else
											echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p>$player</p></td><td style='background-color:red'></td></tr>";
									}
									else if($type=="subst"){
										echo "<tr><td></td><td></td><td></td><td><p>$elapsed</p></td><td></td><td><p><font color='red'>$assist</font> | <font color='green'>$player</font></p></td><td><img src=\"./images/sost.webp\"></td></tr>";
									}
								}
							}

							// --- 3. GESTIONE DELLE STATISTICHE (Ora usiamo i dati dal $matchData) ---
                            $stats = $matchData->statistics;
                            
                            // Troviamo gli array di statistiche per casa e trasferta
                            $homeStats = $stats[0]->statistics;
                            $awayStats = $stats[1]->statistics;

							echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Stats</h1></td><td></td><td><p></p></td><td></td></tr>";
							
                            // Cicliamo su un array di statistiche (ad esempio quelle di casa) e stampiamo entrambe
                            for ($i = 0; $i < count($homeStats); $i++) {
                                $statName = $homeStats[$i]->type;
                                $homeValue = $homeStats[$i]->value ?? '0';
                                $awayValue = $awayStats[$i]->value ?? '0';

                                // Formatta le percentuali
                                if (strpos($statName, 'Passes %') !== false || strpos($statName, 'Ball Possession') !== false) {
                                    // Aggiungi un piccolo controllo per null se il valore è null (dovrebbe essere gestito da ?? '0')
                                    $homeValue = $homeValue ?: '0';
                                    $awayValue = $awayValue ?: '0';
                                }
                                
                                // Controlliamo se è un valore numerico valido o null (ad es. Offsides)
                                if (is_null($homeValue) || $homeValue === "") $homeValue = "-";
                                if (is_null($awayValue) || $awayValue === "") $awayValue = "-";

                                echo "<tr><td></td><td></td><td>$homeValue</td><td>$statName</td><td>$awayValue</td><td></td><td></td></tr>";
                            }


							// --- 4. GESTIONE DELLE FORMAZIONI (Ora usiamo i dati dal $matchData) ---
                            $lineups = $matchData->lineups;
                            
                            // Cerchiamo le formazioni per nome/ID (più sicuro)
                            $homeLineup = null;
                            $awayLineup = null;
                            foreach($lineups as $lineup) {
                                if ($lineup->team->name == $homeTeam) {
                                    $homeLineup = $lineup;
                                } elseif ($lineup->team->name == $awayTeam) {
                                    $awayLineup = $lineup;
                                }
                            }
                            
                            // Se i dati ci sono, procediamo
                            if ($homeLineup && $awayLineup) {
							
                                $homeCoach = $homeLineup->coach->name;
                                $homeFormation = $homeLineup->formation;
                                $awayCoach = $awayLineup->coach->name;
                                $awayFormation = $awayLineup->formation;
                                
                                echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-3\">Lineups</h1></td><td></td><td><p></p></td><td></td></tr>";
                                echo "<tr><td></td><td></td><td>$homeCoach</td><td></td><td>$awayCoach</td><td></td><td></td></tr>";
                                echo "<tr><td></td><td></td><td>$homeFormation</td><td></td><td>$awayFormation</td><td></td><td></td></tr>";
                                echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">11 initial</h1></td><td></td><td><p></p></td><td></td></tr>";
                                
                                $maxPlayers = max(count($homeLineup->startXI), count($awayLineup->startXI));
                                
                                for($i=0; $i < $maxPlayers; $i++){
                                    // Usiamo l'operatore Null Coalesce per gestire i giocatori mancanti (se un array è più corto dell'altro)
                                    $homePlayer = $homeLineup->startXI[$i]->player->name ?? '';
                                    $homeNumber = $homeLineup->startXI[$i]->player->number ?? '';
                                    $awayPlayer = $awayLineup->startXI[$i]->player->name ?? '';
                                    $awayNumber = $awayLineup->startXI[$i]->player->number ?? '';
                                    echo "<tr><td></td><td>$homeNumber</td><td>$homePlayer</td><td></td><td>$awayPlayer</td><td>$awayNumber</td><td></td></tr>";
                                }

                                echo "<tr><tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">Reserves</h1></td><td></td><td><p></p></td><td></td></tr>";
                                
                                $maxSubs = max(count($homeLineup->substitutes), count($awayLineup->substitutes));

                                for($i=0; $i < $maxSubs; $i++){
                                    $homePlayer = $homeLineup->substitutes[$i]->player->name ?? '';
                                    $homeNumber = $homeLineup->substitutes[$i]->player->number ?? '';
                                    $awayPlayer = $awayLineup->substitutes[$i]->player->name ?? '';
                                    $awayNumber = $awayLineup->substitutes[$i]->player->number ?? '';
                                    echo "<tr><td></td><td>$homeNumber</td><td>$homePlayer</td><td></td><td>$awayPlayer</td><td>$awayNumber</td><td></td></tr>";
                                }
                            } else {
                                echo "<tr><td></td><td></td><td></td><td><h1 class=\"title is-4\">Dati formazioni non disponibili</h1></td><td></td><td><p></p></td><td></td></tr>";
                            }
							echo "</table>";
							}
							?>
						</div>
				</div>
		</section>
	</body>
</html>