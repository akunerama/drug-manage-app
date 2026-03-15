<?php  
// fungsi untuk pengecekan tampilan form
// jika form add data yang dipilih
if ($_GET['form']=='add') { ?> 
  <!-- tampilan form add data -->
  <!-- Content Header (Page header) -->
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
          <form role="form" class="form-horizontal" action="modules/satuan/proses.php?act=insert" method="POST">
            <div class="box-body">
              <?php  
              // fungsi untuk membuat id transaksi
              $query_id = mysqli_query($mysqli, "SELECT RIGHT(id_satuan,6) as id FROM is_satuan
                                                ORDER BY id_satuan DESC LIMIT 1")
                                                or die('Ada kesalahan pada query tampil kode_obat : '.mysqli_error($mysqli));

              $count = mysqli_num_rows($query_id);

              if ($count <> 0) {
                  // mengambil data kode_obat
                  $data_id = mysqli_fetch_assoc($query_id);
                  $kode    = $data_id['id']+1;
              } else {
                  $kode = 1;
              }

              // buat kode_obat
              $buat_id   = str_pad($kode, 6, "0", STR_PAD_LEFT);
              $kode_obat = "S$buat_id";
              ?>

              <div class="form-group">
                <label class="col-sm-2 control-label">Kode Satuan</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="id_satuan" value="<?php echo $kode_obat; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama Satuan</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama_satuan" autocomplete="off" required>
                </div>
              </div>

            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=satuan" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->

  <!-- Modal Tambah Satuan -->
  <div class="modal fade" id="modalSatuan" tabindex="-1" role="dialog" aria-labelledby="modalSatuanLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modalSatuanLabel">Tambah Satuan Baru</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nama_satuan">Nama Satuan</label>
            <input type="text" class="form-control" id="nama_satuan" placeholder="Masukkan nama satuan">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btnSimpanSatuan">Simpan</button>
        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    // Simpan satuan baru via AJAX
    $('#btnSimpanSatuan').click(function() {
      var namaSatuan = $('#nama_satuan').val();
      
      if(namaSatuan == '') {
        alert('Nama satuan tidak boleh kosong');
        return;
      }
      
      $.ajax({
        url: 'modules/obat/proses_satuan.php?act=insert',
        type: 'POST',
        data: {nama_satuan: namaSatuan},
        success: function(response) {
          if(response.status == 'success') {
            // Tambahkan opsi baru ke dropdown
            $('#satuan').append($('<option>', {
              value: response.id_satuan,
              text: namaSatuan,
              selected: true
            }));
            
            // Refresh chosen select
            $('#satuan').trigger('chosen:updated');
            
            // Tutup modal dan reset input
            $('#modalSatuan').modal('hide');
            $('#nama_satuan').val('');
          } else {
            alert(response.message);
          }
        },
        dataType: 'json'
      });
    });
  });
  </script>

<?php
}
// jika form edit data yang dipilih
elseif ($_GET['form']=='edit') { 
  if (isset($_GET['id'])) {
      // fungsi query untuk menampilkan data dari tabel obat
      $query = mysqli_query($mysqli, "SELECT * FROM is_satuan WHERE id_satuan='$_GET[id]'") 
                                      or die('Ada kesalahan pada query tampil Data obat : '.mysqli_error($mysqli));
      $data  = mysqli_fetch_assoc($query);
    }
?>
  <!-- tampilan form edit data -->
  <!-- Content Header (Page header) -->
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

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" action="modules/satuan/proses.php?act=update" method="POST">
            <div class="box-body">
              
              <div class="form-group">
                <label class="col-sm-2 control-label">Kode Satuan</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="id_satuan" value="<?php echo $data['id_satuan']; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Nama Satuan</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nama_satuan" autocomplete="off" value="<?php echo $data['nama_satuan']; ?>" required>
                </div>
              </div>

            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=satuan" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->

  <!-- Modal Tambah Satuan -->
  <div class="modal fade" id="modalSatuan" tabindex="-1" role="dialog" aria-labelledby="modalSatuanLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modalSatuanLabel">Tambah Satuan Baru</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nama_satuan">Nama Satuan</label>
            <input type="text" class="form-control" id="nama_satuan" placeholder="Masukkan nama satuan">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btnSimpanSatuan">Simpan</button>
        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    // Simpan satuan baru via AJAX
    $('#btnSimpanSatuan').click(function() {
      var namaSatuan = $('#nama_satuan').val();
      
      if(namaSatuan == '') {
        alert('Nama satuan tidak boleh kosong');
        return;
      }
      
      $.ajax({
        url: 'modules/obat/proses_satuan.php?act=insert',
        type: 'POST',
        data: {nama_satuan: namaSatuan},
        success: function(response) {
          if(response.status == 'success') {
            // Tambahkan opsi baru ke dropdown
            $('#satuan').append($('<option>', {
              value: response.id_satuan,
              text: namaSatuan,
              selected: true
            }));
            
            // Refresh chosen select
            $('#satuan').trigger('chosen:updated');
            
            // Tutup modal dan reset input
            $('#modalSatuan').modal('hide');
            $('#nama_satuan').val('');
          } else {
            alert(response.message);
          }
        },
        dataType: 'json'
      });
    });
  });
  </script>
<?php
}
?>