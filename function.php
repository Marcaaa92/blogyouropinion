<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require("dbconnection.php");
date_default_timezone_set('Europe/Rome');
error_reporting(E_ERROR | E_PARSE);
require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';

$smtp_host = 'smtp.gmail.com'; // Es. 'smtp.gmail.com' o il tuo server hosting
$smtp_port = 587; // Solitamente 587 (TLS) o 465 (SSL)
$from = 'server.marca.mail@gmail.com'; // L'indirizzo che apparirà come mittente


function timeDiff($firstTime)
{
    $dt = new DateTime($firstTime);
    $lt = new DateTime();
    $dh = ($lt->getTimestamp() - $dt->getTimestamp()) / 3600;
    return $dh;
}
function request($link)
{
    global $tokenApi;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
      CURLOPT_URL => $link,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_RETURNTRANSFER => true, // ESSENZIALE! Assicurati che sia qui.
      CURLOPT_TIMEOUT => 30, // Aggiungi un timeout per non bloccare
      CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: api-football-v1.p.rapidapi.com",
        "x-rapidapi-key: " . $tokenApi,
      ],
    ]);
    
    $response_exec = curl_exec($curl);
    $curl_error = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($curl_error) {
        error_log("ERRORE cURL: " . $curl_error);
    }

    if ($response_exec === false || empty($response_exec)) {
        return null;
    }

    return json_decode($response_exec);
}
function sendMail($to, $subject, $message){
    // Rendi accessibili le variabili globali del server SMTP e del mittente
    global $smtp_host, $smtp_username, $smtp_password, $smtp_port, $from; 

    // 1. Inizializza PHPMailer
    $mail = new PHPMailer(true); // 'true' abilita le eccezioni
    
    try {
        // 2. Configurazione del server SMTP
        $mail->isSMTP();                                            // Usa SMTP
        $mail->Host       = $smtp_host;                             // Specifica il server SMTP
        $mail->SMTPAuth   = true;                                   // Abilita l'autenticazione SMTP
        $mail->Username   = $smtp_username;                         // Nome utente SMTP (la tua email)
        $mail->Password   = $smtp_password;                         // Password SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Abilita TLS encryption (usare ENCRYPTION_SMTPS per la porta 465)
        $mail->Port       = $smtp_port;                             // Porta TCP (es. 587)
        
        $mail->CharSet = 'UTF-8';                                   // Imposta la codifica UTF-8
        $mail->isHTML(true);                                        // Imposta il formato email in HTML
        
        // 3. Destinatari e Mittente
        $mail->setFrom($from, 'Blogyouropinion');                   // Imposta il mittente
        $mail->addAddress($to);                                     // Aggiunge il destinatario
                
        // 4. Contenuto della mail
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        // Se si vuole un testo alternativo (non HTML)
        $mail->AltBody = strip_tags($message); 

        // 5. Invio
        $mail->send();
        
        return true;
        
    } catch (Exception $e) {
        // Se c'è un errore, viene catturato qui.
        error_log("Errore invio mail con PHPMailer: {$mail->ErrorInfo}");
        return false;
    }
}
function sendMessage($id,$text){
global $tokenTelegram; 
    
    // Invia i dati tramite POST per maggiore affidabilità con Telegram API
    $data = [
        'chat_id' => $id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    $website = "https://api.telegram.org/bot" . $tokenTelegram . "/sendMessage";
    
    // Inizializzazione cURL
    $ch = curl_init($website);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Log di controllo
    if ($http_code != 200) {
        // Se Telegram non risponde con 200 OK, logga l'errore per il debug
        error_log("Telegram API Error! Chat ID: {$id}, Status: {$http_code}, cURL Error: {$curl_error}, Response: " . $response);
    }
    
    // Puoi restituire il risultato se necessario, ma non è obbligatorio
    return $response;
}
function sendPhoto($id, $photo, $caption) {
    // 1. Rendi accessibile la variabile globale $token
    global $tokenTelegram; 
    
    // Per sicurezza, pulisci il token
    $clean_token = trim($tokenTelegram); 

    // 2. Costruisci l'URL della foto
    // ATTENZIONE: Se il tuo sito non usa HTTPS, la chiamata fallirà.
    $photo_url = "https://" . $_SERVER['SERVER_NAME'] . "/blogyouropinion/" . $photo;
    // 3. Dati da inviare al server Telegram in POST
    $data = [
        'chat_id' => $id,
        'photo' => $photo_url,
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];
    
    $website = "https://api.telegram.org/bot" . $clean_token . "/sendPhoto";
    
    // --- Utilizzo di cURL (metodo più robusto) ---
    $ch = curl_init($website);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Log di controllo in caso di errore
    if ($http_code != 200) {
        error_log("Telegram sendPhoto API Error! Chat ID: {$id}, Status: {$http_code}, cURL Error: {$curl_error}, Response: " . $response);
    }
    
    return $response;
}
function loadNav()
{
    echo '
    <script src="function.js"></script>
    <script src="https://kit.fontawesome.com/22edea3724.js" crossorigin="anonymous"></script>
      	<nav class="navbar is-link is-fixed-top" role="navigation" aria-label="main navigation">
      			<div class="navbar-brand">
      				<a class="navbar-item" href="index.php">
      				    <amp-img><img src="images/logo.webp" alt="website-logo"></amp-img>
      				</a>
              <div class="navbar-burger burger" data-target="navMenubd-example">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
              </div>
              </a>
          </div>
          <div id="navMenubd-example" class="navbar-menu">
            <div class="navbar-end">';
    if (isset($_SESSION["id"]))
    {
        echo '
      						<div class="navbar-item has-dropdown is-hoverable">
      							<a class="navbar-link">
      									Edit profile
      							</a>

      							<div class="navbar-dropdown">
      									<a class="navbar-item" href="edit.php?action=nickname">
      									Nickname
      									</a>
      									<a class="navbar-item" href="edit.php?action=email">
      									Email
      									</a>
      									<a class="navbar-item" href="edit.php?action=password">
      									Password
      									</a>
      									<a class="navbar-item" href="edit.php?action=edit-team">
      									Edit team
      									</a>
                        <a class="navbar-item" href="edit.php?action=delete">
                        Delete profile
                        </a>
      									<a class="navbar-item" href="edit.php?action=show">
      									Show only profile
      									</a>
      							</div>
                  </div>
                  <div class="navbar-item">
              			<div class="buttons">
                    <a class="button" href="https://t.me/blogyouropinionfeed_bot" onmouseover="telegramInfo()">
                      <span class="icon">
                        <i class="fab fa-telegram" aria-hidden="true"></i>
                      </span>
                      <span>Telegram bot</span>
                    </a>';
        if ($_SESSION["role"] == "redactor")
        {
            echo '
								<a class="button is-light" href="request.php">
									Request area
								</a>';
        }
        else if ($_SESSION["role"] == "journalist")
        {
            echo '<a class="button is-light" href="create.php">
									   Create articles
								  </a>';
        }
        else if ($_SESSION["role"] == "removed")
        {

        }
        else
        {
            echo '<a class="button is-light" href="audition.php">
									   Become a journalist
								  </a>';
        }
        echo '<a class="button is-light" href="logout.php">
                Log out
              </a>
              </div>
            </div>';
    }
    else
    {
        echo '<div class="navbar-item">
    						<div class="buttons">
    							<a class="button is-link" href="register_page.php">
    								<strong>Sign up</strong>
    							</a>
    							<a class="button is-light" href="login_page.php">
    								Log in
    							</a>
                </div>
              </div>';
    }
echo '    </div>
        </div>
      </nav>';
}
function loadArticle($title, $subtitle, $categoryName, $team, $date, $nickname,$article,$teamId,$image,$views)
{
    echo '
		<section class="section">
  		<div class="columns is-desktop">
    		<div class="column">
    		  <div class="container is-max-widescreen">
          <h1 class="title is-1">'.$title.'</h1>
          <p>Written by: '.$nickname.'<br>'.date("d-m-Y H:i", strtotime($date)).' - <a href="team.php?id='.$teamId.'&action=article">'.$team.'</a></p>
          <div style="text-align:center"><img src="'.substr_replace($image, "webp/", 11, 0).'.webp" alt="Image" loading="lazy"></div>
          <h1 class="title is-3">'.$subtitle.'</h1>
            '.$article.'
            <br>
            Views: '.$views.'
          </div>
        </div>
      </div>
    </section>';
}
function loadArticleMini($stmt,$categoryName){
  if ($stmt->rowCount() != 0) {
    if($categoryName!="")
    echo '<h1 class="title is-3" style="text-align:center">'.$categoryName.'</h1>';
    echo '<div class="columns is-desktop">';
    while($row = $stmt->fetch()){
      echo '
      <div class="column is-full-mobile is-full-tablet is-full-desktop is-one-fifth-widescreen is-one-fifth-fullhd">
        <div class="box">
          <article class="media">
          <div class="rows">
           <div class="row">
              <figure class="image" style="width:80%">
                <img src="'.substr_replace($row["imgdir"], "webp/", 11, 0).'.webp" loading="lazy" alt="Image">
              </figure>
            </div>
             <div class="row">
                <p>
                  <a href="article.php?id='.$row["id"].'"><strong class="titleart">'.$row["title"].'</strong></a><br> <small>@'.$row["nickname"].'</small> <br><small>'.str_replace(" ", "<br>", date("d-m-Y H:i", strtotime($row["date"]))).'</small><br><a href="./team.php?id='.$row["teamId"].'&action=article"><strong>'.$row["teamName"].'</strong></a>
                    <p class="titleart1">'.$row["subtitle"].'</p>
                </p>
            </div>
            </div>
          </article>
        </div>
      </div>';
    }
    echo '</div>';
  }
}
function loadArticleMiniNavigation($stmt){
  if ($stmt->rowCount() != 0) {
    $i=0;
    echo '<div class="columns is-desktop">';
    while($row = $stmt->fetch()){
      $i++;
      if($i==5){
        $i=0;
        echo '</div><div class="columns is-desktop">';
      }
      echo '
      <div class="column is-full-mobile is-full-tablet is-full-desktop is-one-fifth-widescreen is-one-fifth-fullhd">
        <div class="box">
          <article class="media">
          <div class="rows">
           <div class="row">
              <figure class="image" style="width:80%">
                <img src="'.substr_replace($row["imgdir"], "webp/", 11, 0).'.webp" loading="lazy" alt="Image" >
              </figure>
            </div>
             <div class="row">
                <p>
                  <a  href="article.php?id='.$row["id"].'"><strong class="titleart">'.$row["title"].'</strong></a><br> <small>@'.$row["nickname"].'</small> <br><small>'.str_replace(" ", "<br>", date("d-m-Y H:i", strtotime($row["date"]))).'</small><br><a href="./team.php?id='.$row["teamId"].'&action=article"><strong>'.$row["teamName"].'</strong></a>
                    <p class="titleart1">'.$row["subtitle"].'</p>
                </p>
            </div>
            </div>
          </article>
        </div>
        </div>';
    }
  }
  else{
    echo '<h1 class="title is-6" style="text-align:center">No articles...</h1>';
  }
}
$stmt = $db->prepare("SELECT role.roleName FROM user JOIN role ON role.id = user.role WHERE user.id=?");
$stmt->execute([$_SESSION["id"]]);
$row = $stmt->fetch();
$_SESSION["role"] = $row["roleName"];
?>
