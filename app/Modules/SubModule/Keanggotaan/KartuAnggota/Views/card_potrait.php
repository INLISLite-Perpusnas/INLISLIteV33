<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Kartu Anggota</title>

	<style>
		@page { size: 618px 1004px;}
    	body { background-color: orange; }

		* {
			margin: 0;
			font-family: arial, helvetica, sans-serif !important;
			font-size: 36px !important;
			font-weight: bold;
		}
	</style>
</head>

<pdf>
	<page margin="0">
		<?=$content?>
	</page>
</pdf>