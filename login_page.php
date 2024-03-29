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
							<h1 class="title is-3 " style="text-align:center">Login</h1>
							<form action="" method="post" class="box">
								<div class="field">
									<label class="label">Email</label>
									<div class="control has-icons-left">
										<input class="input" type="email" name="emaillog" id="email" placeholder="Insert your email" value="<?php if(isset($_POST["emaillog"])){echo $_POST["emaillog"];}?>" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                  </div>
								</div>
								<div class="field">
									<label class="label">Password</label>
									<div class="control has-icons-left">
										<input class="input" type="password" name="passwordlog" id="password" placeholder="Insert your password" required>
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
                <a href="register_page.php"><p>Not registered yet? Sign in</p></a>
								<a href="reset_password.php?action=email"><p>Forgotten your password?</p></a>
							</form>
							<?php
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You are arleady logged in</h1>';
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
														echo '<h1 class="title is-4 " style="text-align:center">You are logged in!</h1>';
														header("Refresh:0; url=index.php");
													}
													else{
														echo '<h1 class="title is-4 " style="text-align:center">Password or mail incorrect</h1>
                            <script>document.getElementById("password").classList.toggle("is-danger")</script>
                            <script>document.getElementById("email").classList.toggle("is-danger")</script>';
													}
									}
								?>
						</div>
					</div>
			</div>
		</section>
	</body>
</html>
