<?php
require_once 'connexion/config.php';

class GestionComptes {
    private $connecte;

    public function __construct($connecte) {
        $this->connecte = $connecte;
    }

    public function getComptes() {
        try {
            $stmt = $this->connecte->query("SELECT id_compte, numero_compte, solde FROM comptebancaire");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function effectuerOperation($id_compte, $montant, $action) {
        try {
            $this->connecte->beginTransaction();

            $stmt = $this->connecte->prepare("SELECT solde FROM comptebancaire WHERE id_compte = :id_compte");
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->execute();
            $solde_actuel = $stmt->fetchColumn();

            switch ($action) {
                case 'retrait':
                    $nouveau_solde = $solde_actuel - $montant;
                    break;
                case 'depot':
                    $nouveau_solde = $solde_actuel + $montant;
                    break;
                case 'virement':
                    break;
                default:
                    return "Action non valide.";
            }

            $stmt = $this->connecte->prepare("
                INSERT INTO operationbancaire (id_compte, type_operation, montant)
                VALUES (:id_compte, :type_operation, :montant)
            ");
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->bindParam(':type_operation', $action);
            $stmt->bindParam(':montant', $montant);
            $stmt->execute();

            $stmt = $this->connecte->prepare("
                UPDATE comptebancaire SET solde = :nouveau_solde WHERE id_compte = :id_compte
            ");
            $stmt->bindParam(':nouveau_solde', $nouveau_solde);
            $stmt->bindParam(':id_compte', $id_compte);
            $stmt->execute();

            $this->connecte->commit();

            return "Opération effectuée avec succès.";
        } catch (PDOException $e) {
            $this->connecte->rollBack();
            return "Erreur : " . $e->getMessage();
        }
    }
}
?>