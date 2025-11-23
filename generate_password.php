<?php
/**
 * Script pour générer le hash du mot de passe
 * Utilisez ce script pour créer le hash du mot de passe à insérer dans la base de données
 */

$password = "Anacim2025@#";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe: " . $password . "\n";
echo "Hash généré: " . $hash . "\n";
echo "\nRequête SQL à exécuter:\n";
echo "UPDATE utilisateurs SET mot_de_passe = '$hash' WHERE email = 'coutay.ba@anacim.sn';\n";
?>
