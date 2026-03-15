

<?php
/* panggil file database.php untuk koneksi ke database */
require_once "config/database.php";
/* panggil file fungsi tambahan */
require_once "config/fungsi_tanggal.php";
require_once "config/fungsi_rupiah.php";

// fungsi untuk pengecekan status login user 
// jika user belum login, alihkan ke halaman login dan tampilkan message = 1
// session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}
else {
	if (isset($_GET['module'])) {
		if ($_GET['module'] == 'beranda') {
			include "modules/beranda/view.php";
			
		} elseif ($_GET['module'] == 'obat') {
			include "modules/obat/view.php";

		} elseif ($_GET['module'] == 'form_obat') {
			include "modules/obat/form.php";

		} elseif ($_GET['module'] == 'obat_masuk') {
			include "modules/obat-masuk/view.php";
			
		} elseif ($_GET['module'] == 'form_obat_masuk') {
			include "modules/obat-masuk/form.php";

		} elseif ($_GET['module'] == 'lap_stok') {
			include "modules/lap-stok/view.php";

		} elseif ($_GET['module'] == 'lap_pemakaian') {
			include "modules/lap-pemakaian/view.php";

		} elseif ($_GET['module'] == 'lap_obat_masuk') {
			include "modules/lap-obat-masuk/view.php";

		} elseif ($_GET['module'] == 'obat_Keluar') {
			include "modules/obat-Keluar/view.php";

		} elseif ($_GET['module'] == 'form_obat_Keluar') {
			include "modules/obat-Keluar/form.php";

		} elseif ($_GET['module'] == 'lap_obat_Keluar') {
			include "modules/lap-obat-Keluar/view.php";

		} elseif ($_GET['module'] == 'user') {
			include "modules/user/view.php";
			
		} elseif ($_GET['module'] == 'form_user') {
			include "modules/user/form.php";

		} elseif ($_GET['module'] == 'pasien') {
			include "modules/pasien/view.php";

		} elseif ($_GET['module'] == 'form_pasien') {
			include "modules/pasien/form.php";

		} elseif ($_GET['module'] == 'expired_date') {
			include "modules/expired/view.php";

		} elseif ($_GET['module'] == 'stok') {
			include "modules/stock/view.php";

		} elseif ($_GET['module'] == 'profil') {
			include "modules/profil/view.php";

		} elseif ($_GET['module'] == 'form_profil') {
			include "modules/profil/form.php";

		} elseif ($_GET['module'] == 'password') {
			include "modules/password/view.php";

		} elseif ($_GET['module'] == 'satuan') {
			include "modules/satuan/view.php";

		} elseif ($_GET['module'] == 'form_satuan') {
			include "modules/satuan/form.php";
		}		
	} else {
		// Jika module tidak ditentukan, default ke beranda atau halaman lain
		include "modules/beranda/view.php";
	}
}

?>