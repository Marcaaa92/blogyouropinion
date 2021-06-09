<?php
session_start();
require_once("function.php");
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>BlogYourOpinion - Request List</title>
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
	</head>
	<body>
		<?php
			loadNav();
		?>
		<section class="section">
			<div class="columns is-desktop">
				<div class="column is-full-mobile is-full-tablet is-full-desktop is-half-widescreen is-half-fullhd">
					<div class="container is-max-widescreen">
							<?php
							if($_SESSION["role"]=="redactor"){
								if(isset($_GET["action"])){
									if($_GET["type"]=="audition"){
										if($_GET["action"]=="accepted"){
											$stmt = $db->prepare("UPDATE audition SET status = ? WHERE id=?");
											$stmt->execute(["accepted", $_GET["id"]]);
											$stmt = $db->prepare("UPDATE user SET role = ? WHERE id=?");
											$stmt->execute([2, $_GET["iduser"]]);
											sendMail($_GET["email"], 'Result of your audition BlogYourOpinion', "hello, your request to become a journalist has been accepted, now you can publish articles in the dedicated button of the site");
										}
										else if($_GET["action"]=="rejected")
										{
											$stmt = $db->prepare("UPDATE audition SET status = ? WHERE id=?");
											$stmt->execute(["rejected", $_GET["id"]]);
											sendMail($_GET["email"], 'Result of your audition BlogYourOpinion', "hello, your request to become a journalist has been rejected.");
										}
										else if($_GET["action"]=="remove")
										{
											$stmt = $db->prepare("UPDATE audition SET status = ? WHERE id=?");
											$stmt->execute(["removed", $_GET["id"]]);
											$stmt = $db->prepare("UPDATE user SET role = ? WHERE id=?");
											$stmt->execute([4, $_GET["iduser"]]);
											sendMail($_GET["email"], 'Result of your audition BlogYourOpinion', "hello, you have been removed from the role 'journalist'");
										}
                    header("Refresh:0; url=request.php");
									}
									else{
										$stmt = $db->prepare("UPDATE articles SET approvated = ? WHERE id=?");
										$stmt->execute([1, $_GET["id"]]);
										sendMail($_GET["email"], 'Result of your article BlogYourOpinion', "Your article has been approved<br>Link: https://".$_SERVER['SERVER_NAME']."/article.php?id=". $_GET["id"]);
										$stmt = $db->prepare("SELECT user.telegramId, articles.title, articles.subtitle, articles.imgdir FROM user JOIN articles WHERE articles.id=? AND telegramId IS NOT NULL AND articles.team=user.team");
										$stmt->execute([$_GET["id"]]);
										while($row = $stmt->fetch()){
											sleep(0.03);
											sendPhoto($row["telegramId"],$row["imgdir"],"New article pubblished!\n<b>".$row["title"]."</b>\n<i>".$row["subtitle"]."</i>\nLink:"."https://".$_SERVER['SERVER_NAME']."/article.php?id=". $_GET["id"]);
                    }
                    header("Refresh:0; url=request.php");
									}
								}
								?>
								<?php
								$stmt = $db->prepare("SELECT audition.id,audition.status, user.id AS userId, user.name, user.surname, user.nickname, user.email, audition.cvDir, audition.shortDescription FROM audition JOIN user ON user.id = audition.userId ORDER BY audition.id DESC");
								$stmt->execute([]);
								echo'<div class="table-container"><table class="table">
								<thead><tr><th>ID</th><th>Name</th><th>Surname</th><th>Nickname</th><th>Email</th><th>cvDir</th><th>Short description</th><th>Status</th></tr></thead><tbody>';
								while($row = $stmt->fetch()){
									$idUser=$row["userId"];
									$idReq=$row["id"];
									$nameReq=$row["name"];
									$emailReq=$row["email"];
									if($row["status"]=="pending"){
									echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["surname"]."</td><td>".$row["nickname"]."</td><td>".$row["email"]."</td><td><a href='cv.php?cv=".substr($row["cvDir"],6)."' >".substr($row["cvDir"],6)."</a></td><td>".$row["shortDescription"]."</td><td><a href='request.php?iduser=$idUser&id=$idReq&type=audition&action=accepted&name=$nameReq&email=$emailReq'>Accept</a>/<a href='request.php?iduser=$idUser&id=$idReq&action=rejected&name=$nameReq&email=$emailReq&type=audition'>Reject</a></td></tr>";
									}
									else if($row["status"]=="accepted"){
									echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["surname"]."</td><td>".$row["nickname"]."</td><td>".$row["email"]."</td><td><a href='cv.php?cv=".substr($row["cvDir"],6)."' >".substr($row["cvDir"],6)."</a></td><td>".$row["shortDescription"]."</td><td>Accepted<br><a href='request.php?iduser=$idUser&id=$idReq&type=audition&action=remove&name=$nameReq&email=$emailReq'>Remove role</a></td></tr>";
									}
									else if($row["status"]=="rejected"){
									echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["surname"]."</td><td>".$row["nickname"]."</td><td>".$row["email"]."</td><td><a href='cv.php?cv=".substr($row["cvDir"],6)."' >".substr($row["cvDir"],6)."</a></td><td>".$row["shortDescription"]."</td><td>Rejected</td></tr>";
									}
									else if($row["status"]=="removed"){
									echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["surname"]."</td><td>".$row["nickname"]."</td><td>".$row["email"]."</td><td><a href='cv.php?cv=".substr($row["cvDir"],6)."' >".substr($row["cvDir"],6)."</a></td><td>".$row["shortDescription"]."</td><td>Removed</td></tr>";
									}
								}
								echo'</tbody></table></div>';
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You have not permission to see this page</h1>';
							}
							?>
						</div>
					</div>
				<div class="column is-full-mobile is-full-tablet is-full-desktop is-half-widescreen is-half-fullhd">
					<div class="container is-max-widescreen">
							<?php
							if($_SESSION["role"]=="redactor"){
								$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.date, user.nickname, user.email FROM articles JOIN user ON user.id=articles.journalist ORDER BY articles.id DESC");
								$stmt->execute([]);
								echo'<div class="table-container"><table class="table" style="white-space: normal;">
								<thead><tr><th>ID</th><th>Journalist</th><th>Title</th><th>Date</th><th>Limited</th><th>Status</th></tr></thead><tbody>';
								while($row = $stmt->fetch()){
									$idReq=$row["id"];
									if($row["approvated"]==0){
									$email=$row["email"];
									echo "<tr><td>".$row["id"]."</td><td>".$row["nickname"]."</td><td><a href='article.php?id=".$row["id"]."''>".$row["title"]."</a></td><td>".$row["date"]."</td><td>".$row["limited"]."</td><td><a href='request.php?id=$idReq&email=$email&action=accepted&type=article'>Accept</a></td></tr>";
									}
									else if($row["approvated"]==1){
										echo "<tr><td>".$row["id"]."</td><td>".$row["nickname"]."</td><td><a href='article.php?id=".$row["id"]."''>".$row["title"]."</a></td><td>".$row["date"]."</td><td>".$row["limited"]."</td><td>Accepted</td></tr>";
									}
								}
								echo'</tbody></table></div>';
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You have not permission to see this page</h1>';
							}
							?>
						</div>
					</div>
				</div>
		</section>
	</body>
</html>
