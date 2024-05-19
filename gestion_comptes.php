<?php
require_once 'connexion/config.php';

$message = '';
$comptes = [];

try {
    $stmt = $connecte->query("SELECT id_compte, numero_compte, solde FROM comptebancaire");
    $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur : " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_compte = $_POST['id_compte'];
    $montant = $_POST['montant'];
    $action = $_POST['action'];

    
    if (!is_numeric($montant) || $montant <= 0) {
        $message = "Veuillez saisir un montant valide.";
    } else {
        try {
            
            $connecte->beginTransaction();

            
            switch ($action) {
                case 'retrait':
                    $nouveau_solde = $compte['solde'] - $montant;
                    break;
                case 'depot':
                    $nouveau_solde = $compte['solde'] + $montant;
                    break;
                case 'virement':
                    
                    break;
                default:
                    $message = "Action non valide.";
            }

            
            $stmt = $connecte->prepare("
                INSERT INTO operationbancaire (id_compte, type_operation, montant)
                VALUES (:id_compte, :type_operation, :montant)
            ");
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->bindParam(':type_operation', $action);
            $stmt->bindParam(':montant', $montant);
            $stmt->execute();

            $stmt = $connecte->prepare("
                UPDATE comptebancaire SET solde = :nouveau_solde WHERE id_compte = :id_compte
            ");
            $stmt->bindParam(':nouveau_solde', $nouveau_solde);
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->execute();

            $connecte->commit();

            $message = "Opération effectuée avec succès.";
        } catch (PDOException $e) {
            $connecte->rollBack();
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Comptes</title>
    <link rel="stylesheet" href="gestion_comptes.css">
</head>
<body>
     <h1>Gestion des Comptes</h1>
    <table>
        <thead>
            <tr>
                <th>Numéro de Compte</th>
                <th>Solde</th>
                <th>Montant</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comptes as $compte) : ?>
                <tr>
                    <td><?= $compte['numero_compte'] ?></td>
                    <td><?= $compte['solde'] ?></td>
                    <td>
                        <form method="POST" action="gestion_comptes.php">
                            <input type="hidden" name="id_compte" value="<?= $compte['id_compte'] ?>">
                            <input type="number" name="montant" step="0.01" required>
                    </td>
                    <td>
                            <select name="action">
                                <option value="retrait">Retrait</option>
                                <option value="depot">Dépôt</option>
                                <option value="virement">Virement</option>
                            </select>
                            <button type="submit">Valider</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($message) : ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>