<script type="text/javascript">
  function tampil_obat(input) {
    var num = input.value;

    $.post("modules/obat-masuk/obat.php", {
      dataidobat: num,
    }, function(response) {
      var data = JSON.parse(response);
      $('#stok').html(data.stok_html); // Update stok
      $('#satuan').html(data.satuan_html); // Update stok
      // $('#expired_date').val(data.expired_date); // Update expired date
      document.getElementById('jumlah_masuk').focus();
    });
  }


  function cek_jumlah_masuk(input) {
    jml = document.formObatMasuk.jumlah_masuk.value;
    var jumlah = eval(jml);
    if (jumlah < 1) {
      alert('Jumlah Masuk Tidak Boleh Nol !!');
      input.value = input.value.substring(0, input.value.length - 1);
    }
  }

  function hitung_total_stok() {
    var bil1 = parseInt(document.formObatMasuk.stok.value) || 0;
    var bil2 = parseInt(document.formObatMasuk.jumlah_masuk.value) || 0;
    if (bil2 == "") {
      var hasil = "";
    } else {
      var hasil = eval(bil1) + eval(bil2);
    }
    document.formObatMasuk.total_stok.value = bil1 + bil2;
  }

  // Tambah batch baru
  document.getElementById('add-batch').addEventListener('click', function () {
    const container = document.getElementById('batch-container');
    const batchItem = container.querySelector('.batch-item');
    const newBatch = batchItem.cloneNode(true);

    // Bersihkan value input
    newBatch.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(newBatch);
  });

  // Hapus batch
  document.getElementById('batch-container').addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
      const items = document.querySelectorAll('.batch-item');
      if (items.length > 1) {
        e.target.closest('.batch-item').remove();
      }
    }
  });

</script>

<?php
// fungsi untuk pengecekan tampilan form
// jika form add data yang dipilih
if ($_GET['form'] == 'add') { ?>
  <!-- tampilan form add data -->
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Input Data Obat Masuk
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda </a></li>
      <li><a href="?module=obat_masuk"> Obat Masuk </a></li>
      <li class="active"> Tambah </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" action="modules/obat-masuk/proses.php?act=insert" method="POST" name="formObatMasuk">
            <div class="box-body">
              <?php
              // fungsi untuk membuat kode transaksi
              $query_id = mysqli_query($mysqli, "SELECT RIGHT(kode_transaksi,7) as kode FROM is_obat_masuk
                                                ORDER BY kode_transaksi DESC LIMIT 1")
                or die('Ada kesalahan pada query tampil kode_transaksi : ' . mysqli_error($mysqli));

              $count = mysqli_num_rows($query_id);

              if ($count <> 0) {
                // mengambil data kode transaksi
                $data_id = mysqli_fetch_assoc($query_id);
                $kode    = $data_id['kode'] + 1;
              } else {
                $kode = 1;
              }

              // buat kode_transaksi
              $tahun          = date("Y");
              $buat_id        = str_pad($kode, 7, "0", STR_PAD_LEFT);
              $kode_transaksi = "TM-$tahun-$buat_id";
              ?>

              <div class="form-group">
                <label class="col-sm-2 control-label">Kode Transaksi</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="kode_transaksi" value="<?php echo $kode_transaksi; ?>" readonly required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Tanggal</label>
                <div class="col-sm-5">
                  <!-- <input type="text" class="form-control" name="tanggal_masuk" autocomplete="off" placeholder="dd-mm-yyyy" require > -->
                  <input type="date" class="form-control" name="tanggal_masuk" required>
                </div>
              </div>

              <hr>

              <div class="form-group">
                <label class="col-sm-2 control-label">Obat</label>
                <div class="col-sm-5">
                  <select class="chosen-select" name="kode_obat" data-placeholder="-- Pilih Obat --" onchange="tampil_obat(this)" autocomplete="off" required>
                    <option value=""></option>
                    <?php
                    $query_obat = mysqli_query($mysqli, "SELECT kode_obat, nama_obat FROM is_obat ORDER BY nama_obat ASC")
                      or die('Ada kesalahan pada query tampil obat: ' . mysqli_error($mysqli));
                    while ($data_obat = mysqli_fetch_assoc($query_obat)) {
                      echo "<option value=\"$data_obat[kode_obat]\"> $data_obat[kode_obat] | $data_obat[nama_obat] </option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <span id='stok'>
                <div class="form-group">
                  <label class="col-sm-2 control-label">Stok</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="stok" name="stok" readonly required>
                  </div>
                </div>
              </span>

              <span id='satuan'>
                <div class="form-group">
                  <label class="col-sm-2 control-label">Satuan</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="satuan" name="satuan" readonly required>
                  </div>
                </div>
              </span>
              
              <!-- <div class="form-group">
                <label class="col-sm-2 control-label">Jumlah Masuk</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" id="jumlah_masuk" name="jumlah_masuk" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" onkeyup="hitung_total_stok(this)&cek_jumlah_masuk(this)" required>
                </div>
              </div> -->

              
              <div id="batch-container">
                <div class="form-group row batch-item">
                  <div class="col-sm-3">
                    <label>Jumlah Masuk</label>
                    <input type="number" class="form-control" name="jumlah_masuk[]" required>
                  </div>
                  <div class="col-sm-3">
                    <label>Expired Date</label>
                    <input type="date" class="form-control" name="expired_date[]" required>
                  </div>
                  <!-- <div class="col-sm-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-remove form-control">Hapus</button>
                  </div> -->
                </div>
              </div>

              <!-- <div class="form-group row mt-2">
                <div class="col-sm-8">
                  <button type="button" class="btn btn-success" id="add-batch">+ Tambah Batch</button>
                </div>
              </div> -->


              <!-- <div class="form-group">
                <label class="col-sm-2 control-label">Total Stok</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" id="total_stok" name="total_stok" readonly required>
                </div>
              </div> -->

              <!-- <div class="form-group">
                <label class="col-sm-2 control-label">Expired Date</label>
                <div class="col-sm-5">
                  <input type="date" class="form-control" name="expired_date" required>
                  <input type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy" id="expired_date" name="expired_date" autocomplete="off" required>
                </div>
              </div> -->



            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                  <a href="?module=obat_masuk" class="btn btn-default btn-reset">Batal</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div><!-- /.box -->
      </div><!--/.col -->
    </div> <!-- /.row -->
  </section><!-- /.content -->
<?php
}
?>