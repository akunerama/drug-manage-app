<?php
require_once "../../config/database.php";

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$tanggal_awal = $bulan && $tahun ? "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01" : '';
$tanggal_akhir = $tanggal_awal ? date('Y-m-01', strtotime("+1 month", strtotime($tanggal_awal))) : '';

$where = "";
if (!empty($cari)) {
  $search = mysqli_real_escape_string($mysqli, $cari);
  $where .= " AND (o.kode_obat LIKE '%$search%' OR o.nama_obat LIKE '%$search%')";
}

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
    WHERE tanggal_masuk < '$tanggal_awal'
    GROUP BY kode_obat
  ) m ON o.kode_obat = m.kode_obat
  LEFT JOIN (
    SELECT kode_obat, SUM(jumlah_keluar) AS total_keluar
    FROM is_obat_keluar
    WHERE tanggal_keluar < '$tanggal_awal'
    GROUP BY kode_obat
  ) k ON o.kode_obat = k.kode_obat
  LEFT JOIN (
    SELECT kode_obat, SUM(jumlah_masuk) AS total_masuk_bulan_ini
    FROM is_view_masuk
    WHERE tanggal_masuk >= '$tanggal_awal' AND tanggal_masuk < '$tanggal_akhir'
    GROUP BY kode_obat
  ) m_bulan_ini ON o.kode_obat = m_bulan_ini.kode_obat
  LEFT JOIN (
    SELECT kode_obat, SUM(jumlah_keluar) AS pemakaian
    FROM is_obat_keluar
    WHERE tanggal_keluar >= '$tanggal_awal' AND tanggal_keluar < '$tanggal_akhir'
    GROUP BY kode_obat
  ) k_bulan_ini ON o.kode_obat = k_bulan_ini.kode_obat
  WHERE 1=1 $where
  ORDER BY o.nama_obat ASC
");

$nama_bulan = [
  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
  '04' => 'April', '05' => 'Mei', '06' => 'Juni',
  '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
  '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$judul_bulan = ($bulan && $tahun) ? "{$nama_bulan[$bulan]} $tahun" : "Semua Bulan";
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Laporan Pemakaian Obat</title>
  <style>
    body { font-family: Arial; font-size: 12px; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 40px; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 5px; text-align: center; }
    h2, h4 { text-align: center; margin: 0; }

    /* Tombol Cetak */
    .print-button {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #007bff;
      color: white;
      padding: 8px 16px;
      border: none;
      cursor: pointer;
      font-size: 14px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .print-button:hover {
      background-color: #0056b3;
    }

    @media print {
      .print-button {
        display: none;
      }
    }
  </style>
</head>
<body>
  <button class="print-button" onclick="window.print()">Cetak</button>

  <h2>Laporan Pemakaian Obat</h2>
  <h4>Periode: <?= $judul_bulan ?></h4>

  <table>
    <thead>
      <tr>
        <th>No.</th>
        <th>Nama Obat</th>
        <th>Satuan</th>
        <th>Persediaan Awal</th>
        <th>Dropping</th>
        <th>Jumlah</th>
        <th>Pemakaian</th>
        <th>Sisa Obat</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $data_ada = false;

      while ($row = mysqli_fetch_assoc($query)) {
        $persediaan_awal = (int)$row['total_masuk_sebelum'] - (int)$row['total_keluar_sebelum'];
        $dropping = (int)$row['dropping'];
        $pemakaian = (int)$row['pemakaian'];
        $jumlah = $persediaan_awal + $dropping;
        $sisa = $jumlah - $pemakaian;

        if ($persediaan_awal == 0 && $dropping == 0 && $pemakaian == 0) continue;

        $data_ada = true;

        echo "<tr>
          <td>{$no}</td>
          <td style='text-align:left'>{$row['nama_obat']}</td>
          <td>{$row['satuan']}</td>
          <td>{$persediaan_awal}</td>
          <td>{$dropping}</td>
          <td>{$jumlah}</td>
          <td>{$pemakaian}</td>
          <td>{$sisa}</td>
        </tr>";
        $no++;
      }

      if (!$data_ada) {
        echo "<tr><td colspan='8'><em>Tidak ada data untuk periode ini.</em></td></tr>";
      }
      ?>
    </tbody>
  </table>
</body>
</html>
