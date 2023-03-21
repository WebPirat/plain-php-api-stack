<?php
// Erstellen einer neuen Instanz der Datenbankklasse
$db = new Database();

// SELECT-Abfrage mit Bedingung
$results = $db->select('users', '*', "id = 1");

// Einfügen eines neuen Datensatzes
$data = array(
    'username' => 'johndoe',
    'email' => 'johndoe@example.com',
    'password' => 'mypassword'
);
$inserted = $db->insert('users', $data);

// Aktualisieren eines Datensatzes
$data = array(
    'username' => 'newusername',
    'email' => 'newemail@example.com'
);
$updated = $db->update('users', $data, "id = 1");

// Löschen eines Datensatzes
$deleted = $db->delete('users', "id = 1");