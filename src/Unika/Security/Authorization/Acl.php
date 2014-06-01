<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Default Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

class Acl implements AclInterface 
{
	/**
	 *
	 *	is aro allowed to access aco?
	 *	@return boolean
	 */
	public function isAllowed(AroInterface $aro,AcoInterface $aco);

	/**
	 *
	 *	grant aro to aco
	 *	@return boolean
	 */

	public function grant(AroInterface $aro,AcoInterface $aco);
	
	/**
	 *
	 *	deny aro to aco
	 *	@return boolean
	 */
	public function deny(AroInterface $aro,AcoInterface $aco);		
}