<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $sonuc = $_POST['sonuc'];

    $stmt = $pdo->prepare("UPDATE fiiller SET sonuc = ? WHERE id = ?");
    $stmt->execute([$sonuc, $id]);

    echo "Başarıyla güncellendi!";
}
?>
