<?php
session_start();
require_once("db_conn.php");
?>
<html>
	<head>
		<title>BlogYourOpinion-Register</title>
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
			<div class="container is-max-desktop">
					<div class="columns is-desktop">
						<div class="column">
						<?php
							if(!isset($_SESSION["id"])){
						?>
						<h2 class="title is-3 " style="text-align:center">Sign up</h2>
							<form action="" method="post" class="box">
								<div class="field">
								  <label class="label">Name</label>
								  <div class="control">
									<input class="input" type="text" name="name" placeholder="Insert your name" minlength="2" maxlength="20" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Surname</label>
								  <div class="control">
									<input class="input" type="text" name="surname" placeholder="Insert your name surname" minlength="2" maxlength="20" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Nickname</label>
								  <div class="control">
									<input class="input" type="text" name="nickname" placeholder="Insert your name nickname" minlength="2" maxlength="20" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Email</label>
								  <div class="control">
									<input class="input" type="email" name="email" placeholder="Insert mail" minlength="6" maxlength="60" required>
								  </div>
								</div>
								<label class="label">Your favourite team</label>
								<div class="select">
									<select name="team">
										<?php
												$stmt = $db->prepare("SELECT * FROM team");
												$stmt->execute([]);
												while($row = $stmt->fetch()){
													echo '<option value="'. $row["id"] .'">'. $row["teamName"] .'</option>';
												}
										?>
									</select>
								</div>
								<div class="field">
								  <label class="label">Password</label>
								  <div class="control">
									<input class="input" type="password" name="password1" id="pw1" oninput="checkpw()" placeholder="Insert your name password" minlength="6" maxlength="20" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Reinserisci la password</label>
								  <div class="control">
									<input class="input" type="password" name="password2" id="pw2" oninput="checkpw()" placeholder="Re-nsert your name password" minlength="6" maxlength="20" required>
									<p class="label" id="check-text-pw"></p>
								  </div>
								</div>
								<div class="field is-grouped">
								  <div class="control">
									<button class="button is-link" type="submit" name="register" id="register" value="register">Sign up</button>
								  </div>
								</div>
							</form>
							<?php
							}
							else{
								echo '<h2 class="title is-3 " style="text-align:center">You are arleady signed up in</h2>';
							}
							?>
							<?php
							require_once 'jwt/src/BeforeValidException.php';
							require_once 'jwt/src/ExpiredException.php';
							require_once 'jwt/src/SignatureInvalidException.php';
							require_once 'jwt/src/JWT.php';
							use \Firebase\JWT\JWT;
							if(isset($_POST['register'])){
								if(isset($_POST["nickname"])||isset($_POST["name"])||isset($_POST["surname"])||isset($_POST["email"])||isset($_POST["password1"])||isset($_POST["password"])){
									$nickname=strip_tags($_POST["nickname"]);
									$name=strip_tags($_POST["name"]);
									$surname=strip_tags($_POST["surname"]);
									$email=strip_tags($_POST["email"]);
									$team=strip_tags($_POST["team"]);
									$password1=$_POST["password1"];
									$password2=$_POST["password2"];
									echo $mail;
									if($password1!=$password2){
										echo '<h2 class="title is-3 " style="text-align:center">Le password non coincidono</h2>';
									}
									else{
											$stmt = $db->prepare("SELECT nickname FROM user WHERE nickname = ?");
											$stmt->execute([$nickname]);

											if ($stmt->rowCount() == 0) {
												  $stmt = $db->prepare("SELECT email FROM user WHERE email = ?");
												  $stmt->execute([$email]);
												  if ($stmt->rowCount() == 0) {
													  $key = "zlatan";
														$issuedAt = time();
														$expirationTime = $issuedAt + 60*60;
													  $token = array(
														  "jti"  => base64_encode(rand(0, 10000000)),
															'exp' => $expirationTime,
														  "data" => array(
															  "nickname" => $nickname,
															  "name" => $name,
															  "surname" => $surname,
															  "email" => $email,
															  "team" => $team,
															  "password" => hash('sha512', $password1),
														  )
													  );
													$jwt = JWT::encode($token, $key);
													 mail($email, 'Email confirm - BlogYourOpinion', "Hey $name confirm your email following this link https://blogyouropinion.ddns.net/register_confirm.php?id=$jwt");
														echo '<h2 class="title is-4 " style="text-align:center">Check your email inbox to confirm your email</h2>';
												  }
												  else{
													echo '<h2 class="title is-3 " style="text-align:center">Email gia presa</h2>';
												  }
											}
											else{
												echo '<h2 class="title is-3 " style="text-align:center">Nickname gia preso</h2>';
											}
									}
								}
								else{
									echo '<h2 class="title is-3 " style="text-align:center">Manca qualche campo, controlla tutti i campi</h2>';
								}
							}
							?>
						</div>
					</div>
				</div>
		</section>
	</body>
	<script>
	function checkpw() {
	  var pw1 = document.getElementById("pw1").value;
	  var pw2 = document.getElementById("pw2").value;
	  if(pw1!=pw2){
	  	document.getElementById("check-text-pw").innerHTML = "Le password non coincidono";
			document.getElementById("register").disabled = true;
		}
	  else{
			var valore=document.getElementById("pw1").value;
			var verifica=/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&?\."]).*$/;
			if(valore.match(verifica)){
				document.getElementById("check-text-pw").innerHTML = "";
				document.getElementById("register").disabled = false;
			}
			else
			{
				document.getElementById("check-text-pw").innerHTML = "The password must contain more than 8 characters, downcase,uppercase letters,number and a characters like: '! # $ % & ? .'";
				document.getElementById("register").disabled = true;
			}
		}
	}
	</script>
</html>
