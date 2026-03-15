<?php
session_start();
require_once "../../config/database.php";

// Cek login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
    exit;
} else {
    // ========================= INSERT =========================
    if ($_GET['act'] == 'insert') {
        if (isset($_POST['simpan'])) {
            $nip  = mysqli_real_escape_string($mysqli, trim($_POST['nip']));
            $nama = mysqli_real_escape_string($mysqli, trim($_POST['nama']));

            // Cek apakah NIP sudah terdaftar
            $cek = mysqli_query($mysqli, "SELECT nip FROM is_pasien WHERE nip = '$nip'")
                   or die('Ada kesalahan pada query cek: ' . mysqli_error($mysqli));

            if (mysqli_num_rows($cek) > 0) {
                echo "<script>
                        alert('NIP \"$nip\" sudah terdaftar. Silakan gunakan NIP lain.');
                        window.history.back();
                    </script>";
                exit; // <-- ini penting untuk menghentikan eksekusi PHP setelah alert
            } else {
                // Simpan data
                $query = mysqli_query($mysqli, "INSERT INTO is_pasien(nip, nama) 
                                                VALUES('$nip', '$nama')")
                         or die('Ada kesalahan pada query insert: ' . mysqli_error($mysqli));

                if ($query) {
                    header("location: ../../main.php?module=pasien&alert=1");
                }
            }
        }
    }

    // ========================= UPDATE =========================
    elseif ($_GET['act'] == 'update') {
        if (isset($_POST['simpan']) && isset($_POST['nip'])) {
            $nip  = mysqli_real_escape_string($mysqli, trim($_POST['nip']));
            $nama = mysqli_real_escape_string($mysqli, trim($_POST['nama']));

            $query = mysqli_query($mysqli, "UPDATE is_pasien 
                                            SET nama = '$nama'
                                            WHERE nip = '$nip'")
                     or die('Ada kesalahan pada query update: ' . mysqli_error($mysqli));

            if ($query) {
                header("location: ../../main.php?module=pasien&alert=2");
            }
        }
    }

    // ========================= DELETE =========================
    elseif ($_GET['act'] == 'delete') {
        if (isset($_GET['id'])) {
            $nip = $_GET['id'];

            $query = mysqli_query($mysqli, "DELETE FROM is_pasien WHERE nip = '$nip'")
                     or die('Ada kesalahan pada query delete: ' . mysqli_error($mysqli));

            if ($query) {
                header("location: ../../main.php?module=pasien&alert=3");
            }
        }
    }
}
?>
