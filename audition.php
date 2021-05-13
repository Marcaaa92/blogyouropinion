<?php
session_start();
require_once ("db_conn.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Audition page</title>
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
							$id = $_SESSION["id"];
							$stmt = $db->prepare("SELECT id FROM audition WHERE userId = ?");
							$stmt->execute([$id]);
							if ($stmt->rowCount() == 0)
							{
								echo '
								<form action="" method="post" enctype="multipart/form-data" class="box">
								 <h2 class="title is-3 " style="text-align:center">Form to canditate as journalist</h2>
								 <p style="text-align:center">Compile this form to candidate as journalist, you will recive an email if you have been accept</p>
								 <br>
								 <div class="file">
										<label class="file-label">
										<input class="file-input" type="file" name="cv" required>
										<span class="file-cta">
										<span class="file-icon">
										<i class="fas fa-upload"></i>
										</span>
										<span class="file-label">
										Load your curriculum vitae...
										</span>
										</span>
										</label>
								 </div>
								 <br>
								 <div class="field is-grouped">
										<label class="label">Birthday: </label>
										<br>
										<div class="control">
											 <input class="input" type="date" name="birthday" placeholder="Insert " required></input>
										</div>
								 </div>
								 <div class="field">
										<label class="label">Location: </label>
										<div class="control">
											 <input class="input" type="text" name="Location" placeholder="Insert " required></input>
										</div>
								 </div>
								 <div class="field">
										<label class="label">Short description of you and your passion</label>
										<div class="control">
											 <textarea class="textarea" type="text" name="description" placeholder="Insert " minlength="100" maxlength="255" required></textarea>
										</div>
								 </div>
								 <div class="field is-grouped">
										<div class="control">
											 <button class="button is-link" type="submit" name="request" id="request" value="request">Send request</button>
										</div>
								 </div>
								</form>';
							}
							else
							{
								$stmt = $db->prepare("SELECT status FROM audition WHERE userId = ?");
								$stmt->execute([$id]);
								$row = $stmt->fetch();
								if($row["status"]=="rejected"){
									echo '<h2 class="title is-3 " style="text-align:center">Im sorry, your request has been rejected</h2>';
								}
								else{
								echo '<h2 class="title is-3 " style="text-align:center">You have already made a request or you are already journalist or redactor, wait for the response of the redactor</h2>';
								}
							}
							?>
						<?php
							if (isset($_POST["request"]))
							{
								$description = htmlspecialchars($_POST["description"]);
								$birthday = date($_POST["birthday"]);
								$location = htmlspecialchars($_POST["location"]);
								$filename = $_FILES['cv']['name'];
								if (stripos($filename, ".pdf"))
								{
									$filename = 'cvdir/' . $_SESSION["nickname"] . "-" . $filename;
									move_uploaded_file($_FILES['cv']['tmp_name'], $filename);
									$stmt = $db->prepare("INSERT INTO audition (cvDir,shortDescription,userId,status,birthday,location) VALUES(?,?,?,?,?,?)");
									$stmt->execute([$filename, $description, $id, "pending", $birthday, $location]);
									echo '<h2 class="title is-3 " style="text-align:center">Request sent</h2>';
									header("Refresh:1; url=index.php");
								}
								else
								{
									echo '<h2 class="title is-3 " style="text-align:center">It isnt a pdf file, load a pdf file</h2>';
								}
							}
							?>
						</div>
					</div>
		</section>
	</body>
</html>
