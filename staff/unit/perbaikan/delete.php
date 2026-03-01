<?php
	// ambil id dan sanitasi
	if (!isset($_GET['id'])) {
		header('Location: dashboard_staff.php?unit=perbaikan&err=ID tidak diberikan!');
		exit;
	}
	$id = intval($_GET['id']);

	// ambil nama file bukti_struk dari DB
	$sel = mysqli_query($config, "SELECT bukti_struk FROM tb_perbaikan_barang WHERE perbaikan_id = '$id'");
	if ($sel) {
		$row = mysqli_fetch_assoc($sel);
		if (!empty($row['bukti_struk'])) {
			$file = __DIR__ . '/bukti_struk/' . $row['bukti_struk'];
			if (file_exists($file)) {
				@unlink($file);
			}
		}
	}

	// hapus record dari DB
	$query  = "DELETE FROM tb_perbaikan_barang WHERE perbaikan_id = '$id' ";
	if(mysqli_query($config, $query)){
		 header('Location: dashboard_staff.php?unit=perbaikan&msg=Data perbaikan berhasil dihapus!');
		exit;
	} else {
		header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menghapus data dalam database!');
		exit;
	}
	?>