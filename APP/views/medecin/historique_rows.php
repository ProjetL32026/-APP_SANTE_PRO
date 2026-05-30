<?php
/**
 * Fichier : APP/views/medecin/historique_rows.php
 * @var array $historique
 */
?>
<?php if (!empty($historique)): ?>
    <?php foreach ($historique as $h): ?>
        <tr class="align-middle">
            <td><?= date('d/m/Y', strtotime($h['date'])) ?></td>
            <td class="fw-bold"><?= htmlspecialchars(($h['nom'] ?? '') . ' ' . ($h['prenom'] ?? '')) ?></td>
            <td><?= substr(htmlspecialchars($h['diagnostic'] ?? ''), 0, 50) ?>...</td>
            <td class="text-center">
                <button class="btn btn-sm text-white px-3 fw-medium border-0 shadow-sm btn-historique-voir"
                    style="background-color: var(--teal, #008080);"
                    data-bs-toggle="modal"
                    data-bs-target="#modalConsultation"
                    data-patient="<?= htmlspecialchars(($h['nom'] ?? '') . ' ' . ($h['prenom'] ?? ''), ENT_QUOTES) ?>"
                    data-diag="<?= htmlspecialchars($h['diagnostic'] ?? '', ENT_QUOTES) ?>"
                    data-presc="<?= htmlspecialchars(nl2br($h['prescription'] ?? ''), ENT_QUOTES) ?>">
                    <i class="bi bi-eye me-1"></i> Voir
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>