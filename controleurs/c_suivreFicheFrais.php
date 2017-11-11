<?php
$nomselect = '';
if(!isset($_SESSION['cocher'])){
    $_SESSION['cocher']='';
}
$nomprenomselect = '';
$numAnnee=Null;
$lesMois = getMoisVisiteur();
$lesVisiteurs = getLesVisiteursAvecFicheDeFrais();
require './vues/v_suivreFicheFrais.php';
?>