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
		<title>BlogYourOpinion - Register</title>
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
						<h1 class="title is-3 " style="text-align:center">Sign up</h1>
							<form action="" method="post" class="box">
								<div class="field">
								  <label class="label">Name</label>
								  <div class="control">
									<input class="input" type="text" name="name" placeholder="Insert your name" minlength="2" maxlength="20" value="<?php if(isset($_POST["name"])){echo $_POST["name"];}?>" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Surname</label>
								  <div class="control">
									<input class="input" type="text" name="surname" placeholder="Insert your name surname" minlength="2" maxlength="20" value="<?php if(isset($_POST["surname"])){echo $_POST["surname"];}?>" required>
								  </div>
								</div>

								<div class="field">
								  <label class="label">Nickname</label>
								  <div class="control has-icons-left">
									<input class="input" type="text" name="nickname" id="nickname" placeholder="Insert your name nickname" minlength="2" maxlength="20" value="<?php if(isset($_POST["nickname"])){echo $_POST["nickname"];}?>" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                  </span>
                  </div>
								</div>

								<div class="field">
								  <label class="label">Email</label>
								  <div class="control has-icons-left">
									<input class="input" type="email" name="email" id="email" placeholder="Insert mail" minlength="6" maxlength="60" value="<?php if(isset($_POST["email"])){echo $_POST["email"];}?>" required>
                  <span class="icon is-small is-left">
                      <i class="fas fa-envelope"></i>
                  </span>
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
								  <div class="control has-icons-left">
									<input class="input" type="password" name="password1" id="pw1" oninput="checkpw()" placeholder="Insert your password" minlength="6" maxlength="20" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                  </span>
                  </div>
								</div>

								<div class="field">
								  <label class="label">Confirm password</label>
								  <div class="control has-icons-left">
									<input class="input" type="password" name="password2" id="pw2" oninput="checkpw()" placeholder="Re-insert your name password" minlength="6" maxlength="20" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                  </span>
                  <p class="label" id="check-text-pw"></p>
								  </div>
								</div>
								<div class="field is-grouped">
								  <div class="control">
									<button class="button is-link" type="submit" name="register" id="register" value="register" disabled>Sign up</button>
								  </div>
								</div>
							</form>
							<?php
							}
							else{
								echo '<h1 class="title is-3 " style="text-align:center">You are arleady logged in</h1>';
							}
							?>
							<?php
							require_once 'jwt/src/BeforeValidException.php';
							require_once 'jwt/src/ExpiredException.php';
							require_once 'jwt/src/SignatureInvalidException.php';
							require_once 'jwt/src/JWT.php';
							use \Firebase\JWT\JWT;
              $key = "zlatan";
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
										echo '<h1 class="title is-4 " style="text-align:center">Passwords do not match</h1>';
									}
									else{
											$stmt = $db->prepare("SELECT nickname FROM user WHERE nickname = ?");
											$stmt->execute([$nickname]);

											if ($stmt->rowCount() == 0) {
												  $stmt = $db->prepare("SELECT email FROM user WHERE email = ?");
												  $stmt->execute([$email]);
												  if ($stmt->rowCount() == 0) {
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

													 sendMail($email, 'Email confirm - BlogYourOpinion', "Hey $name confirm your email following this link https://blogyouropinion.ddns.net/register_page.php?action=confirm&id=$jwt");
														echo '<h1 class="title is-4 " style="text-align:center">Check your email inbox to confirm your email</h1>';
												  }
												  else{
													echo '<h1 class="title is-4 " style="text-align:center">Email arleady taken</h1>
                                <script>document.getElementById("email").classList.toggle("is-danger")</script>';
												  }
											}
											else{
												echo '<h1 class="title is-4 " style="text-align:center">Nickname arleady taken</h1>
                        <script>document.getElementById("nickname").classList.toggle("is-danger")</script>';
											}
									}
								}
								else{
									echo '<h1 class="title is-4 " style="text-align:center">Missing fields, check if all fields are filled</h1>';
								}
							}
              else if($_GET["action"]=="confirm"&&isset($_GET["id"])){
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
              }
							?>
						</div>
					</div>
				</div>
		</section>
	</body>
  <script src="function.js"></script>
<script src="https://kit.fontawesome.com/ee36c308c7.js" crossorigin="anonymous"></script>
</html>
