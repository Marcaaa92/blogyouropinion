
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
					<title>BlogYourOpinion - Logout</title>
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
								if(isset($_SESSION["id"])){
									echo'<h1 class="title is-2 " style="text-align:center">Logging out</h1>';
									session_destroy();
									header("Refresh:1; url=index.php");
								}
								else{
									echo '<h1 class="title is-2 " style="text-align:center">You are not logged in, therefore, you will be taken to the homepage</h1>';
									header("Refresh:1; url=index.php");
								}
								?>
							</div>
					</section>
				</body>
			</html>
