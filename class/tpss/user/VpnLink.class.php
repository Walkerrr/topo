<?php

namespace user;

/**
 * 
 * @author Therfaint-
 * 
 */

class VpnLink{
		private $sess;
		private $dao;
	
		public function __construct(){
			$this->sess = \framework\Session::getInstance();
			$this->dao = new \dao\DaoVpnLink();
		}
}