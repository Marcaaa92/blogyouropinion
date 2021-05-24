title is-4<?php
use \Firebase\JWT\JWT;
session_start();
require_once("function.php");
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>BlogYourOpinion - Reset password</title>
		<link rel="stylesheet" href="css/bulma.css" type="text/css">
		<link rel="stylesheet" href="css/edited.css?ciao=4" type="text/css">
	</head>
	<body>
		<?php
			loadNav();
		?>
		<section class="section">
      <div class="container is-max-desktop">
					<div class="columns is-desktop">
						<div class="column">
							<?php
							if(!isset($_SESSION["id"])){
								if(isset($_GET["action"])){
									if($_GET["action"]=="email"){
							?>
							<h1 class="title is-3 " style="text-align:center">Reset password form</h1>
							<form action="" method="post" class="box">
								<div class="field">
									<label class="label">Email</label>
									<div class="control has-icons-left">
										<input class="input" type="email" name="email" placeholder="Insert your email" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                  </div>
								</div>
								<div class="field is-grouped">
									<div class="control">
										<button class="button is-link" type="submit" name="reset" id="reset" value="sendMail">Reset password</button>
									</div>
								</div>
							</form>
							<?php
									}
									else if($_GET["action"]=="setPassword"){
										if(isset($_GET["id"])){
											$key = "zlatan";

											try{
												  $jwt = $_GET["id"];
												  $decoded = JWT::decode($jwt, $key, array('HS256'));
												  $decoded_array = (array) $decoded;
												  JWT::$leeway = 60;

												  $decoded_data = (array) $decoded_array["data"];
												  $email = $decoded_data["email"];
													$stmt = $db->prepare("SELECT email FROM user WHERE email = ?");
													$stmt->execute([$email]);
												  if ($stmt->rowCount() == 1){
													 echo '
														<h1 class="title is-3 " style="text-align:center">Reset password form</h1>
														<form action="" method="post" class="box">
															<div class="field">
																<label class="label">New password</label>
																<div class="control has-icons-left">
																	<input class="input" type="password" name="password1" id="pw1" oninput="checkpw()" placeholder="Insert your new password" minlength="6" maxlength="20" required>
                                  <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                  </span>
                                </div>
															</div>
															<div class="field">
																<label class="label">Re-insert new Password</label>
																<div class="control has-icons-left">
																	<input class="input" type="password" name="password2" id="pw2" oninput="checkpw()" placeholder="Re-insert your new password" minlength="6" maxlength="20" required>
                                  <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                  </span>
                                </div>
																<p class="label" id="check-text-pw"></p>
															</div>
															<div class="field is-grouped">
																<div class="control">
																	<button class="button is-link" type="submit" name="reset" id="reset" value="setPassword">Set password</button>
																</div>
															</div>
														</form>';
												  }
												  else{
														echo '<h1 class="title is-4 " style="text-align:center">Something went wrong</h1>';
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
											echo '<h1 class="title is-4 " style="text-align:center">No token sended</h1>';
										}
									}
									else{
										echo '<h1 class="title is-4 " style="text-align:center">Wrong action</h1>';
									}
								}
								else{
									echo '<h1 class="title is-4 " style="text-align:center">No action selected</h1>';
								}
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You are arleady logged in</h1>';
							}
							?>
							<?php
							if(isset($_POST["reset"])){
								if($_POST["reset"]=="sendMail"){
									$email=$_POST["email"];
									$stmt = $db->prepare("SELECT email FROM user WHERE email = ?");
									$stmt->execute([$email]);
									if ($stmt->rowCount() == 1) {
										$key = "zlatan";
										$issuedAt = time();
										$expirationTime = $issuedAt + 60*60;
										$token = array(
											"jti"  => base64_encode(rand(0, 10000000)),
											'exp' => $expirationTime,
											"data" => array(
												"email" => $email,
											)
										);
										$jwt = JWT::encode($token, $key);
										sendMail($email, 'Edit password - BlogYourOpinion', "Hey $name confirm your action to edit password following this link https://blogyouropinion.ddns.net/reset_password.php?action=setPassword&id=$jwt, if you have not requested a change of password, ignore this mail");
										echo '<h1 class="title is-4 " style="text-align:center">Check your email inbox to confirm your email</h1>';
									}
									else{
										echo '<h1 class="title is- " style="text-align:center">No account associated</h1>';
									}
								}
								else if($_POST["reset"]=="setPassword"){
									if($_POST["password1"]==$_POST["password2"]){
										$password=hash('sha512',$_POST["password1"]);
										$stmt = $db->prepare("UPDATE user SET password = ? WHERE email=?");
										$r = $stmt->execute([$password,$email]);
                    header("Refresh:1; url=login_page.php");
									}
									else{
										echo '<h1 class="title is-3 " style="text-align:center">Password doesnt match</h1>';
									}
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
