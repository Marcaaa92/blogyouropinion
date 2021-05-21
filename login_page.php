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
		<title>BlogYourOpinion - Login</title>
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
							<h2 class="title is-3 " style="text-align:center">Login</h2>
							<form action="" method="post" class="box">
								<div class="field">
									<label class="label">Email</label>
									<div class="control has-icons-left">
										<input class="input" type="email" name="emaillog" placeholder="Insert your email" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                  </div>
								</div>
								<div class="field">
									<label class="label">Password</label>
									<div class="control has-icons-left">
										<input class="input" type="password" name="passwordlog" placeholder="Insert your password" required>
                    <span class="icon is-small is-left">
                      <i class="fas fa-lock"></i>
                    </span>
                  </div>
								</div>
								<div class="field is-grouped">
									<div class="control">
										<button class="button is-link" type="submit" name="login" id="login" value="login">Login</button>
									</div>
								</div>
								<a href="reset_password.php?action=email"><p>Forgotten your password?</p></a>
							</form>
							<?php
							}
							else{
								echo '<h2 class="title is-3 " style="text-align:center">You are arleady logged in</h2>';
							}
							?>
								<?php
									if(isset($_POST['login'])){
											$email=strip_tags($_POST["emaillog"]);
											$password=hash('sha512',$_POST["passwordlog"]);
													$stmt = $db->prepare("SELECT email,password FROM user WHERE email = ? AND password = ?");
													$stmt->execute([$email, $password]);
													if ($stmt->rowCount() == 1) {
														$stmt = $db->prepare("SELECT utente.id as id, utente.name, utente.surname, utente.nickname, utente.email, utente.subscribed, role.roleName, team.id as teamId, team.teamName, team.logo FROM user utente JOIN role ON role.id=utente.role JOIN team ON team.id=utente.team WHERE email = ? AND password = ?");
														$stmt->execute([$email, $password]);
														$row = $stmt->fetch();
															session_start();
															$_SESSION["id"]=$row['id'];
															$_SESSION["nickname"]=$row['nickname'];
															$_SESSION["email"]=$row['email'];
															$_SESSION["name"]=$row['name'];
															$_SESSION["surname"]=$row['surname'];
															$_SESSION["role"]=$row['roleName'];
															$_SESSION["teamId"]=$row['teamId'];
														echo '<h2 class="title is-3 " style="text-align:center">You are logged in!</h2>';
														header("Refresh:1; url=index.php");
													}
													else{
														echo '<h2 class="title is-3 " style="text-align:center">Password or mail incorrect</h2>';
													}
									}
								?>
						</div>
					</div>
			</div>
		</section>
	</body>
  <script src="https://kit.fontawesome.com/ee36c308c7.js" crossorigin="anonymous"></script>
</html>
