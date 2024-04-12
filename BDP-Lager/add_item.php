<?php
// Verbindung zur MySQL-Datenbank herstellen (ersetzen Sie die Platzhalter mit Ihren eigenen Daten)
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// �berpr�fen Sie die Verbindung
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Daten aus dem Formular erhalten
$itemName = $_POST['itemName'];
$description = $_POST['description'];
$location = $_POST['location'];

// Bild verarbeiten (wenn hochgeladen)
$image = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    // Dateipfad und -name f�r das Bild
    $imageFilename = "images/image_" . uniqid() . ".png";
    
    // Bild speichern
    move_uploaded_file($_FILES['image']['tmp_name'], $imageFilename);
    
    // Setze den Dateipfad f�r das Bild in der Datenbank
    $image = $imageFilename;
}

// SQL-Abfrage zum Einf�gen des Items in die Datenbank
$sqlInsert = "INSERT INTO items (itemName, description, image, location) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("ssss", $itemName, $description, $image, $location);

if ($stmtInsert->execute()) {
    // ID des eingef�gten Elements abrufen
    $lastInsertId = $stmtInsert->insert_id;

    // Barcode-Link erstellen
    $barcodeLink = "https://www.webarcode.com/barcode/image.php?code=$lastInsertId&type=C128B&xres=1&height=50&width=63&font=3&output=png&style=197";

    // Dateipfad und -name f�r das PNG-Bild
    $pngFilename = "barcodes/barcode_" . $lastInsertId . ".png";

    // Barcode-Bild herunterladen und als PNG-Datei speichern
    file_put_contents($pngFilename, file_get_contents($barcodeLink));

    // Bildpfad in der Datenbank speichern
    $sqlUpdate = "UPDATE items SET barcode = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $pngFilename, $lastInsertId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    echo "Item wurde erfolgreich hinzugef�gt. ID: " . $lastInsertId;

    // HTML-Code f�r die Anzeige des Barcodes und des Download-Buttons
    echo '
    <div>
        <img src="' . $barcodeLink . '" alt="Barcode" />
        <br>
        <a href="' . $pngFilename . '" download="barcode.png">
            <button>Barcode herunterladen</button>
        </a>
    </div>';
} else {
    echo "Fehler beim Hinzuf�gen des Items: " . $stmtInsert->error;
}

$stmtInsert->close();

// Verbindung schlie�en
$conn->close();
?>
