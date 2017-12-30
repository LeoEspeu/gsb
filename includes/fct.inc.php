<?php

/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque visiteur est connecté
 *
 * @return vrai ou faux
 */
function estConnecte() {
    return isset($_SESSION['idVisiteur']);
}

/**
 * Teste si un comptable est connecté
 * 
 * @return vrai ou faux
 */
function estConnecteComptable() {
    return isset($_SESSION['idComptable']);
}

/**
 * Enregistre dans une variable session les infos d'un visiteur
 *
 * @param String $idVisiteur ID du visiteur
 * @param String $nom        Nom du visiteur
 * @param String $prenom     Prénom du visiteur
 *
 * @return null
 */
function connecter($idVisiteur, $nom, $prenom) {
    $_SESSION['idVisiteur'] = $idVisiteur;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
}

/**
 * Enregistre dans une variable session les infos d'un comptable
 *
 * @param String $idVisiteur ID du comptable
 * @param String $nom        Nom du comptable
 * @param String $prenom     Prénom du comptable
 *
 * @return null
 */
function connectercomptable($idVisiteur, $nom, $prenom) {
    $_SESSION['idComptable'] = $idVisiteur;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter() {
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate) {
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate) {
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date) {
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur) {
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers) {
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee) {
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date) {
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais) {
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant) {
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                        "date d'enregistrement du frais dépassé, plus de 1 an"
                );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg) {
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs() {
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}

/**
 * Fonction qui les mois d'un visiteur grace a son id
 * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
 *
 * @return Array de visiteurs
 */
function getMoisVisiteur() {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct mois FROM fichefrais,visiteur order by mois;";
    $res = $pdoSansParam->query($req);

    $lesMois = $res->fetchAll();
    return $lesMois;
}

/**
 * Méthode permettant d'obtenir les mois des fiche de frais cloturé
 * 
 * @param Id du visiteur
 * @return Retourne une liste de mois 
 */
function getMoisVisiteurCloture($id) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct mois FROM fichefrais where idvisiteur='$id' and  idetat='CL' order by mois ;";
    $res = $pdoSansParam->query($req);

    $lesMois = $res->fetchAll();
    return $lesMois;
}

/**
 * Fonction qui retourne l'ID des visiteurs avec des fiches de Frais
 * Avec seulement leurs nom et leurs prenom;
 * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
 *
 * @return Array de visiteurs
 */
function getIdVisiteurAPartirDuNomEtDuPrenom($np) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct id FROM visiteur WHERE nom LIKE '$np';";
    $res = $pdoSansParam->query($req);

    $lesLignes = $res->fetch();
    return $lesLignes;
}

/**
 * Fonction qui retourne la liste des visiteurs avec des fiches de Frais
 * Avec seulement leurs nom et leurs prenom;
 * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
 *
 * @return Array de visiteurs
 */
function getLesVisiteursAvecFicheDeFrais() {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = 'SELECT distinct nom,prenom FROM `gsb_frais`.`visiteur`,`gsb_frais`.`fichefrais` WHERE id=idVisiteur ORDER BY nom,prenom asc;';
    $res = $pdoSansParam->query($req);
    $lesLignes = $res->fetchAll();
    return $lesLignes;
}

/**
 * Fonction qui les fiches de frais d'un visiteur en fonction du mois
 * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
 *
 * @return Array de visiteurs
 */
function getFicheDeFraisEnFonctionDuMois($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct lignefraishorsforfait.mois,lignefraishorsforfait.libelle,date,montant,lignefraishorsforfait.id
    FROM `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
    WHERE '$id' = lignefraishorsforfait.idvisiteur
    AND lignefraishorsforfait.mois = '$mois';";
    $res = $pdoSansParam->query($req);

    $lesFichesFull = $res->fetchAll();
    return $lesFichesFull;
}

/**
 * Fonction qui les fiches de frais non refusé d'un visiteur en fonction du mois
 * 
 * @param type $id
 * @param type $mois
 * @return type
 */
function getFicheDeFraisNonRefuséEnFonctionDuMois($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct lignefraishorsforfait.mois,lignefraishorsforfait.libelle,date,montant,lignefraishorsforfait.id
    FROM `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
    WHERE '$id' = lignefraishorsforfait.idvisiteur
    AND lignefraishorsforfait.mois = '$mois' and libelle not like'%REFUSÉ%';";
    $res = $pdoSansParam->query($req);

    $lesFichesFull = $res->fetchAll();
    return $lesFichesFull;
}

/**
 * Retourne le nombre de justificatifs en fonction du mois et du visiteur
 * 
 * @param Id du visiteur et mois de la fiche de frais
 * @return type
 */
function getNbJustificatif($id, $mois) {

    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "Select distinct nbjustificatifs,visiteur.id
    From fichefrais
    INNER JOIN visiteur
    ON visiteur.id = fichefrais.idVisiteur
    INNER JOIN lignefraisforfait
    ON fichefrais.mois = lignefraisforfait.mois
    WHERE visiteur.id='$id'
    AND lignefraisforfait.mois='$mois';";
    $res = $pdoSansParam->query($req);
    $nbJuste = $res->fetchAll();
    if ($res->rowCount() > 0) {
        return $nbJuste;
    } else {
        ajouterErreur('Aucun justificatif pour cette utilisateur');
    }
}

/**
 *  Retourne les frais hors-forfaits en fonction du mois et du visiteur
 * 
 * @param Id du visiteur et mois de la fiche de frais
 * @return Liste de frais hors-forfaits ou message d'erreur
 */
function getElementForfait($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "Select distinct idfraisforfait,libelle,montant,visiteur.id,lignefraisforfait.quantite
    From lignefraisforfait
    INNER JOIN fraisforfait
    ON lignefraisforfait.idfraisforfait=fraisforfait.id
    INNER JOIN fichefrais
    ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
    INNER JOIN visiteur
    ON visiteur.id=fichefrais.idvisiteur
    WHERE visiteur.id='$id'
    AND lignefraisforfait.mois='$mois';";
    $res = $pdoSansParam->query($req);
    $tripleInnerJoin = $res->fetchAll();
    if ($res->rowCount() > 0) {
        return $tripleInnerJoin;
    } else {
        ajouterErreur('Aucun éléments hors forfaits pour cette utilisateur ou pour ce mois ci');
        return $tripleInnerJoin;
    }
}

/**
 * Retourne une fiche de frais en fonction du mois et du visiteur pour savoir si elle est valide ou non
 * @param type $id
 * @param type $mois
 * @return type
 */
function estFicheValide($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "select * from fichefrais where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $lesFichesValides = $res->fetchAll();
    return $lesFichesValides;
}

/**
 * Fonction permettant de mettre un frais en remboursement en fonction du vsiteur et du mois
 * @param type $id
 * @param type $mois
 */
function fairePayement($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='MP' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

/**
 * Fonction permettant de mettre un frais en remboursement en fonction du vsiteur et du mois
 * @param type $id
 * @param type $mois
 */
function faireremboursement($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='RB' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

/**
 * Passe une fiche de frais de l'état de validé à cloturé
 * 
 * @param Id du visiteur et mois de la fiche de frais
 */
function fairedevalider($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='CL' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

/**
 * Met à jour la date de modification de la fiche de frais à la date d'aujourd'hui
 * 
 * @param Id du visiteur et mois de la fiche de frais
 */
function majdatedemodification($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set datemodif=now() where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

/**
 * Met à jour le libellé du frais hors-forfait
 * 
 * @param Nouveau libellé 
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 * @param Id du frais hors-forfait
 */
function majlibelle($arrlib, $id, $lemois, $idFiche) {

    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'Update `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
        set  lignefraishorsforfait.libelle = :lib
        WHERE :id = lignefraishorsforfait.idvisiteur
        AND lignefraishorsforfait.mois = :date
        AND lignefraishorsforfait.id=:idFiche;'
    );
    $requetePrepare->bindParam(':lib', $arrlib, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idFiche', $idFiche, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la date du frais hors-forfait
 * 
 * @param Nouvelle date
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 * @param Id du frais hors-forfait
 */
function majdate($arrdate, $id, $lemois, $idFiche) {

    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'Update `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
        set  lignefraishorsforfait.date = :date
        WHERE :id = lignefraishorsforfait.idvisiteur
        AND lignefraishorsforfait.mois = :mois
        AND lignefraishorsforfait.id=:idFiche;'
    );
    $requetePrepare->bindParam(':date', $arrdate, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':mois', $lemois, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idFiche', $idFiche, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour le montant du frais hors-forfait
 * 
 * @param Nouveau montant du frais hors-forfait
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 * @param Id du frais hors-forfait
 */
function majmont($arrmont, $id, $lemois, $idFiche) {

    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'Update `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
        set  lignefraishorsforfait.montant = :date
        WHERE :id = lignefraishorsforfait.idvisiteur
        AND lignefraishorsforfait.mois = :mois
        AND lignefraishorsforfait.id=:idFiche;'
    );
    $requetePrepare->bindParam(':date', $arrmont, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':mois', $lemois, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idFiche', $idFiche, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Passe une fiche de frais de l'état de cloturé à validé
 * 
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function validerUneFicheDeFais($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='VA' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

/**
 * Met à jour la quantité des frais d'étape en fonction du mois et du visiteur
 * 
 * @param Nouvelle quantité des frais d'étape
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function majetp($quant, $id, $lemois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'update  
    lignefraisforfait
    INNER JOIN fraisforfait
    ON lignefraisforfait.idfraisforfait=fraisforfait.id
    INNER JOIN fichefrais
    ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
    INNER JOIN visiteur
    ON visiteur.id=fichefrais.idvisiteur
    set quantite=:quan
    WHERE visiteur.id=:id
    AND lignefraisforfait.mois=:date
    AND lignefraisforfait.idfraisforfait="ETP";'
    );
    $requetePrepare->bindParam(':quan', $quant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la quantité de frais kilométrique en fonction du mois et du visiteur
 * 
 * @param Nouvelle quantité de frais kilométrique
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function majkm($quant, $id, $lemois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'update  
    lignefraisforfait
    INNER JOIN fraisforfait
    ON lignefraisforfait.idfraisforfait=fraisforfait.id
    INNER JOIN fichefrais
    ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
    INNER JOIN visiteur
    ON visiteur.id=fichefrais.idvisiteur
    set quantite=:quan
    WHERE visiteur.id=:id
    AND lignefraisforfait.mois=:date
    AND lignefraisforfait.idfraisforfait="KM";'
    );
    $requetePrepare->bindParam(':quan', $quant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la quantité de frais de nuitée en fonction du mois et du visiteur
 * 
 * @param Nouvelle quantité de frais de nuitée
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function majnuit($quant, $id, $lemois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'update  
    lignefraisforfait
    INNER JOIN fraisforfait
    ON lignefraisforfait.idfraisforfait=fraisforfait.id
    INNER JOIN fichefrais
    ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
    INNER JOIN visiteur
    ON visiteur.id=fichefrais.idvisiteur
    set quantite=:quan
    WHERE visiteur.id=:id
    AND lignefraisforfait.mois=:date
    AND lignefraisforfait.idfraisforfait="NUI";'
    );
    $requetePrepare->bindParam(':quan', $quant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la quantité de frais de repas en fonction du mois et du viditeur
 * 
 * @param Nouvelle quantité de frais de repas
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function majrep($quant, $id, $lemois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'update  
    lignefraisforfait
    INNER JOIN fraisforfait
    ON lignefraisforfait.idfraisforfait=fraisforfait.id
    INNER JOIN fichefrais
    ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
    INNER JOIN visiteur
    ON visiteur.id=fichefrais.idvisiteur
    set quantite=:quan
    WHERE visiteur.id=:id
    AND lignefraisforfait.mois=:date
    AND lignefraisforfait.idfraisforfait="REP";'
    );
    $requetePrepare->bindParam(':quan', $quant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la quantité de justificatifs en fonction du mois et du visiteur
 * 
 * @param Nouvelle quantité de justificatifs
 * @param Id du visiteur
 * @param Mois du frais hors-forfait
 */
function majnj($quant, $id, $lemois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'update  
    fichefrais
    INNER JOIN visiteur
    ON visiteur.id = fichefrais.idVisiteur
    INNER JOIN lignefraisforfait
    ON fichefrais.mois = lignefraisforfait.mois
    set nbjustificatifs=:quan
    WHERE visiteur.id=:id
    AND lignefraisforfait.mois=:date;'
    );
    $requetePrepare->bindParam(':quan', $quant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Met à jour la voiture utilisée en fonction du mois et du visiteur
 * 
 * @param Id du visiteur
 * @param Mois du frais hors
 * @param Libellé de la nouvelle voiture
 */
function majvoiture($id, $lemois, $libvoiture) {
    $libvoiture = '%' . $libvoiture . '%';
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'UPDATE 
                fichefrais 
                SET fichefrais.idvoiture = (select id from voiture where libellevoiture like :libelle)
                WHERE fichefrais.idvisiteur=:id AND fichefrais.mois=:date;'
    );
    $requetePrepare->bindParam(':libelle', $libvoiture, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $lemois, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Controle l'etat des valeurs des frais hors forfaits saisis 
 * 
 * @param nuit
 * @param repas
 * @param route
 * @param etape
 * @param justificatif
 * @return Valeur correspondant à une erreur ou si il n'y a rien 
 */
function ControleInfosFrais($nuit, $repas, $route, $etape, $justificatif) {
    if ($nuit == NULL || $repas == NULL || $route == NULL || $etape == NULL || $justificatif == NULL) {
        if ($nuit < 0 || $repas < 0 || $route < 0 || $etape < 0 || $justificatif < 0) {
            return 12;
        } else {
            return 13;
        }
    } else if ($nuit < 0 || $repas < 0 || $route < 0 || $etape < 0 || $justificatif < 0) {
        return 14;
    }
    return 0;
}

/**
 * Controle l'etat des valeurs des frais saisis 
 * 
 * @param dateFrais
 * @param libelle
 * @param montant
 * @return Valeur correspondant à une erreur ou si il n'y a rien 
 */
function valideInfosFraisRetour($dateFrais, $libelle, $montant) {
    if ($dateFrais == '') {
        //return 'Le champ date ne doit pas être vide';
        return 5;
    } else {
        if (!estDatevalide($dateFrais)) {
            //return 'Date invalide';
            return 6;
        }
    }
    if ($libelle == '') {
        //return 'Le champ description ne peut pas être vide';
        return 8;
    }
    if ($montant == '') {
        //return 'Le champ montant ne peut pas être vide';
        return 9;
    } elseif (!is_numeric($montant)) {
        //return 'Le champ montant doit être numérique';
        return 10;
    }
    if ($montant < 0) {
        //return 'Le champ montant ne peut pas être vide';
        return 11;
    }
    return 0;
}

/**
 * Retourne le nom et prénom du visiteur en fonction de son id
 * 
 * @param type $ide
 * @return Nom et prenom du visiteur
 */
function getnomprenomavecid($ide) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "Select nom,prenom from visiteur where id='$ide' ";
    $res = $pdoSansParam->query($req);
    $lesLignes = $res->fetchAll();
    return $lesLignes;
}

/**
 * Met à jour le mois d'un frais hors-forfait
 * 
 * @param arrdate
 * @param id
 * @param laDate
 * @param idFiche
 */
function majmois($arrdate, $id, $laDate, $idFiche) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $requetePrepare = $pdoSansParam->prepare(
            'Update `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
        set  lignefraishorsforfait.mois = :mois
        WHERE :id = lignefraishorsforfait.idvisiteur
        AND lignefraishorsforfait.date = :date
        AND lignefraishorsforfait.id=:idFiche;'
    );
    $requetePrepare->bindParam(':mois', $arrdate, PDO::PARAM_STR);
    $requetePrepare->bindParam(':id', $id, PDO::PARAM_STR);
    $requetePrepare->bindParam(':date', $laDate, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idFiche', $idFiche, PDO::PARAM_STR);
    $requetePrepare->execute();
}

/**
 * Retourne le mois correspondant à la derniére fiche de frais cloturé d'un visiteur
 * 
 * @param Id du visiteur
 * @return Retourne un mois
 */
function maxAnneeVisiteur($id) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT distinct max(mois) as mois FROM fichefrais,visiteur where idvisiteur='$id' order by mois;";
    $res = $pdoSansParam->query($req);
    $lesLignes = $res->fetchAll();
    foreach ($lesLignes as $anCourant) {
        $an = $anCourant[mois];
    }
    return $an;
}

/**
 * Ajoute un pdf duppliqué en fonction du mois et du visiteur
 * 
 * @param Id du visiteur et mois de la fiche de frais
 */
function AddToDupli($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "INSERT INTO duplicata (idvisiteur,datepdf) VALUES (:idvisi, :mois)";
    $res = $pdoSansParam->prepare($req);
    $res->bindParam(':idvisi', $id, PDO::PARAM_STR);
    $res->bindParam(':mois', $mois, PDO::PARAM_STR);
    $res->execute();
}

/**
 * Détermine si le pdf est dupliqué ou non en fonction du mois et de l'id du visiteur
 * 
 * @param Id du visiteur et mois de la fiche de frai
 * @return Retourne vrais ou faux selon si le pdf est duppliqué
 */
function EstDupli($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "SELECT * FROM gsb_frais.duplicata WHERE idvisiteur = '$id' AND datepdf='$mois'; ";
    $res = $pdoSansParam->query($req);
    $lesLignes = $res->fetchAll();
    return $lesLignes; 
}