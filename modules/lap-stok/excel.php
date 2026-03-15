<?php
require_once "../../config/database.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Stok_Obat_" . date('Ymd') . ".xls");

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$where = "";

// Filter cari
if ($cari) {
  $search = mysqli_real_escape_string($mysqli, $cari);
  $where .= " AND (o.kode_obat LIKE '%$search%' OR o.nama_obat LIKE '%$search%')";
}

// Filter bulan dan tahun
if ($bulan != '') {
  $where .= " AND MONTH(m.tanggal_masuk) = '" . mysqli_real_escape_string($mysqli, $bulan) . "'";
}
if ($tahun != '') {
  $where .= " AND YEAR(m.tanggal_masuk) = '" . mysqli_real_escape_string($mysqli, $tahun) . "'";
}

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
") or die("Error: " . mysqli_error($mysqli));

// Format Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
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

$periode = '';
if ($bulan && $tahun) {
  $periode = 'Periode Bulan ' . date('F', mktime(0, 0, 0, $bulan, 10)) . ' ' . $tahun;
} else {
  $periode = 'Semua Data';
}

echo '<div class="title">Laporan Stok Obat</div>';
echo '<div class="subtitle">' . $periode . '</div>';

echo '<table>
  <tr>
    <th>NO</th>
    <th>KODE OBAT</th>
    <th>NAMA OBAT</th>
    <th>SATUAN</th>
    <th>SISA STOK</th>
    <th>JUMLAH MASUK</th>
    <th>JUMLAH KELUAR</th>
    <th>TANGGAL MASUK</th>
    <th>EXPIRED DATE</th>
  </tr>';

$data_obat = [];
while ($row = mysqli_fetch_assoc($query)) {
  $kode = $row['kode_obat'];
  if (!isset($data_obat[$kode])) {
    $data_obat[$kode] = [
      'nama_obat' => $row['nama_obat'],
      'satuan' => $row['satuan'],
      'sisa_stok' => $row['sisa_stok'],
      'detail' => []
    ];
  }

  $data_obat[$kode]['detail'][] = [
    'jumlah_masuk' => $row['jumlah_masuk'],
    'jumlah_keluar' => $row['jumlah_keluar'],
    'tanggal_masuk' => $row['tanggal_masuk'],
    'expired_date' => $row['expired_date']
  ];
}

$no = 1;
foreach ($data_obat as $kode_obat => $obat) {
  $rowspan = count($obat['detail']);
  $i = 0;

  foreach ($obat['detail'] as $detail) {
    echo "<tr>";
    if ($i === 0) {
      echo "
        <td class='center' rowspan='$rowspan'>{$no}</td>
        <td class='center' rowspan='$rowspan'>{$kode_obat}</td>
        <td rowspan='$rowspan'>{$obat['nama_obat']}</td>
        <td class='center' rowspan='$rowspan'>{$obat['satuan']}</td>
        <td class='right' rowspan='$rowspan'>{$obat['sisa_stok']}</td>
      ";
    }

    echo "
      <td class='right'>{$detail['jumlah_masuk']}</td>
      <td class='right'>{$detail['jumlah_keluar']}</td>
      <td class='center'>" . date('d-m-Y', strtotime($detail['tanggal_masuk'])) . "</td>
      <td class='center'>" . date('d-m-Y', strtotime($detail['expired_date'])) . "</td>
    </tr>";

    $i++;
  }
  $no++;
}

echo '</table>';
echo '<div style="margin-top: 20px; font-size: 12px;">
        <strong>Catatan:</strong> Laporan ini dihasilkan otomatis pada ' . date('d-m-Y H:i:s') . '
      </div>';
echo '</body></html>';
exit;
?>
