<!-- Content Header -->
<section class="content-header">
  <h1>
    <i class="fa fa-file-text-o icon-title"></i> Laporan Pemakaian Obat

    <div class="pull-right">
      <?php
        $query_string = http_build_query([
          'bulan' => isset($_GET['bulan']) ? $_GET['bulan'] : '',
          'tahun' => isset($_GET['tahun']) ? $_GET['tahun'] : '',
          'cari' => isset($_GET['cari']) ? $_GET['cari'] : ''
        ]);
      ?>
      <a class="btn btn-success btn-social" href="modules/lap-pemakaian/excel.php?<?php echo $query_string; ?>" target="_blank">
        <i class="fa fa-file-excel-o"></i> Excel
      </a>
      <a class="btn btn-primary btn-social" onclick="printReport()">
        <i class="fa fa-print"></i> Print
      </a>
    </div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body">

          <!-- Form Filter -->
          <form method="GET" action="" class="form-inline" style="margin-bottom: 20px;">
            <input type="hidden" name="module" value="lap_pemakaian">

            <!-- Search Field -->
            <div class="form-group">
              <label>Cari</label>
              <input type="text" name="cari" class="form-control" placeholder="Nama/Kode Obat"
                value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
            </div>

            <div class="form-group" style="margin-left:10px;">
              <label>Bulan</label>
              <select name="bulan" class="form-control">
                <option value="">-- Semua Bulan --</option>
                <?php
                $bulan = [
                  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                  '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                  '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                  '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                foreach ($bulan as $key => $value) {
                  $selected = (isset($_GET['bulan']) && $_GET['bulan'] == $key) ? 'selected' : '';
                  echo "<option value='$key' $selected>$value</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group" style="margin-left:10px;">
              <label>Tahun</label>
              <select name="tahun" class="form-control">
                <option value="">-- Semua Tahun --</option>
                <?php
                $current_year = date('Y');
                for ($i = $current_year; $i >= 2020; $i--) {
                  $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $i) ? 'selected' : '';
                  echo "<option value='$i' $selected>$i</option>";
                }
                ?>
              </select>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-left:10px;">
              <i class="fa fa-filter"></i> Filter
            </button>
            <a href="?module=lap_pemakaian" class="btn btn-default" style="margin-left:10px;">
              <i class="fa fa-refresh"></i> Reset
            </a>
          </form>

          <!-- Table -->
          <table id="dataTables3" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center">No.</th>
                <th class="center">Obat</th>
                <th class="center">Satuan</th>
                <th class="center">Persediaan Awal</th>
                <th class="center">Dropping</th>
                <th class="center">Jumlah</th>
                <th class="center">Pemakaian</th>
                <th class="center">Sisa Obat</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;

              $bulan = $_GET['bulan'] ?? '';
              $tahun = $_GET['tahun'] ?? '';
              $tanggal_awal_bulan_ini = $bulan && $tahun ? "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01" : '';
              $tanggal_awal_bulan_depan = $tanggal_awal_bulan_ini ? date('Y-m-01', strtotime("+1 month", strtotime($tanggal_awal_bulan_ini))) : '';

              $where = "";
              if (isset($_GET['cari']) && !empty($_GET['cari'])) {
                $search = mysqli_real_escape_string($mysqli, $_GET['cari']);
                $where .= " AND (o.kode_obat LIKE '%$search%' OR o.nama_obat LIKE '%$search%')";
              }

              $query = mysqli_query($mysqli, "
                SELECT 
                  o.kode_obat,
                  o.nama_obat,
                  s.nama_satuan AS satuan,

                  IFNULL(m.total_masuk, 0) AS total_masuk_sebelum,
                  IFNULL(k.total_keluar, 0) AS total_keluar_sebelum,

                  IFNULL(m_bulan_ini.total_masuk_bulan_ini, 0) AS dropping,
                  IFNULL(k_bulan_ini.pemakaian, 0) AS pemakaian

                FROM is_obat o
                LEFT JOIN is_satuan s ON o.satuan = s.id_satuan

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
              ") or die(mysqli_error($mysqli));

              $data_ada = false;
              while ($row = mysqli_fetch_assoc($query)) {
                $persediaan_awal = (int)$row['total_masuk_sebelum'] - (int)$row['total_keluar_sebelum'];
                $dropping = (int)$row['dropping'];
                $pemakaian = (int)$row['pemakaian'];
                $jumlah = $persediaan_awal + $dropping;
                $sisa = $jumlah - $pemakaian;

                if ($persediaan_awal == 0 && $dropping == 0 && $pemakaian == 0) {
                  continue;
                }

                $data_ada = true;

                echo "<tr>
                  <td class='center'>{$no}</td>
                  <td>{$row['nama_obat']}</td>
                  <td class='center'>{$row['satuan']}</td>
                  <td class='text-right'>{$persediaan_awal}</td>
                  <td class='text-right'>{$dropping}</td>
                  <td class='text-right'>{$jumlah}</td>
                  <td class='text-right'>{$pemakaian}</td>
                  <td class='text-right'>{$sisa}</td>
                </tr>";

                $no++;
              }

              if (!$data_ada) {
                echo "<tr><td colspan='8' class='text-center'><em>Tidak ada data untuk bulan ini</em></td></tr>";
              }
              ?>
            </tbody>
          </table>

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!--/.col -->
  </div> <!-- /.row -->
</section><!-- /.content -->

<script>
function printReport() {
  const searchParams = new URLSearchParams(window.location.search);
  let printUrl = 'modules/lap-pemakaian/cetak.php';
  
  ['cari', 'bulan', 'tahun'].forEach(key => {
    if (searchParams.has(key)) {
      printUrl += (printUrl.includes('?') ? '&' : '?') + key + '=' + encodeURIComponent(searchParams.get(key));
    }
  });
  
  window.open(printUrl, '_blank');
}

$(document).ready(function () {
  $('#dataTables3').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "pageLength": 10,
    "language": {
      "search": "Filter cepat:",
      "zeroRecords": "Tidak ada data yang ditemukan",
      "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      "infoEmpty": "Tidak ada data yang tersedia",
      "infoFiltered": "(difilter dari _MAX_ total data)"
    }
  });
});
</script>
