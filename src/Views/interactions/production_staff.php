<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj către lider de producție - Casa de Producție Filme</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            min-height: 100vh;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 2.2em;
        }
        .header p {
            color: #666;
            font-size: 1.1em;
            margin: 10px 0;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }
        select, input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        select:focus, input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        textarea {
            height: 120px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .leader-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        .leader-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .leader-info p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }
        .subject-examples {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .subject-examples h4 {
            margin: 0 0 10px 0;
            color: #0056b3;
        }
        .subject-examples ul {
            margin: 0;
            padding-left: 20px;
        }
        .subject-examples li {
            margin-bottom: 5px;
            color: #555;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .char-counter {
            text-align: right;
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .quick-subjects {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .quick-subject {
            background: #e9ecef;
            border: 1px solid #ced4da;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .quick-subject:hover {
            background: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/dashboard" class="back-link">← Înapoi la dashboard</a>
        
        <div class="header">
            <h1>📧 Mesaj către lider</h1>
            <p>Comunică cu liderul de producție pentru diferite situații</p>
        </div>

        <div id="alerts"></div>

        <div class="subject-examples">
            <h4>💡 Exemple de situații pentru care poți trimite mesaj:</h4>
            <ul>
                <li><strong>Probleme tehnice:</strong> Echipament defect, probleme cu locația</li>
                <li><strong>Resurse:</strong> Cereri de materiale, echipament suplimentar</li>
                <li><strong>Programare:</strong> Modificări în program, conflicte de timp</li>
                <li><strong>Colaborare:</strong> Coordonare cu alte departamente</li>
                <li><strong>Urgențe:</strong> Situații neașteptate care necesită intervenție</li>
            </ul>
        </div>

        <form id="staffMessageForm">
            <div class="form-group">
                <label for="to_user_id">🎯 Către liderul de producție:</label>
                <select id="to_user_id" name="to_user_id" required>
                    <option value="">Selectează liderul de producție</option>
                    <?php foreach ($leaders as $leader): ?>
                        <option value="<?= $leader['id'] ?>">
                            <?= htmlspecialchars($leader['name']) ?> - <?= htmlspecialchars($leader['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <div id="selected-leader-info" class="leader-info" style="display: none;">
                    <h4 id="leader-name"></h4>
                    <p id="leader-email"></p>
                </div>
            </div>

            <div class="form-group">
                <label for="subject">📝 Subiectul mesajului:</label>
                <input type="text" id="subject" name="subject" required 
                       placeholder="Ex: Problemă cu echipamentul de filmare" 
                       maxlength="100">
                <div class="char-counter">
                    <span id="subject-count">0</span>/100 caractere
                </div>
                
                <div class="quick-subjects">
                    <span class="quick-subject" data-subject="Problemă tehnică urgentă">🔧 Problemă tehnică</span>
                    <span class="quick-subject" data-subject="Cerere materiale suplimentare">📦 Cerere materiale</span>
                    <span class="quick-subject" data-subject="Modificare program filmare">📅 Modificare program</span>
                    <span class="quick-subject" data-subject="Colaborare cu alte departamente">🤝 Colaborare</span>
                    <span class="quick-subject" data-subject="Raportare progres proiect">📊 Raport progres</span>
                    <span class="quick-subject" data-subject="Întrebare generală">❓ Întrebare</span>
                </div>
            </div>

            <div class="form-group">
                <label for="message">💬 Mesajul detaliat:</label>
                <textarea id="message" name="message" required 
                         placeholder="Descrie situația în detaliu...&#10;&#10;Exemplu:&#10;Bună ziua,&#10;&#10;Vreau să vă raportez o problemă cu echipamentul de filmare din studioul A. Camera principală prezintă defecțiuni și nu mai înregistrează corect. &#10;&#10;Am nevoie de intervenția dumneavoastră pentru a rezolva această situație cât mai curând posibil, deoarece avem programare pentru mâine dimineață.&#10;&#10;Mulțumesc!"
                         maxlength="1000"></textarea>
                <div class="char-counter">
                    <span id="message-count">0</span>/1000 caractere
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <button type="submit" class="btn">📧 Trimite mesajul</button>
                    <a href="/dashboard" class="btn btn-secondary">❌ Anulează</a>
                </div>
            </div>
        </form>
    </div>

    <script>
    // Afișează informații despre liderul selectat
    document.getElementById('to_user_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const leaderInfo = document.getElementById('selected-leader-info');
        
        if (this.value) {
            const text = selectedOption.text;
            const [name, email] = text.split(' - ');
            
            document.getElementById('leader-name').textContent = name;
            document.getElementById('leader-email').textContent = email;
            leaderInfo.style.display = 'block';
        } else {
            leaderInfo.style.display = 'none';
        }
    });

    // Quick subjects
    document.querySelectorAll('.quick-subject').forEach(function(element) {
        element.addEventListener('click', function() {
            document.getElementById('subject').value = this.dataset.subject;
            updateCharCounter('subject', 'subject-count', 100);
        });
    });

    // Character counters
    function updateCharCounter(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        const length = input.value.length;
        
        counter.textContent = length;
        
        if (length > maxLength * 0.9) {
            counter.style.color = '#dc3545';
        } else if (length > maxLength * 0.7) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#666';
        }
    }

    document.getElementById('subject').addEventListener('input', function() {
        updateCharCounter('subject', 'subject-count', 100);
    });

    document.getElementById('message').addEventListener('input', function() {
        updateCharCounter('message', 'message-count', 1000);
    });

    // Form submission
    document.getElementById('staffMessageForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = '📤 Se trimite...';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/email/staff-message', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            const alertDiv = document.getElementById('alerts');
            if (result.success) {
                alertDiv.innerHTML = `
                    <div class="alert alert-success">
                        ✅ ${result.message}
                        <br><small>Liderul va primi mesajul pe email în câteva momente.</small>
                    </div>
                `;
                this.reset();
                document.getElementById('selected-leader-info').style.display = 'none';
                updateCharCounter('subject', 'subject-count', 100);
                updateCharCounter('message', 'message-count', 1000);
            } else {
                alertDiv.innerHTML = `
                    <div class="alert alert-error">
                        ❌ ${result.message}
                        <br><small>Te rugăm să încerci din nou.</small>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('alerts').innerHTML = `
                <div class="alert alert-error">
                    ❌ Eroare la trimiterea mesajului
                    <br><small>Verifică conexiunea la internet și încearcă din nou.</small>
                </div>
            `;
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
        
        // Scroll to alerts
        document.getElementById('alerts').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'nearest' 
        });
    });

    // Initialize character counters
    updateCharCounter('subject', 'subject-count', 100);
    updateCharCounter('message', 'message-count', 1000);
    </script>
</body>
</html>