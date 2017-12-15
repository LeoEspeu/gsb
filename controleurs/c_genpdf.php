<?php

session_start();
require('../includes/fpdf181/fpdf.php');
include '../includes/fct.inc.php';
include '../includes/class.pdogsb.inc.php';

$pdo = PdoGsb::getPdoGsb();
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

$voitureMois = $pdo->ObtenirVoiture($idVisiteur, $leMois);
$MoisFicheFrais = estFicheValide($idVisiteur, $leMois);
foreach ($MoisFicheFrais as $idEtat) {
    $idEtatFiche = $idEtat['idetat'];
}
foreach ($voitureMois as $coef) {
    $coefVoiture = $coef['coefficient'];
}

$quo=2.5;
if (count($lesFraisHorsForfait)>6 && count($lesFraisHorsForfait)<13){
   
    $quo=2.4;
}
else if (count($lesFraisHorsForfait)<=5){
    
    $quo=0.6;
}
else if (count($lesFraisHorsForfait)<=7){
    
    $quo=0.8;
}
else if (count($lesFraisHorsForfait)>=10){
    
    $quo=3.2;
}
else if (count($lesFraisHorsForfait)>=13){
    
    $quo=1.0;
}
$tailleLigne=count($lesFraisHorsForfait)/$quo;


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

    function Tableau($libelem, $nuiteQ, $nuit,$tailleLigne) {
        $this->SetFont('Times', '', 11);
        $this->SetDrawColor(122, 147, 178);
        $this->Cell(40, $tailleLigne, utf8_decode($libelem), 1, 0, 'L');
        $this->Cell(50, $tailleLigne, $nuiteQ, 1, 0, 'R');
        $this->Cell(50, $tailleLigne, $nuit, 1, 0, 'R');
        $this->Cell(40, $tailleLigne, $nuit * $nuiteQ, 1, 1, 'R');
    }

    function FraisHF($tailleLigne) {
        $this->SetTextColor(122, 147, 178,$tailleLigne);
        $this->SetFont('Times', 'BI', 11);
        $this->Cell(60, $tailleLigne, utf8_decode('Date'), 1, 0, 'C');
        $this->Cell(80, $tailleLigne, utf8_decode('Libellé'), 1, 0, 'C');
        $this->Cell(40, $tailleLigne, utf8_decode('Montant'), 1, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    function LigneFraisHF($date, $libelle, $montant,$tailleLigne) {
        $this->SetFont('Times', '', 11);
        $this->Cell(60, $tailleLigne, $date, 1, 0, 'L');
        $this->Cell(80, $tailleLigne, $libelle, 1, 0, 'L');
        $this->Cell(40, $tailleLigne, $montant, 1, 1, 'R');
    }

    function Total($date, $total,$tailleLigne) {
        $this->setX(110);
        $this->Cell(40, $tailleLigne, 'Total : ' . $date, 1, 0, 'L');
        $this->Cell(40, $tailleLigne, $total, 1, 1, 'R');
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
    if($libelem=='Frais Kilométrique'){
        $montelem+=$coefVoiture;
    }
    $rez = $quanti;
    $cumulFF += $quanti * $montelem;

    $pdf->Tableau($libelem, $quanti, $montelem,$tailleLigne);
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
    $cumul += $fiche['montant'];
    $pdf->LigneFraisHF($datemodif, utf8_decode($libelleLigne), $montant,$tailleLigne);
}
$pdf->Ln(8);
$pdf->Total($numMois . '/' . $numAnnee, $cumulFF + $cumul,$tailleLigne);
if($idEtatFiche=='RB'){
    $pdf->Signature();
}
$pdf->Output();

}
else{
    var_dump($lesFraisHorsForfait);
    $_SESSION['pdfdupli']=true;  
    header('Location:../index.php?uc=etatFrais&action=selectionnerMois');
    exit();
}
