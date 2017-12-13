<?php

session_start();
require('../includes/fpdf181/fpdf.php');
include '../includes/fct.inc.php';

$cumul = 0;
$cumulFF = 0;
$idVisiteur = $_SESSION['idVisiteur'];
$leMois = $_SESSION['moissle'];

$lesFraisHorsForfait = getFicheDeFraisEnFonctionDuMois($idVisiteur, $leMois);
$lesFraisForfait = getElementForfait($idVisiteur, $leMois);
$elem = getElementForfait($idVisiteur, $leMois);
$numAnnee = substr($leMois, 0, 4);
$numMois = substr($leMois, 4, 2);
$nomprenom = getnomprenomavecid($idVisiteur);




$dupli=EstDupli($idVisiteur,$leMois);
AddToDupli($idVisiteur,$leMois);
class PDF extends FPDF {

// En-tête
    function Header() {
        $this->SetDrawColor(122, 147, 178);
        $this->SetTextColor(122, 147, 178);
        $this->SetY(35);
        // Logo
        $this->Image('../images/logo.jpg', 77, 16, 52);
        // Police Arial gras 15
        $this->SetFont('Times', 'B', 15);
        // Décalage à droite
        
        // Titre

        
        // Saut de ligne
    }

    function Resultat($visiteur, $mois, $idVisiteur) {
        // Positionnement au millieu
        $this->SetY(60);
        // Police Arial italique 8
        $this->SetFont('Times', '', 10);
        $this->Cell(50, 10, 'Visiteur : ' . '           ' . $idVisiteur . '           ' . $visiteur, 0, 1, 'L');
        $this->Cell(30, 10, 'Mois : ' . $mois, 0, 0, 'L');
        $this->Cell(125, 10, '', 0, 0, 'C');
        // Saut de ligne
    }

    function EnteteTableau() {
        $this->SetDrawColor(122, 147, 178);
        $this->SetFont('Times', 'BI', 11);
        
        $this->Cell(40, 10, utf8_decode('Frais Forfaitaires'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Quantité'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Montant unitaire'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Total'), 1, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    function Tableau($libelem, $nuiteQ, $nuit) {
        $this->SetFont('Times', '', 11);
        $this->SetDrawColor(122, 147, 178);
        $this->Cell(40, 07, utf8_decode($libelem), 1, 0, 'L');
        $this->Cell(50, 07, $nuiteQ, 1, 0, 'R');
        $this->Cell(50, 07, $nuit, 1, 0, 'R');
        $this->Cell(40, 07, $nuit * $nuiteQ, 1, 1, 'R');
    }

    function FraisHF() {
        $this->SetTextColor(122, 147, 178);
        $this->SetFont('Times', 'BI', 11);
        $this->Cell(60, 10, utf8_decode('Date'), 1, 0, 'C');
        $this->Cell(80, 10, utf8_decode('Libellé'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Montant'), 1, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    function LigneFraisHF($date, $libelle, $montant) {
        $this->SetFont('Times', '', 11);
        $this->Cell(60, 07, $date, 1, 0, 'L');
        $this->Cell(80, 07, $libelle, 1, 0, 'L');
        $this->Cell(40, 07, $montant, 1, 1, 'R');
    }

    function Total($date, $total) {
        $this->setX(110);
        $this->Cell(40, 07, 'Total : ' . $date, 1, 0, 'L');
        $this->Cell(40, 07, $total, 1, 1, 'R');
    }

    function Signature() {
        $this->Ln(12);
        $this->setX(150);
        $this->Cell(40, 10, utf8_decode('Fait à Paris,le ') . date('d') . '/' . date('m') . '/' . date('Y'), 0, 1, 'R');
        $this->setX(150);
        $this->Cell(40, 10, utf8_decode('Vu par l\'agent comptable'), 0, 0, 'R');
        $this->Ln(12);
        $this->Image('../images/sig.png', 120, null, 70, 20);
    }

// Pied de page
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Times', 'I', 8);
        // Numéro de page
        
    }
   

}


    
if(empty($dupli)){
$_SESSION['pdfdupli']=false;  
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
    $rez = $quanti;
    $cumulFF += $quanti * $montelem;

    $pdf->Tableau($libelem, $quanti, $montelem);
}
$pdf->SetTextColor(122, 147, 178);
$pdf->SetFont('Times', 'BI', 11);
$pdf->Cell(180, 10, 'Autres Frais', 1, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Times', '', 11);
$pdf->FraisHF();
foreach ($lesFraisHorsForfait as $fiche) {

    $montant = $fiche['montant'];
    $datemodif = $fiche['date'];
    $libelleLigne = $fiche['libelle'];
    $cumul += $fiche['montant'];
    $pdf->LigneFraisHF($datemodif, utf8_decode($libelleLigne), $montant);
}
$pdf->Ln(8);
$pdf->Total($numMois . '/' . $numAnnee, $cumulFF + $cumul);
$pdf->Signature();
$pdf->Output();

}
else{
    $_SESSION['pdfdupli']=true;
    header('Location:../index.php?uc=etatFrais&action=selectionnerMois');
    exit();
}