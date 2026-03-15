<?php
session_start();
require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
} else {
    if ($_GET['act'] == 'insert') {
        if (isset($_POST['simpan'])) {
            $kode_transaksi = mysqli_real_escape_string($mysqli, trim($_POST['kode_transaksi']));
            $tanggal_Keluar = date('Y-m-d', strtotime($_POST['tanggal_Keluar']));
            $created_user = $_SESSION['id_user'];

            $kode_obat = $_POST['kode_obat'];
            $jumlah_Keluar = $_POST['jumlah_Keluar'];

            for ($i = 0; $i < count($kode_obat); $i++) {
                $kode = mysqli_real_escape_string($mysqli, $kode_obat[$i]);
                $jumlah = mysqli_real_escape_string($mysqli, $jumlah_Keluar[$i]);

                // Cek stok
                $cek = mysqli_query($mysqli, "SELECT stok FROM is_obat WHERE kode_obat='$kode'");
                $stok_data = mysqli_fetch_assoc($cek);
                $stok = $stok_data['stok'];

                if ($jumlah > $stok) {
                    echo "<script>alert('Stok tidak cukup untuk $kode'); window.history.back();</script>";
                    exit;
                }

                // Ambil expired date terdekat
                $exp = mysqli_query($mysqli, "SELECT expired_date FROM is_obat_masuk 
                                              WHERE kode_obat='$kode' AND jumlah_masuk > 0 
                                              ORDER BY expired_date ASC LIMIT 1");
                $exp_data = mysqli_fetch_assoc($exp);
                $expired_date = $exp_data['expired_date'];

                // Insert data ke tabel is_obat_Keluar
                mysqli_query($mysqli, "INSERT INTO is_obat_Keluar(kode_transaksi, tanggal_Keluar, expired_date, kode_obat, jumlah_Keluar, created_user)
                                       VALUES('$kode_transaksi', '$tanggal_Keluar', '$expired_date', '$kode', '$jumlah', '$created_user')");

                // Update stok obat
                $sisa_stok = $stok - $jumlah;
                mysqli_query($mysqli, "UPDATE is_obat SET stok='$sisa_stok' WHERE kode_obat='$kode'");
            }

            header("location: ../../main.php?module=obat_Keluar&alert=1");
        }
    }
}
?>