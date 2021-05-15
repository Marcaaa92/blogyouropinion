<?php
session_start();
require_once("function.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Article</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
	</head>
	<body>
		<?php
		loadNav();
		?>
						<?php
						if (isset($_GET["id"]))
						{
							$idArticle = $_GET["id"];
							$stmt = $db->prepare("SELECT articles.approvated, articles.limited, articles.title, articles.subtitle, articles.article, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.id=?");
							$stmt->execute([$idArticle]);
							if ($stmt->rowCount() == 1)
							{
								while ($row = $stmt->fetch())
								{
									if ($_SESSION["role"] == "journalist" || $_SESSION["role"] == "redactor")
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"]);
										if ($row["approvated"] == 1)
										{
											if ($row["limited"] == 1)
											{
												echo '<br><br><h1 class="title is-5 " style="color:grey">Approvato in modalita limitata</h1>';
											}
											else
											{
												echo '<br><br><h1 class="title is-5 " style="color:grey">Approvato</h1>';
											}
										}
										else
										{
											echo '<br><br><h1 class="title is-5 " style="color:grey">Pending o non approvato</h1>';
										}
									}
									else if (($_SESSION["role"] == "registred" || $_SESSION["role"]) == "removed" && $row["approvated"] == 1 && $row["limited"] == 1)
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"]);
									}
									else if ($row["limited"] == 0 && $row["approvated"] == 1)
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"]);
									}
									else
									{
										echo '<h2 class="title is-3 " style="text-align:center">Non puoi leggere questo articolo oppure non è stato approvato</h2>';
									}
								}
							}
							else
							{
								echo '<h2 class="title is-3 " style="text-align:center">Articolo non trovato</h2>';
							}
						}
						else
						{
							echo '<h2 class="title is-3 " style="text-align:center">Non hai richiesto articoli</h2>';
						}
						?>
	</body>
</html>
