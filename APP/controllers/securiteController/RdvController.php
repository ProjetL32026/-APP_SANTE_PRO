<?php
require_once dirname(__DIR__, 2) . '/models/Smodel/RdvModel.php';
require_once __DIR__ . '/MailController.php';

class RdvController
{
    public function notifierAnnulation($id_medecin, $date)
    {
        $model = new RdvModel();
        $mailCtrl = new MailController();

        // 1. On récupère la liste des patients
        $patients = $model->getPatientsByAbsence($id_medecin, $date);

        // Optionnel : Récupérer le nom du médecin pour le mail
        // $nomMedecin = $model->getNomMedecin($id_medecin); 
        $nomMedecin = "Hocine";

        foreach ($patients as $p) {
            // 2. IMPORTANT : On change le statut en BDD pour chaque RDV
            // Sans ça, l'écran bleu ne saura pas que c'est annulé !
            $model->updateStatut($p['id_rdv'], 'annule');

            // 3. On envoie le mail avec la date
            $mailCtrl->envoyerAlerteAbsence(
                $p['email'],
                $p['nom'],
                $date, // On ajoute la date ici !
                $nomMedecin
            );
        }

        echo "Succès : " . count($patients) . " patients notifiés et RDV annulés.";
    }
}