<?php
require_once dirname(__DIR__) . '/MODEL/RdvModel.php';
require_once __DIR__ . '/MailController.php'; // On a besoin du mailer

class RdvController
{
    public function notifierAnnulation($id_medecin, $date)
    {
        $model = new RdvModel();
        $mailCtrl = new MailController();

        $patients = $model->getPatientsByAbsence($id_medecin, $date);
        $nomMedecin = "Le Médecin"; // Tu peux le récupérer via un MedecinModel

        foreach ($patients as $p) {
            // On utilise la méthode pro de MailController au lieu de mail()
            $mailCtrl->envoyerAlerteAbsence($p['email'], $p['nom'], $nomMedecin);
        }
    }
}