<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION['idComptable'])) {
    header('Location:index.php');
}
//récupération de toute les variable ou initialisation de celle ci:
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

  

    $idDuVisiteur = $pdo->getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);

    foreach ($idDuVisiteur as $uneId) {
        $uneId = $idDuVisiteur['id'];
    }




    $lesFichesFull = $pdo->getFicheDeFraisEnFonctionDuMois($uneId, $moisBDD);
    echo '<br><br>';
    $nbJustifi= $pdo->getNbJustificatif($uneId, $moisBDD);
    $elem= $pdo->getElementForfait($uneId, $moisBDD);
    $idetatVisiteur= $pdo->estFicheValide($uneId, $moisBDD);
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

$lesMois = $pdo->getMoisVisiteur();
$lesVisiteurs = $pdo->getLesVisiteursAvecFicheDeFrais();

require './vues/v_valideFicheFrais.php';
