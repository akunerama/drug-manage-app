<section class="content-header">
    <h1><i class="fa fa-exclamation-triangle"></i> Obat Mendekati atau Sudah Expired</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Daftar Obat </h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Kode Obat</th>
                        <th>Tanggal Expired</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                            $no = 1;
                            $today = date('Y-m-d');
                            $three_month_ahead = date('Y-m-d', strtotime('+3 months'));

                            $query = mysqli_query($mysqli, "SELECT o.nama_obat, o.kode_obat, m.expired_date
                                FROM 
                                    is_obat_masuk m
                                INNER JOIN 
                                    is_obat o ON m.kode_obat = o.kode_obat
                                WHERE 
                                    m.expired_date <= '$three_month_ahead'
                                    AND m.expired_date IS NOT NULL
                                ORDER BY 
                                    m.expired_date ASC
                            ") or die('Query Error: ' . mysqli_error($mysqli));

                            while ($data = mysqli_fetch_assoc($query)) {
                                $expired_date = $data['expired_date'];
                                $expired_timestamp = strtotime($expired_date);
                                $today_timestamp = strtotime($today);
                                $selisih_hari = round(($expired_timestamp - $today_timestamp) / (60 * 60 * 24));

                                if ($selisih_hari < 0) {
                                    $status = "<span class='label label-danger'>Expired</span>";
                                    $row_style = "style='background-color:#ffe6e6;'";
                                    $sisa = "Sudah lewat " . abs($selisih_hari) . " hari";
                                } else {
                                    $status = "<span class='label label-warning'>Mendekati Expired</span>";
                                    $row_style = "style='background-color:#fff7e6;'";
                                    $sisa = "$selisih_hari hari lagi";
                                }

                                $hari = date('l', strtotime($expired_date));
                                $tanggal = date('d-m-Y', strtotime($expired_date));
                                $indo_hari = [
                                    'Sunday' => 'Minggu',
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu'
                                ];
                                $hari_indo = $indo_hari[$hari];
                                $expired_label = "$hari_indo, $tanggal";

                                echo "<tr $row_style>
                                    <td>{$no}</td>
                                    <td>{$data['nama_obat']}</td>
                                    <td>{$data['kode_obat']}</td>
                                    <td>{$expired_label}</td>
                                    <td>{$sisa}</td>
                                    <td>{$status}</td>
                                </tr>";
                                $no++;
                            }
                            ?>
                </tbody>
            </table>
        </div>
    </div>
</section>