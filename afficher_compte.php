<?php
require_once 'connexion/config.php';
require_once 'Client.php';
require_once 'CompteBancaire.php';

function getAllComptes($connecte) {
    try {
        $stmt = $connecte->prepare("
            SELECT comptebancaire.*, Client.*
            FROM comptebancaire
            JOIN client ON comptebancaire.id_client = client.id_client
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comptes = [];
        foreach ($result as $row) {
            $client = new Client($row['id_client'], $row['nom'], $row['prenom'], $row['adresse'], $row['telephone']);
            $compte = new CompteBancaire($row['id_compte'], $row['numero_compte'], $row['solde'], $client);
            $comptes[] = $compte;
        }
        return $comptes;
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des comptes : " . $e->getMessage());
    }
}

$comptes = [];
$message = '';

try {
    $comptes = getAllComptes($connecte);
} catch (Exception $e) {
    $message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Comptes Bancaires</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Liste des Comptes Bancaires</h1>
    <?php if ($message) : ?>
        <p><?= $message ?></p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>ID Compte</th>
                    <th>Numéro de Compte</th>
                    <th>Solde</th>
                    <th>Nom du Client</th>
                    <th>Prénom du Client</th>
                    <th>Adresse du Client</th>
                    <th>Téléphone du Client</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comptes as $compte) : ?>
                    <tr>
                        <td><?= $compte->getIdCompte() ?></td>
                        <td><?= $compte->getNumeroCompte() ?></td>
                        <td><?= $compte->getSolde() ?> €</td>
                        <td><?= $compte->getClient()->getNom() ?></td>
                        <td><?= $compte->getClient()->getPrenom() ?></td>
                        <td><?= $compte->getClient()->getAdresse() ?></td>
                        <td><?= $compte->getClient()->getTelephone() ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
