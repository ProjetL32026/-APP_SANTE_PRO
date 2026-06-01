<?php
// On gère les chemins proprement pour le Model et le Mailer
$pathModel = dirname(__DIR__, 2) . '/models/Smodel/RdvModel.php';
$pathMail = __DIR__ . '/MailController.php';

require_once $pathModel;
require_once $pathMail;

class RdvController
{
    private $db;

    public function __construct($db = null)
    {
        if ($db === null && isset($GLOBALS['db'])) {
            $db = $GLOBALS['db'];
        }
        $this->db = $db;
    }

    /**
     * Cette fonction vérifie si le médecin est absent et annule les RDV en envoyant des mails.
     * Elle est conçue pour être appelée dès que le système détecte le changement de statut en BDD.
     */
    public function notifierAnnulation($id_medecin, $date = null)
    {
        // Si aucune date n'est fournie, on prend celle d'aujourd'hui
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $model = new RdvModel($this->db);
        $mailCtrl = new MailController();

        // 1. On interroge la BDD pour voir si le statut du médecin est 'absent'
        // (On suppose que tu as une méthode getStatus dans ton RdvModel ou MedecinModel)
        $statusMedecin = $model->getMedecinStatus($id_medecin);

        if ($statusMedecin === 'absent') {

            // 2. On récupère la liste des patients du jour qui n'ont pas encore été annulés
            $patients = $model->getPatientsByAbsence($id_medecin, $date);

            // On récupère le vrai nom du médecin depuis la BDD pour le mail
            $nomMedecin = $model->getNomMedecin($id_medecin) ?: "votre médecin";
            $compteur = 0;
            foreach ($patients as $p) {
                // 3. IMPORTANT : On vérifie si le mail n'a pas déjà été envoyé
                // On utilise la colonne 'mail_envoye' que nous avons ajoutée en BDD
                if (isset($p['mail_envoye']) && $p['mail_envoye'] == 0) {

                    // 4. On change le statut du RDV en 'annule'
                    $model->updateStatut($p['id_rdv'], 'Annulé');

                    // 5. On envoie le mail via PHPMailer (Mailtrap)
                    $envoiOk = $mailCtrl->envoyerAlerteAbsence(
                        $p['email'],
                        $p['nom'],
                        $date,
                        $nomMedecin
                    );

                    // 6. On marque en BDD que le mail est envoyé pour ce RDV précis
                    if ($envoiOk) {
                        $model->marquerMailEnvoye($p['id_rdv']);
                        $compteur++;
                    }
                }
            }

            if ($compteur > 0) {
                error_log("Succès : $compteur patients notifiés pour l'absence du Dr. $nomMedecin.");
            }
            return true;
        }

        return false;
    }
}