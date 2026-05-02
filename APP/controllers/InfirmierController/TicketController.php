<?php
class TicketController {
    private $model;

    public function __construct($ticketModel) {
        $this->model = $ticketModel;
    }

    /**
     * RÉCUPÉRATION DU PATIENT (NOM/PRÉNOM)
     * Affiche les infos seulement si statut = 'Chez le médecin'
     */
    public function afficherTicketActuel($id_medecin) {
        if (!$id_medecin) return null;
        return $this->model->getTicketEnCours($id_medecin);
    }

    /**
     * RÉCUPÉRATION DU NUMÉRO DE TICKET SEUL
     * Affiche le numéro du ticket 'Présent' ou 'Chez le médecin'
     */
    public function afficherNumeroSeul($id_medecin) {
        if (!$id_medecin) return '---';
        return $this->model->getProchainTicketAffichage($id_medecin);
    }

    /**
     * ACTION DE L'INFIRMIER (APPELER SUIVANT)
     * Termine l'ancien, ne touche pas au nouveau.
     */
    public function appelerSuivant($id_medecin) {
        if ($id_medecin) {
            // Le modèle passe l'ancien patient à 'Terminé'
            $this->model->appelerProchainPatient($id_medecin);
            return true;
        }
        return false;
    }
}
?>