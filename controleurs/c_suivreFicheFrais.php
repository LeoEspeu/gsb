<?php
$nomselect = '';
$nomprenomselect = '';
$numAnnee=Null;
$lesMois = getMoisVisiteur();
$lesVisiteurs = getLesVisiteursAvecFicheDeFrais();
require './vues/v_suivreFicheFrais.php';
?>