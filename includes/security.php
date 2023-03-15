<?php

// Inkludieren der "init.php"-Datei für die Datenbankverbindung und andere wichtige Funktionen
require_once("init.php");

// Funktion zum Hashen von Passwörtern mit bcrypt
function hash_password($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}

// Funktion zum Escapen von Zeichenfolgen für sichere SQL-Abfragen
function escape_string($string) {
    global $pdo;
    return $pdo->quote($string);
}

// Funktion zum Überprüfen von Benutzereingaben
function validate_input($input, $type) {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
        case 'date':
            $date = DateTime::createFromFormat('Y-m-d', $input);
            return $date !== false && !array_sum($date->getLastErrors());
        default:
            return true;
    }
}

// Funktion zum Überprüfen von Berechtigungen
function check_permission($user_id, $permission) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM permissions WHERE user_id = :user_id AND permission = :permission");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':permission', $permission, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchColumn();

    return ($result > 0);
}
