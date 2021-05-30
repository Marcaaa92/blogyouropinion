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
		<title>BlogYourOpinion-Create Article</title>
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
							<?php
								if($_SESSION["role"]=="journalist"||$_SESSION["role"]=="redactor"){
									echo'
                  <h1 class="title is-3 " style="text-align:center">Article publish form</h1>
										<form action="" method="post" enctype="multipart/form-data" class="box">
													<div class="field">
													  <label class="label">Title</label>
													  <div class="control">
														<input class="input" type="text" name="title" placeholder="Insert the title of article" maxlength="255" value="';if(isset($_POST["title"])){echo $_POST["title"];};echo'" required>
													</div>
													<div class="field">
													  <label class="label">Subtitle</label>
													  <div class="control">
														<input class="input" type="text" name="subtitle" id="article" placeholder="Insert the subtitle of article" maxlength="255" required>
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
															<textarea class="textarea" type="text" name="article" placeholder="Insert the contents of article" value="';if(isset($_POST["article"])){echo $_POST["article"];};echo'" maxlength="16777215"></textarea>
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
																Load article pic...
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
									echo '<h1 class="title is-4 " style="text-align:center">You have not permission to create articles</h1>';
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
									if($_FILES['pic']['type']=="image/png"||$_FILES['pic']['type']=="image/jpeg"){
											$filename='imgarticle/'.$filename.".webp";
												if(stripos($filename, ".jpg")||stripos($filename, ".jpeg")){
												$img = imagecreatefromjpeg($_FILES['pic']['tmp_name']);
												}
												else{
												$img = imagecreatefrompng($_FILES['pic']['tmp_name']);
												}
												imagewebp($img, $filename, 85);
											if(isset($_POST["limited"])){
												$limited=1;
											}
											$stmt = $db->prepare("INSERT INTO articles (journalist,limited,approvated, title, subtitle, category,article,date,team, imgdir, views) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
											$stmt->execute([$id,$limited,0,$title,$subtitle,$category,$article,$date,$team,$filename,0]);
											$idInsert = $db->lastInsertId();
											echo '<a href="article.php?id='. $idInsert. '"><p>Look at the article just written</p></a>';
									}
									else{
											echo '<h1 class="title is-4 " style="text-align:center">It isn\'t a jpg, png or jpeg file, load it in this format</h1>';
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
