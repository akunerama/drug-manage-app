<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="fa fa-home icon-title"></i> BPK Jatim Poli Umum
  </h1>
  <ol class="breadcrumb">
    <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-lg-12 col-xs-12">
      <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p style="font-size:15px">
          <i class="icon fa fa-user"></i> Selamat datang
          <strong><?php echo $_SESSION['nama_user']; ?></strong> di Aplikasi Persediaan Obat Poli Umum
        </p>
      </div>
    </div>
  </div>

  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="clearfix visible-lg-block"></div>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div style="background-color:#00c0ef;color:#fff" class="small-box">
        <div class="inner">
          <?php
          // fungsi query untuk menampilkan data dari tabel obat
          $query = mysqli_query($mysqli, "SELECT COUNT(kode_obat) as jumlah FROM is_obat")
            or die('Ada kesalahan pada query tampil Data Obat: ' . mysqli_error($mysqli));

          // tampilkan data
          $data = mysqli_fetch_assoc($query);
          ?>
          <h3><?php echo $data['jumlah']; ?></h3>
          <p>Data Obat</p>
        </div>
        <div class="icon">
          <i class="fa fa-folder"></i>
        </div>
        <?php
        if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Gudang') { ?>
          <a href="?module=form_obat&form=add" class="small-box-footer" title="Tambah Data" data-toggle="tooltip"><i
              class="fa fa-plus"></i></a>
        <?php
        } else { ?>
          <a class="small-box-footer"><i class="fa"></i></a>
        <?php
        }
        ?>
      </div>
    </div><!-- ./col -->

    <?php
    if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Gudang') {
    ?>
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div style="background-color:#00a65a;color:#fff" class="small-box">
          <div class="inner">
            <?php
            // fungsi query untuk menampilkan data dari tabel obat keluar
            $query = mysqli_query($mysqli, "SELECT COUNT(kode_transaksi) as jumlah FROM is_obat_masuk")
              or die('Ada kesalahan pada query tampil Data obat Masuk: ' . mysqli_error($mysqli));

            // tampilkan data
            $data1 = mysqli_fetch_assoc($query);
            ?>
            <h3><?php echo $data1['jumlah']; ?></h3>
            <p>Data Obat Masuk</p>
          </div>
          <div class="icon">
            <i class="fa fa-sign-in"></i>
          </div>
          <a href="?module=form_obat_masuk&form=add" class="small-box-footer" title="Tambah Data" data-toggle="tooltip"><i
              class="fa fa-plus"></i></a>
        </div>
      </div><!-- ./col -->
    <?php
    }
    ?>


    <?php
    if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Gudang') {
    ?>
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div style="background-color:#dd4b39;color:#fff" class="small-box">
          <div class="inner">
            <?php
            // fungsi query untuk menampilkan data dari tabel obat keluar
            $query = mysqli_query($mysqli, "SELECT COUNT(kode_transaksi) as jumlah FROM is_obat_keluar")
              or die('Ada kesalahan pada query tampil Data obat Keluar: ' . mysqli_error($mysqli));

            // tampilkan data
            $data1 = mysqli_fetch_assoc($query);
            ?>
            <h3><?php echo $data1['jumlah']; ?></h3>
            <p>Data Obat Keluar</p>
          </div>
          <div class="icon">
            <i class="fa fa-sign-in"></i>
          </div>
          <a href="?module=form_obat_Keluar&form=add" class="small-box-footer" title="Tambah Data"
            data-toggle="tooltip"><i class="fa fa-plus"></i></a>
        </div>
      </div><!-- ./col -->
    <?php
    }
    ?>


    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div style="background-color:#00a65a;color:#fff" class="small-box">
        <div class="inner">
          <?php
          // fungsi query untuk menampilkan data dari tabel obat masuk
          $query = mysqli_query($mysqli, "SELECT COUNT(kode_transaksi) as jumlah FROM is_obat_masuk")
            or die('Ada kesalahan pada query tampil Data obat Masuk: ' . mysqli_error($mysqli));
          // tampilkan data
          $data = mysqli_fetch_assoc($query);
          ?>
          <h3><?php echo $data['jumlah']; ?></h3>
          <p>Laporan Obat Masuk</p>
        </div>
        <div class="icon">
          <i class="fa fa-clone"></i>
        </div>
        <a href="?module=lap_obat_masuk" class="small-box-footer" title="Cetak Laporan" data-toggle="tooltip"><i
            class="fa fa-print"></i></a>
      </div>
    </div><!-- ./col -->
  </div><!-- /.row -->
</section><!-- /.content -->

<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div style="background-color:#f39c12;color:#fff" class="small-box">
    <div class="inner">
      <?php
      // fungsi query untuk menampilkan data dari tabel obat
      $query = mysqli_query($mysqli, "SELECT COUNT(kode_obat) as jumlah FROM is_obat")
        or die('Ada kesalahan pada query tampil Data Obat: ' . mysqli_error($mysqli));

      // tampilkan data
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah']; ?></h3>
      <p>Laporan Stok Obat</p>
    </div>
    <div class="icon">
      <i class="fa fa-file-text-o"></i>
    </div>
    <a href="?module=lap_stok" class="small-box-footer" title="Cetak Laporan" data-toggle="tooltip"><i
        class="fa fa-print"></i></a>
  </div>
</div><!-- ./col -->

<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div style="background-color:#dd4b39;color:#fff" class="small-box">
    <div class="inner">
      <?php
      // fungsi query untuk menampilkan data dari tabel obat keluar
      $query = mysqli_query($mysqli, "SELECT COUNT(kode_transaksi) as jumlah FROM is_obat_keluar")
        or die('Ada kesalahan pada query tampil Data obat Keluar: ' . mysqli_error($mysqli));

      // tampilkan data
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah']; ?></h3>
      <p>Laporan Obat Keluar</p>
    </div>
    <div class="icon">
      <i class="fa fa-clone"></i>
    </div>
    <a href="?module=lap_obat_Keluar" class="small-box-footer" title="Cetak Laporan" data-toggle="tooltip"><i
        class="fa fa-print"></i></a>
  </div>
</div><!-- ./col -->

<!-- Expired Date -->
<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div class="small-box" style="background-color:#dd4b39; color:#fff;">
    <div class="inner">
      <?php
      // Query untuk menghitung obat yang mendekati expired (kurang dari 1 bulan) atau sudah expired
      $query = mysqli_query(
        $mysqli,
        "SELECT COUNT(kode_obat) as jumlah_masuk FROM is_obat_masuk WHERE expired_date <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)"
      ) or die('Ada kesalahan pada query tampil Data Obat Expired: ' . mysqli_error($mysqli));

      // Tampilkan data
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah_masuk']; ?></h3>
      <p>Obat Expired / Hampir Expired</p>
    </div>
    <div class="icon">
      <i class="fa fa-exclamation-triangle"></i>
    </div>
    <a href="?module=expired_date" class="small-box-footer" title="Lihat Detail" data-toggle="tooltip">
      <i class="fa fa-eye"></i>
    </a>
  </div>
</div><!-- ./col -->

<!-- Obat Stok Rendah -->
<div class="col-lg-3 col-xs-6">
  <div class="small-box" style="background-color:#f39c12; color:#fff;">
    <div class="inner">
      <?php
      // Query untuk menghitung obat dengan stok kurang dari 20
      $query = mysqli_query($mysqli, "SELECT COUNT(kode_obat) as jumlah FROM is_obat WHERE stok < 20");
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah']; ?></h3>
      <p>Stok Obat Menipis</p>
    </div>
    <div class="icon">
      <i class="fa fa-exclamation-circle"></i>
    </div>
    <a href="?module=stok" class="small-box-footer" title="Lihat Stok Obat">
      <i class="fa fa-eye"></i>
    </a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div style="background-color:#00a65a; color:#fff" class="small-box">
    <div class="inner">
      <?php
      // fungsi query untuk menampilkan jumlah data pasien
      $query = mysqli_query($mysqli, "SELECT COUNT(nip) as jumlah FROM is_pasien")
        or die('Ada kesalahan pada query tampil Data Pasien: ' . mysqli_error($mysqli));

      // tampilkan hasil query
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah']; ?></h3>
      <p>Data Pasien</p>
    </div>
    <div class="icon">
      <i class="fa fa-user"></i>
    </div>
    <a href="?module=pasien" class="small-box-footer" title="Lihat Data Pasien" data-toggle="tooltip">
      <i class="fa fa-arrow-circle-right"></i>
    </a>
  </div>
</div><!-- ./col -->

<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div style="background-color:#00a65a; color:#fff" class="small-box">
    <div class="inner">
      <?php
      // fungsi query untuk menampilkan jumlah data pasien
      $query = mysqli_query($mysqli, "SELECT COUNT(id_satuan) as jumlah FROM is_satuan")
        or die('Ada kesalahan pada query tampil Data Pasien: ' . mysqli_error($mysqli));

      // tampilkan hasil query
      $data = mysqli_fetch_assoc($query);
      ?>
      <h3><?php echo $data['jumlah']; ?></h3>
      <p>Data Satuan</p>
    </div>
    <div class="icon">
      <i class="fa fa-user"></i>
    </div>
    <a href="?module=satuan" class="small-box-footer" title="Lihat Data Satuan" data-toggle="tooltip">
      <i class="fa fa-arrow-circle-right"></i>
    </a>
  </div>
</div><!-- ./col -->



</div><!-- /.row -->


</section><!-- /.content -->