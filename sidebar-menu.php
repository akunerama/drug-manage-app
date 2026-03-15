<?php
function menu_item($module, $icon, $label, $current_module, $sub = false)
{
    $active = ($module === $current_module) ? 'active' : '';
    $sub_class = $sub ? 'fa fa-circle-o' : $icon;
    echo "<li class='$active'><a href='?module=$module'><i class='$sub_class'></i> $label</a></li>";
}

function laporan_menu($current_module)
{
    $laporan_modules = [
        'lap_stok' => 'Stok Obat',
        'lap_pemakaian' => 'Pemakaian',
        'lap_obat_masuk' => 'Obat Masuk',
        'lap_obat_Keluar' => 'Obat Keluar',
    ];

    $active = array_key_exists($current_module, $laporan_modules) ? 'active' : '';
    echo "<li class='treeview $active'>
        <a href='javascript:void(0);'>
            <i class='fa fa-file-text'></i> <span>Laporan</span> <i class='fa fa-angle-left pull-right'></i>
        </a>
        <ul class='treeview-menu'>";

    foreach ($laporan_modules as $module => $label) {
        $active_sub = ($module === $current_module) ? 'active' : '';
        echo "<li class='$active_sub'><a href='?module=$module'><i class='fa fa-circle-o'></i> $label</a></li>";
    }

    echo "</ul></li>";
}

$hak_akses = $_SESSION['hak_akses'] ?? '';
$current_module = $_GET['module'] ?? '';

if ($hak_akses === 'Super Admin' || $hak_akses === 'Manajer') {
    echo "<ul class='sidebar-menu'>
        <li class='header'>MAIN MENU</li>";

    menu_item('beranda', 'fa fa-home', 'Beranda', $current_module);

    if ($hak_akses === 'Super Admin') {
        menu_item('obat', 'fa fa-folder', 'Data Obat', $current_module);
        menu_item('obat_masuk', 'fa fa-clone', 'Data Obat Masuk', $current_module);
        menu_item('obat_Keluar', 'fa fa-clone', 'Data Obat Keluar', $current_module);

        // Pindahkan laporan ke sini (di atas pasien)
        laporan_menu($current_module);

        menu_item('pasien', 'fa fa-user', 'Data Pasien', $current_module);

        // Ganti icon Data Satuan jadi lebih representatif, misalnya 'fa-cube'
        menu_item('satuan', 'fa fa-cube', 'Data Satuan', $current_module);

        menu_item('expired_date', 'fa fa-eye', 'Data Obat Expired', $current_module);
        menu_item('stok', 'fa fa-exclamation-circle', 'Stok Menipis', $current_module);
        menu_item('user', 'fa fa-user', 'Manajemen User', $current_module);
    } elseif ($hak_akses === 'Manajer') {
        // Kalau Manajer hanya lihat laporan dan ubah password misalnya
        laporan_menu($current_module);
    }

    menu_item('password', 'fa fa-lock', 'Ubah Password', $current_module);
    echo "</ul>";
}
