-- Script de restauration de l'application "GSB Frais"
-- Administration de la base de données
CREATE DATABASE IF NOT EXISTS gsb_frais ;
GRANT SHOW DATABASES ON *.* TO userGsb@localhost IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON `gsb_frais`.* TO userGsb@localhost;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
USE gsb_frais ;

DROP TABLE IF EXISTS lignefraishorsforfait,lignefraisforfait,fichefrais,visiteur,etat,fraisforfait,comptable,voiture,duplicata;

-- Création de la structure de la base de données
CREATE TABLE IF NOT EXISTS fraisforfait (
  id char(3) NOT NULL,
  libelle char(20) DEFAULT NULL,
  montant decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS etat (
  id char(2) NOT NULL,
  libelle varchar(30) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS comptable (
  id char(4) NOT NULL,
  nom char(30) DEFAULT NULL,
  prenom char(30)  DEFAULT NULL, 
  login char(20) DEFAULT NULL,
  mdp char(20) DEFAULT NULL,
  adresse char(30) DEFAULT NULL,
  cp char(5) DEFAULT NULL,
  ville char(30) DEFAULT NULL,
  dateembauche date DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS visiteur (
  id char(4) NOT NULL,
  nom char(30) DEFAULT NULL,
  prenom char(30)  DEFAULT NULL, 
  login char(20) DEFAULT NULL,
  mdp char(20) DEFAULT NULL,
  adresse char(30) DEFAULT NULL,
  cp char(5) DEFAULT NULL,
  ville char(30) DEFAULT NULL,
  dateembauche date DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS voiture(
id char(4) not null,
libellevoiture char(50) not null,
coefficient decimal(5,2) not null,
PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fichefrais (
  idvisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  nbjustificatifs int(11) DEFAULT NULL,
  montantvalide decimal(10,2) DEFAULT NULL,
  datemodif date DEFAULT NULL,
  idetat char(2) DEFAULT 'CR',
  idvoiture char(4) default '3',
  PRIMARY KEY (idvisiteur,mois),
  FOREIGN KEY (idetat) REFERENCES etat(id),
  FOREIGN KEY (idvoiture) REFERENCES voiture(id),
  FOREIGN KEY (idvisiteur) REFERENCES visiteur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lignefraisforfait (
  idvisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  idfraisforfait char(3) NOT NULL,
  quantite int(11) DEFAULT NULL,
  PRIMARY KEY (idvisiteur,mois,idfraisforfait),
  FOREIGN KEY (idvisiteur, mois) REFERENCES fichefrais(idvisiteur, mois),
  FOREIGN KEY (idfraisforfait) REFERENCES fraisforfait(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS duplicata (
  `idduplicata` INT NOT NULL,
  `idvisiteur` char(4) NULL,
  `datepdf` char(6) NULL,
  PRIMARY KEY (`idduplicata`));
ALTER TABLE duplicata 
CHANGE COLUMN `idduplicata` `idduplicata` INT(11) NOT NULL AUTO_INCREMENT ;


CREATE TABLE IF NOT EXISTS lignefraishorsforfait (
  id int(11) NOT NULL auto_increment,
  idvisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  libelle varchar(100) DEFAULT NULL,
  date date DEFAULT NULL,
  montant decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (idvisiteur, mois) REFERENCES fichefrais(idvisiteur, mois)
) ENGINE=InnoDB;

-- Alimentation de la table voiture
INSERT INTO voiture(id,libellevoiture,coefficient) VALUES
('1' , 'Véhicule  4CV Diesel',-0.10),
('2' , 'Véhicule 5/6CV Diesel',-0.04),
('3' , 'Véhicule  4CV Essence',0),
('4' , 'Véhicule 5/6CV Essence',0.05);
-- Alimentation des données paramètres
INSERT INTO fraisforfait (id, libelle, montant) VALUES
('ETP', 'Forfait Etape', 110.00),
('KM', 'Frais Kilométrique', 0.62),
('NUI', 'Nuitée Hôtel', 80.00),
('REP', 'Repas Restaurant', 25.00);

INSERT INTO etat (id, libelle) VALUES
('RB', 'Remboursée'),
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('MP', 'Mise en payement'),
('VA', 'Validée et mise en paiement');

-- Récupération des utilisateurs
INSERT INTO visiteur (id, nom, prenom, login, mdp, adresse, cp, ville, dateembauche) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', old_password('jux7g'), '8 rue des Charmes', '46000', 'Cahors', '2005-12-21'),
('a17', 'Andre', 'David', 'dandre', old_password('oppg5'), '1 rue Petit', '46200', 'Lalbenque', '1998-11-23'),
('a55', 'Bedos', 'Christian', 'cbedos', old_password('gmhxd'), '1 rue Peranud', '46250', 'Montcuq', '1995-01-12'),
('a93', 'Tusseau', 'Louis', 'ltusseau', old_password('ktp3s'), '22 rue des Ternes', '46123', 'Gramat', '2000-05-01'),
('b13', 'Bentot', 'Pascal', 'pbentot', old_password('doyw1'), '11 allée des Cerises', '46512', 'Bessines', '1992-07-09'),
('b16', 'Bioret', 'Luc', 'lbioret', old_password('hrjfs'), '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11'),
('b19', 'Bunisset', 'Francis', 'fbunisset', old_password('4vbnd'), '10 rue des Perles', '93100', 'Montreuil', '1987-10-21'),
('b25', 'Bunisset', 'Denise', 'dbunisset', old_password('s1y1r'), '23 rue Manin', '75019', 'paris', '2010-12-05'),
('b28', 'Cacheux', 'Bernard', 'bcacheux', old_password('uf7r3'), '114 rue Blanche', '75017', 'Paris', '2009-11-12'),
('b34', 'Cadic', 'Eric', 'ecadic', old_password('6u8dc'), '123 avenue de la République', '75011', 'Paris', '2008-09-23'),
('b4', 'Charoze', 'Catherine', 'ccharoze', old_password('u817o'), '100 rue Petit', '75019', 'Paris', '2005-11-12'),
('b50', 'Clepkens', 'Christophe', 'cclepkens', old_password('bw1us'), '12 allée des Anges', '93230', 'Romainville', '2003-08-11'),
('b59', 'Cottin', 'Vincenne', 'vcottin', old_password('2hoh9'), '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18'),
('c14', 'Daburon', 'François', 'fdaburon', old_password('7oqpv'), '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11'),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', old_password('13 rue Barthes'), '94000', 'Créteil', '2010-12-14'),
('c54', 'Debelle', 'Michel', 'mdebelle', old_password('od5rt'), '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23'),
('d13', 'Debelle', 'Jeanne', 'jdebelle', old_password('nvwqq'), '134 allée des Joncs', '44000', 'Nantes', '2000-05-11'),
('d51', 'Debroise', 'Michel', 'mdebroise', old_password('sghkb'), '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17'),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', old_password('f1fob'), '14 Place d Arc', '45000', 'Orléans', '2005-11-12'),
('e24', 'Desnost', 'Pierre', 'pdesnost', old_password('4k2o5'), '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05'),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', old_password('44im8'), '18 rue de l église', '23120', 'GrandBourg', '2000-08-01'),
('e49', 'Duncombe', 'Claude', 'cduncombe', old_password('qf77j'), '19 rue de la tour', '23100', 'La souteraine', '1987-10-10'),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', old_password('y2qdu'), '25 place de la gare', '23200', 'Gueret', '1995-09-01'),
('e52', 'Eynde', 'Valérie', 'veynde', old_password('i7sn3'), '3 Grand Place', '13015', 'Marseille', '1999-11-01'),
('f21', 'Finck', 'Jacques', 'jfinck', old_password('mpb3t'), '10 avenue du Prado', '13002', 'Marseille', '2001-11-10'),
('f39', 'Frémont', 'Fernande', 'ffremont', old_password('xs5tq'), '4 route de la mer', '13012', 'Allauh', '1998-10-01'),
('f4', 'Gest', 'Alain', 'agest', old_password('dywvt'), '30 avenue de la mer', '13025', 'Berre', '1985-11-01');

-- Récupération des fiches de frais
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201609',2,2696.19,'2016-11-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201610',8,2251.02,'2016-12-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201611',7,2282.59,'2017-01-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201612',10,3361.19,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201701',5,3268.16,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201702',7,4183.68,'2017-04-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201703',2,2556.36,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201704',0,2528.85,'2017-06-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201705',4,3654.40,'2017-07-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201706',7,4703.15,'2017-08-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201707',11,3337.73,'2017-08-01','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a131','201708',1,0.00,'2017-08-05','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201609',8,4030.24,'2016-11-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201610',6,2769.96,'2016-12-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201611',1,2072.36,'2017-01-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201612',5,2842.91,'2017-02-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201701',11,4042.77,'2017-03-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201702',4,3032.12,'2017-04-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201703',9,2493.47,'2017-05-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201704',1,1806.74,'2017-06-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201705',11,2456.58,'2017-07-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201706',2,4656.66,'2017-08-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201707',9,5015.72,'2017-08-05','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a17','201708',6,0.00,'2017-08-06','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201609',6,4345.88,'2016-11-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201610',3,1755.76,'2016-12-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201611',9,4446.57,'2017-01-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201612',4,2176.53,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201701',4,4444.02,'2017-03-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201702',0,2547.32,'2017-04-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201703',0,4325.73,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201704',11,3724.11,'2017-06-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201705',8,4473.59,'2017-07-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201706',9,2090.37,'2017-08-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201707',12,1805.68,'2017-08-03','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a55','201708',6,0.00,'2017-08-02','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201609',8,3601.26,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201610',11,2847.94,'2016-12-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201611',5,3297.63,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201612',6,2424.93,'2017-02-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201701',12,2653.39,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201702',8,2666.09,'2017-04-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201703',12,4998.56,'2017-05-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201704',4,3468.02,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201705',1,4008.41,'2017-07-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201706',12,2997.18,'2017-08-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201707',3,2365.44,'2017-08-04','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('a93','201708',1,0.00,'2017-08-06','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201609',1,2625.55,'2016-11-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201610',4,1888.13,'2016-12-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201611',10,3733.30,'2017-01-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201612',2,3182.46,'2017-02-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201701',10,2491.70,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201702',12,5072.71,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201703',4,2004.47,'2017-05-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201704',10,2225.76,'2017-06-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201705',2,5171.74,'2017-07-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201706',12,3262.12,'2017-08-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201707',9,1604.18,'2017-08-08','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b13','201708',0,0.00,'2017-08-06','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201609',0,2235.79,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201610',10,2997.66,'2016-12-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201611',11,3619.43,'2017-01-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201612',0,3391.38,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201701',6,4097.99,'2017-03-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201702',1,3216.62,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201703',8,3862.69,'2017-05-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201704',7,3146.02,'2017-06-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201705',2,2797.72,'2017-07-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201706',12,1836.33,'2017-08-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201707',7,4331.88,'2017-08-08','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b16','201708',9,0.00,'2017-08-01','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201609',7,4760.43,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201610',1,2806.70,'2016-12-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201611',9,3283.39,'2017-01-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201612',11,2616.00,'2017-02-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201701',9,1912.53,'2017-03-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201702',9,3234.86,'2017-04-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201703',2,4487.03,'2017-05-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201704',11,3217.78,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201705',0,3402.89,'2017-07-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201706',6,3520.60,'2017-08-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201707',8,4983.24,'2017-08-06','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b19','201708',3,0.00,'2017-08-07','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201609',0,2425.18,'2016-11-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201610',5,2605.71,'2016-12-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201611',9,2599.82,'2017-01-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201612',7,2898.75,'2017-02-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201701',5,3107.93,'2017-03-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201702',7,4137.45,'2017-04-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201703',6,2924.89,'2017-05-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201704',9,4790.38,'2017-06-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201705',3,2142.36,'2017-07-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201706',10,4162.08,'2017-08-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201707',7,2913.15,'2017-08-03','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b25','201708',9,0.00,'2017-08-03','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201609',10,2576.66,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201610',1,2478.52,'2016-12-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201611',11,3909.84,'2017-01-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201612',6,3054.20,'2017-02-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201701',7,2613.95,'2017-03-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201702',4,4679.09,'2017-04-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201703',5,3775.27,'2017-05-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201704',10,3646.79,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201705',6,2525.58,'2017-07-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201706',9,3213.24,'2017-08-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201707',9,2240.24,'2017-08-04','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b28','201708',9,0.00,'2017-08-04','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201609',8,3162.90,'2016-11-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201610',0,3313.27,'2016-12-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201611',6,4114.51,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201612',1,2818.38,'2017-02-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201701',9,3040.94,'2017-03-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201702',8,3315.66,'2017-04-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201703',1,3848.42,'2017-05-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201704',0,4140.10,'2017-06-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201705',1,1416.27,'2017-07-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201706',3,1989.51,'2017-08-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201707',10,4115.22,'2017-08-02','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b34','201708',7,0.00,'2017-08-01','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201609',7,2318.52,'2016-11-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201610',3,2184.45,'2016-12-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201611',3,2929.59,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201612',12,3592.79,'2017-02-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201701',4,1521.73,'2017-03-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201702',10,3067.99,'2017-04-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201703',12,3036.35,'2017-05-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201704',5,3951.16,'2017-06-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201705',0,1667.14,'2017-07-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201706',1,3967.90,'2017-08-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201707',5,2163.92,'2017-08-02','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b4','201708',12,0.00,'2017-08-08','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201609',5,2692.88,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201610',10,3229.13,'2016-12-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201611',8,1625.76,'2017-01-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201612',1,3547.86,'2017-02-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201701',3,3698.67,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201702',11,4046.34,'2017-04-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201703',2,1554.35,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201704',8,3855.04,'2017-06-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201705',11,4175.36,'2017-07-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201706',9,2564.22,'2017-08-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201707',2,2382.96,'2017-08-03','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b50','201708',12,0.00,'2017-08-08','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201609',2,2856.19,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201610',10,2443.39,'2016-12-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201611',2,2948.91,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201612',2,1988.21,'2017-02-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201701',0,1398.87,'2017-03-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201702',10,2432.98,'2017-04-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201703',4,2374.62,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201704',10,2709.97,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201705',2,2972.08,'2017-07-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201706',10,2220.16,'2017-08-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201707',1,3671.66,'2017-08-03','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('b59','201708',3,0.00,'2017-08-07','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201609',9,2272.21,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201610',2,3716.29,'2016-12-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201611',4,3186.53,'2017-01-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201612',3,2062.95,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201701',10,3666.31,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201702',5,4259.52,'2017-04-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201703',12,2679.84,'2017-05-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201704',3,3443.71,'2017-06-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201705',7,2580.14,'2017-07-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201706',1,1603.02,'2017-08-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201707',1,1968.22,'2017-08-02','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c14','201708',11,0.00,'2017-08-04','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201609',6,1813.25,'2016-11-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201610',11,3230.02,'2016-12-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201611',1,4262.27,'2017-01-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201612',5,4195.05,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201701',8,5025.96,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201702',9,2365.80,'2017-04-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201703',0,3410.37,'2017-05-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201704',0,2158.20,'2017-06-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201705',4,2547.17,'2017-07-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201706',3,5059.93,'2017-08-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201707',2,3390.02,'2017-08-05','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c3','201708',4,0.00,'2017-08-02','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201609',8,2975.70,'2016-11-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201610',2,2647.10,'2016-12-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201611',10,2275.61,'2017-01-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201612',4,2654.42,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201701',10,3562.16,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201702',1,1873.64,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201703',0,4197.22,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201704',1,2969.79,'2017-06-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201705',7,4826.71,'2017-07-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201706',2,2285.16,'2017-08-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201707',9,3153.68,'2017-08-07','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('c54','201708',9,0.00,'2017-08-02','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201609',3,1337.18,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201610',0,4248.97,'2016-12-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201611',1,2518.72,'2017-01-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201612',8,4033.12,'2017-02-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201701',1,4367.32,'2017-03-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201702',2,2362.58,'2017-04-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201703',7,3261.40,'2017-05-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201704',10,2823.91,'2017-06-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201705',6,2367.88,'2017-07-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201706',10,4261.96,'2017-08-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201707',8,2746.53,'2017-08-06','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d13','201708',11,0.00,'2017-08-08','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201609',11,3725.81,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201610',2,3239.85,'2016-12-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201611',12,3678.25,'2017-01-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201612',3,5055.46,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201701',10,2540.14,'2017-03-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201702',8,3186.29,'2017-04-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201703',8,3507.85,'2017-05-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201704',9,2865.82,'2017-06-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201705',12,3211.84,'2017-07-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201706',8,2321.59,'2017-08-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201707',3,3479.94,'2017-08-05','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('d51','201708',11,0.00,'2017-08-06','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201609',12,3049.90,'2016-11-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201610',10,2381.77,'2016-12-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201611',8,3639.18,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201612',8,3110.22,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201701',1,3365.63,'2017-03-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201702',9,3421.02,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201703',7,2339.55,'2017-05-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201704',4,1980.15,'2017-06-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201705',5,2912.32,'2017-07-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201706',12,2770.69,'2017-08-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201707',4,2785.98,'2017-08-07','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e22','201708',3,0.00,'2017-08-05','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201609',12,5122.12,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201610',3,4706.08,'2016-12-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201611',8,3171.28,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201612',3,2444.08,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201701',1,1384.40,'2017-03-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201702',11,2435.22,'2017-04-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201703',5,2681.60,'2017-05-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201704',0,2988.41,'2017-06-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201705',4,2521.45,'2017-07-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201706',12,1822.30,'2017-08-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201707',9,2003.89,'2017-08-04','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e24','201708',1,0.00,'2017-08-08','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201609',9,3572.80,'2016-11-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201610',4,4341.59,'2016-12-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201611',7,1410.90,'2017-01-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201612',4,3251.17,'2017-02-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201701',8,3279.15,'2017-03-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201702',10,4788.24,'2017-04-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201703',7,2867.49,'2017-05-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201704',2,5188.31,'2017-06-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201705',1,4575.18,'2017-07-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201706',4,1734.05,'2017-08-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201707',5,4459.35,'2017-08-01','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e39','201708',2,0.00,'2017-08-02','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201609',4,2304.96,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201610',7,1934.52,'2016-12-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201611',5,3604.77,'2017-01-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201612',10,2411.24,'2017-02-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201701',12,2522.77,'2017-03-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201702',8,3378.28,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201703',8,3608.33,'2017-05-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201704',2,1709.90,'2017-06-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201705',1,2677.07,'2017-07-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201706',3,3570.35,'2017-08-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201707',4,4378.85,'2017-08-04','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e49','201708',10,0.00,'2017-08-07','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201609',8,2701.06,'2016-11-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201610',7,3485.38,'2016-12-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201611',2,2540.49,'2017-01-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201612',6,2636.74,'2017-02-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201701',11,2781.16,'2017-03-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201702',1,3534.52,'2017-04-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201703',12,4288.79,'2017-05-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201704',8,3335.40,'2017-06-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201705',0,2514.51,'2017-07-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201706',2,3271.82,'2017-08-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201707',6,4949.68,'2017-08-07','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e5','201708',12,0.00,'2017-08-04','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201609',6,3509.93,'2016-11-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201610',2,2398.99,'2016-12-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201611',5,1625.49,'2017-01-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201612',1,3631.41,'2017-02-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201701',7,3636.41,'2017-03-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201702',12,4171.30,'2017-04-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201703',5,4653.85,'2017-05-03','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201704',1,2528.56,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201705',0,2258.16,'2017-07-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201706',9,3507.02,'2017-08-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201707',4,3011.65,'2017-08-05','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('e52','201708',5,0.00,'2017-08-08','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201609',11,2538.89,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201610',3,1862.79,'2016-12-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201611',5,3866.50,'2017-01-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201612',1,3915.54,'2017-02-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201701',7,3503.15,'2017-03-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201702',3,3000.30,'2017-04-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201703',2,4350.73,'2017-05-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201704',8,2866.23,'2017-06-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201705',7,3040.19,'2017-07-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201706',4,2445.80,'2017-08-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201707',8,3264.63,'2017-08-03','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f21','201708',10,0.00,'2017-08-06','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201609',10,3186.86,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201610',8,4128.89,'2016-12-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201611',2,2128.17,'2017-01-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201612',9,3195.47,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201701',8,4546.08,'2017-03-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201702',6,1987.26,'2017-04-06','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201703',3,2978.27,'2017-05-04','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201704',8,3859.83,'2017-06-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201705',8,3390.60,'2017-07-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201706',1,3382.61,'2017-08-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201707',7,3687.16,'2017-08-04','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f39','201708',0,0.00,'2017-08-05','CR');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201609',8,3269.70,'2016-11-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201610',10,3438.36,'2016-12-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201611',11,3813.93,'2017-01-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201612',12,3588.48,'2017-02-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201701',0,2216.36,'2017-03-08','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201702',2,1719.82,'2017-04-05','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201703',5,2718.49,'2017-05-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201704',10,2596.76,'2017-06-01','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201705',2,3750.17,'2017-07-07','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201706',2,2982.80,'2017-08-02','RB');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201707',1,2732.56,'2017-08-05','VA');
INSERT INTO `fichefrais` (`idvisiteur`,`mois`,`nbjustificatifs`,`montantvalide`,`datemodif`,`idetat`) VALUES ('f4','201708',10,0.00,'2017-08-05','CR');

-- Récupération des comptables
INSERT INTO comptable(id, nom, prenom, login, mdp, adresse, cp, ville, dateembauche) VALUES
('m13', 'espeu', 'léo', 'eleo', old_password('leo96'), '8 rue des Charmes', '46000', 'Cahors', '2005-12-21'),
('m54', 'benbahri', 'mehdi', 'bmehdi', old_password('12345'), '8 rue de la colline', '46000', 'Cahors', '2003-10-16'),
('m80', 'Gil', 'José', 'jgil', old_password('12345'), ' Avenue Winston Churchill', '83000', 'Toulon', '2003-10-16'),
('m81', 'Roche', 'Benoit', 'broche', old_password('12345'), ' Avenue Winston Churchill', '83000', 'Toulon', '2003-10-16'),
('m55', 'Robert', 'André', 'randre', old_password('ran55'), '9 rue de jonquille', '83000', 'Toulon', '2001-11-13');
SET SQL_SAFE_UPDATES = 0;
UPDATE fichefrais SET idetat='CL';
SET SQL_SAFE_UPDATES = 1;