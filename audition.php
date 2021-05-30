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
		<title>BlogYourOpinion-Audition page</title>
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
								 <h1 class="title is-3 " style="text-align:center">Journalist candidation form</h1>
								 <p style="text-align:center">Compile this form to candidate as journalist, you will recive an email if you have been accepted</p>
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
										<label class="label">Birthday:<br></label>
										<div class="control">
											 <input class="input" type="date" name="birthday" placeholder="Insert your birthday" value="';if(isset($_POST["birthday"])){echo $_POST["birthday"];};echo'" required></input>
										</div>
								 </div>
								 <div class="field">
										<label class="label">Short description of you and your passion</label>
										<div class="control">
											 <textarea class="textarea" type="text" name="description" placeholder="Insert something" minlength="100" maxlength="255" value="';if(isset($_POST["description"])){echo $_POST["description"];};echo'" required></textarea>
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
									echo '<h1 class="title is-3 " style="text-align:center">I\'m sorry, your request has been rejected</h1>';
								}
								else{
								echo '<h1 class="title is-3 " style="text-align:center">You have already made a request or you are already journalist or redactor, wait for the redactor to reply</h1>';
								}
							}
							?>
						<?php
							if (isset($_POST["request"]))
							{
								$description = strip_tags($_POST["description"]);
								$birthday = date($_POST["birthday"]);
								$filename = $_FILES['cv']['name'];
								if ($_FILES['cv']['type']=="application/pdf")
								{
									$filename = 'cvdir/' . $_SESSION["nickname"] . "-" . $filename;
									move_uploaded_file($_FILES['cv']['tmp_name'], $filename);
									$stmt = $db->prepare("INSERT INTO audition (cvDir,shortDescription,userId,status,birthday) VALUES(?,?,?,?,?)");
									$stmt->execute([$filename, $description, $id, "pending", $birthday]);
									echo '<h1 class="title is-4 " style="text-align:center">Request sent</h1>';
									header("Refresh:1; url=index.php");
								}
								else
								{
									echo '<h1 class="title is-4 " style="text-align:center">It isn\'t a pdf file, load a pdf file</h1>';
								}
							}
							?>
						</div>
					</div>
		</section>
	</body>
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</html>
