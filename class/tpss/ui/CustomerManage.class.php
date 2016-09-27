<?php

namespace ui;

use framework\Controller;
/**
 * 
 * @author Therfaint-
 * 
 */

class CustomerManage extends \framework\Controller{
	
	private $customer;
	
	public function __construct(){
		$this->customer = new \user\Customer();
	}
	
	public function getParamInfoInterface()
    {
		$actionParam=array(
			"addNewCustomerInfoAction"=>array("customerid"=>"string","customername"=>"string","vpnservid"=>"string"),
			"getAllVpnByCustomerIdAction"=>array("customerid"=>"string","page"=>"int","rows"=>"int"),
			"delCustomerInfoAction"=>array("customerid"=>"string"),
			"updateCustomerInfoAction"=>array("customerid"=>"string","customername"=>"string","vpnservid"=>"string")
		);
		return $actionParam;
    }
    
	public function addNewCustomerInfoAction($customerid, $customername, $vpnservid)
	{
		$jsonStr = $this->customer->addNewCustomerInfo($customerid, $customername, $vpnservid);
		return $jsonStr;
	}
	
	public function getAllVpnByCustomerIdAction($customerid, $page, $rows)
	{
		$jsonStr = $this->customer->getAllVpnByCustomerId($customerid, $page, $rows);
		return $jsonStr;
	}
	
	public function delCustomerInfoAction($customerid)
	{
		$jsonStr = $this->customer->delCustomerInfo($customerid);
		return $jsonStr;
	}
	
	public function updateCustomerInfoAction($customerid,$customername,$vpnservid)
	{
		$jsonStr = $this->customer->updateCustomerInfo($customerid,$customername,$vpnservid);
		return $jsonStr;
	}
    
}