<?php
require_once 'Client.php';

class CompteBancaire {
    private $id_compte;
    private $numero_compte;
    private $solde;
    private $client;

    public function __construct($id_compte, $numero_compte, $solde, $client) {
        $this->id_compte = $id_compte;
        $this->numero_compte = $numero_compte;
        $this->solde = $solde;
        $this->client = $client;
    }

    public function getIdCompte() {
        return $this->id_compte;
    }

    public function getNumeroCompte() {
        return $this->numero_compte;
    }

    public function getSolde() {
        return $this->solde;
    }

    public function getClient() {
        return $this->client;
    }

    public static function getCompteById($connecte, $id_compte) {
        try {
            $stmt = $connecte->prepare("
                SELECT comptebancaire.*, client.*
                FROM comptebancaire
                JOIN client ON comptebancaire.id_client = client.id_client
                WHERE id_compte = :id_compte
            ");
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $client = new Client($row['id_client'], $row['nom'], $row['prenom'], $row['adresse'], $row['telephone']);
                return new CompteBancaire($row['id_compte'], $row['numero_compte'], $row['solde'], $client);
            } else {
                throw new Exception("Compte non trouvé");
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du compte : " . $e->getMessage());
        }
    }

    public function deposer($montant) {
        $this->solde += $montant;
    }

    public function retirer($montant) {
        if ($montant > $this->solde) {
            throw new Exception("Solde insuffisant");
        }
        $this->solde -= $montant;
    }

    public function transferer($montant, $compteDestinataire) {
        if ($montant > $this->solde) {
            throw new Exception("Solde insuffisant");
        }
        $this->retirer($montant);
        $compteDestinataire->deposer($montant);
    }

    public function mettreAJourSolde($connecte) {
        try {
            $stmt = $connecte->prepare("
                UPDATE comptebancaire
                SET solde = :solde
                WHERE id_compte = :id_compte
            ");
            $stmt->bindParam(':solde', $this->solde);
            $stmt->bindParam(':id_compte', $this->id_compte);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du solde : " . $e->getMessage());
        }
    }
}
?>