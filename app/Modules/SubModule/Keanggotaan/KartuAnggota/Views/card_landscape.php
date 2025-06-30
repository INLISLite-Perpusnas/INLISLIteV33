<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Kartu Anggota</title>

	<style>
		@page {
			size: 1004px 593px;
		}

		body {
			background-color: orange;
		}

		* {
			margin: 0;
			padding: 0;
			font-family: verdana !important;
			font-size: 32px !important;
		}
	</style>
</head>

<pdf>
	<page margin="0">
		<?= $content ?>
	</page>
</pdf>