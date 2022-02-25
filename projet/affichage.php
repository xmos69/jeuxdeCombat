<?php


// On enregistre notre autoload.
function chargerClasse($classname)
{
    require "class/" . $classname . '.php';
}

spl_autoload_register('chargerClasse');

session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: affichage.php');
    exit();
}

// Connexion à la BDD
try {
    $db = new PDO('mysql:host=localhost;dbname=jeuxCombat;charset=utf8', 'root'); // Tentative de connexion.

} catch (PDOException $e) // On attrape les exceptions PDOException.
{
    echo 'La connexion a échoué.<br />';
    echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); // On affiche le n° de l'erreur ainsi que le message.
}
// Création d'un objet manager
$manager = new PersonnagesManager($db);

// Si la session perso existe, on restaure l'objet.
if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}

if (isset($_POST['creer']) && isset($_POST['nom'])) { // on a voulu créer un perso
    $perso = new Personnage(['nom' => $_POST['nom']]);

    if (!$perso->nomValide()) {
        // le personnage est invalide
        $message = "le personnage choisi est invalide!!";
        unset($perso);
    } else if ($manager->exists($perso->getNom())) {
        $message = "Le personnage existe déja!!";  // si le nom du perso existe deja
        unset($perso);
    } else {
        // on peut creer un nouveau perso! en bdd
        $manager->add($perso);
        $message = "Votre personnage à bien été créer";
    }
}
///on a voulu utiliser un perso
elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($manager->exists($_POST['nom'])) {
        $perso = $manager->get($_POST['nom']);
    } else {
        $message = "Ce personnage n'existe pas!!";
    }
} elseif (isset($_GET['frapper'])) { //est ce qu'on a frapper un perso?

    if (!isset($perso)) {
        echo " etes vous sure d avoir utiliser un personnage";
    } else {
        if (!$manager->exists(intval($_GET['frapper']))) {
            $message = "Ce personnage n'existe pas!!";
        } else {
            $persoAttaquer = $manager->get(intval($_GET['frapper']));
            $retourFrapper = $perso->frapper($persoAttaquer);
            // print_r($persoAttaquer);
            // print_r($retourFrapper);
            //echo "<script>console.log ($retourFrapper)</script>" ;
            switch ($retourFrapper) {
                case Personnage::CEST_MOI:
                    $message = "Arrete de vouloir te frapper!!!";
                    break;

                case Personnage::PERSONNAGE_FRAPPE:
                    $message = "le personnage: " . $persoAttaquer->getNom() . " à reçu 5 points de dégats";
                    $manager->update($perso);
                    $manager->update($persoAttaquer);

                    break;
                case Personnage::PERSONNAGE_TUE:
                    $message = "le personnage: " . $persoAttaquer->getNom() . " à été tué";
                    $manager->update($perso);
                    $manager->delete($persoAttaquer);
                    break;
                    // default:
                    //     $message="erreur programme";
            }
        }
    }
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
body{
    background-image: url(img/StreetFighter.png);
    background-color: greenyellow;
}
</style>

<body>
    <div>Nombre de personnages disponible: <?php echo $manager->count() ?></div>
    <?php
    if (isset($message)) {
        echo "<div>" . $message . "</div>";
    }
    if (isset($perso)) { //utilisation d'un perso 
    ?>
        <a href='?deconnexion=1'>deconnexion</a>"
        <div>
            Information personnages:<br />
            nom:<?php echo $perso->getNom(); ?> <br />
            degats: <?php echo $perso->getDegats(); ?><br />

        </div>
        <div>
            <?php
            $persos = $manager->getList($perso->getNom());
            if (empty($persos)) {
                echo "pas de personnages à frapper";
            } else {
                foreach ($persos as $unPersonnage) {
                    echo '<a href="?frapper=' . $unPersonnage->getId() . '">' . $unPersonnage->getNom() . "</a>";
                    echo "<p>les degats du personnage sont:" . $unPersonnage->getDegats() . "</p>";
                }
            }

            ?>
        </div>

    <?php
    } else { //afficher le formulaire pour creer ou utiliser
    ?>

        <form action="" method="POST">
            <label for="nom">Nom de votre personnage:</label>
            <input type="text" name="nom" id="nom">
            <label for="creer"></label>
            <input type="submit" name="creer" value="Crée">
            <input type="submit" name="utiliser" value="Utiliser personnage">
        </form>
    <?php
    }
    ?>

</body>

</html>
<?php
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}


?>