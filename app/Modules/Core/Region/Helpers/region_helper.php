<?php
if (!function_exists('get_region')) {
	function get_region($sub_district_id)
	{
		$codes = explode('.', $sub_district_id);
		$province_id = $codes[0];
		$city_id = $province_id . '.' . $codes[1];
		$district_id = $city_id . '.' . $codes[2];

		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code', $province_id)->first();
		$city = $regionModel->where('code', $city_id)->first();
		$district = $regionModel->where('code', $district_id)->first();
		$sub_district = $regionModel->where('code', $sub_district_id)->first();

		$data = array(
			'province' => $province->name,
			'province_id' => $province_id,
			'city' => $city->name,
			'city_id' => $city_id,
			'district' => $district->name,
			'district_id' => $district_id,
			'sub_district' => $sub_district->name,
			'sub_district_id' => $sub_district_id,
		);

		return (object) $data;
	}
}
if (!function_exists('get_province')) {
	function get_province($province_id)
	{
		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code', $province_id)->where('level', 1)->first();

		return $province;
	}
}
if (!function_exists('get_city')) {
	function get_city($city_id)
	{
		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code', $city_id)->where('level', 2)->first();

		return $province;
	}
}
if (!function_exists('get_district')) {
	function get_district($district_id)
	{
		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code', $district_id)->where('level', 3)->first();

		return $province;
	}
}
if (!function_exists('npp_region')) {
	function npp_region($npp = null, $level = 1)
	{
		$regionModel = new \Region\Models\RegionModel();
		$data = $regionModel->where('npp', $npp)->orWhere('code', $npp)->where('level', $level)->first();

		return $data;
	}
}
if (!function_exists('get_region')) {
	function get_region($sub_district_id)
	{
		$codes = explode('.', $sub_district_id);
		$province_id = $codes[0];
		$city_id = $province_id . '.' . $codes[1];
		$district_id = $city_id . '.' . $codes[2];

		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code', $province_id)->first();
		$city = $regionModel->where('code', $city_id)->first();
		$district = $regionModel->where('code', $district_id)->first();
		$sub_district = $regionModel->where('code', $sub_district_id)->first();

		$data = array(
			'province' => $province->name,
			'province_id' => $province_id,
			'city' => $city->name,
			'city_id' => $city_id,
			'district' => $district->name,
			'district_id' => $district_id,
			'sub_district' => $sub_district->name,
			'sub_district_id' => $sub_district_id,
		);

		return (object) $data;
	}
}
