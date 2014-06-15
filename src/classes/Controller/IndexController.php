<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Default Controller
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{		
		//$this->app['config_database']['package::app.Eloquent.table'] = 'masterkey';

		if( $this->app['auth']->check() )
			return $this->app['view']->render('default/test')->with('page_title','Welcome to the jungle!');
		else
			return 'Hello World<br>you are not logged in.';
	}
}