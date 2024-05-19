<?php
require_once 'connexion/config.php';

class ReleveCompte {
    // Méthode pour récupérer tous les comptes bancaires associés à un client
    public static function getComptesByClient($connecte, $id_client) {
        try {
            $stmt = $connecte->prepare("
                SELECT *
                FROM comptebancaire
                WHERE id_client = :id_client
            ");
            $stmt->bindParam(':id_client', $id_client);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des comptes du client : " . $e->getMessage());
        }
    }

    // Méthode pour récupérer toutes les opérations bancaires d'un compte
    public static function getOperationsByCompte($connecte, $id_compte) {
        try {
            $stmt = $connecte->prepare("
                SELECT *
                FROM operationbancaire
                WHERE id_compte = :id_compte
            ");
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des opérations du compte : " . $e->getMessage());
        }
    }
}
?>