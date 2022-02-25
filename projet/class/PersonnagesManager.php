<?php


class PersonnagesManager
{
    private $_db; // Instance de PDO

    public function __construct($db)
    {
        $this->setDb($db);
    }

    // remplir les méthodes :

    public function add(Personnage $perso)
    {
        // Préparation de la requête d'insertion.
        // Assignation des valeurs pour le nom du personnage.
        // Exécution de la requête.
        $requete = $this->_db->prepare('INSERT INTO TP_personnages (nom) values(?)');
        $requete->execute([$perso->getNom()]);

        // Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0
        ]);
    }

    public function count()
    {
        // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
        // $requete= $this->_bd ->query('SELECT COUNT(*) FROM TP_personnages');
        // return $requete->fetchColumn();
        return $this->_db->query('SELECT COUNT(*) FROM TP_personnages')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        // tu veux suprimer un personnage qui a un certain id
        // Supprimer des données

        // Exécute une requête de type DELETE.
        $requete = $this->_db->prepare('DELETE FROM TP_personnages WHERE id=? ');
        $requete->execute([$perso->getId()]);
    }

    public function exists($info)
    {
        // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
        // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
        if (is_int($info)) {
            $requete = $this->_db->prepare('SELECT count(*) FROM TP_personnages WHERE id=?');
            $requete->execute([$info]);
            return (bool) $requete->fetchColumn();
        } else {

            $requete = $this->_db->prepare('SELECT count(*) FROM TP_personnages WHERE nom=?');
            $requete->execute([$info]);
            return (bool) $requete->fetchColumn();
        }
        // Sinon c'est qu'on a passé un nom.
        // Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.
    }

    public function get($info)
    {
        // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
        // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
        if (is_int($info)) {
            $requete = $this->_db->prepare('SELECT * FROM TP_personnages WHERE id=?');
            $requete->execute([$info]);
            $donnee = $requete->fetch(PDO::FETCH_ASSOC); // permet de reuperer les donnes d une requete sous forme de tableaux associatifs
            return new Personnage($donnee);
        } else {

            $requete = $this->_db->prepare('SELECT * FROM TP_personnages WHERE nom=?');
            $requete->execute([$info]);
            $donnee = $requete->fetch(PDO::FETCH_ASSOC);
            return new Personnage($donnee);
        }
        // Sinon, on veut récupérer le personnage avec son nom.
        // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
    }

    public function getList($nom)
    {

        $persos = [];
        // Retourne la liste des personnages dont le nom n'est pas $nom.
        // Le résultat sera un tableau d'instances de Personnage.
        $requete = $this->_db->prepare('SELECT * FROM TP_personnages WHERE nom != ? ');
        $requete->execute([$nom]);
        while($donnees=$requete->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Personnage($donnees);


        }
       return $persos;
            
    }

    public function update(Personnage $perso)
    {
        // Prépare une requête de type UPDATE.
        // Assignation des valeurs à la requête.
        // Exécution de la requête.
        $requete = $this->_db->prepare('UPDATE TP_personnages SET degats=? WHERE id=?');
        $requete->execute([$perso->getDegats(), $perso->getId()]);
    }

    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}
