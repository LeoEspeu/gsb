<?php

/*
 * Cette classe va recevoir toute les
 * informations nessessaire pour modifier la base de données
 * pour mettre a jour la base de données
 * 
 * 
 */

class MajLigneDeFrais {

    private $nom = '';
    private $prenom = '';
    private $dateSelect = '';
    private $montant = '';
    private $dateDuFrais = '';
    private $etatDeLaFiche = '';

    function __construct($nom, $prenom, $dateSelect, $montant, $dateDuFrais, $etatDeLaFiche) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateSelect = $dateSelect;
        $this->montant = $montant;
        $this->dateDuFrais = $dateDuFrais;
        $this->etatDeLaFiche = $etatDeLaFiche;
    }

    function getNom() {
        return $this->nom;
    }

    function getPrenom() {
        return $this->prenom;
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

    function setNom($nom) {
        $this->nom = $nom;
    }

    function setPrenom($prenom) {
        $this->prenom = $prenom;
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
