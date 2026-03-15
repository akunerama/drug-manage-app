<?php
session_start();
require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_GET['act'] == 'insert') {
    if (isset($_POST['nama_satuan'])) {
        $nama_satuan = mysqli_real_escape_string($mysqli, trim($_POST['nama_satuan']));
        
        // Cek apakah satuan sudah ada
        $query = mysqli_query($mysqli, "SELECT id_satuan FROM is_satuan WHERE nama_satuan = '$nama_satuan'")
                                      or die(json_encode(['status' => 'error', 'message' => 'Database error']));
        
        if (mysqli_num_rows($query) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Satuan sudah ada']);
            exit;
        }
        
        // Simpan satuan baru
        $query = mysqli_query($mysqli, "INSERT INTO is_satuan(nama_satuan) VALUES('$nama_satuan')")
                                    or die(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan satuan']));
        
        if ($query) {
            $id_satuan = mysqli_insert_id($mysqli);
            echo json_encode(['status' => 'success', 'id_satuan' => $id_satuan]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan satuan']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nama satuan tidak boleh kosong']);
    }
}
?>