CREATE TABLE utilisateur(
   id_utilisateur INT AUTO_INCREMENT,
   email VARCHAR(50)  NOT NULL,
   password VARCHAR(100)  NOT NULL,
   nom VARCHAR(50)  NOT NULL,
   prenom VARCHAR(50) ,
   role ENUM('admin', 'utilisateur', 'visiteur') NOT NULL,
   date_inscription DATE NOT NULL,
   avatar VARCHAR(255) ,
   PRIMARY KEY(id_utilisateur),
   UNIQUE(email)
);

CREATE TABLE annonce(
   id_annonce INT AUTO_INCREMENT,
   titre VARCHAR(100)  NOT NULL,
   description VARCHAR(500)  NOT NULL,
   statut VARCHAR(30)  NOT NULL,
   date_creation DATE NOT NULL,
   image1 VARCHAR(255)  NOT NULL,
   image2 VARCHAR(255) ,
   image3 VARCHAR(255) ,
   marque VARCHAR(50)  NOT NULL,
   modele VARCHAR(50) ,
   kilometrage INT NOT NULL,
   prix DECIMAL(15,2)   NOT NULL,
   annee INT,
   carburant VARCHAR(50) ,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_annonce),
   FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE message (
   id_message INT AUTO_INCREMENT,
   contenu TEXT NOT NULL,
   date_envoi DATETIME NOT NULL,
   id_annonce INT NOT NULL,
   id_expedie INT NOT NULL,
   id_recoit INT NOT NULL,
   PRIMARY KEY(id_message),
   FOREIGN KEY(id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE,
   FOREIGN KEY(id_expedie) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
   FOREIGN KEY(id_recoit) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE favoris(
   id_utilisateur INT,
   id_annonce INT,
   PRIMARY KEY(id_utilisateur, id_annonce),
   FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
   FOREIGN KEY(id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);
