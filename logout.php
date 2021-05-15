
			<?php
			session_start();
			require_once("function.php");
			?>
			<html>
				<head>
					<title>BlogYourOpinion-Match center</title>
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
								if(isset($_SESSION["id"])){
									echo'<h2 class="title is-2 " style="text-align:center">Login out</h2>';
									session_destroy();
									header("Refresh:1; url=index.php");
								}
								else{
									echo '<h2 class="title is-2 " style="text-align:center">Non sei loggato, quindi verrai riportato alla home</h2>';
									header("Refresh:1; url=index.php");
								}
								?>
							</div>
					</section>
				</body>
			</html>
