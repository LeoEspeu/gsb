<form action="index.php?uc=suivreFrais&action=confirmerFrais" method="post">
    Choix du visiteur: 
    <select class="form-control" name="listVisiteur" id="listNom"  required="">
        <option selected value> <?php $nomprenomselect ?> </option>

        <?php
        foreach ($lesVisiteurs as $unVisiteur) {
            $nom = htmlspecialchars($unVisiteur['nom']);
            $prenom = $unVisiteur['prenom'];
            $concatiser = $nom . ' ' . $prenom;
            ?>           
            <option value="<?php echo $concatiser; ?>"> <?php echo $concatiser; ?></option>

            <?php
        }
        ?>

    </select>
    <br>
    <input id="but" type="submit" value="Valider" class="btn btn-success"/>
    <!-- Button trigger modal -->
    <?php
    $compteur = 0;

    if (isset($_POST['listVisiteur'])) {
        $concatiser = $_POST['listVisiteur'];
        echo '<br>Visiteur selectionné : <b>' . $concatiser . '</b>';
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="text-center">Fiches de frais à mettre en payement</h3>
            </div>
            <div class="panel-body">
                <h3>La sélection d'une fiche de frais aura pour conséquence de la mettre en payement aprés validation</h3>
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
                    $lesFichesFull = getFicheDeFraisValideEnFonctionDuMois($uneId, $moisBDD);
                    echo '<br><br>';
                    $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                    $elem = getElementForfaitValide($uneId, $moisBDD);
                    ?>
                <li class="list-group-item">
                <?php

                    if (isset($_POST['cocher'])) {
                        ?>
                        
                    <input type="checkbox" value="" checked>
                            <?php
                        } else {
                            ?>
                    <input type="checkbox" value="">
                            <?php
                        }
                        echo 'Fiche de frais du ', $numMois . '/' . $numAnnee;
                        ?>
                        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal<?php echo $compteur; ?>">
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
                                            echo 'Fiche de frais de ', $concatiser, ' du ', $numMois . '/' . $numAnnee ;
                                            ?>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        if ($elem == NULL) {
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
                                        if ($lesFichesFull == NULL) {
                                            echo '<b><h3>Aucun élément Hors Forfait pour ce mois ci</h3></b><br>';
                                        } else {
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
                                            }
                                            ?>
                                        </table>
                                        <?php
                                        $nbJustifi = getNbJustificatif($uneId, $moisBDD);
                                        $nbJ = $nbJustifi[0]['nbjustificatifs'];
                                        if ($nbJ != NULL) {
                                            echo '<br> <button class="btn btn-primary" type="button">Justificatif(s) <span class="badge">', $nbJ, '</span></button> ';
                                        } else {
                                            echo '<b><h3>Aucun justificatif pour ce mois ci</h3></b><br> ';
                                        }
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Dévalider</button>
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
            <button id="créer" type="submit" class="btn btn-success btn-lg" name="cocher">Tout sélectionner</button>
            <button id="créer" type="submit" class="btn btn-danger btn-lg" name="submit">Mettre en payement</button>
        </p>
        <?php
    }
    ?>
</form>

<!--Bootstrap core JavaScript ============================================ -->
<!--Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="styles\bootstrap\bootstrap.min.js"></script>