<?php

session_start();
include '../includes/classMajLigneDeFrais.php';
include '../includes/fct.inc.php';
include '../includes/class.pdogsb.inc.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Recupération de l'identifiant de chaque LigneFraisHorsForfait
 * pour pouvoir les mettres à jour une à une.
 */
$cloture = false;
$pdo = PdoGsb::getPdoGsb();
$i = 1;
$o = 0;
$j = 1;
$max = 1;

$id = $_POST['leID'];
$lemois = $_POST['unmois'];
$lesFichesFull = getFicheDeFraisEnFonctionDuMois($id, $lemois);
$lesMois = getMoisVisiteurCloture($id);

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
    $voiture = $_POST['voiture'];

    while (isset($_POST["$j"])) {
        $max++;
        $j++;
    }
    for ($index = 1; $index < $max; $index++) {
        if (isset($_POST['reporter' . $index])) {
            while ($cloture == false) {
                if (count($lesMois) != 0) {
                    for ($index1 = 0; $index1 < count($lesMois); $index1++) {
                        $moisCourant = intval($lesMois[$index1]['mois']);
                        $lemoisnum = intval($lemois);
                        if ($lemois < $moisCourant) {
                            $dateFraisHorsForfait = $date = filter_input(INPUT_POST, "date" . $index, FILTER_SANITIZE_SPECIAL_CHARS);
                            majmois($lesMois[$index1]['mois'], $id, $dateFraisHorsForfait, $idFiche[$index - 1]);
                            $_SESSION['moisreport'] = $lesMois[$index1]['mois'];
                            majdatedemodification($id, $lemois);
                            majdatedemodification($id, $_SESSION['moisreport']);
                            $_SESSION['ok'] = 15;
                            header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
                            exit();
                        }
                    }
                }
                if ($cloture == false) {
                    $dernierMois = maxAnneeVisiteur($id);
                    $numAnnee = substr($dernierMois, 0, -2);
                    $numMois = substr($dernierMois, -2);
                    if ($numMois == '12') {
                        $anneeValNewFiche = intval($numAnnee) + 1;
                        $anneeStringNewFiche = $anneeValNewFiche . '01';
                        $pdo->creeNouvellesLignesFrais($id, $anneeStringNewFiche);
                        $lesMois = getMoisVisiteurCloture($id);
                    } else {
                        if (intval($numMois + 1) >= 10) {
                            $anneeStringNewFiche = $numAnnee . intval($numMois + 1);
                        } else {
                            $anneeStringNewFiche = $numAnnee . '0' . intval($numMois + 1);
                        }
                        $pdo->creeNouvellesLignesFrais($id, $anneeStringNewFiche);
                        $lesMois = getMoisVisiteurCloture($id);
                    }
                }
            }
        }
    }

    if (ControleInfosFrais($nui, $rep, $km, $etp, $nbjour) != 0) {
        $_SESSION['ok'] = ControleInfosFrais($nui, $rep, $km, $etp, $nbjour);
        header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
        exit();
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
        majvoiture($id, $lemois, $voiture);
        majdatedemodification($id, $lemois);

        $i++;
        $o++;
    }
} else {
    $etp = $_POST['n1'];
    $km = $_POST['n2'];
    $nui = $_POST['n3'];
    $rep = $_POST['n4'];
    $nbjour = $_POST['nbJ'];
    $voiture = $_POST['voiture'];
    if (ControleInfosFrais($nui, $rep, $km, $etp, $nbjour) != 0) {
        $_SESSION['ok'] = ControleInfosFrais($nui, $rep, $km, $etp, $nbjour);
        header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
        exit();
    }
    majetp($etp, $id, $lemois);
    majkm($km, $id, $lemois);
    majnuit($nui, $id, $lemois);
    majrep($rep, $id, $lemois);
    majnj($nbjour, $id, $lemois);
    majvoiture($id, $lemois, $voiture);
    majdatedemodification($id, $lemois);
    validerUneFicheDeFais($id, $lemois);
}

$_SESSION['ok'] = 0;
header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
exit();

