<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=ppe;charset=utf8', 'root', ''); //connection à la bdd
if(isset($_POST['bouton'])) //Si l'utilisateur valide
{
$nom_Entreprise = htmlspecialchars(trim($_POST['nom_Entreprise']));
$nom_Responsable = htmlspecialchars(trim($_POST['nom_Responsable']));
$adresse_Entreprise = htmlspecialchars(trim($_POST['adresse_Entreprise']));
$login = htmlspecialchars(trim($_POST['login']));
$mdp = htmlspecialchars(trim($_POST['mdp']));
$mdp2 = htmlspecialchars(trim($_POST['mdp2']));
  if($nom_Entreprise&&$nom_Responsable&&$adresse_Entreprise&&$login&&$mdp&&$mdp2) //Si touts les champs sont entrés
  {
    if($mdp==$mdp2) // Si les 2 mots de passes entrés se correspondent
    {
      if(strlen($mdp)>3) // Si le mot de passe est supérieur à 3
      {

        $reqpseudo = $bdd -> prepare("SELECT * FROM connexion WHERE login = ?");
        $reqpseudo -> execute(array($login));
        $pseudoexist = $reqpseudo -> rowCount();
        if($pseudoexist == 0)
        {

              $req = $bdd->prepare('INSERT INTO connexion(login, mdp, mdp2, id_droit,accepte) VALUES(:login, :mdp, :mdp2, :id_droit, :accepte)');

              $req->execute(array(
              'mdp' => $mdp,
              'login' => $login,
              'mdp2' => $mdp2,
              'id_droit' => 2,
              'accepte' => 1
               ));


              $req2 = $bdd->prepare('INSERT INTO producteurs(nom_Responsable, nom_Entreprise, adresse_Entreprise, id_connexion, Interne) VALUES(:nom_Responsable, :nom_Entreprise, :adresse_Entreprise, LAST_INSERT_ID(), :Interne)');

              $req2->execute(array(
              'nom_Entreprise' => $nom_Entreprise,
              'nom_Responsable' => $nom_Responsable,
              'adresse_Entreprise' => $adresse_Entreprise,
              'Interne' => "Interne"
               ));


        } else $messageErreur = '<p class = "phrase"> Le nom d\'utilisateur est déjà utilisé</p>';

      }else $messageErreur = '<p class = "phrase"> Le mot de passe est trop petit </p>'; 

    }else $messageErreur = '<p class = "phrase"> Les mots de passes ne sont pas identiques </p>';

  }else $messageErreur ='<p class = "phrase"> Veuillez saisir tous les champs </p>';
}

?>

<html class="inscription">
    <head>
        <!-- En-tête de la page -->
        <meta charset="utf-8" />
        <link rel="stylesheet" href="index2.css" />
        <link href="/www/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <title>Projet PPE</title>
    </head>

    <body class="sierra">

    <?php
    include('inc/menuAccueil.php');
    ?>

    <div class="formPosition">
    <form action="#" method="POST" class="form-horizontal">
        <center> <h1 style="color:darkgreen">Ajouter un producteur interne</h1></center>
        <br>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="nom_Entreprise" id="text" placeholder="Entrez le Nom de la société">
                </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="nom_Responsable" id="text" placeholder="Entrez le Nom du Responsable">
                </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="adresse_Entreprise" id="text" placeholder="Entrez l'adresse de la société">
                </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="login" id="text" placeholder="Entrez votre login">
                </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4"> 
                    <input type="password" class="form-control" name="mdp" id="pwd" placeholder="Entrez votre mot de passe">
                </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4"></label>
                <div class="col-sm-4"> 
                    <input type="password" class="form-control" name="mdp2" id="pwd" placeholder="Confirmez votre mot de passe">
                </div>
        </div>
        <div class="form-group"> 
            <div class="col-sm-offset-4 col-sm-4">
                <button type="submit" name="bouton" class="btn btn-default"><b>Valider</b></button>

                <br><br>    
                
                <?php 
                    if(isset($messageErreur)){
                        echo $messageErreur;
                    }
                ?>

            </div>
        </div>
    </form>
    <center> <h1 style="color:darkgreen">Demande de producteurs externes</h1></center>
      <?php
            $req = $bdd->query('SELECT * FROM producteurs, connexion WHERE producteurs.id_connexion = connexion.id_connexion AND connexion.accepte = 0'); 

        ?>
        <table class="table table-hover">
                <tr> 
                    <th>Login</th>
                    <th>Nom du responsable</th>
                    <th>Nom de l'entreprise </th>
                    <th>Adresse de l'entreprise </th>
                    <th></th>

                </tr>
            <?php
            while ($donnees = $req -> fetch())
            { 
            ?>
                <tr>
                    <td><?php echo $donnees['login'] ?></td>
                    <td><?php echo $donnees['nom_Responsable'] ?></td>
                    <td><?php echo $donnees['nom_Entreprise'] ?></td>
                    <td><?php echo $donnees['adresse_Entreprise'] ?></td>
                    <td><a href="prodExt/accepteProdExt.php?id=<?php echo $donnees['id_connexion'];?>">Accepter </a>|
                    <a href="prodExt/refuserProdExt.php?id=<?php echo $donnees['id_connexion'];?>">Refuser</a> </td>
                </tr>
            <?php
            }
            ?>
        </table>

</html>