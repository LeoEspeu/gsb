<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(
                PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = old_password( :unMdp )'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne les informations d'un comptable
     * 
     * @param type $login
     * @param type $mdp
     * @return type
     */
    public function getInfosComptable($login, $mdp) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT comptable.id AS id, comptable.nom AS nom,comptable.prenom AS prenom FROM comptable WHERE comptable.login = :unLogin AND comptable.mdp = old_password( :unMdp )'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT fraisforfait.id as idfrais, '
                . 'fraisforfait.libelle as libelle, '
                . 'lignefraisforfait.quantite as quantite '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait '
                . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour la voiture de la table ficheFrais
     * pour le mois et le visiteur concerné
     * 
     * @param type $idVoiture
     * @param type $idVisiteur
     * @param type $mois
     */
    public function majVoitureForfait($idVoiture, $idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE fichefrais '
                . 'SET fichefrais.idvoiture = :uneVoiture '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois '
        );
        $requetePrepare->bindParam(':uneVoiture', $idVoiture, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Returne la fiche de frais du visiteur pour obtenir l'id de 
     * sa voiture pour le mois et le visiteur concerné
     * 
     * @param type $idVisiteur
     * @param type $mois
     * @return type
     */
    public function ObtenirVoiture($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'select * from fichefrais '
                . 'inner join voiture on fichefrais.idvoiture=voiture.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchall();
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $requetePrepare = PdoGB::$monPdo->prepare(
                'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
                ':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
                . 'montantValide,dateModif,idEtat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idFraisForfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                    ':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
    $idVisiteur, $mois, $libelle, $date, $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT ficheFrais.idEtat as idEtat, '
                . 'ficheFrais.dateModif as dateModif,'
                . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
                . 'ficheFrais.montantValide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN Etat ON ficheFrais.idEtat = Etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ficheFrais '
                . 'SET idEtat = :unEtat, dateModif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * 
     * @param type $idVisiteur Id du visiteur
     * @param type $mois       Mois de la fiche de frais
     * @param type $montant    Nouveau montant validé
     */
    public function majMontantValideFicheFrais($idVisiteur, $mois, $montant) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ficheFrais '
                . 'SET montantvalide = :unMontant '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Détermine si le pdf est dupliqué ou non en fonction du mois et de l'id du visiteur
     * 
     * @param Id du visiteur et mois de la fiche de frai
     * @return Retourne vrais ou faux selon si le pdf est duppliqué
     */
    function EstDupli($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("SELECT * FROM gsb_frais.duplicata WHERE idvisiteur =:unIdVisiteur AND datepdf=:unMois ");
        $req->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $req->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $req->execute();
        $lesLignes = $req->fetchAll();
        return $lesLignes;
    }

    /**
     * Ajoute un pdf duppliqué en fonction du mois et du visiteur
     * 
     * @param Id du visiteur et mois de la fiche de frais
     */
    function AddToDupli($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("INSERT INTO duplicata(idvisiteur,datepdf) VALUES ( :idvisi, :mois)");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Retourne le mois correspondant à la derniére fiche de frais cloturé d'un visiteur
     * 
     * @param Id du visiteur
     * @return Retourne un mois
     */
    function maxAnneeVisiteur($id) {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct max(mois) as mois FROM fichefrais,visiteur where idvisiteur= :unIdVisiteur order by mois;");
        $req->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $req->execute();
        $lesLignes = $req->fetchAll();
        foreach ($lesLignes as $anCourant) {
            $an = $anCourant[mois];
        }
        return $an;
    }

    /**
     * Met à jour le mois d'un frais hors-forfait
     * @param arrdate * 

     * @param id
     * @param laDate
     * @param idFiche
     */
    function majmois($arrdate, $id, $laDate, $idFiche) {

        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la voiture utilisée en fonction du mois et du visiteur
     * 
     * @param Id du visiteur
     * @param Mois du frais hors
     * @param Libellé de la nouvelle voiture
     */
    function majvoiture($id, $lemois, $libvoiture) {
        $libvoiture = '%' . $libvoiture . '%';
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Retourne le nom et prénom du visiteur en fonction de son id
     * 
     * @param type $id
     * @return Nom et prenom du visiteur
     */
    function getnomprenomavecid($id) {
        $req = PdoGsb::$monPdo->prepare("Select nom,prenom from visiteur where id=:id ");
        $req->bindParam(':id', $id, PDO::PARAM_STR);
        $req->execute();
        $lesLignes = $req->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la quantité de justificatifs en fonction du mois et du visiteur
     * 
     * @param Nouvelle quantité de justificatifs
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function majnj($quant, $id, $lemois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la quantité de frais de repas en fonction du mois et du viditeur
     * 
     * @param Nouvelle quantité de frais de repas
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function majrep($quant, $id, $lemois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la quantité de frais de nuitée en fonction du mois et du visiteur
     * 
     * @param Nouvelle quantité de frais de nuitée
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function majnuit($quant, $id, $lemois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la quantité de frais kilométrique en fonction du mois et du visiteur
     * 
     * @param Nouvelle quantité de frais kilométrique
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function majkm($quant, $id, $lemois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la quantité des frais d'étape en fonction du mois et du visiteur
     * 
     * @param Nouvelle quantité des frais d'étape
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function majetp($quant, $id, $lemois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Passe une fiche de frais de l'état de cloturé à validé
     * 
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     */
    function validerUneFicheDeFais($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("update fichefrais set idetat='VA' where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
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
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la date du frais hors-forfait
     * 
     * @param Nouvelle date
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     * @param Id du frais hors-forfait
     */
    function majdate($arrdate, $id, $lemois, $idFiche) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour le libellé du frais hors-forfait
     * 
     * @param Nouveau libellé 
     * @param Id du visiteur
     * @param Mois du frais hors-forfait
     * @param Id du frais hors-forfait
     */
    function majlibelle($arrlib, $id, $lemois, $idFiche) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
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
     * Met à jour la date de modification de la fiche de frais à la date d'aujourd'hui
     * 
     * @param Id du visiteur et mois de la fiche de frais
     */
    function majdatedemodification($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("update fichefrais set datemodif=now() where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Passe une fiche de frais de l'état de validé à cloturé
     * 
     * @param Id du visiteur et mois de la fiche de frais
     */
    function fairedevalider($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("update fichefrais set idetat='CL' where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Fonction permettant de mettre un frais en remboursement en fonction du vsiteur et du mois
     * @param type $id
     * @param type $mois
     */
    function faireremboursement($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("update fichefrais set idetat='RB' where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Fonction permettant de mettre un frais en remboursement en fonction du vsiteur et du mois
     * @param type $id
     * @param type $mois
     */
    function fairePayement($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("update fichefrais set idetat='MP' where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Retourne une fiche de frais en fonction du mois et du visiteur pour savoir si elle est valide ou non
     * @param type $id
     * @param type $mois
     * @return type
     */
    function estFicheValide($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("select * from fichefrais where idvisiteur =:idvisi and mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $lesFichesValides = $req->fetchAll();
        return $lesFichesValides;
    }

    /**
     *  Retourne les frais hors-forfaits en fonction du mois et du visiteur
     * 
     * @param Id du visiteur et mois de la fiche de frais
     * @return Liste de frais hors-forfaits ou message d'erreur
     */
    function getElementForfait($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("Select distinct idfraisforfait,libelle,montant,visiteur.id,lignefraisforfait.quantite
               From lignefraisforfait
               INNER JOIN fraisforfait
               ON lignefraisforfait.idfraisforfait=fraisforfait.id
               INNER JOIN fichefrais
               ON fichefrais.idvisiteur=lignefraisforfait.idvisiteur
               INNER JOIN visiteur
               ON visiteur.id=fichefrais.idvisiteur
               WHERE visiteur.id=:idvisi
               AND lignefraisforfait.mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $tripleInnerJoin = $req->fetchAll();
        if ($req->rowCount() > 0) {
            return $tripleInnerJoin;
        } else {
            ajouterErreur('Aucun éléments hors forfaits pour cette utilisateur ou pour ce mois ci');
            return $tripleInnerJoin;
        }
    }

    /**
     * Retourne le nombre de justificatifs en fonction du mois et du visiteur
     * 
     * @param Id du visiteur et mois de la fiche de frais
     * @return type
     */
    function getNbJustificatif($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("Select distinct nbjustificatifs,visiteur.id
               From fichefrais
               INNER JOIN visiteur
               ON visiteur.id = fichefrais.idVisiteur
               INNER JOIN lignefraisforfait
               ON fichefrais.mois = lignefraisforfait.mois
               WHERE visiteur.id=:idvisi
               AND lignefraisforfait.mois=:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $nbJuste = $req->fetchAll();
        if ($req->rowCount() > 0) {
            return $nbJuste;
        } else {
            ajouterErreur('Aucun justificatif pour cette utilisateur');
        }
    }
    
    /**
     * Fonction qui les fiches de frais non refusé d'un visiteur en fonction du mois
     * 
     * @param type $id
     * @param type $mois
     * @return type
     */
    function getFicheDeFraisNonRefuséEnFonctionDuMois($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct lignefraishorsforfait.mois,lignefraishorsforfait.libelle,date,montant,lignefraishorsforfait.id
               FROM `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
               WHERE :idvisi = lignefraishorsforfait.idvisiteur
               AND lignefraishorsforfait.mois =:mois and libelle not like'%REFUSÉ%';");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $lesFichesFull = $req->fetchAll();
        return $lesFichesFull;
    }

    /**
     * Fonction qui les fiches de frais d'un visiteur en fonction du mois
     * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
     *
     * @return Array de visiteurs
     */
    function getFicheDeFraisEnFonctionDuMois($id, $mois) {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct lignefraishorsforfait.mois,lignefraishorsforfait.libelle,date,montant,lignefraishorsforfait.id
               FROM `gsb_frais`.`lignefraishorsforfait`,visiteur,fichefrais
               WHERE :idvisi = lignefraishorsforfait.idvisiteur
               AND lignefraishorsforfait.mois =:mois ;");
        $req->bindParam(':idvisi', $id, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();

        $lesFichesFull = $req->fetchAll();
        return $lesFichesFull;
    }

    /**
     * Fonction qui retourne la liste des visiteurs avec des fiches de Frais
     * Avec seulement leurs nom et leurs prenom;
     * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
     *
     * @return Array de visiteurs
     */
    function getLesVisiteursAvecFicheDeFrais() {
        $req = PdoGsb::$monPdo->prepare('SELECT distinct nom,prenom FROM `gsb_frais`.`visiteur`,`gsb_frais`.`fichefrais` WHERE id=idVisiteur ORDER BY nom,prenom asc;');
        $req->execute();
        $lesLignes = $req->fetchAll();
        return $lesLignes;
    }

    /**
     * Fonction qui retourne l'ID des visiteurs avec des fiches de Frais
     * Avec seulement leurs nom et leurs prenom;
     * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
     *
     * @return Array de visiteurs
     */
    function getIdVisiteurAPartirDuNomEtDuPrenom($np) {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct id FROM visiteur WHERE nom LIKE :nom ;");
        $req->bindParam(':nom', $np, PDO::PARAM_STR);
        $req->execute();
        $lesLignes = $req->fetch();
        return $lesLignes;
    }

    /**
     * Méthode permettant d'obtenir les mois des fiche de frais cloturé
     * 
     * @param Id du visiteur
     * @return Retourne une liste de mois 
     */
    function getMoisVisiteurCloture($id) {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct mois FROM fichefrais where idvisiteur='$id' and  idetat='CL' order by mois ;");
        $req->execute();
        $lesMois = $req->fetchAll();
        return $lesMois;
    }

    /**
     * Fonction qui les mois d'un visiteur grace a son id
     * @param PDO $pdo instance de la classe PDO utilisée pour se connecter
     *
     * @return Array de visiteurs
     */
    function getMoisVisiteur() {
        $req = PdoGsb::$monPdo->prepare("SELECT distinct mois FROM fichefrais,visiteur order by mois;");
        $req->execute();

        $lesMois = $req->fetchAll();
        return $lesMois;
    }

}
