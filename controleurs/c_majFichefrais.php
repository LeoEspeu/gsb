<?php

include '../includes/classMajLigneDeFrais.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$i = 1;
$j = 1;
$max = 1;
if (isset($_GET["$j"])) {
   

while (isset($_GET["$j"])) {
    $max++;
    $j++;
}
while ($i < $max) {
    $arrmont[$i] = $montant = $_GET["mont$i"];
    $arrdate[$i] = $date = $_GET["date$i"];
    $arrlib[$i] = $libdate = $_GET["lib$i"];

    $objet = new MajLigneDeFrais('Jean', 'Rachid', '201709', $arrmont[$i], $arrdate[$i], $arrlib[$i]);
    echo '<br>';
    $i++;
}

var_dump($arrmont);
echo '<br>';
var_dump($arrdate);
echo '<br>';
var_dump($arrlib);

}
else{

      header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
      exit();
}
