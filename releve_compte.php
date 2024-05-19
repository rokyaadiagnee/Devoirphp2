<?php
require_once 'connexion/config.php';

$message = '';
$comptes = [];

try {
    $stmt = $connecte->query("SELECT id_compte, numero_compte FROM comptebancaire");
    $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur : " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_compte = $_POST['id_compte'] ?? null;
    try {
        $stmt = $connecte->prepare("
            SELECT comptebancaire.numero_compte, comptebancaire.solde, 
                   client.nom AS nom_client, client.prenom AS prenom_client
            FROM comptebancaire
            INNER JOIN client ON comptebancaire.id_client = client.id_client
            WHERE comptebancaire.id_compte = :id_compte
        ");
        $stmt->bindParam(':id_compte', $id_compte);
        $stmt->execute();
        $compte_details = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $connecte->prepare("
            SELECT type_operation, montant, date_operation
            FROM operationbancaire
            WHERE id_compte = :id_compte
        ");
        $stmt->bindParam(':id_compte', $id_compte);
        $stmt->execute();
        $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Compte</title>
    <link rel="stylesheet" href="releve_compte.css">
</head>
<body>
    <h1>Relevé de Compte</h1>
    <form method="POST" action="releve_compte.php">
        <label for="id_compte">Sélectionner un Compte :</label><br>
        <select name="id_compte" id="id_compte">
            <?php foreach ($comptes as $compte) : ?>
                <option value="<?= $compte['id_compte'] ?>"><?= $compte['numero_compte'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Afficher le Relevé</button>
    </form>

    <?php if ($message) : ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <?php if (isset($compte_details)) : ?>
        <h2>Informations sur le Compte :</h2>
        <p>Nom du propriétaire : <?= $compte_details['prenom_client'] ?> <?= $compte_details['nom_client'] ?></p>
        <p>Numéro de Compte : <?= $compte_details['numero_compte'] ?></p>
        <p>Solde : <?= $compte_details['solde'] ?> €</p>

        <?php if (!empty($operations)) : ?>
            <h2>Opérations Bancaires :</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type d'Opération</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($operations as $operation) : ?>
                        <tr>
                            <td><?= $operation['date_operation'] ?></td>
                            <td><?= $operation['type_operation'] ?></td>
                            <td><?= $operation['montant'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucune opération bancaire disponible pour ce compte.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>