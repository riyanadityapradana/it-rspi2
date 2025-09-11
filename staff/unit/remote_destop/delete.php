<?php
	require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

	//$query = mysqli_query ($mysqli, "SELECT * FROM tb_produk WHERE id_produk='$id'");
	//$row   = mysqli_fetch_array($query);
	//unlink("../../assets/assets-admin/img/produk/$row[foto_produk]"); // Koding Hapus Foto Dari DB dan Folder
	
	$id   = $_GET['id'];
	$query  = "DELETE FROM tb_remote WHERE id_remote = '$id' ";
	
	if(mysqli_query($config, $query)){
		header('Location: dashboard_staff.php?unit=remote&msg=Data Remote berhasil Dihapus!');
    		exit;
	} else {
		header('Location: dashboard_staff.php?unit=remote&err=Gagal menyimpan data dalam database!');
    		exit;
	}
	?>