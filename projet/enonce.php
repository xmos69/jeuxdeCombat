<?php

/* 

1. Cahier des charges.

  Chaque visiteur pourra créer un personnage avec lequel il pourra frapper d'autres personnages. 
  Le personnage frappé recevra des dégâts.

  Un personnage est défini selon 2 caractéristiques :
    * Son nom (unique).
    * Ses dégâts.

  Les dégâts d'un personnage sont compris entre 0 et 100. 
  Au début, il a 0 de dégât. 
  Chaque coup qui lui sera porté lui fera prendre 5 points de dégâts. 

  Une fois arrivé à 100 points de dégâts, le personnage est mort (on le supprimera alors de la BDD).

2. Notions utilisées

  Points techniques que l'on va mettre en pratique :

    * Les attributs et méthodes ;
    * l'instanciation de la classe ;
    * les constantes de classe ;
    * et surtout, tout ce qui touche à la manipulation de données stockées.

3. Création de la table personnages

  Dans PHPMyAdmin, copier-coller le code suivant dans l'onglet SQL après avoir sélectionné une BDD :

*/

  CREATE TABLE IF NOT EXISTS TP_personnages (
    id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    nom varchar(50),
    degats tinyint(3) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    UNIQUE KEY nom (nom)
  );

/*

4. La classe personnage

Caractéristiques et fonctionnalités du personnage

*/

  class Personnage {

    // attributs
    private $_id, $_degats, $_nom;

    // Trois constantes de classe renvoyées par la méthode frapper()
    // si on se frappe soi-même
    const CEST_MOI = 1;
    // si on a tué le personnage en le frappant
    const PERSONNAGE_TUE = 2;
    // si on a bien frappé le personnage
    const PERSONNAGE_FRAPPE = 3;

    // méthodes à remplir
    public function frapper(Personnage $perso) {
      // renvoie la constante de classe CEST_MOI
    }

    public function recevoirDegats() {
      // renvoie les constantes de classe PERSONNAGE_TUE ou PERSONNAGE_FRAPPE
    }

/*

Les méthodes n'ont en général pas besoin d'être masquées à l'utilisateur : on les met en visibilité public

5. Accéder aux attributs pour lire et modifier leurs valeurs

On rajoute ce code dans notre classe :

*/

    // GETTERS pour lire les valeurs des attributs
    public function getDegats() { 

    }
    public function getId() { 

    }
    public function getNom() { 

    }

    // SETTERS pour modifier les valeurs des attributs
    public function setDegats($degats) {

    }

    public function setId($id) {

    }

    public function setNom($nom) {

    }

  }

/*

6. Donner des valeurs aux attributs

Au début, nous avons un objet Personnage dont les attributs sont vides. 
==> L'hydratation consiste à assigner des valeurs aux attributs : nous avons donc hydraté l'objet

*/

  class Personnage {
    // ...
    
    public function hydrate(array $donnees) {
      foreach ($donnees as $key => $value) {
        // ucfirst : met le premier caractère en majuscule
        $method = 'set'.ucfirst($key); 
        
        if (method_exists($this, $method)) {  
        // method_exists : vérifie si la méthode existe (ici pour l'objet $this)
          $this->$method($value);
        }
      }
    }
    
    // ...
  }

/*

7. Constructeur

Il ne manque plus qu'à implémenter le constructeur pour qu'on puisse directement hydrater notre objet lors de l'instanciation de la classe. 

Pour cela, ajoutez un paramètre : $donnees. 
Appelez ensuite directement la méthode hydrate().

*/

  class Personnage {
    // ...
    
    public function __construct(array $donnees) {
      $this->hydrate($donnees);
    }
    
    // ...
  }


// 8. Manager : remplissage BDD
// Le rôle de la classe PersonnagesManager est de stocker nos personnages dans une base de données

class PersonnagesManager {
  private $_db; // Instance de PDO
  
  public function __construct($db) {
    $this->setDb($db);
  }
  
  // remplir les méthodes :

  public function add(Personnage $perso) {
    // Préparation de la requête d'insertion.
    // Assignation des valeurs pour le nom du personnage.
    // Exécution de la requête.
    
    // Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
  }
  
  public function count() {
    // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
  }
  
  public function delete(Personnage $perso) {
    // Exécute une requête de type DELETE.
  }
  
  public function exists($info) {
    // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
      // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
    
    // Sinon c'est qu'on a passé un nom.
    // Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.
  }
  
  public function get($info) {
    // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
      // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
    
    // Sinon, on veut récupérer le personnage avec son nom.
    // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
  }
  
  public function getList($nom) {
    // Retourne la liste des personnages dont le nom n'est pas $nom.
    // Le résultat sera un tableau d'instances de Personnage.
  }
  
  public function update(Personnage $perso) {
    // Prépare une requête de type UPDATE.
    // Assignation des valeurs à la requête.
    // Exécution de la requête.
  }
  
  public function setDb(PDO $db) {
    $this->_db = $db;
  }
}


// 9. Formulaire 

/*

10. Autoload

On ranges toutes nos classes dans un répertoire "classes"
Par exemple, chien.class.php, chat.class.php, animal.class.php, rose.class.php, vegetal.class.php etc...

Le code ci-dessous est bien utile, car il signifie : ne va chercher l'include que si j'instancie la classe, ou une classe mère par héritage etc.

*/

  spl_autoload_register(function ($class) {
    require 'class/' . $class . '.class.php';
  });

  //$chien = new Chien('Médor');

/* 

La dernière ligne déclenchera l'include de animal.class.php puis celui de chien.class.php et seulement ces deux-là, pas plus pas moins, et dans le bon ordre !

Avec le même préambule, $rose = new Rose(); déclenchera l'include de vegetal.class.php, puis celui de rose.class.php, et seulement ces deux-là, pas plus pas moins et dans le bon ordre !

11. Méthode nomValide() : à rajouter dans la classe Personnage.

*/

  class Personnage {

    // ...
    
    public function nomValide() {
      return !empty($this -> _nom);
    }
    
    // ...

  }

/* 

12. Le formulaire

*/

  // On enregistre notre autoload.
  function chargerClasse($classname) {
    require $classname.'.php';
  }

  spl_autoload_register('chargerClasse');

  session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload

  if (isset($_GET['deconnexion']))
  {
    session_destroy();
    header('Location: .');
    exit();
  }

  // Connexion à la BDD
  $db = new PDO('mysql:host=localhost;dbname=poo', 'root', 'root');
  // On émet une alerte à chaque fois qu'une requête a échoué.
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 

  // Création d'un objet manager
  $manager = new PersonnagesManager($db);

  // Si la session perso existe, on restaure l'objet.
  if (isset($_SESSION['perso'])) 
  {
    $perso = $_SESSION['perso'];
  }

  /* 

    Afficher le nombre de personnages créés sur la page d'accueil.get list ou get

    2 choix : 
      * créer un nouveau personnage 
          (traiter les cas si le nom choisi est invalide ou que le nom est déjà pris) 
      * utiliser un personnage
          (traiter le cas si le personnage choisi n'existe pas)

    Puis, si on veut frapper un personnage
        (traiter le cas où le personnage que l'on veut frapper n'existe pas)
      On a trois cas : 
        - on se frappe soi-même
        - on frappe un autre personnage
        - on tue un autre personnage

    Si on utilise un personnage :
      * afficher son nom
      * afficher ses dégâts

  */





