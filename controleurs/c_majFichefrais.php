<?php

include '../includes/classMajLigneDeFrais.php';
include '../includes/fct.inc.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function majlibelle ($arrlib,$id,$lemois,$idFiche) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
        $requetePrepare = $pdoSansParam->prepare(
                'Update `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
        set  lignefraishorsforfait.libelle = :lib
        WHERE :id = lignefraishorsforfait.idvisiteur
        AND lignefraishorsforfait.mois = :date
        AND lignefraishorsforfait.id=:idFiche;'
        );
        $requetePrepare->bindParam(':lib', $arrlib, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFiche', $idFiche, PDO::PARAM_STR);
        $requetePrepare->execute();
}

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

        majlibelle($arrlib[$i],$id,$lemois,$idFiche[$o]);





        $i++;
        $o++;
    }
} else {

    header('Location: /GSB/index.php?uc=validerFrais&action=confirmerFrais');
    exit();
}




echo '<br>';
