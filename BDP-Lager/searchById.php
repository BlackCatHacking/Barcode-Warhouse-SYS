<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// �berpr�fen Sie die Verbindung
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Holen Sie die ID aus der Anfrage
$id = $_GET['id'];

// Suchen Sie den Eintrag in der Datenbank nach ID
$sql = "SELECT * FROM items WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Eintrag gefunden, Daten ausgeben
    $row = $result->fetch_assoc();
    echo json_encode($row); // Hier k�nnen Sie die Daten formatieren und ausgeben (z.B. als JSON)
} else {
    echo "Kein Eintrag gefunden";
}

// Verbindung schlie�en
$conn->close();
?>
