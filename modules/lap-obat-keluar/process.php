<?php
session_start();
ob_start();
require_once "../../config/database.php";

// Ambil data dari form
$tgl1     = $_GET['tgl_awal'];
$explode  = explode('-', $tgl1);
$tgl_awal = $explode[2] . "-" . $explode[1] . "-" . $explode[0];

$tgl2      = $_GET['tgl_akhir'];
$explode   = explode('-', $tgl2);
$tgl_akhir = $explode[2] . "-" . $explode[1] . "-" . $explode[0];

// Cek action (cetak atau download)
$action = $_GET['action'];

// Query data obat keluar
$query = mysqli_query($mysqli, "SELECT a.kode_transaksi, a.tanggal_Keluar, a.expired_date,
                                       a.kode_obat, a.jumlah_Keluar, 
                                       b.kode_obat, b.nama_obat, s.nama_satuan
                                FROM is_obat_Keluar as a 
                                INNER JOIN is_obat as b 
                                LEFT JOIN is_satuan as s ON b.satuan = s.id_satuan
                                ON a.kode_obat=b.kode_obat
                                WHERE a.tanggal_Keluar 
                                BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                ORDER BY a.kode_transaksi 
                                ASC") or die('Ada kesalahan pada query tampil Transaksi : ' . mysqli_error($mysqli));

// Jika tombol Cetak ditekan
if ($action == "lihat") {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>LAPORAN DATA OBAT KELUAR</title>
        <link rel="stylesheet" type="text/css" href="../../assets/css/laporan.css" />

        <style>
            @media print {

                .print-button,
                .download-button {
                    display: none;
                    position: absolute;
                    top: 10px;
                    right: 20px;
                    padding: 8px 12px;
                    background: rgb(202, 216, 3);
                    color: white;
                    border: none;
                    cursor: pointer;
                    font-size: 14px;
                    border-radius: 5px;
                }

                .print-button:hover {
                    background: #218838;
                }

                @page {
                    margin: 0;
                }

                body {
                    margin: 1cm;
                }

                /* .print-button {
                    position: absolute;
                    top: 10px;
                    right: 20px;
                    padding: 8px 12px;
                    background: rgb(202, 216, 3);
                    color: white;
                    border: none;
                    cursor: pointer;
                    font-size: 14px;
                    border-radius: 5px;
                } */
            }
        </style>
        <script>
            function printPage() {
                window.print(); // Memanggil fungsi print browser
            }
        </script>

    </head>

    <body>
        <div id="title">LAPORAN DATA OBAT KELUAR</div>
        <div id="title-tanggal">Tanggal <?php echo $tgl_awal; ?> s.d. <?php echo $tgl_akhir; ?></div>

        <button class="print-button" onclick="printPage()">🖨️ Print</button>

        <hr><br>
        <div id="isi">
            <table width="100%" border="1">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>KODE TRANSAKSI</th>
                        <th>TANGGAL KELUAR</th>
                        <th>EXPIRED DATE</th>
                        <th>KODE OBAT</th>
                        <th>NAMA OBAT</th>
                        <th>JUMLAH KELUAR</th>
                        <th>SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($query)) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$data['kode_transaksi']}</td>
                                <td>{$data['tanggal_Keluar']}</td>
                                <td>{$data['expired_date']}</td>
                                <td>{$data['kode_obat']}</td>
                                <td>{$data['nama_obat']}</td>
                                <td>{$data['jumlah_Keluar']}</td>
                                <td>{$data['nama_satuan']}</td>
                              </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- <script>window.print();</script> -->
    </body>

    </html>
<?php
    exit();
}

// Jika tombol Download ditekan
if ($action == "download") {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Obat_Masuk_$tgl_awal-$tgl_akhir.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";
    echo "<tr>
            <th>NO.</th>
            <th>KODE TRANSAKSI</th>
            <th>TANGGAL KELUAR</th>
            <th>EXPIRED DATE</th>
            <th>KODE OBAT</th>
            <th>NAMA OBAT</th>
            <th>JUMLAH KELUAR</th>
            <th>SATUAN</th>
          </tr>";

    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$data['kode_transaksi']}</td>
                <td>{$data['tanggal_Keluar']}</td>
                <td>{$data['expired_date']}</td>
                <td>{$data['kode_obat']}</td>
                <td>{$data['nama_obat']}</td>
                <td>{$data['jumlah_Keluar']}</td>
                <td>{$data['nama_satuan']}</td>
              </tr>";
        $no++;
    }
    echo "</table>";
    exit();
}
?>