<?php
class Client {
    private $connecte;

    public function __construct($connecte) {
        $this->connecte = $connecte;
    }

    public function ajouterClient($nom, $prenom, $adresse, $telephone) {
        try {
            $stmt = $this->connecte->prepare("
                INSERT INTO client (nom, prenom, adresse, telephone) 
                VALUES (:nom, :prenom, :adresse, :telephone)
            ");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->execute();

            // Récupération de l'ID du client nouvellement ajouté
            return $this->connecte->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>