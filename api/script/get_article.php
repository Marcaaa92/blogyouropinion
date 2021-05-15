<?php
header('Content-type:application/json');
require_once("../../function.php");
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if(isset($_GET["team"])){
    $team=$_GET["team"];
    if($team!="all"){
        $stmt = $db->prepare("SELECT teamName FROM team WHERE id = ?");
        $stmt->execute([$team]);
        if ($stmt->rowCount() != 0) {
          if(isset($_GET["category"])){
            $category=$_GET["category"];
              if($category!="all"){
              $stmt = $db->prepare("SELECT categoryName FROM category WHERE id = ?");
              $stmt->execute([$category]);
              if ($stmt->rowCount() != 0) {
                $stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.article, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE team.id=? AND category.id=? AND articles.approvated=1 ORDER BY articles.date DESC LIMIT 50");
                $stmt->execute([$team, $category]);
                $rowos=array();
                while($row = $stmt->fetch()){
                  $rowo=array(
                  "Title"=>$row["title"],
                  "Subtitle"=>$row["subtitle"],
                  "Article"=>$row["article"],
                  "Date"=>$row["date"],
                  "Imgdir"=>"https://blogyouropinion.ddns.net/".$row["imgdir"],
                  "Category name"=>$row["categoryName"],
                  );
                  array_push($rowos,$rowo);
                }
                http_response_code(200);
                echo json_encode($rowos);
              }
              else{
                $errore=array("errore"=>"category not found","code"=>404);
                echo json_encode($errore);
                http_response_code(404);
              }
            }
            else{
              $stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.article, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, articles.imgdir FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE team.id=? AND articles.approvated=1 ORDER BY articles.date DESC LIMIT 50");
              $stmt->execute([$team]);
              $rowos=array();
              while($row = $stmt->fetch()){
                $rowo=array(
                "Title"=>$row["title"],
                "Subtitle"=>$row["subtitle"],
                "Article"=>$row["article"],
                "Date"=>$row["date"],
                "Imgdir"=>"https://blogyouropinion.ddns.net/".$row["imgdir"],
                "Category name"=>$row["categoryName"],
                );
                array_push($rowos,$rowo);
              }
              http_response_code(200);
              echo json_encode($rowos);
            }
          }
          else{
            $errore=array("errore"=>"Error missing article type","code"=>400);
            echo json_encode($errore);
            http_response_code(400);
          }
        }
      else{
        $errore=array("errore"=>"Team not found","code"=>404);
        echo json_encode($errore);
        http_response_code(404);
      }
    }
    else{
      $stmt = $db->prepare("SELECT articles.id, articles.approvated, articles.limited, articles.title, articles.article, articles.subtitle, articles.date, articles.imgdir, user.nickname, category.categoryName, articles.imgdir, team.teamName FROM articles JOIN user ON user.id=articles.journalist JOIN category ON category.id=articles.category JOIN team ON team.id=articles.team WHERE articles.approvated=1 ORDER BY articles.date DESC LIMIT 50");
      $stmt->execute([]);
      $rowos=array();
      while($row = $stmt->fetch()){
        $rowo=array(
        "Title"=>$row["title"],
        "Subtitle"=>$row["subtitle"],
        "Article"=>$row["article"],
        "Date"=>$row["date"],
        "Imgdir"=>"https://blogyouropinion.ddns.net/".$row["imgdir"],
        "Category name"=>$row["categoryName"],
        "Team name"=>$row["teamName"],
        );
        array_push($rowos,$rowo);
      }
      http_response_code(200);
      echo json_encode($rowos);
    }
  }
  else{
    $errore=array("errore"=>"Error missing team name and article type","code"=>400);
    echo json_encode($errore);
    http_response_code(400);
  }
}
else{
  $errore=array("errore"=>"Method not allowed error, only GET method","Error code"=>405);
  echo json_encode($errore);
  http_response_code(405);
}
?>
