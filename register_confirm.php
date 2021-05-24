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
		<title>BlogYourOpinion - Register confirm</title>
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
          if(isset($_GET["id"])){
						try{
							  $jwt = $_GET["id"];
							  $decoded = JWT::decode($jwt, $key, array('HS256'));
							  $decoded_array = (array) $decoded;
							  JWT::$leeway = 60;

							  $decoded_data = (array) $decoded_array["data"];
							  $nickname = $decoded_data["nickname"];
							  $name = $decoded_data["name"];
							  $surname = $decoded_data["surname"];
							  $email = $decoded_data["email"];
							  $team = $decoded_data["team"];
							  $password = $decoded_data["password"];
							  $date=date("Y-m-d");
								$stmt = $db->prepare("SELECT nickname,email FROM user WHERE nickname = ? OR email=?");
								$stmt->execute([$nickname, $email]);
							  if ($stmt->rowCount() == 1){
								  echo '<h1 class="title is-4 " style="text-align:center">Email already confirmed</h1>';
							  }
							  else{
									$stmt = $db->prepare("INSERT INTO user (nickname,name,surname,email,password,subscribed,role,team) VALUES (?,?,?,?,?,?,?,?)");
									$r = $stmt->execute([$nickname,$name,$surname,$email,$password,$date,1,$team]);
								echo '<h1 class="title is-4" style="text-align:center">Email confirmed successfully</h1>';
							  }
							} catch (Exception $e) {
								if($e->getMessage()=="Expired token"){
									echo '<h1 class="title is-4 " style="text-align:center">Time expired</h1>';
								}
								else{
										echo '<h1 class="title is-4 " style="text-align:center">Token manumited or not valid</h1>';
								}
							}
						}
						else{
							echo '<h1 class="title is-4" style="text-align:center">Token not sent</h1>';
						}
				?>
				</div>
			</div>
		</section>
	</body>
</html>
