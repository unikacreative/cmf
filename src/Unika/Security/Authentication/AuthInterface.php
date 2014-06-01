<?php
/**
 *	Unika-CMF Project
 *	Auth Service Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */
namespace Unika\Security\Authentication;

interface AuthInterface
{	
	CONST VIA_NORMAL_LOGIN = 'normal';
	CONST VIA_REMEMBER_ME = 'remember_me';
	CONST VIA_FORCE_LOGIN = 'force';
	CONST VIA_ONCE		= 'once';
	//ex : OAUTH,OPENID or reserved for future, actual info can be retrieve via session_info
	CONST VIA_CUSTOM = 'custom';
	
	/**
	 *
	 *	login attempt
	 *
	 *	@param $credentials array, ussualy its username and password but it can be anything
	 * 	@return boolean
	 */
	public function attempt(array $credentials ,$remember = False,$expired = null);

	/**
	 *
	 *	allows you to validate a user's credentials without actually logging them into the application
	 *
	 *	@return boolean
	 */
	public function validate(array $credentials);

	/**
	 *
	 *	single request login no session or cookie
	 */
	public function once(array $credentials);

	//determine if user already loggedin
	public function check();

	//logout user
	public function logout();

	//login user manually
	public function forceLogin(array $user);

	/**
	 *
	 *	@return User Object null if user not already loggedin
	 */
	public function user();

	
	/**
	 *	valid return value see CONST
	 *	@return mixed
	 */
	public function signinMethod();
}