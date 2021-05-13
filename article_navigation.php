<?php
session_start();
require_once("db_conn.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Serie A articles</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
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
							echo '<h1 class="title is-2" style="text-align:center">Serie A News</h1>';
							if(isset($_SESSION["id"])){
								$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 ORDER BY articles.date DESC");
								$stmt->execute([]);
								loadArticleMiniNavigation($stmt);
							}
							else{
								$stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 AND articles.limited=0 ORDER BY articles.date DESC");
								$stmt->execute([]);
								loadArticleMiniNavigation($stmt);
							}
							 ?>
						</div>
					</div>
		</section>
	</body>
</html>
