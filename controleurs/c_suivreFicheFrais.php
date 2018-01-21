<?php
if (!isset($_SESSION['idComptable'])) {
    header('Location:index.php');
}

$nomselect = '';
$btndeval = '';
if (!isset($_SESSION['cocher'])) {
    $_SESSION['cocher'] = '';
}
$nomprenomselect = '';
$numAnnee = Null;
$lesMois = $pdo->getMoisVisiteur();
$pagesMois = count($lesMois);

/**
 * Action mettant en payement puis en remboursement une fiche de frais
 */
if (isset($_POST['payer'])) {
    $nomprenomselect = $_SESSION['cocher'];
    list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);
    $idDuVisiteur = $pdo->getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);
    $monId = '';
    foreach ($idDuVisiteur as $value) {
        $monId = $idDuVisiteur['id'];
    }
    for ($index = 0; $index < count($lesMois); $index++) {
        if (isset($_POST['case' . $index])) {
            $pdo->fairePayement($monId, $_POST['case' . $index]);
            $pdo->faireremboursement($monId, $_POST['case' . $index]);
            $pdo->majdatedemodification($monId, $_POST['case' . $index]);
            $_SESSION['ok'] = 16;
        }
    }
}

/**
 * Boucle permettant de "devalider" une fiche de frais
 */
for ($index1 = 0; $index1 < count($lesMois); $index1++) {
    if (isset($_POST['deval' . $index1])) {
        $btndeval = 'deval' . $index1;
        $nomprenomselect = $_SESSION['cocher'];
        list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);
        $idDuVisiteur = $pdo->getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);
        $monId = '';
        foreach ($idDuVisiteur as $value) {
            $monId = $idDuVisiteur['id'];
        }
        $pdo->fairedevalider($monId, $_POST[$btndeval]);
        $pdo->majdatedemodification($monId, $_POST[$btndeval]);
        $_SESSION['ok'] = 17;
        $pdo->majMontantValideFicheFrais($monId,$_POST[$btndeval],0);
    }
}

/**
 * Si on selectionne un nouvel utilisateur ,on revient la premiére page de mois
 */
if(!isset($_POST['precedent']) && !isset($_POST['suivant']) && !isset($_POST['cocher']) && !isset($_POST['payer']) && !isset($_POST[$btndeval])){
    $_SESSION['page'] =0;
}
/**
 * On va sur la page affichant les mois qui précedent
 */
elseif (isset ($_POST['precedent']) && !isset($_POST['cocher']) && !isset($_POST['payer']) && !isset($_POST[$btndeval])) {
    $_SESSION['page'] -=10;
}
/**
 * On va sur la page affichant la suite des mois
 */
 elseif (!isset($_POST['cocher']) && !isset($_POST['payer']) && !isset($_POST[$btndeval])) {
    $_SESSION['page'] +=10;
}

$_SESSION['MontantValide']=0;
$lesVisiteurs = $pdo->getLesVisiteursAvecFicheDeFrais();
require './vues/v_suivreFicheFrais.php';
?>
