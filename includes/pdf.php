<?php

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
        $this->Ln(20);
    }
//Affiche le resultat
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
//Affiche l'entête du table qui affiche les ligne de frais de frais forfait
    function EnteteTableau() {
        $this->SetDrawColor(122, 147, 178);
        $this->SetFont('Times', 'BI', 11);

        $this->Cell(40, 10, utf8_decode('Frais Forfaitaires'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Quantité'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Montant unitaire'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Total'), 1, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }
//Affiche de les ligne de frais de frais forfait
    function Tableau($libelem, $nuiteQ, $nuit, $tailleLigne) {
        $this->SetFont('Times', '', 11);
        $this->SetDrawColor(122, 147, 178);
        $this->Cell(40, $tailleLigne, utf8_decode($libelem), 1, 0, 'L');
        $this->Cell(50, $tailleLigne, $nuiteQ, 1, 0, 'R');
        $this->Cell(50, $tailleLigne, $nuit, 1, 0, 'R');
        $this->Cell(40, $tailleLigne, $nuit * $nuiteQ, 1, 1, 'R');
    }
//Affiche l'entête du table qui affiche les ligne de frais de frais hors forfait
    function FraisHF($tailleLigne) {
        $this->SetTextColor(122, 147, 178, $tailleLigne);
        $this->SetFont('Times', 'BI', 11);
        $this->Cell(60, $tailleLigne, utf8_decode('Date'), 1, 0, 'C');
        $this->Cell(80, $tailleLigne, utf8_decode('Libellé'), 1, 0, 'C');
        $this->Cell(40, $tailleLigne, utf8_decode('Montant'), 1, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }
//Affiche de les ligne de frais de frais hors forfait
    function LigneFraisHF($date, $libelle, $montant, $tailleLigne) {
        $this->SetFont('Times', '', 11);
        $this->Cell(60, $tailleLigne, $date, 1, 0, 'L');
        $this->Cell(80, $tailleLigne, $libelle, 1, 0, 'L');
        $this->Cell(40, $tailleLigne, $montant, 1, 1, 'R');
    }
//Fonction afficher le total en pied de page 
    function Total($date, $total, $tailleLigne) {
        $this->setX(110);
        $this->Cell(40, $tailleLigne, 'Total : ' . $date, 1, 0, 'L');
        $this->Cell(40, $tailleLigne, $total, 1, 1, 'R');
    }
//Fonction pour la signature
    function Signature() {
        $this->Ln(12);
        $this->setX(150);
        $this->Cell(40, 10, utf8_decode('Fait à Paris,le ') . date('d') . '/' . date('m') . '/' . date('Y'), 0, 1, 'R');
        $this->setX(150);
        $this->Cell(40, 10, utf8_decode('Vu par l\'agent comptable'), 0, 0, 'R');
        $this->Ln(12);
        $this->Image('../images/sig.png', 145, null, 40, 7);
    }

// Pied de page
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Times', 'I', 8);
        // Numéro de page
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

}