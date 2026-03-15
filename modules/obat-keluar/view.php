<section class="content-header">
  <h1>
    <i class="fa fa-sign-in icon-title"></i> Data Obat Keluar
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Alert -->
      <?php
      if (isset($_GET['alert'])) {
        if ($_GET['alert'] == 1) {
          echo "<div class='alert alert-success'>Data obat keluar berhasil disimpan.</div>";
        } elseif ($_GET['alert'] == 2) {
          echo "<div class='alert alert-danger'>Data obat keluar berhasil dihapus.</div>";
        }
      }
      ?>

      <!-- Filter Form -->
      <div class="box box-default">
        <div class="box-body">
          <a class="btn btn-primary btn-social pull-right" href="?module=form_obat_Keluar&form=add" title="Tambah Data" data-toggle="tooltip">
            <i class="fa fa-plus"></i> Tambah
          </a>
          <form method="GET" action="">
            <input type="hidden" name="module" value="obat_Keluar">
            <div class="form-inline">
              <label for="tanggal_awal">Filter Tanggal: </label>
              <input type="date" name="tanggal_awal" class="form-control" value="<?= $_GET['tanggal_awal'] ?? '' ?>">
              <label>s/d</label>
              <input type="date" name="tanggal_akhir" class="form-control" value="<?= $_GET['tanggal_akhir'] ?? '' ?>">
              <button type="submit" class="btn btn-sm btn-success">Filter</button>
              <a href="?module=obat_Keluar" class="btn btn-sm btn-default">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="box box-primary">
        <div class="box-body">
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center" style="width: 30px;">No.</th>
                <th class="center">Kode Transaksi</th>
                <th class="center">Tanggal Keluar</th>
                <th class="center">Nama Pasien</th>
                <th class="center">Diagnosa</th>
                <th class="center">Expired</th>
                <th class="center">Kode Obat</th>
                <th class="center">Nama Obat</th>
                <th class="center">Jumlah Keluar</th>
                <th class="center">Satuan</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $filter = '';
              if (!empty($_GET['tanggal_awal']) && !empty($_GET['tanggal_akhir'])) {
                $tgl_awal = $_GET['tanggal_awal'];
                $tgl_akhir = $_GET['tanggal_akhir'];
                $filter = "WHERE a.tanggal_Keluar BETWEEN '$tgl_awal' AND '$tgl_akhir'";
              }

              $query = mysqli_query($mysqli, "SELECT 
                  a.kode_transaksi,
                  a.tanggal_Keluar,
                  p.nama,
                  a.diagnosa,
                  a.expired_date,
                  a.kode_obat,
                  a.jumlah_Keluar,
                  b.nama_obat,
                  s.nama_satuan
                FROM is_obat_Keluar AS a
                INNER JOIN is_obat AS b ON a.kode_obat = b.kode_obat
                INNER JOIN is_pasien AS p ON a.nip = p.nip
                LEFT JOIN is_satuan AS s ON b.satuan = s.id_satuan
                $filter
                ORDER BY a.kode_transaksi DESC, a.kode_obat ASC")
                or die('Ada kesalahan pada query: ' . mysqli_error($mysqli));

              $data_transaksi = [];
              while ($row = mysqli_fetch_assoc($query)) {
                $kode = $row['kode_transaksi'];

                if (!isset($data_transaksi[$kode])) {
                  $data_transaksi[$kode] = [
                    'tanggal_Keluar' => $row['tanggal_Keluar'],
                    'nama'    => $row['nama'],
                    'diagnosa'       => $row['diagnosa'],
                    'obat'           => []
                  ];
                }

                $data_transaksi[$kode]['obat'][] = [
                  'expired_date'   => $row['expired_date'],
                  'kode_obat'      => $row['kode_obat'],
                  'nama_obat'      => $row['nama_obat'],
                  'jumlah_Keluar'  => $row['jumlah_Keluar'],
                  'satuan'         => $row['nama_satuan']
                ];
              }

              $no = 1;
              foreach ($data_transaksi as $kode_transaksi => $data) {
                $tanggal_Keluar = date("d-m-Y", strtotime($data['tanggal_Keluar']));
                $row_count = count($data['obat']);
                $row_index = 0;

                foreach ($data['obat'] as $obat) {
                  $expired = !empty($obat['expired_date']) ? date("d-m-Y", strtotime($obat['expired_date'])) : '-';
                  $nama_pasien = $data['nama'];
                  $diagnosa = $data['diagnosa'];

                  echo "<tr>";
                  if ($row_index === 0) {
                    echo "<td class='center' rowspan='$row_count'>$no</td>";
                    echo "<td class='center' rowspan='$row_count'>$kode_transaksi</td>";
                    echo "<td class='center' rowspan='$row_count'>$tanggal_Keluar</td>";
                    echo "<td class='center' rowspan='$row_count'>$nama_pasien</td>";
                    echo "<td class='center' rowspan='$row_count'>$diagnosa</td>";
                  }

                  echo "
                    <td class='center'>$expired</td>
                    <td class='center'>{$obat['kode_obat']}</td>
                    <td>{$obat['nama_obat']}</td>
                    <td class='text-right'>{$obat['jumlah_Keluar']}</td>
                    <td class='center'>{$obat['satuan']}</td>
                  </tr>";

                  $row_index++;
                }
                $no++;
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>