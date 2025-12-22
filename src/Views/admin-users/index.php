<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Views/admin-users/index.php
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizatori activi - Casa de Producție Filme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg,rgb(102, 15, 34) 0%,rgb(36, 36, 101) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.37);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 1200px;
        }
        .header {
            margin-bottom: 30px;
        }
        .title {
            font-size: 2em;
            font-weight: normal;
            color: black;
            margin-bottom: 10px;
        }
        .subtitle {
            color: black;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .btn {
            display: inline-block;
            margin: 5px 2px;
            padding: 8px 14px;
            background-color:rgb(157, 164, 171);
            color: black;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: normal;
            transition: all 0.3s ease;
            border: 1px solid;
            cursor: pointer;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 57, 61, 0.3);
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color:rgb(8, 12, 16);
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255,255,255,0.85);
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px 6px;
            text-align: center;
        }
        th {
            background: #e9ecef;
            color: #222;
        }
        tr:nth-child(even) {
            background: #f7f7f7;
        }
        form {
            display: inline-block;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Înapoi la pagina principală</a>
        <div class="header">
            <div class="title">Gestionare Utilizatori</div>
            <div class="subtitle">Administrare roluri și apartenență la proiecte</div>
        </div>
        <table>
            <tr>
                <th>Nume</th>
                <th>Email</th>
                <th>Username</th>
                <th>Telefon</th>
                <th>Roluri</th>
                <th>Proiecte (Membru)</th>
                <th>Acțiuni</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['prenume'] . ' ' . $user['nume_familie']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['numar_telefon']) ?></td>
                <td>
                    <?php if (empty($user['roles'])): ?>
                        <span style="color:#888;">Fără rol</span>
                    <?php else: ?>
                        <?php foreach ($user['roles'] as $role): ?>
                            <?= htmlspecialchars($role['nume']) ?><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (empty($user['projects'])): ?>
                        <span style="color:#888;">Nu este membru</span>
                    <?php else: ?>
                        <?php foreach ($user['projects'] as $project): ?>
                            <?= htmlspecialchars($project['title']) ?><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (empty($user['roles'])): ?>
                        <?php if (empty($roles)): ?>
                            <div class="no-roles">
                                <h3>Nu există roluri</h3>
                                <p>Nu au fost găsite roluri în baza de date.</p>
                                <a href="/roles/create" class="btn btn-warning">Creează primul rol</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="/admin/user/add-role" style="margin-bottom:10px;">
                                <?php echo csrf_token_field(); ?>
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                                <select name="id_rol" required>
                                    <option value="">Selecteaza rolul</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['nume']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="date" name="expires_at" placeholder="Expires at (optional)">
                                <button type="submit" class="btn btn-success">Adaugă rol</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Sterge rol -->
                    <?php foreach ($user['roles'] as $role): ?>
                        <form method="POST" action="/admin/user/remove-role" style="display:inline;">
                            <?php echo csrf_token_field(); ?>
                            <input type="hidden" name="role_user_id" value="<?= $role['id'] ?>">
                            <button type="submit" class="btn btn-danger">Sterge rol <?= htmlspecialchars($role['nume']) ?></button>
                        </form>
                    <?php endforeach; ?>

                    <!-- Adauga contributie la proiect -->
                    <form method="POST" action="/admin/user/add-project" style="margin-bottom:10px;">
                        <?php echo csrf_token_field(); ?>
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="id_proiect" required>
                            <option value="">Selecteaza proiect</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="tip_echipa" placeholder="Tip echipa (optional)">
                        <input type="date" name="expires_at" placeholder="Expires at (optional)">
                        <button type="submit" class="btn btn-success">Adauga user la proiect</button>
                    </form>

                    <!-- Sterge contributie la proiect -->
                    <?php foreach ($user['projects'] as $project): ?>
                        <form method="POST" action="/admin/user/remove-project" style="display:inline;">
                            <?php echo csrf_token_field(); ?>
                            <input type="hidden" name="project_member_id" value="<?= $project['membership_id'] ?>">
                            <button type="submit" class="btn btn-danger">Sterge contributia la <?= htmlspecialchars($project['title']) ?></button>
                        </form>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Zona de administrare roluri -->
        <div class="role-management" style="margin-bottom: 40px; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
            <h2 style="margin-bottom: 15px;">Administrare Roluri</h2>
            
            <!-- Formular adaugare rol nou -->
            <form method="POST" action="/admin/roles/create" style="margin-bottom: 20px;">
                <?php echo csrf_token_field(); ?>
                <label for="role_name" style="font-weight:bold;">Nume rol nou:</label>
                <input type="text" id="role_name" name="nume" required style="margin-right:10px;">
                <input type="text" id="descriere" name="descriere" placeholder="Descriere rol ..." style="margin-right:10px;">
                <button type="submit" class="btn btn-success">Adaugă rol</button>
            </form>

            <!-- Lista roluri existente cu buton de ștergere -->
            <div>
                <h3>Roluri existente:</h3>
                <?php if (empty($roles)): ?>
                    <p style="color:#888;">Nu există roluri definite.</p>
                <?php else: ?>
                    <ul style="list-style:none; padding:0;">
                        <?php foreach ($roles as $role): ?>
                            <li style="margin-bottom:8px;">
                                <span style="font-weight:bold;"><?= htmlspecialchars($role['nume']) ?></span>
                                <form method="POST" action="/admin/roles/delete" style="display:inline;">
                                    <?php echo csrf_token_field(); ?>
                                    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                                    <?php $count = $roleUserCounts[$role['id']] ?? 0; ?>
                                    <?php if ($count > 0): ?>
                                        <button class="btn btn-danger" disabled>Nu poți șterge</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Sigur vrei să ștergi acest rol?');">Șterge</button>
                                    <?php endif; ?>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>