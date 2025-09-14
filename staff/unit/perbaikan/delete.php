<?php
	$id   = $_GET['id'];
	$query  = "DELETE FROM tb_perbaikan_barang WHERE perbaikan_id = '$id' ";
	if(mysqli_query($config, $query)){
		 header('Location: dashboard_staff.php?unit=perbaikan&msg=Data perbaikan berhasil dihapus!');
        exit;
	} else {
		header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menghapus data dalam database!');
		exit;
	}
	?>