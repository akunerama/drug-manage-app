<?php
require_once "../../config/database.php";
require_once "../../libs/tcpdf/tcpdf.php";

// Filter
$where = "";
if (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
  $where .= " AND MONTH(m.tanggal_masuk) = '".$_GET['bulan']."'";
}
if (isset($_GET['tahun']) && !empty($_GET['tahun'])) {
  $where .= " AND YEAR(m.tanggal_masuk) = '".$_GET['tahun']."'";
}

// Create new PDF document
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Aplikasi Farmasi');
$pdf->SetTitle('Laporan Stok Obat');
$pdf->SetSubject('Stok Obat');

// Add a page
$pdf->AddPage();

// Content
$html = '<h2 style="text-align:center;">Laporan Stok Obat</h2>';
$html .= '<table border="1" cellpadding="4">
            <tr>
              <th>No</th>
              <th>Kode Obat</th>
              <th>Nama Obat</th>
              <th>Satuan</th>
              <th>Sisa Stok</th>
              <th>Jumlah Masuk</th>
              <th>Jumlah Keluar</th>
              <th>Tanggal Masuk</th>
              <th>Expired Date</th>
            </tr>';

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
  FROM is_obat_masuk m
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
  $html .= '<tr>
              <td>'.$no.'</td>
              <td>'.$row['kode_obat'].'</td>
              <td>'.$row['nama_obat'].'</td>
              <td>'.$row['satuan'].'</td>
              <td>'.$row['sisa_stok'].'</td>
              <td>'.$row['jumlah_masuk'].'</td>
              <td>'.$row['jumlah_keluar'].'</td>
              <td>'.date('d-m-Y', strtotime($row['tanggal_masuk'])).'</td>
              <td>'.date('d-m-Y', strtotime($row['expired_date'])).'</td>
            </tr>';
  $no++;
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Stok_Obat_'.date('Ymd').'.pdf', 'D');
?>