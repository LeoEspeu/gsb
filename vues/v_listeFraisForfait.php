<?php
/**
 * Vue Liste des frais au forfait
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
<div class="row">    
    <h2>Renseigner ma fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post" 
              action="index.php?uc=gererFrais&action=validerMajFraisForfait" 
              role="form">
            <fieldset>       
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite'];
                    ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <label for="idFrais">Puissance de la voiture</label>
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="voiture" id="optionsRadios1" value="1" <?php if($maVoiture==1){echo 'checked';} ?>>                            
                            Véhicule  4CV Diesel
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="voiture" id="optionsRadios1" value="2" <?php if($maVoiture==2){echo 'checked';} ?>>
                            Véhicule 5/6CV Diesel
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="voiture" id="optionsRadios1" value="3" <?php if($maVoiture==3){echo 'checked';}?>>
                            Véhicule  4CV Essence
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="voiture" id="optionsRadios1" value="4" <?php if($maVoiture==4){echo 'checked';} ?>>
                            Véhicule 5/6CV Essence
                        </label>
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>
