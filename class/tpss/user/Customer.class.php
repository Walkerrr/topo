<?php

namespace user;

/**
 * 
 * @author Therfaint-
 *
 */

class Customer{
	
	private $sess;
	private $dao;

	public function __construct(){
		$this->sess = \framework\Session::getInstance();
		$this->dao = new \dao\DaoCustomer();
	}

	/**
	 * 新增用户及其对应的vpn信息.
	 * @param string $customerid 用户id
	 * @param string $customername 用户名
	 * @param string $vpnservid 私有服务vpnid
	 * @throws
	 */
	public function addNewCustomerInfo($customerid, $customername, $vpnservid)
	{
		$jsonStr = $this->dao->addNewCustomerInfo($customerid, $customername, $vpnservid);
	
		return $jsonStr;
	}
	
	/**
	 * 通过用户id查询属于该用户的vpnid
	 * @param string $customerid
	 * @return 该用户所有的私有vpnid
	 * @throws
	 */
	public function getAllVpnByCustomerId($customerid, $page, $rows)
	{
		
		$result = $this->dao->getAllVpnByCustomerId($customerid);
		
		return $result;
		
	}

	/**
	 * 通过用户id删除该条记录
	 * @param string $customerid
	 * @return boolean 删除成功返回 TRUE 失败返回FALSE
	 * @throws
	 */
	public function delCustomerInfo($customerid)
	{
		$jsonStr = $this->dao->delCustomerInfo($customerid);
		
		return $jsonStr;
	}

	/**
	 * 更新用户信息
	 * @param $customerid,$customername,$vpnservid
	 */
	public function updateCustomerInfo($customerid,$customername,$vpnservid)
	{
		$jsonStr = $this->dao->updateCustomerInfo($customerid, $customername, $vpnservid);
		
		return $jsonStr;
	}

}

?>