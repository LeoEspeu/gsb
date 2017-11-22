<?php

$nomselect = '';
$btndeval = '';
if (!isset($_SESSION['cocher'])) {
    $_SESSION['cocher'] = '';
}
$nomprenomselect = '';
$numAnnee = Null;
$lesMois = getMoisVisiteur();
if (isset($_POST['payer'])) {
    $nomprenomselect = $_SESSION['cocher'];
    list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);
    $idDuVisiteur = getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);
    $monId = '';
    foreach ($idDuVisiteur as $value) {
        $monId = $idDuVisiteur['id'];
    }
    for ($index = 0; $index < count($lesMois); $index++) {
        if (isset($_POST['case' . $index])) {
            fairePayement($monId, $_POST['case' . $index]);
            faireremboursement($monId, $_POST['case' . $index]);
        }
    }
}
for ($index1 = 0; $index1 < count($lesMois); $index1++) {
    if (isset($_POST['deval' . $index1])) {
        $btndeval = 'deval' . $index1;
        $nomprenomselect = $_SESSION['cocher'];
        list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);
        $idDuVisiteur = getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);
        $monId = '';
        foreach ($idDuVisiteur as $value) {
            $monId = $idDuVisiteur['id'];
        }
        fairedevalider($monId, $_POST[$btndeval]);
    }
}
$lesVisiteurs = getLesVisiteursAvecFicheDeFrais();
require './vues/v_suivreFicheFrais.php';
?>