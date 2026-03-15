<?php
require_once "../../config/database.php";

// Filter
$where = "";
if (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
  $where .= " AND MONTH(m.tanggal_masuk) = '".$_GET['bulan']."'";
}
if (isset($_GET['tahun']) && !empty($_GET['tahun'])) {
  $where .= " AND YEAR(m.tanggal_masuk) = '".$_GET['tahun']."'";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Cetak Laporan Stok Obat</title>
  <style>
    body { font-family: Arial; font-size: 12px; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 40px; }
    th, td { border: 1px solid #000; padding: 5px; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* Tombol Cetak */
    .print-button {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #4CAF50;
      color: white;
      padding: 8px 16px;
      border: none;
      cursor: pointer;
      font-size: 14px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .print-button:hover {
      background-color: #45a049;
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

  <h2 class="text-center">Laporan Stok Obat</h2>
  
  <table>
    <thead>
      <tr>
        <th class="text-center">No.</th>
        <th class="text-center">Kode Obat</th>
        <th class="text-center">Nama Obat</th>
        <th class="text-center">Satuan</th>
        <th class="text-center">Sisa Stok</th>
        <th class="text-center">Jumlah Masuk</th>
        <th class="text-center">Jumlah Keluar</th>
        <th class="text-center">Tanggal Masuk</th>
        <th class="text-center">Expired Date</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $query = mysqli_query($mysqli, "
        SELECT 
          o.kode_obat,
          o.nama_obat,
          o.satuan,
          o.stok AS sisa_stok,
          m.jumlah_masuk,
          IFNULL(k.total_keluar, 0) AS jumlah_keluar,
          m.tanggal_masuk,
          m.expired_date
        FROM is_view_masuk m
        INNER JOIN is_obat o ON m.kode_obat = o.kode_obat
        LEFT JOIN (
          SELECT kode_obat, expired_date, SUM(jumlah_keluar) AS total_keluar
          FROM is_obat_keluar
          GROUP BY kode_obat, expired_date
        ) k ON m.kode_obat = k.kode_obat AND m.expired_date = k.expired_date
        WHERE 1=1 $where
        ORDER BY o.nama_obat ASC, m.tanggal_masuk ASC
      ") or die(mysqli_error($mysqli));

      while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>
                <td class='text-center'>$no</td>
                <td class='text-center'>".$row['kode_obat']."</td>
                <td>".$row['nama_obat']."</td>
                <td class='text-center'>".$row['satuan']."</td>
                <td class='text-right'>".$row['sisa_stok']."</td>
                <td class='text-right'>".$row['jumlah_masuk']."</td>
                <td class='text-right'>".$row['jumlah_keluar']."</td>
                <td class='text-center'>".date('d-m-Y', strtotime($row['tanggal_masuk']))."</td>
                <td class='text-center'>".date('d-m-Y', strtotime($row['expired_date']))."</td>
              </tr>";
        $no++;
      }
      ?>
    </tbody>
  </table>
</body>
</html>
