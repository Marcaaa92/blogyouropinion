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
		<title>BlogYourOpinion-Article</title>
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
							$stmt = $db->prepare("SELECT articles.approvated, articles.limited, articles.title, articles.subtitle, articles.article, articles.views, articles.date, articles.imgdir, user.nickname, category.categoryName, team.teamName, team.id as teamId FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.id=?");
							$stmt->execute([$idArticle]);

							if ($stmt->rowCount() == 1)
							{
								while ($row = $stmt->fetch())
								{
									if ($_SESSION["role"] == "journalist" || $_SESSION["role"] == "redactor")
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"],$row["views"]);
                    $stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
                    $stmt->execute([$idArticle]);
										if ($row["approvated"] == 1)
										{
											if ($row["limited"] == 1)
											{
												echo '<br><br><h1 class="title is-5 " style="color:grey">Approved in limited edition</h1>';
											}
											else
											{
												echo '<br><br><h1 class="title is-5 " style="color:grey">Approved</h1>';
											}
										}
										else
										{
											echo '<br><br><h1 class="title is-5 " style="color:grey">Pending or not approvated</h1>';
										}
									}
									else if (($_SESSION["role"] == "registred" || $_SESSION["role"]) == "removed" && $row["approvated"] == 1 && $row["limited"] == 1)
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"],$row["views"]);
                    $stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
                    $stmt->execute([$idArticle]);
									}
									else if ($row["limited"] == 0 && $row["approvated"] == 1)
									{
										loadArticle($row["title"], $row["subtitle"], $row["categoryName"], $row["teamName"], $row["date"], $row["nickname"],$row["article"],$row["teamId"],$row["imgdir"],$row["views"]);
                    $stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
                    $stmt->execute([$idArticle]);
									}
									else
									{
										echo '<h2 class="title is-3 " style="text-align:center">Cannot read this article or it has not been approved yet</h2>';
									}
								}
							}
							else
							{
								echo '<h2 class="title is-3 " style="text-align:center">Article not found</h2>';
							}
						}
						else
						{
							echo '<h2 class="title is-3 " style="text-align:center">You have not requested any articles.</h2>';
						}
						?>
	</body>
</html>
