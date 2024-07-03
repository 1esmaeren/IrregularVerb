<?php
include 'db.php';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM fiiller WHERE hali1 LIKE ? OR hali2 LIKE ? OR hali3 LIKE ? OR turkce_anlam LIKE ? OR sonuc LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
    $fiiller = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM fiiller");
    $fiiller = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hali1 = $_POST['hali1'];
    $hali2 = $_POST['hali2'];
    $hali3 = $_POST['hali3'];
    $turkce_anlam = $_POST['turkce_anlam'];
    $sonuc = $_POST['sonuc'];

    $stmt = $pdo->prepare("INSERT INTO fiiller (hali1, hali2, hali3, turkce_anlam, sonuc) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$hali1, $hali2, $hali3, $turkce_anlam, $sonuc]);

    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM fiiller WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Düzensiz Fiiller</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .öğrenilecek { 
            background-color: orange; 
        }
        .öğrenildi { 
            background-color: lightgreen; 
        }
        .önemli { 
            background-color: yellow; 
        }
        h1, h2 {
            text-align: center;
        }
        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        form input, form select {
            padding: 10px;
            width: 18%;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        form input::placeholder {
            color: #aaa;
        }
        form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        form button:hover {
            background-color: #45a049;
        }
        .delete-button {
            color: red;
            cursor: pointer;
        }
        .search-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            padding: 5px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .header .search-container {
            flex: 1;
        }
    </style>
    <script>
        // Fonksiyon: Satır renklendirme ve local storage işlemleri
        function updateRowColor(row, value) {
            row.classList.remove('öğrenilecek', 'öğrenildi', 'önemli');
            if (value === 'Öğrenilecek') {
                row.classList.add('öğrenilecek');
            } else if (value === 'Öğrenildi') {
                row.classList.add('öğrenildi');
            } else if (value === 'Önemli') {
                row.classList.add('önemli');
            }

            // Local storage'a kaydet
            var id = row.dataset.id;
            localStorage.setItem('sonuc_' + id, value);
        }

        // Fonksiyon: Sonuç değiştiğinde
        function onSonucChange(selectElement) {
            var row = selectElement.closest('tr');
            updateRowColor(row, selectElement.value);

            var formData = new FormData();
            formData.append('id', selectElement.dataset.id);
            formData.append('sonuc', selectElement.value);

            fetch('update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
        }

        // Fonksiyon: Satır silme
        function deleteRow(id) {
            if (confirm('Bu fiili silmek istediğinize emin misiniz?')) {
                fetch(`index.php?delete=${id}`, {
                    method: 'GET'
                })
                .then(response => {
                    if (response.ok) {
                        document.querySelector(`tr[data-id="${id}"]`).remove();
                    } else {
                        alert('Silme işlemi sırasında bir hata oluştu.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Fonksiyon: Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            // Satırların renklendirilmesi
            document.querySelectorAll('tr').forEach(function(row) {
                var id = row.dataset.id;
                var savedSonuc = localStorage.getItem('sonuc_' + id);
                if (savedSonuc) {
                    updateRowColor(row, savedSonuc);
                    // Select elementini güncelle
                    var selectElement = row.querySelector('select[name="sonuc"]');
                    if (selectElement) {
                        selectElement.value = savedSonuc;
                    }
                }
            });

            // Sonuç değişikliği olayı ekle
            document.querySelectorAll('select[name="sonuc"]').forEach(function(selectElement) {
                selectElement.addEventListener('change', function() {
                    onSonucChange(selectElement);
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Düzensiz Fiiller</h1>
        </div>
        <form method="POST" action="">
            <input type="text" name="hali1" placeholder="1. Hali" required>
            <input type="text" name="hali2" placeholder="2. Hali" required>
            <input type="text" name="hali3" placeholder="3. Hali" required>
            <input type="text" name="turkce_anlam" placeholder="Türkçe Anlamı" required>
            <select name="sonuc">
                <option value="Öğrenilecek">Öğrenilecek</option>
                <option value="Öğrenildi">Öğrenildi</option>
                <option value="Önemli">Önemli</option>
            </select>
            <button type="submit">Ekle</button>
        </form>
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Arama yapın" value="<?= htmlspecialchars($search) ?>">
            </form>
        </div>
        <h2>Fiiller</h2>
        <table>
            <tr>
                <th>1. Hali</th>
                <th>2. Hali</th>
                <th>3. Hali</th>
                <th>Türkçe Anlamı</th>
                <th>Sonuç</th>
                <th>İşlem</th>
            </tr>
            <?php if (!empty($fiiller)): ?>
                <?php foreach ($fiiller as $fiil): ?>
                <tr data-id="<?= $fiil['id'] ?>" class="<?= strtolower($fiil['sonuc']) ?>">
                    <td><?= htmlspecialchars($fiil['hali1']) ?></td>
                    <td><?= htmlspecialchars($fiil['hali2']) ?></td>
                    <td><?= htmlspecialchars($fiil['hali3']) ?></td>
                    <td><?= htmlspecialchars($fiil['turkce_anlam']) ?></td>
                    <td>
                        <select name="sonuc" data-id="<?= $fiil['id'] ?>" onchange="onSonucChange(this)">
                            <option value="Öğrenilecek" <?= $fiil['sonuc'] == 'Öğrenilecek' ? 'selected' : '' ?>>Öğrenilecek</option>
                            <option value="Öğrenildi" <?= $fiil['sonuc'] == 'Öğrenildi' ? 'selected' : '' ?>>Öğrenildi</option>
                            <option value="Önemli" <?= $fiil['sonuc'] == 'Önemli' ? 'selected' : '' ?>>Önemli</option>
                        </select>
                    </td>
                    <td>
                        <span class="delete-button" onclick="deleteRow(<?= $fiil['id'] ?>)">Sil</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Kayıt bulunamadı.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
