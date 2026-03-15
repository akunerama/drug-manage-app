<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="fa fa-user icon-title"></i> Data Pasien

    <a class="btn btn-primary btn-social pull-right" href="?module=form_pasien&form=add" title="Tambah Data" data-toggle="tooltip">
      <i class="fa fa-plus"></i> Tambah
    </a>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

    <?php  
    // fungsi untuk menampilkan pesan
    if (empty($_GET['alert'])) {
      echo "";
    } 
    elseif ($_GET['alert'] == 1) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> Sukses!</h4>
              Data pasien berhasil disimpan.
            </div>";
    }
    ?>

      <div class="box box-primary">
        <div class="box-body">
          <!-- tampilan tabel Pasien -->
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center">No.</th>
                <th class="center">NIP</th>
                <th class="center">Nama Pasien</th>
              </tr>
            </thead>
            <tbody>
            <?php  
            $no = 1;
            // query untuk ambil data dari tabel pasien
            $query = mysqli_query($mysqli, "SELECT nip, nama FROM is_pasien ORDER BY nama ASC")
                  or die('Ada kesalahan pada query tampil Data Pasien: '.mysqli_error($mysqli));

            while ($data = mysqli_fetch_assoc($query)) { 
              echo "<tr>
                      <td class='center' width='50'>$no</td>
                      <td class='center'>$data[nip]</td>
                      <td class='center'>$data[nama]</td>
                      <td class='center' width='100'>
                        <div>
                          <a data-toggle='tooltip' data-placement='top' title='Ubah' style='margin-right:5px' class='btn btn-primary btn-sm' href='?module=form_pasien&form=edit&id=$data[nip]'>
                              <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
                          </a>";
            ?>
                          <a data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger btn-sm" href="modules/pasien/proses.php?act=delete&id=<?php echo $data['nip'];?>" onclick="return confirm('Anda yakin ingin menghapus obat <?php echo $data['nama']; ?> ?');">
                              <i style="color:#fff" class="glyphicon glyphicon-trash"></i>
                          </a>
            <?php
              echo "    </div>
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
  </div>   <!-- /.row -->
</section><!-- /.content -->
