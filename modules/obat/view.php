<?php  
// fungsi untuk menampilkan pesan
if (empty($_GET['alert'])) {
  echo "";
} elseif ($_GET['alert'] == 1) {
  echo "<div class='alert alert-success alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Sukses!</h4>
          Data obat baru berhasil disimpan.
        </div>";
} elseif ($_GET['alert'] == 2) {
  echo "<div class='alert alert-success alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Sukses!</h4>
          Data obat berhasil diubah.
        </div>";
} elseif ($_GET['alert'] == 3) {
  echo "<div class='alert alert-success alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Sukses!</h4>
          Data obat berhasil dihapus.
        </div>";
}
?>

<section class="content-header">
  <h1>
    <i class="fa fa-folder-o icon-title"></i> Data Obat
    <a class="btn btn-primary btn-social pull-right" href="?module=form_obat&form=add" title="Tambah Data" data-toggle="tooltip">
      <i class="fa fa-plus"></i> Tambah
    </a>
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body">
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center">No.</th>
                <th class="center">Kode Obat</th>
                <th class="center">Nama Obat</th>
                <th class="center">Satuan</th>
                <th class="center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php  
              $no = 1;
              // Perbaikan: JOIN ke tabel is_satuan untuk menampilkan nama satuan
              $query = mysqli_query($mysqli, "
                SELECT o.kode_obat, o.nama_obat, s.nama_satuan 
                FROM is_obat AS o
                LEFT JOIN is_satuan AS s ON o.satuan = s.id_satuan
                ORDER BY o.kode_obat DESC
              ") or die('Ada kesalahan pada query tampil Data Obat: '.mysqli_error($mysqli));

              while ($data = mysqli_fetch_assoc($query)) {
                echo "<tr>
                        <td class='center'>$no</td>
                        <td class='center'>$data[kode_obat]</td>
                        <td>$data[nama_obat]</td>
                        <td class='center'>$data[nama_satuan]</td>
                        <td class='center'>
                          <div>
                            <a data-toggle='tooltip' title='Ubah' class='btn btn-primary btn-sm' href='?module=form_obat&form=edit&id=$data[kode_obat]'>
                              <i class='glyphicon glyphicon-edit' style='color:#fff'></i>
                            </a>
                            <a data-toggle='tooltip' title='Hapus' class='btn btn-danger btn-sm' href='modules/obat/proses.php?act=delete&id=$data[kode_obat]' onclick=\"return confirm('Anda yakin ingin menghapus obat $data[nama_obat] ?');\">
                              <i class='glyphicon glyphicon-trash' style='color:#fff'></i>
                            </a>
                          </div>
                        </td>
                      </tr>";
                $no++;
              }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!--/.col -->
  </div><!-- /.row -->
</section><!-- /.content -->
