<?php
header('Content-type:application/json');
require_once("../../function.php");
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $errore=array("errore"=>"Error no action selectet","code"=>400);
  echo json_encode($errore);
  http_response_code(400);
}
else{
  $errore=array("errore"=>"Method not allowed error, only GET method","Error code"=>405);
  echo json_encode($errore);
  http_response_code(405);
}
?>
