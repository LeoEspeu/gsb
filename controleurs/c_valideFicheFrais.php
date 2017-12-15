<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION['idComptable'])) {
    header('Location:index.php');
}

$_SESSION['MontantValide']=0;
$coefVoiture=null;
$coefNBVoiture=null;
$idetat='';
$nomselect = '';
$nomprenomselect = '';
$numAnnee=Null;
if (isset($_POST['lstMois'])) {
    
    $moisSelect = $_POST['lstMois'];
    $moisBDD = preg_replace('#/#', '', $moisSelect);
    $GmoisVar = "$moisBDD";

    $numAnnee = substr($GmoisVar, 0, -2);
    $numMois = substr($GmoisVar, -2);
    $dateForme=$numMois . '/'. $numAnnee;
    
}
if (isset($_POST['listVisiteur'])) {
    
    $nomprenomselect = $_POST['listVisiteur'];



    list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);

  

    $idDuVisiteur = getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);

    foreach ($idDuVisiteur as $uneId) {
        $uneId = $idDuVisiteur['id'];
    }




    $lesFichesFull = getFicheDeFraisEnFonctionDuMois($uneId, $moisBDD);
    echo '<br><br>';
    $nbJustifi= getNbJustificatif($uneId, $moisBDD);
    $elem= getElementForfait($uneId, $moisBDD);
    $idetatVisiteur= estFicheValide($uneId, $moisBDD);
    $voitureVisiteur = $pdo->ObtenirVoiture($uneId, $moisBDD);
    $idetat='';
    foreach ($idetatVisiteur as $value) {
        $idetat=$value['idetat'];
    }
    foreach ($voitureVisiteur as $value) {
        $coefVoiture= $value['idvoiture'];
        $coefNBVoiture=$value['coefficient'];
    }
}

$lesMois = getMoisVisiteur();
$lesVisiteurs = getLesVisiteursAvecFicheDeFrais();

require './vues/v_valideFicheFrais.php';
