<?php
require_once("dbconnection.php");
date_default_timezone_set('Europe/Rome');
error_reporting(E_ERROR | E_PARSE);
function timeDiff($firstTime)
{
    $dt = new DateTime($firstTime);
    $lt = new DateTime();
    $dh = ($lt->getTimestamp() - $dt->getTimestamp()) / 3600;
    return $dh;
}
function request($link)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
    	CURLOPT_URL => $link,
    	CURLOPT_RETURNTRANSFER => true,
    	CURLOPT_FOLLOWLOCATION => true,
    	CURLOPT_ENCODING => "",
    	CURLOPT_MAXREDIRS => 10,
    	CURLOPT_TIMEOUT => 30,
    	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	CURLOPT_CUSTOMREQUEST => "GET",
    	CURLOPT_HTTPHEADER => [
    		"x-rapidapi-host: api-football-v1.p.rapidapi.com",
    		"x-rapidapi-key: " + $tokenApi
    	],
    ]);
    $response= json_decode(curl_exec($curl));
    curl_close($curl);
    return $response;
}
function sendMail($to, $subject, $message){
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= 'From: '.$from."\r\n".
      'Reply-To: '.$from."\r\n" .
      'X-Mailer: PHP/' . phpversion();
  mail($to, $subject, $message, $headers);
}
function sendMessage($id,$text){
  $website="https://api.telegram.org/bot".$token;
	$url="$website/sendMessage?chat_id=$id&parse_mode=html&text=".urlencode($text);
	file_get_contents($url);
}
function sendPhoto($id,$photo,$caption){
  $website="https://api.telegram.org/bot".$token;
	$url="$website/sendPhoto?chat_id=$id&photo=https://".$_SERVER['SERVER_NAME']."/".$photo."&parse_mode=html&caption=".urlencode($caption);
	file_get_contents($url);
}
function loadNav()
{
    echo '
    <script src="function.js"></script>
    <script src="https://kit.fontawesome.com/ee36c308c7.js" crossorigin="anonymous"></script>
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
