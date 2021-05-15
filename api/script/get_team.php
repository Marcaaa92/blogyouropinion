<?php
header('Content-type:application/json');
require_once("../../function.php");
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $stmt = $db->prepare("SELECT * FROM team");
  $stmt->execute([]);
  $rowos=array();
  while($row = $stmt->fetch()){
    $rowo=array(
    "TeamName"=>$row["teamName"],
    "Id"=>$row["id"],
    );
    array_push($rowos,$rowo);
  }
  http_response_code(200);
  echo json_encode($rowos);
}
else{
  $errore=array("errore"=>"Method not allowed error, only GET method","Error code"=>405);
  echo json_encode($errore);
  http_response_code(405);
}
?>
