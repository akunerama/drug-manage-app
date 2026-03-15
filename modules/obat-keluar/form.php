  <script type="text/javascript">
    // function tampil_obat(selectElement) {
    //   const kode_obat = selectElement.value;
    //   const row = selectElement.closest("tr");
    //   if (kode_obat !== "") {
    //     $.ajax({
    //       url: "modules/obat-Keluar/ajax_obat.php",
    //       method: "POST",
    //       data: {
    //         dataidobat: kode_obat
    //       },
    //       dataType: "json",
    //       success: function(data) {
    //         if (data.error) {
    //           alert("Error: " + data.error);
    //           return;
    //         }
    //         row.querySelector(".stok").value = data.stok;
    //         row.querySelector(".expired_date").value = data.expired_date;
    //         row.querySelector(".jumlah_Keluar").focus();
    //       },
    //       error: function(xhr, status, error) {
    //         alert("Gagal memuat data obat.");
    //         console.error("AJAX Error:", status, error);
    //       }
    //     });
    //   }
    // }

    function tampil_obat(selectElement) {
    const kode_obat = selectElement.value;
    const row = selectElement.closest("tr");
    
    // Reset nilai
    row.querySelector(".stok").value = '';
    row.querySelector(".jumlah_Keluar").value = '';
    row.querySelector(".total_stok").value = '';
    row.querySelector(".expired_date").value = '';
    
    if (kode_obat !== "") {
        $.ajax({
            url: "modules/obat-Keluar/ajax_obat.php",
            method: "POST",
            data: { dataidobat: kode_obat },
            dataType: "json",
            success: function(data) {
                if (data.error) {
                    alert("Error: " + data.error);
                    return;
                }
                
                row.querySelector(".stok").value = data.stok_total;
                row.querySelector(".expired_date").value = data.expired_info;
                
                // Simpan data batch ke atribut data
                row.dataset.batchDetails = JSON.stringify(data.batch_details);
                row.querySelector(".jumlah_Keluar").focus();
            },
            error: function(xhr, status, error) {
                alert("Gagal memuat data obat.");
                console.error("AJAX Error:", status, error);
            }
        });
    }
}

    function hitung_pengambilan(input) {
    const row = input.closest("tr");
    const jumlahKeluar = parseInt(input.value) || 0;
    const stokTotal = parseInt(row.querySelector(".stok").value) || 0;
    const batchDetails = JSON.parse(row.dataset.batchDetails || '[]');
    
    // Validasi input
    if (jumlahKeluar < 1) {
        alert('Jumlah Keluar Tidak Boleh Nol atau Kosong!');
        input.value = "";
        input.focus();
        return false;
    }
    
    if (jumlahKeluar > stokTotal) {
        alert('Jumlah Keluar Melebihi Stok yang Tersedia!');
        input.value = "";
        input.focus();
        return false;
    }
    
    // Hitung pengambilan per batch (FIFO)
    let sisaKeluar = jumlahKeluar;
    let pengambilan = [];
    let hiddenInputs = '';
    
    for (let i = 0; i < batchDetails.length && sisaKeluar > 0; i++) {
        const batch = batchDetails[i];
        const ambil = Math.min(batch.sisa_stok, sisaKeluar);
        
        if (ambil > 0) {
            pengambilan.push({
                id_masuk: batch.kode_transaksi,
                expired_date: batch.expired_date,
                ambil: ambil,
                sisa: batch.sisa_stok - ambil
            });
            
            // Buat input hidden untuk setiap pengambilan
            hiddenInputs += `
                <input type="hidden" name="detail[${batch.kode_transaksi}][id_masuk]" value="${batch.kode_transaksi}">
                <input type="hidden" name="detail[${batch.kode_transaksi}][jumlah]" value="${ambil}">
            `;
            
            sisaKeluar -= ambil;
        }
    }
    
    // Update tampilan
    let expiredText = pengambilan.map(item => 
        `Exp: ${item.expired_date} (Ambil: ${item.ambil}, Sisa: ${item.sisa})`
    ).join('\n');
    
    // Tambahkan hidden inputs ke row
    const existingHidden = row.querySelector('.hidden-details');
    if (existingHidden) {
        existingHidden.innerHTML = hiddenInputs;
    } else {
        const hiddenDiv = document.createElement('div');
        hiddenDiv.className = 'hidden-details';
        hiddenDiv.style.display = 'none';
        hiddenDiv.innerHTML = hiddenInputs;
        row.appendChild(hiddenDiv);
    }
    
    row.querySelector(".expired_date").value = expiredText;
    row.querySelector(".total_stok").value = stokTotal - jumlahKeluar;
    
    return true;
}

    function cek_jumlah_Keluar(input) {
      const jumlah = parseInt(input.value) || 0;
      const stok = parseInt(input.closest("tr").querySelector(".stok").value) || 0;

      if (jumlah < 1) {
        alert('Jumlah Keluar Tidak Boleh Nol atau Kosong!');
        input.value = "";
        input.focus();
      } else if (jumlah > stok) {
        alert('Jumlah Keluar Melebihi Stok yang Tersedia!');
        input.value = "";
        input.focus();
      }
    }

    function hitung_total_stok(input) {
      const row = input.closest("tr");
      const stok = parseInt(row.querySelector(".stok").value) || 0;
      const jumlahKeluar = parseInt(input.value) || 0;

      if (jumlahKeluar === "") {
        row.querySelector(".total_stok").value = "";
      } else {
        row.querySelector(".total_stok").value = stok - jumlahKeluar;
      }
    }

    function addRow() {
        const table = document.querySelector("#table-obat tbody");
        
        // Clone baris pertama
        const newRow = table.rows[0].cloneNode(true);
        
        // Hapus semua komponen Chosen dan reset semua input
        const selects = newRow.querySelectorAll("select");
        selects.forEach(select => {
            // Reset elemen select asli
            select.selectedIndex = 0;
            select.value = "";
            
            // Hapus elemen Chosen yang dibuat
            const chosenContainer = select.nextElementSibling;
            if (chosenContainer && chosenContainer.classList.contains('chosen-container')) {
                chosenContainer.remove();
            }
        });
        
        // Reset semua input text/number dan textarea
        newRow.querySelectorAll("input, textarea").forEach(el => {
            el.value = "";
        });
        
        // Hapus data batch yang tersimpan
        if (newRow.dataset && newRow.dataset.batchDetails) {
            delete newRow.dataset.batchDetails;
        }
        
        // Hapus semua hidden inputs
        const hiddenElements = newRow.querySelectorAll('.hidden-details');
        hiddenElements.forEach(el => el.remove());
        
        // Tambahkan baris baru ke tabel
        table.appendChild(newRow);
        
        // Reinisialisasi event handlers
        newRow.querySelector(".obat").onchange = function() {
            tampil_obat(this);
        };
        
        newRow.querySelector(".jumlah_Keluar").onkeyup = function() {
            hitung_pengambilan(this);
        };
        
        // Reinisialisasi Chosen pada baris baru
        $(newRow).find('.chosen-select').chosen({
            width: '100%',
            placeholder_text_single: "-- Pilih Obat --"
        });
    }

    function removeRow(button) {
      const row = button.closest("tr");
      const table = document.querySelector("#table-obat tbody");
      if (table.rows.length > 1) {
        table.removeChild(row);
      }
    }

    function previewObat() {
      const rows = document.querySelectorAll("#table-obat tbody tr");
      let obatList = "<h4>Daftar Obat yang Akan Dikeluarkan:</h4><ul>";
      let isEmpty = true;
      
      rows.forEach(row => {
        const kodeObat = row.querySelector(".obat").value;
        const namaObat = row.querySelector(".obat option:checked").text.split('|')[1]?.trim() || '';
        const jumlah = row.querySelector(".jumlah_Keluar").value;
        
        if (kodeObat && jumlah) {
          isEmpty = false;
          obatList += `<li>${namaObat} (${jumlah})</li>`;
        }
      });
      
      obatList += "</ul>";
      
      if (isEmpty) {
        obatList = "<p>Belum ada obat yang ditambahkan</p>";
      }
      
      Swal.fire({
        title: 'Preview Obat',
        html: obatList,
        icon: 'info',
        confirmButtonText: 'OK'
      });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const firstRow = document.querySelector("#table-obat tbody tr");
        if (firstRow) {
            firstRow.querySelector(".obat").onchange = function() {
                tampil_obat(this);
            };
            firstRow.querySelector(".jumlah_Keluar").onkeyup = function() {
                hitung_pengambilan(this);
            };
        }
    });

      // Inisialisasi Select2
      $('.select2').select2({
        placeholder: "Pilih Pasien",
        allowClear: true
      });
  </script>

  <?php
  if ($_GET['form'] == 'add') {
    $id_keluar = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id_keluar) {
      $query = mysqli_query($mysqli, "
        SELECT k.*, p.nama
        FROM is_obat_keluar k 
        INNER JOIN is_pasien p ON k.nip = p.nip 
        WHERE k.id_keluar = '$id_keluar'
      ") or die('Query Error: ' . mysqli_error($mysqli));

      $data = mysqli_fetch_assoc($query);
    } else {
      // Tidak ada ID, tampilkan form kosong
      $data = [
        'nip' => '',
        'nama' => '',
        'diagnosa' => ''
      ];
    }
  ?>

    <section class="content-header">
      <h1>
        <i class="fa fa-edit icon-title"></i> Input Data Obat Keluar
      </h1>
      <ol class="breadcrumb">
        <li><a href="?module=beranda"><i class="fa fa-home"></i> Beranda </a></li>
        <li><a href="?module=obat_Keluar"> Obat Keluar </a></li>
        <li class="active"> Tambah </li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <form role="form" class="form-horizontal" action="modules/obat-Keluar/proses.php?act=insert" method="POST" name="formObatKeluar">
              <div class="box-body">
                <?php
                $query_id1 = mysqli_query($mysqli, "SELECT RIGHT(kode_transaksi,7) as kode FROM is_obat_Keluar ORDER BY kode_transaksi DESC LIMIT 1")
                  or die('Query Error: ' . mysqli_error($mysqli));

                $count = mysqli_num_rows($query_id1);
                $kode1 = ($count <> 0) ? mysqli_fetch_assoc($query_id1)['kode'] + 1 : 1;
                $tahun = date("Y");
                $buat_id1 = str_pad($kode1, 7, "0", STR_PAD_LEFT);
                $kode_transaksi_keluar = "TK-$tahun-$buat_id1";
                ?>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Kode Transaksi Keluar</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" name="kode_transaksi" value="<?= $kode_transaksi_keluar; ?>" readonly required>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Tanggal</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" name="tanggal_Keluar" required>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Nama Pasien</label>
                  <div class="col-sm-5">
                    <select class="form-control select2" name="nip" required>
                      <option value="">-- Pilih Pasien --</option>
                      <?php
                      $query_pasien = mysqli_query($mysqli, "SELECT nip, nama FROM is_pasien ORDER BY nama ASC");
                      while ($pasien = mysqli_fetch_assoc($query_pasien)) {
                        $selected = ($data['nip'] == $pasien['nip']) ? 'selected' : '';
                        echo "<option value=\"$pasien[nip]\" $selected>$pasien[nama]</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Diagnosa</label>
                  <div class="col-sm-5">
                    <textarea class="form-control" name="diagnosa" rows="2" required><?php echo $data['diagnosa']; ?></textarea>
                  </div>
                </div>

                <hr>

                <table class="table table-bordered" id="table-obat">
                  <thead>
                    <tr>
                      <th>Obat</th>
                      <th>Stok</th>
                      <th>Jumlah Keluar</th>
                      <th>Total Stok</th>
                      <th>Expired Date</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <select name="kode_obat[]" class="form-control chosen-select obat" data-placeholder="-- Pilih Obat --" required>
                          <option value=""></option>
                          <?php
                          $query_obat = mysqli_query($mysqli, "
                            SELECT o.kode_obat, o.nama_obat, s.nama_satuan
                            FROM is_obat o
                            JOIN is_satuan s ON o.satuan = s.id_satuan
                            ORDER BY o.nama_obat ASC
                          ");

                          while ($obat = mysqli_fetch_assoc($query_obat)) {
                            echo "<option value=\"{$obat['kode_obat']}\">{$obat['kode_obat']} | {$obat['nama_obat']} | {$obat['nama_satuan']}</option>";
                          }
                          ?>
                        </select>
                      </td>
                      <td><input type="text" name="stok[]" class="form-control stok" readonly></td>
                      <td><input type="number" name="jumlah_Keluar[]" class="form-control jumlah_Keluar" required></td>
                      <td><input type="text" name="total_stok[]" class="form-control total_stok" readonly></td>
                      <!-- <td><input type="text" name="expired_date[]" class="form-control expired_date" rows="3" readonly></td> -->
                      <td><textarea name="expired_date[]" class="form-control expired_date" rows="2" cols ="25"readonly></textarea></td>
                      <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">-</button></td>
                    </tr>
                  </tbody>
                </table>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-success" onclick="addRow()">
                      <i class="fa fa-plus"></i> Tambah Obat
                    </button>
                    <!-- <button type="button" class="btn btn-info" onclick="previewObat()">
                      <i class="fa fa-eye"></i> View Obat
                    </button> -->
                  </div>
                </div>
              </div>

              <div class="box-footer">
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" class="btn btn-primary btn-submit" name="simpan" value="Simpan">
                    <a href="?module=obat_Keluar" class="btn btn-default btn-reset">Batal</a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Tambahkan CSS untuk Select2 -->
    <link rel="stylesheet" href="assets/plugins/select2/select2.min.css">
    
    <!-- Tambahkan JS untuk Select2 dan SweetAlert -->
    <script src="assets/plugins/select2/select2.full.min.js"></script>
    <script src="assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <?php } ?>