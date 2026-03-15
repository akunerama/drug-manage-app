<?php
session_start();
ob_start();

// Panggil koneksi database.php
require_once "../../config/database.php";

// Ambil tanggal awal dan akhir dari GET
$tgl1     = $_GET['tgl_awal'];
$explode  = explode('-', $tgl1);
$tgl_awal = $explode[2] . "-" . $explode[1] . "-" . $explode[0];

$tgl2      = $_GET['tgl_akhir'];
$explode   = explode('-', $tgl2);
$tgl_akhir = $explode[2] . "-" . $explode[1] . "-" . $explode[0];

// Set header agar file dapat diunduh sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Stok_Obat_$tgl_awal-$tgl_akhir.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query data obat masuk dalam rentang tanggal yang dipilih
$query = mysqli_query($mysqli, "SELECT a.kode_transaksi, a.tanggal_Masuk, a.expired_date, 
                                       a.kode_obat, a.jumlah_Masuk, b.kode_obat, 
                                       b.nama_obat, s.nama_satuan 
                                FROM is_view_masuk as a 
                                INNER JOIN is_obat as b ON a.kode_obat = b.kode_obat 
                                LEFT JOIN is_satuan as s ON b.satuan = s.id_satuan
                                WHERE a.tanggal_Masuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                ORDER BY a.kode_transaksi ASC")
        or die('Ada kesalahan pada query tampil Transaksi: ' . mysqli_error($mysqli));

// Mulai output data ke Excel
echo "<table border='1'>";
echo "<tr>
        <th>NO.</th>
        <th>KODE TRANSAKSI</th>
        <th>TANGGAL MASUK</th>
        <th>EXPIRED DATE</th>
        <th>KODE OBAT</th>
        <th>NAMA OBAT</th>
        <th>JUMLAH MASUK</th>
        <th>SATUAN</th>
      </tr>";

$no = 1;
while ($data = mysqli_fetch_assoc($query)) {
    // Konversi tanggal masuk
    $tanggal = $data['tanggal_Masuk'];
    $exp     = explode('-', $tanggal);
    $tanggal_Masuk = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

    // Konversi expired_date jika ada
    $expired_date = ($data['expired_date'] != '0000-00-00') ? date("d-m-Y", strtotime($data['expired_date'])) : "-";

    echo "<tr>
            <td>{$no}</td>
            <td>{$data['kode_transaksi']}</td>
            <td>{$tanggal_Masuk}</td>
            <td>{$expired_date}</td>
            <td>{$data['kode_obat']}</td>
            <td>{$data['nama_obat']}</td>
            <td>{$data['jumlah_Masuk']}</td>
            <td>{$data['nama_satuan']}</td>
          </tr>";
    $no++;
}
echo "</table>";
?>
