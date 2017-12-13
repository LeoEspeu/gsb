<?php
/**
 * Vue Liste des mois
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<h2>Mes fiches de frais</h2>
<?php
if (isset($_SESSION['pdfdupli'])) {
    if ($_SESSION['pdfdupli'] === true) {
        ?> <div class="alert alert-danger alert-dismissable">
            Vous avez déjà généré ce PDF.
        </div> <?php
        $_SESSION['pdfdupli']=false;
    }
}
?>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un mois : </h3>
    </div>
    <div class="col-md-4">
        <form action="index.php?uc=etatFrais&action=voirEtatFrais" 
              method="post" role="form">
            <div class="form-group">
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
<?php
foreach ($lesMois as $unMois) {
    $mois = $unMois['mois'];

    $numAnnee = $unMois['numAnnee'];
    $numMois = $unMois['numMois'];
    if ($mois == $moisASelectionner) {
        ?>
                            <option selected value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>
            <input id="ok" type="submit" value="Valider" name="sub" class="btn btn-success" 
                   role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button">

        </form>
<?php
if (isset($_POST['lstMois'])) {
    $_SESSION['moissle'] = $_POST['lstMois'];
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
}
?>
        <form method="post" action="controleurs/c_genpdf.php" target="_blank">
            <br>
            <input id="ok" type="submit" value="<?php
        if (isset($_POST['lstMois'])) {
            echo 'Télécharger le PDF de la fiche du ' . $numMois . '/' . $numAnnee;
        } else {
            echo 'Veuillez choisir une fiche';
        }
        ?>" name="sub" class="btn btn-primary" role="button"
            <?php
            if (isset($_POST['lstMois'])) {
                
            } else {
                echo 'style="visibility:hidden" ';
            }
            ?>



                   <?php
                   if (isset($_POST['lstMois'])) {
                       
                   } else {
                       echo 'disabled';
                   }
                   ?>>

        </form>
    </div>
</div>