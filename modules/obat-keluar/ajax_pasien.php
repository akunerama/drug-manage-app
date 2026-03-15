<?php
require_once "../../config/database.php";

$search = $_GET['search'] ?? '';

$result = [];

$query = mysqli_query($mysqli, "SELECT nip, nama FROM is_pasien 
                                WHERE nama LIKE '%$search%' 
                                ORDER BY nama ASC");

while ($row = mysqli_fetch_assoc($query)) {
    $result[] = [
        'id' => $row['nip'],        // dikirim ke input "value"
        'text' => $row['nama']      // yang tampil di select2
    ];
}

echo json_encode($result);
