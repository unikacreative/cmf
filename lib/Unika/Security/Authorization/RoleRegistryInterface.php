<?php
/**
 *	This file is part of the UnikaCMF project.
 *	Role Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

Interface RoleRegistryInterface{

	public function addRole(array $role);

	public function removeRole($roleId);

	public function hasRole($roleId);

	public function allRole();

	public function getRole($roleId);
}