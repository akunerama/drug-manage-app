<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="fa fa-file-text-o icon-title"></i> Laporan Data Obat Keluar
  </h1>
  <ol class="breadcrumb">
    <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda</a></li>
    <li class="active">Laporan</li>
    <li class="active">Data Obat Keluar</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

      <!-- Form Laporan -->
      <div class="box box-primary">
        <!-- form start -->
        <form role="form" class="form-horizontal" method="GET" action="modules/lap-obat-keluar/process.php">
          <div class="box-body">

            <div class="form-group">
              <label class="col-sm-1">Tanggal</label>
              <div class="col-sm-2">
                <input type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy" name="tgl_awal" autocomplete="off" required>
              </div>

              <label class="col-sm-1">s.d.</label>
              <div class="col-sm-2">
                <input style="margin-left:-35px" type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy" name="tgl_akhir" autocomplete="off" required>
              </div>
            </div>
          </div>

          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-11">
                <!-- Tombol Cetak -->
                <button type="submit" name="action" value="lihat" class="btn btn-primary btn-social btn-submit" formtarget="_blank">
                  <i class="fa fa-eye"></i> Lihat
                </button>

                <!-- Tombol Download -->
                <button type="submit" name="action" value="download" class="btn btn-primary btn-social btn-submit">
                  <i class="fa fa-download"></i> Download
                </button>
              </div>
            </div>
          </div>
        </form>
      </div><!-- /.box -->
    </div><!--/.col -->
  </div> <!-- /.row -->
</section><!-- /.content -->