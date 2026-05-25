<?php
/** @var array $historique */
// SUPPRIMEZ les require_once header et sidebar ici ![cite: 7]
?>
<?php foreach ($historique as $h): ?>
    <tr>
        <td><?= date('d/m/Y', strtotime($h['date'])) ?></td>
        <td class="fw-bold"><?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?></td>
        <td><?= substr(htmlspecialchars($h['diagnostic']), 0, 50) ?>...</td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalConsultation"
                data-patient="<?= htmlspecialchars($h['nom'] . ' ' . $h['prenom'], ENT_QUOTES) ?>"
                data-diag="<?= htmlspecialchars($h['diagnostic'], ENT_QUOTES) ?>"
                data-presc="<?= htmlspecialchars(nl2br($h['prescription']), ENT_QUOTES) ?>">
                <i class="bi bi-eye"></i> Voir
            </button>
        </td>
    </tr>
<?php endforeach; ?>