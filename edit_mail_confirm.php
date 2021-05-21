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
		<title>BlogYourOpinion-Email Edit Confirm</title>
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
						require_once 'jwt/src/BeforeValidException.php';
						require_once 'jwt/src/ExpiredException.php';
						require_once 'jwt/src/SignatureInvalidException.php';
						require_once 'jwt/src/JWT.php';

						use \Firebase\JWT\JWT;
						$key = "zlatan";

						try{
							$jwt = $_GET["id"];
							$decoded = JWT::decode($jwt, $key, array('HS256'));
							$decoded_array = (array) $decoded;
							JWT::$leeway = 60;

							$decoded_data = (array) $decoded_array["data"];
							$email = $decoded_data["email"];
							$id = $decoded_data["id"];
							$stmt = $db->prepare("UPDATE user SET email = ? WHERE id=?");
							$r = $stmt->execute([$email,$id]);
							echo '<h1 class="title is-4" style="text-align:center">Email successfully confirmed</h1>';
							} catch (Exception $e) {
								if($e->getMessage()=="Expired token"){
									echo '<h1 class="title is-3 " style="text-align:center">Time expired</h1>';
								}
								else{
										echo '<h1 class="title is-3 " style="text-align:center">Token manumited or not valid</h1>';
								}
							}
						?>
				</div>
			</div>
		</section>
	</body>
</html>
