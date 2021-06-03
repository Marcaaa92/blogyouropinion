<?php
session_start();
require_once("function.php");
?>
						<?php
							if(isset($_SESSION["id"])){
								if($_SESSION["role"]=="redactor"){
                  $cv="./cvdir/".$_GET["cv"];
                  if(file_exists($cv)){
                  header('Cache-Control: public');
                  header('Content-Type: application/pdf');
                  header('Content-Disposition: attachment; filename="'.$_GET["cv"].'"');
                  readfile($cv);
                }
                else{
                  echo "non esiste";
                }
							}
							else{
								echo '<h1 class="title is-4 " style="text-align:center">You have not permission to see this content</h1>';
							}
							}
							else{
									echo '<h1 class="title is-4 " style="text-align:center">You have not permission to see this content</h1>';
								}
						?>
