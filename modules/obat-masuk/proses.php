<?php
session_start();

// Panggil koneksi database.php untuk koneksi database
require_once "../../config/database.php";

// fungsi untuk pengecekan status login user 
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
} else {

    if ($_GET['act']=='insert') {
        if (isset($_POST['simpan'])) {
            $kode_transaksi = mysqli_real_escape_string($mysqli, trim($_POST['kode_transaksi']));
            $tanggal_input  = mysqli_real_escape_string($mysqli, trim($_POST['tanggal_masuk']));
            $tanggal_masuk = mysqli_real_escape_string($mysqli, trim($_POST['tanggal_masuk']));
    
            $kode_obat = mysqli_real_escape_string($mysqli, trim($_POST['kode_obat']));
            $created_user = $_SESSION['id_user'];
    
            // Ambil semua batch yang diinputkan
            $jumlah_list = $_POST['jumlah_masuk'];
            $expired_list = $_POST['expired_date'];
    
            $total_stok_masuk = 0;
    
            for ($i = 0; $i < count($jumlah_list); $i++) {
                $jumlah_masuk = mysqli_real_escape_string($mysqli, trim($jumlah_list[$i]));
                $expired_date = mysqli_real_escape_string($mysqli, trim($expired_list[$i]));
    
                $total_stok_masuk += (int)$jumlah_masuk;
    
                // Simpan ke is_obat_masuk
                $query = mysqli_query($mysqli, "INSERT INTO is_obat_masuk(kode_transaksi, tanggal_masuk, expired_date, kode_obat, jumlah_masuk, created_user)
                                                VALUES('$kode_transaksi', '$tanggal_masuk', '$expired_date', '$kode_obat', '$jumlah_masuk', '$created_user')")
                                                or die('Error query insert: '.mysqli_error($mysqli));
                // Simpan ke is_view_masuk
                $query = mysqli_query($mysqli, "INSERT INTO is_view_masuk(kode_transaksi, tanggal_masuk, expired_date, kode_obat, jumlah_masuk, created_user)
                                                VALUES('$kode_transaksi', '$tanggal_masuk', '$expired_date', '$kode_obat', '$jumlah_masuk', '$created_user')")
                                                or die('Error query insert: '.mysqli_error($mysqli));

                $query_tabel_stok = mysqli_query($mysqli, "INSERT INTO is_stok(kode_obat, transaksi_masuk, jml_masuk_per_exp, stok_per_exp, exp_date)
                                                VALUES('$kode_obat', '$kode_transaksi', '$jumlah_masuk', '$jumlah_masuk', '$expired_date')")
                                                or die('Error query insert: '.mysqli_error($mysqli));
            }
    
            // Update stok total di is_obat
            $query_update = mysqli_query($mysqli, "UPDATE is_obat SET stok = stok + $total_stok_masuk WHERE kode_obat = '$kode_obat'")
                                                or die('Error update stok: '.mysqli_error($mysqli));
    
            if ($query && $query_update && $query_tabel_stok) {
                header("location: ../../main.php?module=obat_masuk&alert=1");
            }
        }
    }
    
}
?>
