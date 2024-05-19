<?php
require_once 'connexion/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);

    if (empty($nom) || empty($prenom) || empty($adresse) || empty($telephone)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        try {
            $stmt = $connecte->prepare("
                INSERT INTO client (nom, prenom, adresse, telephone) 
                VALUES (:nom, :prenom, :adresse, :telephone)
            ");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->execute();

            $id_client = $connecte->lastInsertId();

            $stmt = $connecte->prepare("
                INSERT INTO comptebancaire (numero_compte, solde, id_client) 
                VALUES (:numero_compte, 0, :id_client)
            ");
            $numero_compte = uniqid('CB');
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->bindParam(':id_client', $id_client);
            $stmt->execute();

            $message = "Le client a été ajouté avec succès. Numéro de compte attribué : $numero_compte";
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Client</title>
    <link rel="stylesheet" href="ajouter_compte.css">
</head>
<body>
    
    <form method="POST" action="ajouter_client.php"><h1>Ajouter un Client</h1>
        <label for="nom">Nom :</label><br>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="prenom">Prénom :</label><br>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="adresse">Adresse :</label><br>
        <input type="text" id="adresse" name="adresse" required><br><br>

        <label for="telephone">Téléphone :</label><br>
        <input type="text" id="telephone" name="telephone" required><br><br>

        <button type="submit">Ajouter</button>
    </form>

    <?php if ($message) : ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>