<!DOCTYPE html>
<html>
<head>
    <title>Echipă Producție</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f8f9fa; }
    </style>
</head>

<?php
    session_start();
    $isLider = in_array('Lider productie', $_SESSION['roles'] ?? []);
?>


<body>
    <h2>Echipă Producție</h2>
    <table>
        <tr>
            <th>Nume</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Proiecte</th>
        </tr>
    
        <?php foreach ($crew as $member): ?>
            <tr>
                <td><?= htmlspecialchars($member['prenume'] . ' ' . $member['nume_familie']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= htmlspecialchars($member['rol']) ?></td>
                <td>
                    <?php if (!empty($member['projects'])): ?>
                       <?php foreach ($member['projects'] as $project): ?>
                            <?= htmlspecialchars($project['title']) ?>
                            <?php if ($isLider && htmlspecialchars($member['rol']) === 'Staff productie'): ?>
                                <form method="POST" action="/admin/user/remove-project" style="display:inline;">
                                    <?php echo csrf_token_field(); ?>
                                    <input type="hidden" name="project_member_id" value="<?= htmlspecialchars($project['membership_id']) ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Sigur vrei să elimini acest membru din proiect?');">
                                        Șterge contribuția la <?= htmlspecialchars($project['title']) ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="color:#888;">Fără proiecte</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="/" class="btn">Înapoi la meniul principal</a>
</body>
</html>