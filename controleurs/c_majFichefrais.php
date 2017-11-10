<?php

session_start();
include '../includes/classMajLigneDeFrais.php';
include '../includes/fct.inc.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * Recupération de l'identifiant de chaque LigneFraisHorsForfait
 * pour pouvoir les mettres à jour une à une.
 */

$i = 1;
$o = 0;
$j = 1;
$max = 1;
$etp = $_POST['n1'];
$km = $_POST['n2'];
$nui = $_POST['n3'];
$rep = $_POST['n4'];
$nbjour = $_POST['nbJ'];
$id = $_POST['leID'];
$lemois = $_POST['unmois'];
$lesFichesFull = getFicheDeFraisEnFonctionDuMois($id, $lemois);

foreach ($lesFichesFull as $fiche) {

    $idFiche[] = $fiche['id'];
}
$elem = getElementForfait($id, $lemois);

foreach ($elem as $elements) {
    $quanti[] = $elements['quantite'];
}


    if (isset($_POST["$j"])) {


        while (isset($_POST["$j"])) {
            $max++;
            $j++;
        }
        while ($i < $max) {
            $arrmont[$i] = $montant = $_POST["mont$i"];
            $arrdate[$i] = $date = $_POST["date$i"];
            $arrlib[$i] = $libdate = $_POST["lib$i"];

            $objet = new MajLigneDeFrais($id, $lemois, $arrmont[$i], $arrdate[$i], $arrlib[$i], $idFiche[$o]);
            echo '<br>';

            majlibelle($arrlib[$i], $id, $lemois, $idFiche[$o]);
            majdate($arrdate[$i], $id, $lemois, $idFiche[$o]);
            majmont($arrmont[$i], $id, $lemois, $idFiche[$o]);

            majetp($etp, $id, $lemois);
            majkm($km, $id, $lemois);
            majnuit($nui, $id, $lemois);
            majrep($rep, $id, $lemois);
            majnj($nbjour, $id, $lemois);
            $i++;
            $o++;
        }
    } else {
        $_SESSION['result'] = -1;
        header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
        exit();
    }


    $_SESSION['result'] = 0;



     header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
      exit(); 

    