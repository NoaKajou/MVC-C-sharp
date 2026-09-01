<h2>Statistiques des catégories d'utilisateurs</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Catégorie</th>
            <th>Questionnaires répondus</th>
            <th>Total des points</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stats as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['categorie']) ?></td>
                <td><?= htmlspecialchars($row['total_questionnaires']) ?></td>
                <td><?= htmlspecialchars($row['total_points']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
