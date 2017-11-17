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

$id = $_POST['leID'];
$lemois = $_POST['unmois'];
$lesFichesFull = getFicheDeFraisEnFonctionDuMois($id, $lemois);

foreach ($lesFichesFull as $fiche) {

    $idFiche[] = $fiche['id'];
}

var_dump($idFiche);




if (isset($_POST["$j"])) {

    $etp = $_POST['n1'];
    $km = $_POST['n2'];
    $nui = $_POST['n3'];
    $rep = $_POST['n4'];
    $nbjour = $_POST['nbJ'];
    while (isset($_POST["$j"])) {
        $max++;
        $j++;
    }
    while ($i < $max) {
        //filter
        $arrmont[$i] = $montant = filter_input(INPUT_POST, "mont$i", FILTER_SANITIZE_SPECIAL_CHARS);
        $arrdate[$i] = $date = filter_input(INPUT_POST, "date$i", FILTER_SANITIZE_SPECIAL_CHARS);
        $arrlib[$i] = $libdate = filter_input(INPUT_POST, "lib$i", FILTER_SANITIZE_SPECIAL_CHARS);


        if (valideInfosFraisRetour(dateAnglaisVersFrancais($arrdate[$i]), $arrlib[$i], $arrmont[$i]) > 0) {
            //si valideInfoFraisRetour retourne un nombre au dessus de zéro,c'est qu'il y a un message 
            //d'erreur,dans ce cas la variable $_SESSION['ok'] prendra la valeur retourner et on gère le
            //message d'erreur dans la vue.
            $_SESSION['ok'] = valideInfosFraisRetour(dateAnglaisVersFrancais($arrdate[$i]), $arrlib[$i], $arrmont[$i]);
            echo $_SESSION['ok'];
            header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
            exit();
        }


        //on peut envoyer dans la BDD
        majlibelle($arrlib[$i], $id, $lemois, $idFiche[$o]);

        majdate($arrdate[$i], $id, $lemois, $idFiche[$o]);

        majmont($arrmont[$i], $id, $lemois, $idFiche[$o]);

        validerUneFicheDeFais($id, $lemois);

        majetp($etp, $id, $lemois);
        majkm($km, $id, $lemois);
        majnuit($nui, $id, $lemois);
        majrep($rep, $id, $lemois);
        majnj($nbjour, $id, $lemois);

        $i++;
        $o++;
    }
} else {
    $_SESSION['ok'] = -1;
    header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
    exit();
}

$_SESSION['ok'] = 0;
header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
exit();

