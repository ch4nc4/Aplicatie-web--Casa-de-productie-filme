<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Profilul meu</title>
    <style>
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .profile-container h2 { margin-bottom: 20px; }
        .profile-container label { display: block; margin-top: 15px; }
        .profile-container input, .profile-container textarea {
            width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;
        }
        .profile-container button {
            margin-top: 20px; padding: 10px 20px; border-radius: 5px;
            background: #007bff; color: #fff; border: none; cursor: pointer;
        }
        .profile-container img { max-width: 120px; border-radius: 50%; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="profile-container">
    <a href="/" class="back-link" style="display:inline-block; margin-bottom:15px;">← Înapoi la meniul principal</a>

    <h2>Profilul meu</h2>
    <?php if (isset($success)): ?>
        <div style="color:green;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($user['avatar_url'])): ?>
        <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar">
    <?php endif; ?>

    <div style="margin-bottom:20px; background:#f8f9fa; padding:15px; border-radius:8px;">
        <strong>Datele tale actuale:</strong>
        <ul style="list-style:none; padding-left:0;">
            <li><b>Email:</b> <?= htmlspecialchars($user['email']) ?></li>
            <li><b>Prenume:</b> <?= htmlspecialchars($user['prenume']) ?></li>
            <li><b>Nume familie:</b> <?= htmlspecialchars($user['nume_familie']) ?></li>
            <li><b>Username:</b> <?= htmlspecialchars($user['username']) ?></li>
            <li><b>Telefon:</b> <?= htmlspecialchars($user['numar_telefon']) ?></li>
            <li><b>Bio:</b> <?= nl2br(htmlspecialchars($user['bio'])) ?></li>
            <li><b>Avatar URL:</b> <?= htmlspecialchars($user['avatar_url']) ?></li>
        </ul>
    </div>

    <form method="POST" action="/user/profile/update">
        <?php echo csrf_token_field(); ?>

        <label>Email (nu poate fi schimbat):</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

        <label>Prenume:</label>
        <input type="text" name="prenume" value="<?= htmlspecialchars($user['prenume']) ?>">

        <label>Nume familie:</label>
        <input type="text" name="nume_familie" value="<?= htmlspecialchars($user['nume_familie']) ?>">

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">

        <label>Telefon:</label>
        <input type="text" name="numar_telefon" value="<?= htmlspecialchars($user['numar_telefon']) ?>">

        <label>Bio:</label>
        <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>

        <label>Avatar URL:</label>
        <input type="text" name="avatar_url" value="<?= htmlspecialchars($user['avatar_url']) ?>">

        <button type="submit">Salvează modificările</button>
    </form>
</div>
</body>
</html>