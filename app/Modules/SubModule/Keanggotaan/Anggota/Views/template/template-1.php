<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Kartu Anggota 1</title>

	<style>
		@page {
			size: 1004px 618px;
		}

		body {
			background-color: orange;
		}

		* {
			margin: 0;
			font-family: arial, helvetica, sans-serif !important;
			font-size: 36px !important;
		}
	</style>
</head>

<pdf>
	<page margin="0">
		<div class="container-card" style="background-image: url('{perpus_bg}'); background-repeat: no-repeat; font-family: Arial;">
			<table style="border-collapse: collapse; width: 100%; height: 100%;">
				<tbody>
					<tr>
						<td style="height: 200px;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 100%;">
							<table style="border-collapse: collapse; width: 100%; font-weight: bold; margin-top: 25px; margin-bottom: 25px" border="0">
								<tbody>
									<tr>
										<td style="width: 25%; text-align: left; padding-left: 50px">{anggota_foto}</td>
										<td style="width: 75%; text-align: left; padding-left: 15px">
											<table style="width: 100%;" border="0">
												<tbody>
													<tr>
														<td style="width: 25%; padding: 10px;">NOMOR</td>
														<td style="width: 2%; padding: 10px;">:</td>
														<td style="width: auto; padding: 10px;">{anggota_nomor}</td>
													</tr>
													<tr>
														<td style="padding: 10px;">NAMA</td>
														<td style="padding: 10px;">:</td>
														<td style="padding: 10px;">{anggota_nama}</td>
													</tr>
													<tr>
														<td style="padding: 10px;">JENIS</td>
														<td style="padding: 10px;">:</td>
														<td style="padding: 10px;">{anggota_jenis}</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="text-align: center;">{anggota_barcode}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</page>
</pdf>