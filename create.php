<?php
session_start();
require_once("function.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Create Article</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
	</head>
	<body>
		<?php
			loadNav();
		?>
		<section class="section is-four-fifth">
			<div class="columns is-desktop">
				<div class="column">
					<h2 class="title is-3 " style="text-align:center">Form to pubblish an article</h2>
							<?php
								if($_SESSION["role"]=="journalist"||$_SESSION["role"]=="redactor"){
									echo'
										<form action="" method="post" enctype="multipart/form-data" class="box">
													<div class="field">
													  <label class="label">Title(255 max characters)</label>
													  <div class="control">
														<input class="input" type="text" name="title" placeholder="Insert the title of article" maxlength="255" required>
													</div>
													<div class="field">
													  <label class="label">Subtitle(255 max characters)</label>
													  <div class="control">
														<input class="input" type="text" name="subtitle" placeholder="Insert the subtitle of article" maxlength="255" required>
													</div>
													</div>
													<div class="select">
														<select name="category">';
														$stmt = $db->prepare("SELECT * FROM category");
														$stmt->execute([]);
														while($row = $stmt->fetch()){
															echo '<option value="'. $row["id"] .'">'. $row["categoryName"] .'</option>';
														}
										echo '
														</select>
													</div>
													<div class="select">
														<select name="team">';
														$stmt = $db->prepare("SELECT * FROM team");
														$stmt->execute([]);
														while($row = $stmt->fetch()){
															echo '<option value="'. $row["id"] .'">'. $row["teamName"] .'</option>';
														}
										echo '
														</select>
													</div>
													<div class="field">
														<label class="label">The contents of article (write with the markdown sintax)</label>
														<div class="markdown-editor">
															<textarea class="textarea" type="text" name="article" placeholder="Insert the contents of article" maxlength="16777215"></textarea>
															</div>
													</div>
													<div class="file">
													  <label class="file-label">
															<input class="file-input" type="file" name="pic" required>
															<span class="file-cta">
															  <span class="file-icon">
																<i class="fas fa-upload"></i>
															  </span>
															  <span class="file-label">
																Load artticle pic...
															  </span>
															</span>
													  </label>
													</div>
													<label class="radio">
														<input type="checkbox" name="limited">
														Limited?
													</label>
													<br>
													<div class="field is-grouped">
														<div class="control">
														<br>
															<button class="button is-link" type="submit" name="send" id="send" value="send">Send article</button>
														</div>
													</div>
												</form>';
								}
								else{
									echo '<h2 class="title is-3 " style="text-align:center">You have not permission to create articles</h2>';
								}

								if(isset($_POST["send"])){
									require_once "parsedown/Parsedown.php";
									$Parsedown = new Parsedown();
									$id=$_SESSION["id"];
									$title=strip_tags($_POST["title"], ENT_QUOTES);
									$subtitle=strip_tags($_POST["subtitle"], ENT_QUOTES);
									$category=$_POST["category"];
									$team=$_POST["team"];
									$article=$Parsedown->text($_POST["article"]);
									$limited=0;
									$filename = $_FILES['pic']['name'];
									$date=date("Y-m-d H:i:s");
									if(stripos($filename, ".jpg")||stripos($filename, ".png")||stripos($filename, ".jpeg")){
											$filename='imgarticle/'.$filename;
											//	move_uploaded_file($_FILES['pic']['tmp_name'],$filename);
												if(stripos($filename, ".jpg")||stripos($filename, ".jpeg")){
												$img = imagecreatefromjpeg($_FILES['pic']['tmp_name']);
												}
												else{
												$img = imagecreatefrompng($_FILES['pic']['tmp_name']);
												}
												imagejpeg($img, $filename, 95);
											if(isset($_POST["limited"])){
												$limited=1;
											}
											$stmt = $db->prepare("INSERT INTO articles (journalist,limited,approvated, title, subtitle, category,article,date,team, imgdir) VALUES(?,?,?,?,?,?,?,?,?,?)");
											$stmt->execute([$id,$limited,0,$title,$subtitle,$category,$article,$date,$team,$filename]);
											$idInsert = $db->lastInsertId();
											echo '<a href="article.php?id='. $idInsert. '"><p>look at the article just written</p></a>';
									}
									else{
											echo '<h2 class="title is-3 " style="text-align:center">It isnt a jpg, png or jpeg file, load in this format</h2>';
									}
								}
							?>
						</div>
					</div>
				</div>
		</section>
	</body>
	<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
	<script>
		var simplemde = new SimpleMDE();
	</script>
</html>
