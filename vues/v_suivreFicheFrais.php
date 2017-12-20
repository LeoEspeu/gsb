<form action="index.php?uc=suivreFrais&action=confirmerFrais" method="post">
    <div class="input-group">
        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user"></span> Choix du visiteur</span>
        <select class="form-control" name="listVisiteur" id="listNom"  required="" aria-describedby="basic-addon1"> 
            <?php
            foreach ($lesVisiteurs as $unVisiteur) {
                $nom = htmlspecialchars($unVisiteur['nom']);
                $prenom = $unVisiteur['prenom'];
                $concatiser = $nom . ' ' . $prenom;
                if ($concatiser == $nomprenomselect) {
                    ?>           
                    <option selected="" value="<?php echo $concatiser; ?>"> <?php echo $concatiser; ?></option>

                    <?php
                } else {
                    ?>           
                    <option value="<?php echo $concatiser; ?>"> <?php echo $concatiser; ?></option>

                    <?php
                }
            }
            ?>
        </select> 
    </div>
    <br>
    <input id="but" type="submit" value="Valider" class="btn btn-success"/>
    <?php
    $compteur = 0;
    if (isset($_POST['listVisiteur'])) {
        if (isset($_POST['cocher']) || isset($_POST['payer']) || isset($_POST[$btndeval])||isset($_POST['precedent']) || isset($_POST['suivant'])|| isset($_POST[$btndeval])){
            $concatiser = $_SESSION['cocher'];
        } else {
            $concatiser = $_POST['listVisiteur'];
            $_SESSION['cocher'] = $concatiser;
        }
        echo '<p>Visiteur selectionné : <b>' . $concatiser . '</b></p><br>';
        ?>

        <table class="table table-bordered table-responsive" style="text-align: center;">
            <caption style="border-radius:4px; background-color:#f2993a; color:white; text-align: center">Descriptif des Fiches de frais à mettre en payement </caption>
            <thead>
            <th>#</th> 
            <th><span class='glyphicon glyphicon-list-alt'></span> Date</th>
            <th><span>€</span> Montant Total frais </th>
            <th><span>€</span> Montant total frais Hors-Forfait </th>
            <th><span class='glyphicon glyphicon-book'></span> Nombre de justificatifs </th>
            <th><span class='glyphicon glyphicon-ok'></span> Option de devalidation </th>

            </thead>

            <?php
            foreach ($lesMois as $unMois) {
                if ($compteur >= $_SESSION['page'] && $compteur <$_SESSION['page']+10) {
                    $maVoiture = '';
                    $mois = $unMois['mois'];
                    $unMoisVar = "$mois";
                    $numAnnee = substr($unMoisVar, 0, -2);
                    $numMois = substr($unMoisVar, -2);
                    $nomprenomselect = $concatiser;
                    $moisSelect = $numAnnee . '/' . $numMois;
                    $moisBDD = preg_replace('#/#', '', $moisSelect);
                    list($nomselect, $prenomselect) = explode(" ", $nomprenomselect, 2);

                    $idDuVisiteur = getIdVisiteurAPartirDuNomEtDuPrenom($nomselect);

                    foreach ($idDuVisiteur as $uneId) {
                        $uneId = $idDuVisiteur['id'];
                    }
                    $monvisiteur = $pdo->ObtenirVoiture($uneId, $moisBDD);
                    foreach ($monvisiteur as $value) {
                        $maVoiture = $value['coefficient'];
                    }
                    $lesFichesFull = getFicheDeFraisNonRefuséEnFonctionDuMois($uneId, $moisBDD);
                    $fichesValide = estFicheValide($uneId, $moisBDD);
                    $monIdetatFiche = '';
                    foreach ($fichesValide as $value) {
                        $monIdetatFiche = $value['idetat'];
                    }

                    $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                    $elem = getElementForfait($uneId, $moisBDD);
                    ?>
                    <tr>
                        <?php
                        if (isset($_POST['cocher'])) {
                            if ($monIdetatFiche == 'VA') {
                                ?>

                                <th><input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" checked></th>
                                <?php
                            } else {
                                ?>
                                <th><input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" disabled></th>     
                                <?php
                            }
                        } else {
                            if ($monIdetatFiche == 'VA') {
                                ?>
                                <th style="text-align: center"><input type="checkbox" name="case<?php echo $compteur ?>" value="<?php echo $moisBDD ?>"></th>
                                <?php
                            } else {
                                ?>
                                <th style="text-align: center"><input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" disabled> </th> 
                                <?php
                            }
                        }
                        echo '<th style="text-align: center"><h4> ', $numMois . '/' . $numAnnee . '</h4></th>';
                        ?>      
                        <?php
                        if ($elem == NULL || $monIdetatFiche != 'VA') {
                            if ($monIdetatFiche == 'RB') {
                                ?><td colspan="5"><?php echo '<h4>Frais du mois remboursé</h4>'; ?></td><?php
                                } else {
                                    ?><td colspan="6"><?php echo '<h4>Aucun frais validé pour ce mois ci</h4>'; ?></td><?php
                            }
                        } else {
                            $fraistotal = NULL;
                            foreach ($elem as $elements) {

                                $quanti = $elements['quantite'];
                                $libelem = $elements['libelle'];
                                $montelem = $elements['montant'];
                                if ($libelem == 'Frais Kilométrique') {
                                    $montelem += $maVoiture;
                                }
                                $rez = $quanti * $montelem;
                                $fraistotal += $rez;
                            }
                            echo '<th style="text-align: center"><h4> ' . $fraistotal . '</h4></th>';
                        }
                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                            if ($lesFichesFull != NULL) {


                                $horsfraistotal = NULL;
                                foreach ($lesFichesFull as $fiche) {
                                    $montant = $fiche['montant'];
                                    $horsfraistotal += $montant;
                                }
                                echo '<th style="text-align: center"><h4>' . $horsfraistotal . '</h4></th>';
                            } else {
                                echo '<th><h4>Aucun frais hors-forfait pour ce mois ci</h4></th>';
                            }
                        }
                        ?>

                        <?php
                        $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                        $nbJ = $nbJustifi[0]['nbjustificatifs'];
                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                            echo '<th style="text-align: center"> <h4>', $nbJ, '</h4></th> ';
                        }
                        ?>


                        <?php
                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                            ?>
                            <th>
                                <button  type="submit" class="btn btn-danger" name="deval<?php echo $compteur ?>" value="<?php echo $moisBDD; ?>">Dévalider</button></th>
                            <?php
                        }
                        ?>

                        <?php
                    }
                    $compteur += 1;
                }
                ?>
            </tr>
        </table>
        <nav aria-label="Page navigation">
            <ul class="pager">
                <li class="previous"><button type="submit" class="btn btn-warning" name="precedent" <?php if($_SESSION['page']==0){echo 'disabled';} ?>><span aria-hidden="true">&larr;</span> Précédente</button></li>
                <li class="next"><button type="submit" class="btn btn-warning" name="suivant" <?php if($_SESSION['page']+10>=$pagesMois){echo 'disabled';} ?>>Suivante <span aria-hidden="true">&rarr;</span></button></li>
            </ul>
        </nav>
        <p>
            <button id="créer" type="submit" class="btn btn-success " name="cocher">Tout sélectionner</button>
            <button id="créer" type="submit" class="btn btn-danger " name="payer">Mettre en payement</button>
        </p>
        <?php
    }
    ?>
</form>