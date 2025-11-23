<?php
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers l'index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';

// Traitement du formulaire de connexion
if ($_POST) {
    require_once 'config/database.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error_message = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $database = new Database();
            $pdo = $database->getConnection();
            
            $stmt = $pdo->prepare("SELECT id, email, mot_de_passe, nom, prenom, role FROM utilisateurs WHERE email = ? AND actif = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_role'] = $user['role'];
                
                // Mettre à jour la dernière connexion
                $stmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header('Location: index.php');
                exit();
            } else {
                $error_message = 'Email ou mot de passe incorrect.';
            }
        } catch (Exception $e) {
            $error_message = 'Erreur de connexion à la base de données: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de suivi et d'analyse des événements de sûreté</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --accent-yellow: #fbbf24;
            --accent-red: #dc2626;
            --success-color: #10b981;
            --info-color: #3b82f6;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 30%, #fbbf24 70%, #dc2626 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 380px;
            width: 100%;
            max-height: 90vh;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
        }
        
        .logo-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            display: inline-block;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-header img {
            max-height: 80px;
            width: auto;
            margin: 0;
            object-fit: contain;
            border-radius: 8px;
            /* Suppression du filtre pour garder les couleurs originales du logo */
        }
        
        .login-header h1 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .login-header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 0.85rem;
        }
        
        .login-body {
            padding: 30px 25px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 15px;
        }
        
        .form-floating input {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px 15px 8px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            caret-color: transparent; /* Curseur invisible par défaut */
        }
        
        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
            outline: none;
            caret-color: var(--primary-color); /* Curseur visible au focus */
        }
        
        .form-floating label {
            padding: 15px;
            color: #6b7280;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            z-index: 10;
            padding: 5px;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
            color: var(--accent-red);
            border-left: 4px solid var(--accent-red);
        }
        
        .login-footer {
            text-align: center;
            padding: 15px 25px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }
        
        .login-footer p {
            margin: 0;
            color: #6b7280;
            font-size: 0.85rem;
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container fade-in">
        <div class="login-header">
            <div class="logo-container">
                <img src="logo.jpg" alt="Logo ANACIM">
            </div>
            <h1><i class="fas fa-plane-departure me-2"></i>Connexion</h1>
            <p><i class="fas fa-shield-alt me-2"></i>Système de suivi et d'analyse des événements de sûreté</p>
        </div>
        
        <div class="login-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email"><i class="fas fa-envelope me-2"></i>Adresse email</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p><i class="fas fa-shield-alt me-1"></i>Système sécurisé - ANACIM <?php echo date('Y'); ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Suppression du focus automatique pour éviter le curseur clignotant
        document.addEventListener('DOMContentLoaded', function() {
            // Le focus automatique est supprimé pour une meilleure expérience utilisateur
            document.getElementById('email').blur();
        });
    </script>
</body>
</html>
