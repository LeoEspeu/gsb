<?php

/*
 * Cette classe va recevoir toute les
 * informations nessessaire pour modifier la base de données
 * pour mettre a jour la base de données
 * 
 * 
 */

class MajLigneDeFrais {

    private $id = '';
    private $dateSelect = '';
    private $montant = '';
    private $dateDuFrais = '';
    private $etatDeLaFiche = '';
    private $idFicheFrais = '';

    function __construct($id, $dateSelect, $montant, $dateDuFrais, $etatDeLaFiche, $idFicheFrais) {
        $this->id = $id;
        $this->dateSelect = $dateSelect;
        $this->montant = $montant;
        $this->dateDuFrais = $dateDuFrais;
        $this->etatDeLaFiche = $etatDeLaFiche;
        $this->idFicheFrais = $idFicheFrais;
    }

    function getDateSelect() {
        return $this->dateSelect;
    }

    function getMontant() {
        return $this->montant;
    }

    function getDateDuFrais() {
        return $this->dateDuFrais;
    }

    function getEtatDeLaFiche() {
        return $this->etatDeLaFiche;
    }

    function setDateSelect($dateSelect) {
        $this->dateSelect = $dateSelect;
    }

    function setMontant($montant) {
        $this->montant = $montant;
    }

    function setDateDuFrais($dateDuFrais) {
        $this->dateDuFrais = $dateDuFrais;
    }

    function setEtatDeLaFiche($etatDeLaFiche) {
        $this->etatDeLaFiche = $etatDeLaFiche;
    }

   

}
