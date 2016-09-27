<?php

namespace ui;

use framework\Controller;
/**
 *
 * @author Therfaint-
 *
 */

class VpnLinkManage extends \framework\Controller{

	private $vpnlink;

	public function __construct(){
		$this->vpnlink = new \user\VpnLink();
	}

	public function getParamInfoInterface()
	{
		$actionParam=array(
				"addNewLinkAction"=>array("vpnservid"=>"string","linkid"=>"string","startnodeid"=>"string","endnodeid"=>"string","delaytime"=>"string"),
				"getALinkAction"=>array("servid"=>"string","linkid"=>"string"),
				"delLinkAction"=>array("servid"=>"string","linkid"=>"string"),
				"updateLinkAction"=>array("vpnservid"=>"string","linkid"=>"string","startnodeid"=>"string","endnodeid"=>"string","delaytime"=>"string")
		);
		return $actionParam;
	}

	public function addNewLinkAction($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime)
	{
		$jsonStr = $this->vpnlink->addNewLink($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime);
		return $jsonStr;
	}

	public function getALinkAction($servid, $linkid)
	{
		$jsonStr = $this->vpnlink->getALink($servid, $linkid);
		return $jsonStr;
	}

	public function delLinkAction($servid, $linkid)
	{
		$jsonStr = $this->vpnlink->delLink($servid, $linkid);
		return $jsonStr;
	}

	public function updateLinkAction($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime)
	{
		$jsonStr = $this->vpnlink->updateLink($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime);
		return $jsonStr;
	}

}