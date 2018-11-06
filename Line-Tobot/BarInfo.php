<?php

function getBarInfoByArea($area_name) {
   try {
       $pdo = new PDO('sqlite:kanto_bars_list.db');
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

       $stmt = $pdo->prepare("SELECT name, url FROM bar WHERE area like ?");
       $stmt->execute([$area_name . '%']);
       $rs = json_encode($stmt->fetchAll());

       return $rs;

   } catch (Exception $e) {
       echo $e->getMessage() . PHP_EOL;
   }

}

?>
