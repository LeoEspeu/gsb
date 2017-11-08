<?php
/* Dev: Mehdi benbahri
 * Dev2: Léo Espeu
 * HTML/CSS
 * Validation des fiche de frais pour le comptable,seul le comptable peut avoir accès ! 
 */
?>


<?php
$nb = 0;






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
<form method="get" action="../gsb/controleurs/c_majFichefrais.php">
    <table class="table table-bordered" style="text-align: center;">
        <caption style="border-radius:4px; background-color:#f2993a; color:white;">Descriptif des éléments Hors Forfait</caption>


        <tr>
            <th>#</th>
            <th>Montant : </th>
            <th>Date du frais :  </th>
            <th>Etat de la fiche: </th>

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
                <tr> <?php echo '<td name="tnb"> <input id="tdrest', $nb, '" type="number" class="form-control" min="', $nb, '" max="', $nb, '" name="', $nb, '" value="', $nb, '" title="', $restor = "$montant.*.$datemodif.*.$libelleLigne", '"/></td><td> ', "<div class='input-group'><span class='input-group-addon id='group'>€</span><input type='text' id='mont$nb' value='$montant' class='form-control' name='mont$nb' aria-describedby='group'></div>", '</td><td>', "<div class='input-group'><span class='input-group-addon id='group'><span class='glyphicon glyphicon-list-alt'></span></span><input type='text' id='date$nb' value='$datemodif' class='form-control' name='date$nb' aria-describedby='group'></div>", '</td><td> ', "<input type='text' id='lib$nb' value='$libelleLigne' class='form-control' name='lib$nb'>", '</td> ' ?>
                    
                    <td><button type="button" id="restor" title="<?php echo $nb; ?>" class="btn btn-danger">Réintialiser</button></td>
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
            echo '<br> Nombre de Justification : <b>' . "<input type='text' value='$nbJ' class='form-control' id='usr'>" . '</b>';

            foreach ($elem as $elements) {

                $quanti = $elements['quantite'];
                $libelem = $elements['libelle'];
                $montelem = $elements['montant'];
                $rez = $quanti * $montelem;
                echo '<br>', $libelem, ' : <br>', "<input type='text' value='$rez' class='form-control'>";
            }
        }
        echo '<br>';
        $nblignemax = $nb;
        ?>    
    </table>
    <div id="gensub"></div>
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
        idselected=ev.target.title;
   
        donne=document.getElementById('tdrest'+idselected).title;
    
        var str = donne;
        var rez = str.split(".*.");
    
        var ancmont=rez[0];
        var ancdate=rez[1];
        var ancdes=rez[2];
        
        var montant="mont"+idselected;
        var datesel="date"+idselected;
        var libsel="lib"+idselected;
        
        document.getElementById(montant).value=ancmont;
        document.getElementById(datesel).value=ancdate;
        document.getElementById(libsel).value=ancdes;
        
    }
    document.getElementById("but").addEventListener("click", function () {
        console.log("SLT");
    document.getElementById("gensub").InnerHTML += '<input type="submit" class="btn btn-warning"/>';
    });
</script>
