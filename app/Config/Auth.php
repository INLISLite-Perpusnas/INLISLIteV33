<?php 
namespace Config;
class Auth extends \Myth\Auth\Config\Auth
{
	public $defaultUserGroup = 'anggota';

	public $views = [
		'login'           => 'Auth\Views\login',
		'register'        => 'Auth\Views\register',
		'forgot'          => 'Auth\Views\forgot',
		'reset'           => 'Auth\Views\reset',
		'emailForgot'     => 'Auth\Views\emails\forgot',
		'emailActivation' => 'Auth\Views\emails\activation',
	];

	public $allowRegistration = false;
	public $requireActivation = false; 
	public $activeResetter = false;
	public $allowRemembering = true;
	
	public $passwordValidators = [
		'Myth\Auth\Authentication\Passwords\CompositionValidator',
		'Myth\Auth\Authentication\Passwords\NothingPersonalValidator',
		'Myth\Auth\Authentication\Passwords\DictionaryValidator',
		'Myth\Auth\Authentication\Passwords\PwnedValidator',
	];

	public $validFields = [
        'email',
        'username',
    ];

	public $personalFields = [
		'first_name',
		'last_name',
		'phone',
		'branch_id',
		'category',
    ];
}
