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
    <!-- Button trigger modal -->
    <?php
    $compteur = 0;
    if (isset($_POST['listVisiteur'])) {
        if (isset($_POST['cocher']) || isset($_POST['payer'])) {
            $concatiser = $_SESSION['cocher'];
        } else {
            $concatiser = $_POST['listVisiteur'];
            $_SESSION['cocher'] = $concatiser;
        }
        echo '<br>Visiteur selectionné : <b>' . $concatiser . '</b>';
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="text-center">Fiches de frais à mettre en payement</h3>
            </div>
            <div class="panel-body">
                <h3>La sélection d'une fiche de frais aura pour conséquence de la mettre en payement aprés validation ,puis plus tard ,le visiteur sera remboursé</h3>
            </div>
            <ul class="list-group">
                <?php
                foreach ($lesMois as $unMois) {
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
                    $lesFichesFull = getFicheDeFraisEnFonctionDuMois($uneId, $moisBDD);
                    $fichesValide = estFicheValide($uneId, $moisBDD);
                    $monIdetatFiche = '';
                    foreach ($fichesValide as $value) {
                        $monIdetatFiche = $value['idetat'];
                    }
                    echo '<br><br>';
                    $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                    $elem = getElementForfait($uneId, $moisBDD);
                    ?>
                    <li class="list-group-item">
                        <?php
                        if (isset($_POST['cocher'])) {
                            if ($monIdetatFiche == 'VA') {
                                ?>

                                <input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" checked>
                                <?php
                            } else {
                                ?>
                                <input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" disabled>     
                                <?php
                            }
                        } else {
                            if ($monIdetatFiche == 'VA') {
                                ?>
                                <input type="checkbox" name="case<?php echo $compteur ?>" value="<?php echo $moisBDD ?>">
                                <?php
                            } else {
                                ?>
                                <input type="checkbox" name="case<?php echo $compteur; ?>" value="<?php echo $moisBDD; ?>" disabled>  
                                <?php
                            }
                        }
                        echo 'Fiche de frais du ', $numMois . '/' . $numAnnee;
                        ?>
                        <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#myModal<?php echo $compteur; ?>">
                            Voir détails
                        </button>
                        <!-- Modal -->
                        <div class="modal fade bs-example-modal-lg" id="myModal<?php echo $compteur; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">
                                            <?php
                                            echo 'Fiche de frais de ', $concatiser, ' du ', $numMois . '/' . $numAnnee;
                                            ?>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        if ($elem == NULL || $monIdetatFiche != 'VA') {
                                            echo '<br><h3>Aucun frais validé pour ce mois ci</3><br>';
                                        } else {
                                            foreach ($elem as $elements) {

                                                $quanti = $elements['quantite'];
                                                $libelem = $elements['libelle'];
                                                $montelem = $elements['montant'];
                                                $rez = $quanti * $montelem;
                                                echo '<br><h4>', $libelem, ' : ', $rez, '</h4><br>';
                                            }
                                        }
                                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                                            if ($lesFichesFull != NULL) {
                                                echo '<br><h3>Liste des frais hors-Forfait:</h3><br>';
                                                ?>
                                                <table class="table table-bordered" style="text-align: center;">
                                                    <tr>

                                                        <th>Montant : </th>
                                                        <th>Date du frais :  </th>
                                                        <th>Etat de la fiche: </th>

                                                    </tr>
                                                    <?php
                                                    foreach ($lesFichesFull as $fiche) {
                                                        $montant = $fiche['montant'];
                                                        $datemodif = $fiche['date'];
                                                        $libelleLigne = $fiche['libelle'];
                                                        echo '<tr><th>', $montant, '</th><th>', $datemodif, '</th><th>', $libelleLigne, '</th></tr>';
                                                    }
                                                } else {
                                                    echo '<br><h3>Aucun frais hors-forfait pour ce mois ci</3><br>';
                                                }
                                            }
                                            ?>
                                        </table>
                                        <?php
                                        $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                                        $nbJ = $nbJustifi[0]['nbjustificatifs'];
                                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                                            echo '<br> <button class="btn btn-primary" type="button">Justificatif(s) <span class="badge">', $nbJ, '</span></button> ';
                                        }
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <?php
                                        if ($elem != NULL && $monIdetatFiche == 'VA') {
                                            ?>
                                            <button type="button" class="btn btn-primary">Dévalider</button>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $compteur += 1;
                    }
                    ?>
                </li>
            </ul>
        </div>
        <p>
            <button id="créer" type="submit" class="btn btn-success " name="cocher">Tout sélectionner</button>
            <button id="créer" type="submit" class="btn btn-danger " name="payer">Mettre en payement</button>
        </p>
        <?php
    }
    ?>
</form>

<!--Bootstrap core JavaScript ============================================ -->
<!--Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="styles\bootstrap\bootstrap.min.js"></script>