<!DOCTYPE html>
<html lang="vi">
	
	<!-- PHẦN HEAD -->

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=1200,initial-scale=1.0"/>
		<meta name="description" content="Hệ thống đặt lịch tiệm tóc nam">
		<title>Barber Shop | Tiệm tóc nam</title>

		<!-- LIÊN KẾT CSS -->

		<link rel="stylesheet" type="text/css" href="<?= base_url('barber-admin/Design/css/bootstrap.min.css') ?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url('barber-admin/Design/fonts/css/all.min.css') ?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url('barber-admin/Design/css/web-main.css') ?>?v=<?= @filemtime(ROOT_PATH . '/barber-admin/Design/css/web-main.css') ?: time() ?>">

		<link rel="stylesheet" type="text/css" href="<?= base_url('barber-admin/Design/css/barber-icons.css') ?>">
		<?php if (!empty($extraCss)): ?>
		<link rel="stylesheet" type="text/css" href="<?= base_url(htmlspecialchars($extraCss)) ?>">
		<?php endif; ?>

		<!-- PHÔNG CHỮ GOOGLE -->

		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Prata&display=swap" rel="stylesheet">

	</head>

	<!-- PHẦN BODY -->

	<body>
	