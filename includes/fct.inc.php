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
    $req = "SELECT distinct mois FROM fichefrais,visiteur WHERE idetat <> 'VA';";
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

function fairedevalider($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='CL' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

function majlibelle ($arrlib,$id,$lemois,$idFiche) {
    
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

function majdate ($arrdate,$id,$lemois,$idFiche) {
    
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

function majmont ($arrmont,$id,$lemois,$idFiche) {
    
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

function validerUneFicheDeFais($id, $mois) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "update fichefrais set idetat='VA' where idvisiteur = '$id' and mois='$mois';";
    $res = $pdoSansParam->query($req);
    $res->execute();
}

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

function valideInfosFraisRetour($dateFrais, $libelle, $montant) {
    if ($dateFrais == '') {
        //return 'Le champ date ne doit pas être vide';
        return 5;
        
    } else {
        if (!estDatevalide($dateFrais)) {
            //return 'Date invalide';
            return 6;
        } else {
            if (estDateDepassee($dateFrais)) {
                //return "date d'enregistrement du frais dépassé, plus de 1 an";
                return 7;
                
            }
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
    return 0;
}

function getnomprenomavecid($ide) {
    $pdoSansParam = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
    $pdoSansParam->query('SET CHARACTER SET utf8');
    $req = "Select nom,prenom from visiteur where id='$ide' ";
    $res = $pdoSansParam->query($req);
    $lesLignes = $res->fetchAll();
    return $lesLignes;
}