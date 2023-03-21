<?php
// Laden der benötigten Klassen
require_once 'Database.php';
require_once 'Router.php';

// Erstellen einer neuen Instanz des Routers
$router = new Router();

// Erstellen einer neuen Instanz der Datenbankklasse
$db = new Database();

// GET - Alle Benutzer abrufen
$router->get('/users', function() use ($db) {
    $result = $db->select('users', '*');
    echo json_encode($result);
});

// GET - Einzelnen Benutzer abrufen
$router->get('/users/{id:\d+}', function($id) use ($db) {
    $result = $db->select('users', '*', "id = $id");
    echo json_encode($result);
});

// POST - Neuen Benutzer erstellen
$router->post('/users', function(Request $request) use ($db) {
    $data = $request->getParsedBody();
    $inserted = $db->insert('users', $data);
    if ($inserted) {
        echo json_encode(array('message' => 'User created successfully'));
    } else {
        echo json_encode(array('error' => 'Failed to create user'));
    }
});

// PUT - Benutzer aktualisieren
$router->put('/users/{id:\d+}', function($id, Request $request) use ($db) {
    $data = $request->getParsedBody();
    $updated = $db->update('users', $data, "id = $id");
    if ($updated) {
        echo json_encode(array('message' => 'User updated successfully'));
    } else {
        echo json_encode(array('error' => 'Failed to update user'));
    }
});

// DELETE - Benutzer löschen
$router->delete('/users/{id:\d+}', function($id) use ($db) {
    $deleted = $db->delete('users', "id = $id");
    if ($deleted) {
        echo json_encode(array('message' => 'User deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Failed to delete user'));
    }
});

// Starten des Routers
$router->run();