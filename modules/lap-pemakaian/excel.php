<?php
require_once "../../config/database.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Stok_Obat_" . date('Ymd') . ".xls");

// Ambil parameter filter
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

// Tanggal awal dan akhir bulan
$tanggal_awal_bulan_ini = $bulan && $tahun ? "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01" : '';
$tanggal_awal_bulan_depan = $tanggal_awal_bulan_ini ? date('Y-m-01', strtotime("+1 month", strtotime($tanggal_awal_bulan_ini))) : '';

// Filter pencarian
$where = "";
if ($cari) {
    $search = mysqli_real_escape_string($mysqli, $cari);
    $where .= " AND (o.kode_obat LIKE '%$search%' OR o.nama_obat LIKE '%$search%')";
}

// Query data laporan
$query = mysqli_query($mysqli, "
    SELECT 
        o.kode_obat,
        o.nama_obat,
        o.satuan,

        IFNULL(m.total_masuk, 0) AS total_masuk_sebelum,
        IFNULL(k.total_keluar, 0) AS total_keluar_sebelum,

        IFNULL(m_bulan_ini.total_masuk_bulan_ini, 0) AS dropping,
        IFNULL(k_bulan_ini.pemakaian, 0) AS pemakaian

    FROM is_obat o

    LEFT JOIN (
        SELECT kode_obat, SUM(jumlah_masuk) AS total_masuk
        FROM is_view_masuk
        WHERE tanggal_masuk < '$tanggal_awal_bulan_ini'
        GROUP BY kode_obat
    ) m ON o.kode_obat = m.kode_obat

    LEFT JOIN (
        SELECT kode_obat, SUM(jumlah_keluar) AS total_keluar
        FROM is_obat_keluar
        WHERE tanggal_keluar < '$tanggal_awal_bulan_ini'
        GROUP BY kode_obat
    ) k ON o.kode_obat = k.kode_obat

    LEFT JOIN (
        SELECT kode_obat, SUM(jumlah_masuk) AS total_masuk_bulan_ini
        FROM is_view_masuk
        WHERE tanggal_masuk >= '$tanggal_awal_bulan_ini' AND tanggal_masuk < '$tanggal_awal_bulan_depan'
        GROUP BY kode_obat
    ) m_bulan_ini ON o.kode_obat = m_bulan_ini.kode_obat

    LEFT JOIN (
        SELECT kode_obat, SUM(jumlah_keluar) AS pemakaian
        FROM is_obat_keluar
        WHERE tanggal_keluar >= '$tanggal_awal_bulan_ini' AND tanggal_keluar < '$tanggal_awal_bulan_depan'
        GROUP BY kode_obat
    ) k_bulan_ini ON o.kode_obat = k_bulan_ini.kode_obat

    WHERE 1=1 $where
    ORDER BY o.nama_obat ASC
") or die("Error: " . mysqli_error($mysqli));

// Output HTML untuk Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <style>
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; border: 1px solid #ddd; padding: 8px; }
        td { border: 1px solid #ddd; padding: 8px; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>';

echo '<div class="title">Persediaan Obat Poli</div>';
echo '<div class="subtitle">Laporan Stok Obat Bulanan</div>';

echo '<table>
        <tr>
            <td><strong>Bulan</strong></td>
            <td>' . ($bulan ? date('F', mktime(0, 0, 0, $bulan, 10)) : '-') . '</td>
            <td><strong>Tahun</strong></td>
            <td>' . ($tahun ?: '-') . '</td>
        </tr>
      </table><br>';

// Header tabel
echo '<table>
        <tr>
            <th>NO</th>
            <th>OBAT</th>
            <th>SATUAN</th>
            <th>PERSEDIAAN AWAL</th>
            <th>DROPPING</th>
            <th>JUMLAH</th>
            <th>PEMAKAIAN</th>
            <th>SISA OBAT</th>
        </tr>';

$no = 1;
$data_ada = false;
while ($row = mysqli_fetch_assoc($query)) {
    $persediaan_awal = (int)$row['total_masuk_sebelum'] - (int)$row['total_keluar_sebelum'];
    $dropping = (int)$row['dropping'];
    $pemakaian = (int)$row['pemakaian'];
    $jumlah = $persediaan_awal + $dropping;
    $sisa = $jumlah - $pemakaian;

    // Jika tidak ada stok sama sekali, skip baris ini
    if ($persediaan_awal == 0 && $dropping == 0 && $pemakaian == 0) {
        continue;
    }

    $data_ada = true;

    echo "<tr>
        <td class='center'>{$no}</td>
        <td>{$row['nama_obat']}</td>
        <td class='center'>{$row['satuan']}</td>
        <td class='right'>{$persediaan_awal}</td>
        <td class='right'>{$dropping}</td>
        <td class='right'>{$jumlah}</td>
        <td class='right'>{$pemakaian}</td>
        <td class='right'>{$sisa}</td>
    </tr>";
    $no++;
}

if (!$data_ada) {
    echo "<tr><td colspan='8' class='center'><em>Tidak ada data untuk bulan ini.</em></td></tr>";
}

echo '</table>';

echo '<div style="margin-top: 20px; font-size: 12px;">
        <strong>Catatan:</strong> Laporan ini dihasilkan secara otomatis pada ' . date('d-m-Y H:i:s') . '
      </div>';

echo '</body></html>';
exit;
?>
