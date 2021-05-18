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
		<title>BlogYourOpinion-Homepage</title>
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
								if($_SESSION["role"]=="redactor"){
									$cv="./cvdir/".$_GET["cv"];
									header('Content-Type: application/pdf');
									header('Content-Disposition: attachment; filename="'.$_GET["cv"].'"');
									header('Content-Length: ' . filesize($cv));
									ob_clean();
									flush();
									readfile($cv);
								}
								else{
									echo '<h2 class="title is-3 " style="text-align:center">You have not permission to see this content</h2>';
								}
							}
							else{
									echo '<h2 class="title is-3 " style="text-align:center">You have not permission to see this content</h2>';
								}
						?>
				</div>
			</div>
		</section>
	</body>
</html>
