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
		<title>BlogYourOpinion-Edit Profile</title>
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
						require_once 'jwt/src/BeforeValidException.php';
						require_once 'jwt/src/ExpiredException.php';
						require_once 'jwt/src/SignatureInvalidException.php';
						require_once 'jwt/src/JWT.php';
						use \Firebase\JWT\JWT;
						if(isset($_SESSION["id"])){
							$id=$_SESSION["id"];
							echo '<h1 class="title is-4">Your actual info</h1>';
								$stmt = $db->prepare("SELECT user.name, user.surname, user.nickname, user.email, user.subscribed, role.roleName, team.teamName, user.telegramId FROM user JOIN role ON role.id=user.role JOIN team ON team.id=user.team WHERE user.id = ?");
								$stmt->execute([$id]);
								while ($row = $stmt->fetch()) {
									$key = "zlatan";
											$token = array(
												"jti"  => base64_encode(rand(0, 10000000)),
												"data" => array(
													"id" => $id,
												)
											);

											$jwt = JWT::encode($token, $key);
											echo'
                      <div class="box">
											<div class="columns is-desktop" style="text-aling:center">
												<div class="column is-full-mobile is-full-tablet is-full-third-desktop is-half-widescreen is-half-fullhd">
													<strong>Name: </strong>'.$row["name"].'</br>
													<strong>Email: </strong>'.$row["email"].'</br>
													<strong>Registred on: </strong>'.str_replace(" ", "<br>", date("d-m-Y H:i", strtotime($row["subscribed"]))).'</br>
													<strong>Role in the blog: </strong>'.$row["roleName"].'</br>
												</div>
												<div class="column is-full-mobile is-full-tablet is-full-third-desktop is-half-widescreen is-half-fullhd">
													<strong>Surname: </strong>'.$row["surname"].'</br>
													<strong>Nickname: </strong>'.$row["nickname"].'</br>
													<strong>Followed team: </strong>'.$row["teamName"].'</br>';
                          if($row["telegramId"]!=NULL){
                            echo '<strong>Connection with telegram: </strong> Yes [ID='.$row["telegramId"].']</br>';
                          }
                          else{
                            echo '<strong>Connection with telegram: </strong> No</br>';
                          }
												echo '</div>
											</div>
											<div class="columns is-desktop">
												<div class="column is-full-mobile is-full-tablet is-full-third-desktop is-full-widescreen is-full-fullhd" style="word-wrap: break-word;">
													<strong>User token: </strong></br>'.$jwt.'
												</div>
											</div>
                    </div>
											';
								}
							echo "<br>";
							if(isset($_GET["action"])){
								if($_GET["action"]=="nickname"){
									echo '
										<h1 class="title is-4">Nickname edit form</h1>
										<form action="" method="post" class="box">
											<div class="field">
												<label class="label">Nickname</label>
												<div class="control has-icons-left">
													<input class="input" type="nickname" name="nickname" placeholder="Insert new nickname" minlength="2" maxlength="20" required>
                          <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                          </span>
                        </div>
											</div>
											<div class="field">
												<label class="label">Password</label>
												<div class="control has-icons-left">
													<input class="input" type="password" name="password" placeholder="Insert your password to confirm this action" required>
                          <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                          </span>
                        </div>
											</div>
											<div class="field is-grouped">
												<div class="control">
													<button class="button is-link" type="submit" name="submit" id="editnickname" value="editnickname">Edit nickname</button>
												</div>
											</div>
										</form>';
								}
								else if($_GET["action"]=="email"){
										echo '
										<h1 class="title is-4">Edit mail form</h1>
										<form action="" method="post" class="box">
											<div class="field">
												<label class="label">Email</label>
												<div class="control has-icons-left">
													<input class="input" type="email" name="email" placeholder="Insert Mail" minlength="6" maxlength="60" required>
                          <span class="icon is-small is-left">
                              <i class="fas fa-envelope"></i>
                          </span>
                        </div>
											</div>
											<div class="field">
												<label class="label">Password</label>
												<div class="control has-icons-left">
													<input class="input" type="password" name="password" placeholder="Insert your password to confirm this action" required>
                          <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                          </span>
                        </div>
											</div>
											<div class="field is-grouped">
												<div class="control">
													<button class="button is-link" type="submit" name="submit" id="editmail" value="editmail">Edit email</button>
												</div>
											</div>
										</form>';
								}
								else if($_GET["action"]=="password"){
										echo '
										<h1 class="title is-4">Edit password form</h1>
										<form action="" method="post" class="box">
											<div class="field">
												<label class="label">Old password</label>
												<div class="control has-icons-left">
													<input class="input" type="password" name="passwordold" placeholder="Insert your old password to confirm this action" required>
                          <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                          </span>
                        </div>
											</div>
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
													<button class="button is-link" type="submit" name="submit" id="editpassword" value="editpassword">Edit email</button>
												</div>
											</div>
										</form>';
								}
								else if($_GET["action"]=="delete"){
									echo '
										<h1 class="title is-4">Delete profile form</h1>
										<form action="" method="post" class="box">
											<div class="field">
												<label class="label">Password</label>
												<div class="control has-icons-left">
													<input class="input" type="password" name="password" placeholder="Insert your password to confirm this action" required>
                          <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                          </span>
                        </div>
											</div>
												<label class="radio">
												  <input type="checkbox" name="sure" required>
												  Are you sure?
												</label>
											<div class="field is-grouped">
												<div class="control">
												<br>
													<button class="button is-link" type="submit" name="submit" id="delete" value="delete">Delete profile</button>
												</div>
											</div>
									</form>';
								}
								else if($_GET["action"]=="edit-team"){
									echo '
										<h1 class="title is-4">Select your favourite team</h1>
										<form action="" method="post" class="box">
											<div class="select">
												<select name="team">';
														$stmt = $db->prepare("SELECT * FROM team");
														$stmt->execute([]);
														while($row = $stmt->fetch()){
															echo '<option value="'. $row["id"] .'">'. $row["teamName"] .'</option>';
														}
													echo '
												</select>
											</div>
											<div class="field is-grouped">
												<div class="control">
												<br>
													<button class="button is-link" type="submit" name="submit" id="team" value="team">Edit team</button>
												</div>
											</div>
									</form>';
								}
                else if($_GET["action"]=="edit-mail-confirm"){
                  $key = "zlatan";
                  if(isset($_GET["id"])){
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
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You have not selected any action for your account</h1>';
							}
						}
						else{
							echo '<h1 class="title is-4 " style="text-align:center">You cant edit your profile if your are not logged in</h1>';
						}
						?>
						<?php
							if(isset($_POST["submit"])){
								if($_POST["submit"]=="editnickname"){
									$nickname=strip_tags($_POST["nickname"]);
									$password=hash('sha512',$_POST["password"]);
									$id=$_SESSION["id"];
										$stmt = $db->prepare("SELECT nickname FROM user WHERE nickname = ?");
										$stmt->execute([$nickname]);
										if ($stmt->rowCount() == 0){
											$stmt = $db->prepare("SELECT email,password FROM user WHERE id = ? AND password = ?");
											$stmt->execute([$id, $password]);
											if ($stmt->rowCount() == 1) {
												$stmt = $db->prepare("UPDATE user SET nickname = ? WHERE id=?");
												$stmt->execute([$nickname, $id]);
												$_SESSION["nickname"]=$nickname;
												echo '<h1 class="title is-4 " style="text-align:center">Edit successful</h1>';
											}
											else{
												echo '<h1 class="title is-4 " style="text-align:center">Wrong password, type it again</h1>';
											}
										}
										else{
												echo '<h1 class="title is-4 " style="text-align:center">Nickname arleady taken</h1>';
										}
								}
								else if($_POST["submit"]=="editpassword"){
									$passwordold=hash('sha512',$_POST["passwordold"]);
									$password1=$_POST["password1"];
									$password2=$_POST["password2"];
									$id=$_SESSION["id"];
									if($password1==$password2){
											$stmt = $db->prepare("SELECT id,password FROM user WHERE id = ? AND password = ?");
											$stmt->execute([$id, $passwordold]);
											if ($stmt->rowCount() == 1) {
												if($password2==$passwordold){
													echo '<h1 class="title is-4 " style="text-align:center">You entered the same password like the last one</h1>';
												}
												else{
													$stmt = $db->prepare("UPDATE user SET password = ? WHERE id=?");
													$stmt->execute([hash('sha512',$password2), $id]);
													echo '<h1 class="title is-4 " style="text-align:center">Edit successful</h1>';
												}
											}
											else{
												echo '<h1 class="title is-4 " style="text-align:center">Wrong password, type it again</h1>';
											}
									}
									else{
										echo '<h1 class="title is-4 " style="text-align:center">Passwords do not match</h1>';
									}
								}
								else if($_POST["submit"]=="delete"){
									$password=hash('sha512',$_POST["password"]);
									$id=$_SESSION["id"];
										$stmt = $db->prepare("SELECT id,password FROM user WHERE id = ? AND password = ?");
										$stmt->execute([$id, $password]);
										if ($stmt->rowCount() == 1) {
											$stmt = $db->prepare("DELETE FROM user WHERE id = ?");
											$stmt->execute([$id]);
                      header("Refresh:0; url=index.php");
										}
										else{
											echo '<h1 class="title is-4 " style="text-align:center">Wrong password, type it again</h1>';
										}
								}
								else if($_POST["submit"]=="editmail"){
									$email=strip_tags($_POST["email"]);
									$password=hash('sha512',$_POST["password"]);
									$id=$_SESSION["id"];
									$name=$_SESSION["name"];
											$stmt = $db->prepare("SELECT email FROM user WHERE email = ?");
											$stmt->execute([$email]);
											if ($stmt->rowCount() == 0){
												$stmt = $db->prepare("SELECT email,password FROM user WHERE id = ? AND password = ?");
												$stmt->execute([$id, $password]);
												if ($stmt->rowCount() == 1) {
													$key = "zlatan";
													$issuedAt = time();
													$expirationTime = $issuedAt + 60*60;
														  $token = array(
															  "jti"  => base64_encode(rand(0, 10000000)),
																'exp' => $expirationTime,
															  "data" => array(
																  "email" => $email,
																  "id" => $id,
															  )
														  );
														  $jwt = JWT::encode($token, $key);
															sendMail($email, 'Email confirm - BlogYourOpinion',"Hey $name verifiy your email with the following link https://".$_SERVER['SERVER_NAME']."/edit.php?action=edit-mail-confirm&id=$jwt");
															echo '<h1 class="title is-4 " style="text-align:center">Check your email inbox to confirm your email</h1>';
												}
												else{
													echo '<h1 class="title is-4 " style="text-align:center">Wrong password, type it again</h1>';
												}
											}
											else{
												echo '<h1 class="title is-4 " style="text-align:center">Email arleady taken</h1>';
											}
								}
								else if($_POST["submit"]=="team"){
									$id=$_SESSION["id"];
									$team=$_POST["team"];
									$_SESSION["teamId"]=$team;
													$stmt = $db->prepare("UPDATE user SET team = ? WHERE id=?");
													$stmt->execute([$team, $id]);
													echo '<h1 class="title is-4 " style="text-align:center">Edit successfullly</h1>';
								}
							}
						?>
						</div>
					</div>
				</div>
		</section>
	</body>
</html>
