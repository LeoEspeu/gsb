<?php
/* Dev: Mehdi benbahri
 * Dev2: Léo Espeu
 * HTML/CSS
 * Validation des fiche de frais pour le comptable,seul le comptable peut avoir accès ! 
 */
?>


<?php
$nb = 0;
$n = 1;
if (isset($_SESSION['ok'])) {
    if ($_SESSION['ok'] === 0) {
        ?>
        <div class="alert alert-success alert-dismissable">
            Les données on été transférées avec succès à la base de donnée. 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === -1) {
        ?>
        <div class = "alert alert-warning fade in " role = "alert">
            <p>Merci de bien vouloir selectionner un utilisateur et un mois pour celui-ci</p>
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 6) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            La date saisi est invalide ! 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 7) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            date d'enregistrement du frais dépassé, plus de 1 an 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 8) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            Le champ description ne peut pas être vide 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 9) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            Le champ montant ne peut pas être vide 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 10) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            Le champ montant doit être numérique
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
    if ($_SESSION['ok'] === 11) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            Le champ montant doit être superieur à zéro 
        </div>
        <?php
        $_SESSION['ok'] = 1;
    }
}
if ($idetat != 'CL' && $idetat != '') {
    ?>
    <div class="alert alert-danger alert-dismissable">
        La fiche de frais de ce visiteur ce mois ci ne 
        peut être modifiée ,toutes les options sont bloquées ,les raisons possibles:  <br>
        -La fiche de frais est en cours de saisie<br>
        -La fiche de frais est déja remboursée<br>
        -La fiche de frais est déja validée (vous pouvez encore la renvoyée dans ce cas la ,dans la page de suivi des frais)
    </div>
    <?php
}
/* Dev: Mehdi benbahri
 * Dev2: Léo Espeu
 * HTML/CSS
 * Validation des fiche de frais pour le comptable,seul le comptable peut avoir accès ! 
 */
?>
<br>

<form action="index.php?uc=validerFrais&action=confirmerFrais" method="post">

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
    <div class="input-group">
        <span class="input-group-addon" id="basic-addon2"><span class="glyphicon glyphicon-list-alt"></span> Choix de la date (mm/aaaa)</span>
        <select id="lstMois" name="lstMois" class="form-control" aria-describedby="basic-addon1" required>

            <?php
            foreach ($lesMois as $unMois) {
                $mois = $unMois['mois'];
                $unMoisVar = "$mois";

                $numAnnee = substr($unMoisVar, 0, -2);
                $numMois = substr($unMoisVar, -2);

                if ($unMois[0] == $moisSelect) {
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
    <br>

    <input id="but" type="submit" value="Valider" class="btn btn-success"/>
    <button id="reset" type="button" class="btn btn-danger">Réintinaliser</button>
</form>

<form class="form-inline" method="POST" action="../gsb/controleurs/c_majFichefrais.php">
    <table class="table table-bordered" <?php
    if (isset($_POST['lstMois'])) {
        'style="text-align: center;"';
    } else {
        echo 'style="text-align: center; visibility:hidden" ';
    }
    ?> >
        <caption style="border-radius:4px; background-color:#f2993a; color:white;">Descriptif des éléments Hors Forfait - <input style="width: 15%" type="text" class='form-control input-sm' value="<?php
            if (isset($prenomselect)) {
                echo $nomselect;
            }
            ?> " disabled/> <input style="width: 15%" type="text" class='form-control input-sm' value="<?php
                                                                                                                                 if (isset($prenomselect)) {
                                                                                                                                     echo $prenomselect;
                                                                                                                                 }
                                                                                                                                 ?> " disabled/> <input style="visibility:hidden" type='text' id='unmois' value='<?php
                                                                                                                                 if (isset($moisSelect)) {
                                                                                                                                     echo $moisSelect;
                                                                                                                                 }
                                                                                                                                 ?> ' class='form-control' name='unmois'> <input style="visibility:hidden" type='text' id='leID' value='<?php
                                                                                                                                 if (isset($uneId)) {
                                                                                                                                     echo $uneId;
                                                                                                                                 }
                                                                                                                                 ?>' class='form-control' name='leID' > </caption>                                                                                                                              


        <tr>
            <th>#</th>
            <th>Montant : </th>
            <th>Date du frais :  </th>
            <th>Etat de la fiche: </th>
            <th> Outils:</th>
            <th> </th>
        </tr>
        <br>
        <?php
        if (isset($lesFichesFull)) {
            foreach ($lesFichesFull as $fiche) {

                $nb = $nb + 1;
                $montant = $fiche['montant'];
                $datemodif = $fiche['date'];
                $libelleLigne = $fiche['libelle'];
                ?>
                <tr> <?php echo '<td name="tnb"> <input  id="tdrest', $nb, '" type="number" class="form-control" min="', $nb, '" max="', $nb, '" name="', $nb, '" value="', $nb, '" title="', $restor = "$montant.*.$datemodif.*.$libelleLigne", '"/></td><td> ', "<div class='input-group'><span class='input-group-addon id='group'>€</span><input type='number' id='mont$nb' value='$montant' class='form-control' name='mont$nb' aria-describedby='group'></div>", '</td><td>', "<div class='input-group'><span class='input-group-addon id='group'><span class='glyphicon glyphicon-list-alt'></span></span><input type='text' id='date$nb' value='$datemodif' class='form-control' name='date$nb' aria-describedby='group'></div>", '</td><td> ', "<input type='text' id='lib$nb' value='$libelleLigne' class='form-control' name='lib$nb'>", '</td> ' ?>

                    <td> <button id="<?php echo $nb ?>" type="button" title="refuser" onclick="recalculate(this.id)" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span></button> <b style="color:#adadad">|</b> <button type="button" id="restor" title="<?php echo $nb; ?>" class="btn btn-warning" ><span class="glyphicon glyphicon-refresh"></span></button> <b style="color:#adadad">|</b> <button id="<?php echo $nb ?>" type="button" title="refuser" onclick="calculate(this.id)" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button></td>
                    <td id="tr<?php echo $nb ?>"></td>
                </tr>

                <?php
            }
            if ($lesFichesFull == NULL) {
                ajouterErreur('Aucun éléments hors forfait pour ce mois ou cette utilisateur');
                include 'v_erreurs.php';
            }
        }
        if (isset($_POST['lstMois'])) {
            $nbJ = $nbJustifi[0]['nbjustificatifs'];
            echo ' Nombre de Justification : <b>' . "<input style='width: 7%' name='nbJ' type='number' value='$nbJ' class='form-control' id='usr'>" . '</b>';
            foreach ($elem as $elements) {
                $quanti = $elements['quantite'];
                $libelem = $elements['libelle'];
                $montelem = $elements['montant'];
                $rez = $quanti;
                echo ' ', $libelem, ' : ', "<input name='n$n' style='width: 6%' type='number' value='$rez' class='form-control'>";
                $n++;
            }
            echo ' Coef. :';
            ?>
            <select name="voiture" class="form-control">
                <option <?php
                if ($coefVoiture == '1') {
                    echo 'selected';
                }
                ?>>0.52 €/Km</option>
                <option <?php
                if ($coefVoiture == '2') {
                    echo 'selected';
                }
                ?>>0.58 €/Km</option>
                <option <?php
                if ($coefVoiture == '3') {
                    echo 'selected';
                }
                ?>>0.62 €/Km</option>
                <option <?php
                if ($coefVoiture == '4') {
                    echo 'selected';
                }
                ?>>0.68 €/Km</option>
            </select>
            <?php
        }
        echo '<br><br> ';
        $nblignemax = $nb;
        ?>    
    </table>



    <div id="gensub"><input type="submit" class="btn btn-success" value="Valider tout et enregistrer dans la base de donnée"<?php
                            if (isset($_POST['lstMois'])) {
                                
                            } else {
                                echo 'style="visibility:hidden;" ';
                            }
                            ?> <?php
                            if ($idetat != 'CL') {
                                echo 'disabled';
                            }
                            ?>></div>

    <br>
    <br>
</form>
<script type="text/javascript">

    var i = 1;
    var nbLigne = "<?php echo $nblignemax + 1; ?>";
    document.getElementById("reset").addEventListener("click", function () {
        location.reload(false);
        console.log("Rechargement en cour...")
    });
    articles = document.getElementsByTagName('button');
    for (var i = 0; i < articles.length; i++) {
        articles[i].addEventListener('click', redirect);
    }
    function redirect(ev) {
        idselected = ev.target.title;
        donne = document.getElementById('tdrest' + idselected).title;
        var str = donne;
        var rez = str.split(".*.");
        var ancmont = rez[0];
        var ancdate = rez[1];
        var ancdes = rez[2];
        var montant = "mont" + idselected;
        var datesel = "date" + idselected;
        var libsel = "lib" + idselected;
        document.getElementById(montant).value = ancmont;
        document.getElementById(datesel).value = ancdate;
        document.getElementById(libsel).value = ancdes;
    }
    function calculate(idligne) {
        var retient;

        console.log(idligne);
        if (document.getElementById('lib' + idligne).value.substring(0, 8) != '[REFUSÉ]') {
            retient = document.getElementById('lib' + idligne).value;
            document.getElementById('lib' + idligne).value = '[REFUSÉ] ' + retient;


        }



    }
    function recalculate(idligne) {
        var retient;

    function calculate(idligne) {
        var retient;
        
        console.log(idligne);
        if (document.getElementById('lib' + idligne).value.substring(0,8) != '[REFUSÉ]'){
            retient = document.getElementById('lib' + idligne).value;
            document.getElementById('lib' + idligne).value='[REFUSÉ] ' + retient;
            
           
        }
       
        
        
    }
    function recalculate(idligne) {
        var retient;
        
        console.log(idligne);
        if (document.getElementById('lib' + idligne).value.substring(0,8) == '[REFUSÉ]'){
            retient = document.getElementById('lib' + idligne).value.substring(9);
            document.getElementById('lib' + idligne).value=retient;
            
           
        }
       
        
        
    }






</script>
