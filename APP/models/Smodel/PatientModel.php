<?php
require_once __DIR__ . '/../../config/db.php';

class PatientModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function stockerCodeConfirmation($email, $code)
    {
        // On lie id_patient (de la table patient) à id_utilisateur (de la table utilisateur)
        $sql = "UPDATE patient p 
                JOIN utilisateur u ON p.id_patient = u.id_utilisateur 
                SET p.code_confirmation = :code 
                WHERE u.email = :email";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'code' => $code,
            'email' => $email
        ]);
    }

    public function verifierCodeBDD($email, $codeSaisi)
    {
        // On vérifie si le code correspond en passant par la jointure
        $sql = "SELECT p.* FROM patient p
                JOIN utilisateur u ON p.id_patient = u.id_utilisateur
                WHERE u.email = :email AND p.code_confirmation = :code";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'code' => $codeSaisi
        ]);

        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            // Si le code est bon, on valide (is_verified = 1)
            $update = "UPDATE patient p
                       JOIN utilisateur u ON p.id_patient = u.id_utilisateur
                       SET p.is_verified = 1, p.code_confirmation = NULL 
                       WHERE u.email = :email";
            $this->db->prepare($update)->execute(['email' => $email]);
            return true;
        }
        return false;
    }
}