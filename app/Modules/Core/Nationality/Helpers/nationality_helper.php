<?php 
if (!function_exists('get_nationality')) {
	function get_nationality($sub_district_id)
	{
		$codes = explode('.', $sub_district_id);
		$country_id = $codes[0];
		$city_id = $country_id.'.'.$codes[1];
		$district_id = $city_id.'.'.$codes[2];

		$nationalityModel = new \Nationality\Models\NationalityModel();
		$country = $nationalityModel->where('code',$country_id)->first();
		$city = $nationalityModel->where('code',$city_id)->first();
		$district = $nationalityModel->where('code',$district_id)->first();
		$sub_district = $nationalityModel->where('code',$sub_district_id)->first();

		$data = array(
			'country' => $country->name,
			'country_id' => $country_id,
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
?>