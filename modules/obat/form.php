<?php  
// fungsi untuk pengecekan tampilan form
if ($_GET['form']=='add') { ?> 

  <!-- tampilan form add data -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Input Obat
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda </a></li>
      <li><a href="?module=obat"> Obat </a></li>
      <li class="active"> Tambah </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" action="modules/obat/proses.php?act=insert" method="POST">
            <div class="box-body">

              <?php  
              // kode obat otomatis
              $query_id = mysqli_query($mysqli, "SELECT RIGHT(kode_obat,6) as kode FROM is_obat ORDER BY kode_obat DESC LIMIT 1")
                          or die('Ada kesalahan pada query tampil kode_obat : '.mysqli_error($mysqli));
              $count = mysqli_num_rows($query_id);
              $kode = ($count != 0) ? mysqli_fetch_assoc($query_id)['kode'] + 1 : 1;
              $buat_id   = str_pad($kode, 6, "0", STR_PAD_LEFT);
              $kode_obat = "B$buat_id";

              // ambil data satuan
              $query_satuan = mysqli_query($mysqli, "SELECT id_satuan, nama_satuan FROM is_satuan ORDER BY nama_satuan ASC")
                              or die('Ada kesalahan pada query satuan: '.mysqli_error($mysqli));
              ?>

              <div class="form-group">
                <label class="col-sm-2 control-label">Kode Obat</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="kode_obat" value="<?php echo $kode_obat; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama obat</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama_obat" autocomplete="off" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Satuan</label>
                <div class="col-sm-5">
                  <select class="chosen-select" name="satuan" data-placeholder="-- Pilih --" autocomplete="off" required>
                    <option value=""></option>
                    <?php while ($satuan = mysqli_fetch_assoc($query_satuan)) : ?>
                      <option value="<?php echo $satuan['id_satuan']; ?>"><?php echo $satuan['nama_satuan']; ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=obat" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div>
      </div>
    </div>
  </section>

<?php
} elseif ($_GET['form']=='edit') { 
  if (isset($_GET['id'])) {
      $query = mysqli_query($mysqli, "SELECT kode_obat, nama_obat, satuan FROM is_obat WHERE kode_obat='$_GET[id]'") 
                or die('Ada kesalahan pada query tampil Data obat : '.mysqli_error($mysqli));
      $data  = mysqli_fetch_assoc($query);

      // ambil data satuan
      $query_satuan = mysqli_query($mysqli, "SELECT id_satuan, nama_satuan FROM is_satuan ORDER BY nama_satuan ASC")
                      or die('Ada kesalahan pada query satuan: '.mysqli_error($mysqli));
  }
?>

  <!-- tampilan form edit data -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Ubah Obat
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda </a></li>
      <li><a href="?module=obat"> Obat </a></li>
      <li class="active"> Ubah </li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <form role="form" class="form-horizontal" action="modules/obat/proses.php?act=update" method="POST">
            <div class="box-body">

              <div class="form-group">
                <label class="col-sm-2 control-label">Kode Obat</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="kode_obat" value="<?php echo $data['kode_obat']; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama obat</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama_obat" autocomplete="off" value="<?php echo $data['nama_obat']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Satuan</label>
                <div class="col-sm-5">
                  <select class="chosen-select" name="satuan" data-placeholder="-- Pilih --" autocomplete="off" required>
                    <option value=""></option>
                    <?php while ($satuan = mysqli_fetch_assoc($query_satuan)) : ?>
                      <option value="<?php echo $satuan['id_satuan']; ?>" <?php if ($data['satuan'] == $satuan['id_satuan']) echo 'selected'; ?>>
                        <?php echo $satuan['nama_satuan']; ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=obat" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div>
      </div>
    </div>
  </section>

<?php } ?>
