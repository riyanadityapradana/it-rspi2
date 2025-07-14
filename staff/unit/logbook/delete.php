<?php
	require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

	//$query = mysqli_query ($mysqli, "SELECT * FROM tb_produk WHERE id_produk='$id'");
	//$row   = mysqli_fetch_array($query);
	//unlink("../../assets/assets-admin/img/produk/$row[foto_produk]"); // Koding Hapus Foto Dari DB dan Folder
	
	$id   = $_GET['id'];
	$query  = "DELETE FROM tb_logbook WHERE id_log = '$id' ";
	
	if(mysqli_query($config, $query)){
		echo "<script>alert('Data Telah Berhasil Dihapus'); window.location = 'dashboard_staff.php?unit=logbook&action=datagrid'</script>";
        exit();
	} else {
		echo "<script>alert('Gagal menyimpan data dalam database');</script>";
	}
	?>