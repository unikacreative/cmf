<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Role Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

use Illuminate\Database\Eloquent\Model as Eloquent;
use Unika\Security\Authorization\RoleInterface;

class Role extends Eloquent implements RoleInterface
{
	protected $fillable = array('name','description');
	protected $app;

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$this->app = \Application::instance();
		$this->table = $this->app['config']['acl.eloquent.role_table'];
	}

	//belongsTo relation
	public function users()
	{
		return $this->hasMany($this->app['config']['auth.Eloquent.user_class']);
	}

    //RoleInterface
	public function getRoleId()
	{
		return $this->getKey();
	}

	public function getRoleName()
	{
		return $this->name;		
	}

	public function getRoleDescription()
	{
		return $this->description;
	}
}