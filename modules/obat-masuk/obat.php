
<?php
session_start();

// Panggil koneksi database.php untuk koneksi database
require_once "../../config/database.php";

if(isset($_POST['dataidobat'])) {
	$kode_obat = $_POST['dataidobat'];

        $query = mysqli_query($mysqli, "SELECT o.stok,
            s.nama_satuan AS nama_satuan, 
            MIN(m.expired_date) AS expired_date 
        FROM 
            is_obat o
        LEFT JOIN 
            is_obat_masuk m ON o.kode_obat = m.kode_obat 
        LEFT JOIN 
            is_satuan s ON o.satuan = s.id_satuan 
        WHERE 
            o.kode_obat = '$kode_obat'
        ") or die('Error: '.mysqli_error($mysqli));

        $data = mysqli_fetch_assoc($query);

        $stok = isset($data['stok']) ? $data['stok'] : 0;
        $satuan = $data['nama_satuan'];
        $expired_date = isset($data['expired_date']) && $data['expired_date'] != null ? $data['expired_date'] : '';

        $response = array(
        'stok_html' => "<div class='form-group'>
                          <label class='col-sm-2 control-label'>Stok</label>
                          <div class='col-sm-5'>
                            <div class='input-group'>
                              <input type='text' class='form-control' id='stok' name='stok' value='$stok' readonly>
                            </div>
                          </div>
                        </div>",
        'expired_date' => $expired_date,
        'satuan_html' => "<div class='form-group'>
                  <label class='col-sm-2 control-label'>Satuan</label>
                  <div class='col-sm-5'>
                    <input type='text' class='form-control' id='satuan' name='satuan' value='$satuan' readonly>
                  </div>
                </div>"
        );

    echo json_encode($response);

  }
?> 