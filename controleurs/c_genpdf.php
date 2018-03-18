<?php

session_start();
require('../includes/fpdf181/fpdf.php');
include '../includes/fct.inc.php';
include '../includes/class.pdogsb.inc.php';
include '../includes/pdf.php';

$pdo = PdoGsb::getPdoGsb();
$cumul = 0;
$cumulFF = 0;
$idVisiteur = $_SESSION['idVisiteur'];
$leMois = $_SESSION['moissle'];

$lesFraisHorsForfaitValides = $pdo->getFicheDeFraisNonRefuséEnFonctionDuMois($idVisiteur, $leMois);
$lesFraisHorsForfait = $pdo->getFicheDeFraisEnFonctionDuMois($idVisiteur, $leMois);
$lesFraisForfait = $pdo->getElementForfait($idVisiteur, $leMois);
$elem = $pdo->getElementForfait($idVisiteur, $leMois);
$numAnnee = substr($leMois, 0, 4);
$numMois = substr($leMois, 4, 2);
$nomprenom = $pdo->getnomprenomavecid($idVisiteur);

$voitureMois = $pdo->ObtenirVoiture($idVisiteur, $leMois);
$MoisFicheFrais = $pdo->estFicheValide($idVisiteur, $leMois);
foreach ($MoisFicheFrais as $idEtat) {
    $idEtatFiche = $idEtat['idetat'];
}
foreach ($voitureMois as $coef) {
    $coefVoiture = $coef['coefficient'];
}

$tailleLigne = 7;

$dupli = $pdo->EstDupli($idVisiteur, $leMois);
$pdo->AddToDupli($idVisiteur, $leMois);


//vérification si le pdf existe déjà si non il lance tout le script de vérification 
//si oui pas besoin de regénéré le pdf on redirige l'utilisateur vers une copie
//qui existe sur le serveur.
//Ici on instancie un object de la class pdf (dans le dossier "includes") qui est une classe
//Héritière de la class FPDF (voir la doc de ce dernier)
//Ensuite on appelle une à une les fonctions d'une classe
if (empty($dupli)) {
    $_SESSION['pdfdupli'] = false;
// Instanciation de la classe dérivée
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    foreach ($nomprenom as $np) {
        $nom = $np['nom'];
        $prenom = $np['prenom'];
        $pdf->Resultat($prenom . ' ' . $nom, $numMois . '/' . $numAnnee, $idVisiteur);
    }


    $pdf->Ln(10);
    $pdf->SetDrawColor(122, 147, 178);
    $pdf->SetTextColor(122, 147, 178);
    $pdf->SetFont('Times', 'BI', 11);
    $pdf->Cell(180, 10, 'REMBOURSEMENT DE FRAIS ENGAGES', 1, 1, 'C');
    $pdf->SetFont('Times', '', 11);


    $pdf->EnteteTableau();

    foreach ($elem as $elements) {

        $quanti = $elements['quantite'];
        $libelem = $elements['libelle'];
        $montelem = $elements['montant'];
        if ($libelem == 'Frais Kilométrique') {
            $montelem += $coefVoiture;
        }
        $rez = $quanti;
        $cumulFF += $quanti * $montelem;

        $pdf->Tableau($libelem, $quanti, $montelem, $tailleLigne);
    }
    $pdf->SetTextColor(122, 147, 178);
    $pdf->SetFont('Times', 'BI', 11);
    $pdf->Cell(180, 10, 'Autres Frais', 1, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Times', '', 11);
    $pdf->FraisHF($tailleLigne);
    foreach ($lesFraisHorsForfait as $fiche) {

        $montant = $fiche['montant'];
        $datemodif = $fiche['date'];
        $libelleLigne = $fiche['libelle'];
        foreach ($lesFraisHorsForfaitValides as $ficheValide) {
            if ($fiche['id'] == $ficheValide['id']) {
                $cumul += $fiche['montant'];
            }
        }
        $pdf->LigneFraisHF($datemodif, utf8_decode($libelleLigne), $montant, $tailleLigne);
    }
    $pdf->Ln(8);
    $pdf->Total($numMois . '/' . $numAnnee, $cumulFF + $cumul, $tailleLigne);
    if ($idEtatFiche == 'RB') {
        $pdf->Signature();
    }

    //enregistrement du PDF
    $pdf->Output("../pdf/pdf" . $idVisiteur . $leMois . ".pdf", "F");
    //redirection après l'enregistrement pour lui montrer le pdf
    header('Location:../pdf/pdf' . $idVisiteur . $leMois . '.pdf');
    exit();
} else {

    // redirection au cas ou le pdf existe déjà
    header('Location:../pdf/pdf' . $idVisiteur . $leMois . '.pdf');
    exit();
}
