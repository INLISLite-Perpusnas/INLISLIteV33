<?php
//Format 31/12/2021
if ( ! function_exists('indo_short_date')){
	function indo_short_date($date = null, $delimiter = '/'){
		if(empty($date)){$date = date('Y-m-d');}

		$gmdate = gmdate($date, time()+60*60*8);
		$gmdate_arr = explode("-",$gmdate);
		$dd = $gmdate_arr[2];
		$mm = str_pad($gmdate_arr[1], 2, "0", STR_PAD_LEFT); 
		$yyyy = $gmdate_arr[0];

		return $dd. $delimiter .$mm. $delimiter .$yyyy;
	}
}

//Format 31/Des/2021
if ( ! function_exists('indo_medium_date')) {
	function indo_medium_date($date = null, $delimiter = '/') {
		if(empty($date)){$date = date('Y-m-d');}

		$gmdate = gmdate($date, time()+60*60*8);
		$gmdate_arr = explode("-",$gmdate);
		$dd = $gmdate_arr[2];
		$mm = $gmdate_arr[1]; 
		$month = indo_month_medium($mm);
		$yyyy = $gmdate_arr[0];

		return $dd. $delimiter .$month. $delimiter .$yyyy;
	}
}

//Format: 31/Desember/2021
if ( ! function_exists('indo_long_date')) {
	function indo_long_date($date = null, $delimiter = '/') {
		if(empty($date)){$date = date('Y-m-d');}

		$gmdate = gmdate($date, time()+60*60*8);
		$gmdate_arr = explode("-",$gmdate);
		$dd = $gmdate_arr[2];
		$mm = $gmdate_arr[1]; 
		$month = indo_month_long($mm);
		$yyyy = $gmdate_arr[0];

		return $dd. $delimiter .$month. $delimiter .$yyyy;
	}
}
	
//Format: Minggu, 31 Desember 2021
if ( ! function_exists('indo_long_date_day')) {
	function indo_long_date_day($date = null, $delimiter = '/') {
		if(empty($date)){$date = date('Y-m-d');}

		$gmdate = gmdate($date, time()+60*60*8);
		$gmdate_arr = explode("-",$gmdate);
		$dd = $gmdate_arr[2];
		$mm = $gmdate_arr[1]; 
		$month = indo_month_long($mm);
		$yyyy = $gmdate_arr[0];
	
		$day_name = date("l", mktime(0,0,0,$mm,$dd,$yyyy));
		$day_name_alias = indo_day($day_name);

		return $day_name_alias. ', ' .$dd. $delimiter .$month. $delimiter .$yyyy;
	}
}

//Format: Des
if ( ! function_exists('indo_month_medium')){
	function indo_month_medium($mm) {
		switch ($mm) {
			case 1:  return "Jan";  break;
			case 2:  return "Feb";  break;
			case 3:  return "Mar";  break;
			case 4:  return "Apr";  break;
			case 5:  return "Mei";  break;
			case 6:  return "Jun";  break;
			case 7:  return "Jul";  break;
			case 8:  return "Ags";  break;
			case 9:  return "Sep";  break;
			case 10: return "Okt";  break;
			case 11: return "Nov";  break;
			case 12: return "Des";  break;
		}
	}
}

//Format: Desember
if ( ! function_exists('indo_month_long')) {
	function indo_month_long($mm) {
		switch ($mm) {
			case 1:  return "Januari";  break;
			case 2:  return "Februari"; break;
			case 3:  return "Maret";  	break;
			case 4:  return "April";  	break;
			case 5:  return "Mei";  	break;
			case 6:  return "Juni";  	break;
			case 7:  return "Juli";  	break;
			case 8:  return "Agustus";  break;
			case 9:  return "September";break;
			case 10: return "Oktober"; 	break;
			case 11: return "November"; break;
			case 12: return "Desember"; break;
		}
	}
}

//Format: Senin
if ( ! function_exists('indo_day')) {
	function indo_day($day_name) {
		switch ($day_name) {
			case "Sunday":  	return "Minggu"; break;
			case "Monday":  	return "Senin";  break;
			case "Tuesday":  	return "Selasa"; break;
			case "Wednesday":  	return "Rabu";   break;
			case "Thursday":  	return "Kamis";  break;
			case "Friday":  	return "Jumat";  break;
			case "Saturday":  	return "Sabtu";  break;
		}
	}
}

if (!function_exists('tgl_indonesia')) {
	function tgl_indonesia($tgl){ 
		$tanggal = substr($tgl,8,2);
		$nama_bulan = array("Januari", "Februari", "Maret", "April", "Mei", 
				"Juni", "Juli", "Agustus", "September", 
				"Oktober", "November", "Desember");
		$bulan = $nama_bulan[substr($tgl,5,2) - 1];
		$tahun = substr($tgl,0,4);
		return $tanggal.' '.$bulan.' '.$tahun;       
	}
	}