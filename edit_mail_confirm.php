<?php
session_start();
require_once("db_conn.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Email Edit Confirm</title>
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
							echo '<h2 class="title is-4" style="text-align:center">Risultato: Email successful confirmed</h2>';
							} catch (Exception $e) {
								if($e->getMessage()=="Expired token"){
									echo '<h2 class="title is-3 " style="text-align:center">Time expired</h2>';
								}
								else{
										echo '<h2 class="title is-3 " style="text-align:center">Token manumited or not valid</h2>';
								}
							}
						?>
				</div>
			</div>
		</section>
	</body>
</html>
