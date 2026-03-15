<?php
session_start();
require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
    exit;
}

if ($_GET['act'] == 'insert') {
    if (isset($_POST['simpan'])) {
        $kode_transaksi  = mysqli_real_escape_string($mysqli, trim($_POST['kode_transaksi']));
        $tanggal_Keluar  = mysqli_real_escape_string($mysqli, trim($_POST['tanggal_Keluar']));
        $created_user    = $_SESSION['id_user'];

        $nip = mysqli_real_escape_string($mysqli, trim($_POST['nip']));
        $diagnosa = mysqli_real_escape_string($mysqli, trim($_POST['diagnosa']));

        // Ambil nama pasien dari tabel berdasarkan nip
        $query_pasien = mysqli_query($mysqli, "SELECT nama FROM is_pasien WHERE nip = '$nip'");
        $data_pasien = mysqli_fetch_assoc($query_pasien);
        $nama_pasien = $data_pasien ? $data_pasien['nama'] : '';

        $kode_obat       = $_POST['kode_obat'];
        $jumlah_Keluar   = $_POST['jumlah_Keluar'];

        // Validasi: tidak boleh ada obat duplikat
        if (count($kode_obat) !== count(array_unique($kode_obat))) {
            echo "<script>alert('Duplikat obat terdeteksi. Setiap obat hanya boleh satu baris.'); window.history.back();</script>";
            exit;
        }

        // Ambil ID terakhir
        $query_id = mysqli_query($mysqli, "SELECT MAX(id_keluar) AS last_id FROM is_obat_Keluar");
        $data_id = mysqli_fetch_assoc($query_id);
        $last_number = $data_id['last_id'] ? (int)substr($data_id['last_id'], 3) : 0;

        for ($i = 0; $i < count($kode_obat); $i++) {
            $kode   = mysqli_real_escape_string($mysqli, $kode_obat[$i]);
            $jumlah = (int)mysqli_real_escape_string($mysqli, $jumlah_Keluar[$i]);
            $sisa = $jumlah;

            $cek = mysqli_query($mysqli, "SELECT stok FROM is_obat WHERE kode_obat='$kode'");
            $stok_data = mysqli_fetch_assoc($cek);
            $stok = (int)$stok_data['stok'];

            if ($sisa > $stok) {
                echo "<script>alert('Stok tidak cukup untuk obat $kode'); window.history.back();</script>";
                exit;
            }

            // Ambil semua batch berdasarkan tanggal kadaluarsa terdekat (FEFO - First Expired First Out)
            $batch_query = mysqli_query($mysqli, "SELECT kode_transaksi, expired_date, jumlah_masuk FROM is_obat_masuk 
                                            WHERE kode_obat='$kode' AND jumlah_masuk > 0 
                                            ORDER BY expired_date ASC");
            // Konversi hasil query ke array untuk proses lebih lanjut
            $batch_data = [];
            while ($row = mysqli_fetch_assoc($batch_query)) {
                $batch_data[] = $row;
            }
            
            // Jika tidak ada data batch
            if (count($batch_data) == 0) {
                echo "<script>alert('Error: Tidak ada batch tersedia untuk obat $kode'); window.history.back();</script>";
                exit;
            }

            // Proses pengurangan stok dari batch dengan expired terdekat
            foreach ($batch_data as $batch) {
                $batch_id = $batch['kode_transaksi'];
                $batch_exp = $batch['expired_date'];
                $batch_jumlah = (int)$batch['jumlah_masuk'];

                if ($sisa <= 0) {
                    // Jika sisa sudah 0, keluar dari loop
                    break;
                }

if ($batch_jumlah >= $sisa) {
                    // Batch ini cukup untuk memenuhi semua sisa
                    $jumlah_diambil = $sisa;
                    
                    // Insert ke is_obat_Keluar
                    $last_number++;
                    $id_keluar = 'ik_' . str_pad($last_number, 5, '0', STR_PAD_LEFT);
                    mysqli_query($mysqli, "INSERT INTO is_obat_Keluar (
                        id_keluar, kode_transaksi, tanggal_Keluar, expired_date, kode_obat, jumlah_Keluar, created_user, nip, diagnosa
                    ) VALUES (
                        '$id_keluar', '$kode_transaksi', '$tanggal_Keluar', '$batch_exp', '$kode', '$jumlah_diambil', '$created_user', '$nip', '$diagnosa'
                    )");

                    // Update jumlah_masuk batch
                    mysqli_query($mysqli, "UPDATE is_obat_masuk SET jumlah_masuk = jumlah_masuk - $jumlah_diambil WHERE kode_transaksi = '$batch_id'");
                    
                    // Update stok per expired di is_stok jika tabel tersebut digunakan
                    mysqli_query($mysqli, "UPDATE is_stok SET 
                        jml_keluar_per_exp = jml_keluar_per_exp + $jumlah_diambil, 
                        stok_per_exp = stok_per_exp - $jumlah_diambil 
                        WHERE kode_obat = '$kode' AND exp_date = '$batch_exp'");
                    
                    $sisa = 0;
                } else {
                    // Batch ini tidak cukup, ambil semua dari batch ini
                    $jumlah_diambil = $batch_jumlah;
                    
                    // Insert ke is_obat_Keluar
                    $last_number++;
                    $id_keluar = 'ik_' . str_pad($last_number, 5, '0', STR_PAD_LEFT);
                    mysqli_query($mysqli, "INSERT INTO is_obat_Keluar (
                        id_keluar, kode_transaksi, tanggal_Keluar, expired_date, kode_obat, jumlah_Keluar, created_user, nip, diagnosa
                    ) VALUES (
                        '$id_keluar', '$kode_transaksi', '$tanggal_Keluar', '$batch_exp', '$kode', '$jumlah_diambil', '$created_user', '$nip', '$diagnosa'
                    )");

                    // Update jumlah_masuk batch menjadi 0
                    mysqli_query($mysqli, "UPDATE is_obat_masuk SET jumlah_masuk = 0 WHERE kode_transaksi = '$batch_id'");
                    
                    // Update stok per expired di is_stok jika tabel tersebut digunakan
                    mysqli_query($mysqli, "UPDATE is_stok SET 
                        jml_keluar_per_exp = jml_keluar_per_exp + $jumlah_diambil, 
                        stok_per_exp = 0 
                        WHERE kode_obat = '$kode' AND exp_date = '$batch_exp'");
                    
                    // Kurangi sisa yang perlu diambil
                    $sisa -= $jumlah_diambil;
                }
            }
            
            // Update total stok di tabel is_obat
            mysqli_query($mysqli, "UPDATE is_obat SET stok = stok - $jumlah WHERE kode_obat = '$kode'");
        }
        header("location: ../../main.php?module=obat_Keluar&alert=1");
    }
}