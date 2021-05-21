<?php
require_once("function.php");
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
use \Firebase\JWT\JWT;
$update=file_get_contents("php://input");
$update = json_decode($update, TRUE);

$message=$update["message"]["text"];
$id=$update["message"]["from"]["id"];
$name=$update["message"]["from"]["first_name"];
$surname=$update["message"]["from"]["last_name"];
$username=$update["message"]["from"]["username"];
if ($message=="/start") {
sendMessage($id,"Here you can receive real-time notifications from your team, if you want to enable this function go to https://blogyouropinion.ddns.net/edit.php?action=show, copy the token and paste it after /set");
}
else if(substr($message, 0, 4)=="/set"){
  $token=substr($message, 5, strlen($message));
  $key = "zlatan";

  $stmt = $db->prepare("SELECT telegramId FROM user WHERE telegramId=?");
  $stmt->execute([$id]);
  if ($stmt->rowCount() == 0) {
    try{
      $jwt = $token;
      $decoded = JWT::decode($jwt, $key, array('HS256'));
      $decoded_array = (array) $decoded;
      JWT::$leeway = 60;

      $decoded_data = (array) $decoded_array["data"];
      $tokenid = $decoded_data["id"];
          $stmt = $db->prepare("SELECT id FROM user WHERE id=? AND telegramId IS NOT NULL");
          $stmt->execute([$tokenid]);
          if ($stmt->rowCount() == 0) {
            $stmt = $db->prepare("UPDATE user SET telegramId = ? WHERE id=?");
            $r = $stmt->execute([$id,$tokenid]);
            sendMessage($id,"Newsletter set");
          }
          else{
            sendMessage($id,"You have arleady set the newsletter");
          }
        }
    catch (Exception $e) {
        sendMessage($id,"Token manumitted");
    }
  }
  else{
    sendMessage($id,"You have arleady registred this telegram account");
  }
}
else{
    sendMessage($id,"I'm sorry i don't understand");
}
?>
