<?php  
// Form Tambah Data Pasien
if ($_GET['form'] == 'add') { ?> 

  <section class="content-header">
    <h1><i class="fa fa-edit icon-title"></i> Input Pasien</h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda</a></li>
      <li><a href="?module=pasien"> Pasien</a></li>
      <li class="active"> Tambah</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">

        <!-- Tampilkan alert jika ada duplikat NIP -->
        <?php if (isset($_GET['alert']) && $_GET['alert'] == 'duplikat') { ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Gagal!</h4>
            NIP sudah terdaftar. Silakan masukkan NIP yang berbeda.
          </div>
        <?php } ?>

        <div class="box box-primary">
          <form role="form" class="form-horizontal" action="modules/pasien/proses.php?act=insert" method="POST">
            <div class="box-body">

              <div class="form-group">
                <label class="col-sm-2 control-label">NIP</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nip" autocomplete="off" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama Pasien</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama" autocomplete="off" required>
                </div>
              </div>

            </div>

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=pasien" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

<?php 
// Form Edit Data Pasien
} elseif ($_GET['form'] == 'edit') {
  if (isset($_GET['id'])) {
      $query = mysqli_query($mysqli, "SELECT nip, nama FROM is_pasien WHERE nip='$_GET[id]'")
                or die('Ada kesalahan pada query: ' . mysqli_error($mysqli));
      $data = mysqli_fetch_assoc($query);
  }
?>

  <section class="content-header">
    <h1><i class="fa fa-edit icon-title"></i> Ubah Pasien</h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda</a></li>
      <li><a href="?module=pasien"> Pasien</a></li>
      <li class="active"> Ubah</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <form role="form" class="form-horizontal" action="modules/pasien/proses.php?act=update" method="POST">
            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-2 control-label">NIP</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nip" value="<?php echo $data['nip']; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama Pasien</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama" autocomplete="off" value="<?php echo $data['nama']; ?>" required>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=pasien" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

<?php } ?>