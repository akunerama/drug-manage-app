<?php
require_once "../../config/database.php";

header('Content-Type: application/json');


if (isset($_POST['dataidobat'])) {
    $kode_obat = mysqli_real_escape_string($mysqli, $_POST['dataidobat']);

    $response = [
        'stok_total' => 0,
        'expired_info' => 'Stok Habis',
        'batch_details' => [],
        'error' => null
    ];

    // Hitung stok total dari is_obat_masuk (jumlah_masuk - yang sudah keluar)
    
    $query_stok = mysqli_query($mysqli, "
        SELECT 
            o.kode_obat,
            o.nama_obat,
            o.satuan,
            o.stok - IFNULL((
                SELECT SUM(okd.jumlah_keluar) 
                FROM is_obat_keluar okd
                JOIN is_obat_masuk om2 ON okd.kode_transaksi = om2.kode_transaksi
                WHERE om2.kode_obat = o.kode_obat
            ), 0) AS stok_total
        FROM is_obat o
        WHERE o.kode_obat = '$kode_obat';
    ");
    // $query_stok = mysqli_query($mysqli, "
    //     SELECT 
    //         o.kode_obat,
    //         o.nama_obat,
    //         o.satuan,
    //         SUM(om.jumlah_masuk) - IFNULL((
    //             SELECT SUM(okd.jumlah_keluar) 
    //             FROM is_obat_keluar okd
    //             JOIN is_obat_masuk om2 ON okd.kode_transaksi = om2.kode_transaksi
    //             WHERE om2.kode_obat = o.kode_obat
    //         ), 0) as stok_total
    //     FROM is_obat o
    //     LEFT JOIN is_obat_masuk om ON o.kode_obat = om.kode_obat
    //     WHERE o.kode_obat = '$kode_obat'
    //     GROUP BY o.kode_obat
    // ");
    if ($data_stok = mysqli_fetch_assoc($query_stok)) {
        $response['stok_total'] = (int)$data_stok['stok_total'];
        $response['satuan'] = $data_stok['satuan'];
    }

    // Ambil semua batch yang masih ada stoknya
    $query_batch = mysqli_query($mysqli, "
        SELECT *
        FROM (
        SELECT
            om.kode_transaksi,
            DATE_FORMAT(om.tanggal_masuk, '%d-%m-%Y') AS tanggal_masuk,
            DATE_FORMAT(om.expired_date, '%d-%m-%Y') AS expired_date,
            IF(@stok_sisa >= om.jumlah_masuk,
            om.jumlah_masuk,
            GREATEST(@stok_sisa, 0)
            ) AS sisa_stok,
            @stok_sisa := GREATEST(@stok_sisa - om.jumlah_masuk, 0) AS stok_sisa_setelah
        FROM (
            SELECT * FROM is_obat_masuk
            WHERE kode_obat = '$kode_obat'
            ORDER BY expired_date ASC
        ) om
        JOIN (SELECT @stok_sisa := (
            SELECT stok FROM is_obat WHERE kode_obat = '$kode_obat'
        )) vars
        ) result
        WHERE sisa_stok > 0;
    ");
    // $query_batch = mysqli_query($mysqli, "
    //     SELECT 
    //         om.kode_transaksi as id_masuk,
    //         om.jumlah_masuk - IFNULL((
    //             SELECT SUM(okd.jumlah_keluar) 
    //             FROM is_obat_keluar okd
    //             WHERE okd.kode_transaksi = om.kode_transaksi
    //         ), 0) as sisa_stok,
    //         om.kode_transaksi,
    //         DATE_FORMAT(om.tanggal_masuk, '%d-%m-%Y') as tanggal_masuk,
    //         DATE_FORMAT(om.expired_date, '%d-%m-%Y') as expired_date
    //     FROM is_obat_masuk om
    //     WHERE om.kode_obat = '$kode_obat'
    //     HAVING sisa_stok > 0
    //     ORDER BY om.expired_date ASC
    // ");

    $batch_details = [];
    while ($data_batch = mysqli_fetch_assoc($query_batch)) {
        $batch_details[] = $data_batch;
    }
    $response['batch_details'] = $batch_details;

    // Format info expired date untuk ditampilkan
    if (!empty($batch_details)) {
        $expired_info = [];
        foreach ($batch_details as $batch) {
            $expired_info[] = "Exp: {$batch['expired_date']} (Stok: {$batch['sisa_stok']})";
        }
        $response['expired_info'] = implode("\n", $expired_info);
    }

    echo json_encode($response);
} else {
    echo json_encode([
        'error' => 'Parameter dataidobat tidak ditemukan',
        'stok_total' => 0,
        'expired_info' => 'Stok Habis',
        'batch_details' => []
    ]);
}
?>