<?php

/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
	
namespace utils;

/**
 * 功能：对Excel操作的工具类，目前主要提供的功能有：
 *       1）将多个excel中的指定sheet合并成一个新的excel文件
 * 
 * @author liupan
 * @since 1.0
 * @change:2015-11-24 1.增强了错误检查和返回码, songqj
 *                    2.增加了日志输出, songqj
 */

class ExcelUtils
{
	
    private $logger;                   //日志对象
	private $defImportDir;            //Excel源文件默认路径
	private $defExportDir;             //Excel输出文件默认路径

    const MERGESHEETS_SUCCESS = 0;               //成功
    const MERGESHEETS_DESTHASEXIST = 1001;       //目标文件已存在                     
    const MERGESHEETS_DESTFILEERROR = 1002;      //目标文件路径错误                   
    const MERGESHEETS_NOSRCFILE = 1003;          //源文件未提供                       
    const MERGESHEETS_SRCFILENOEXIST = 1004;     //源文件不存在                       
    const MERGESHEETS_SRCFILEUNSUPPORT = 1005;   //源文件格式不支持                   
    const MERGESHEETS_NOSRCSHEET = 1006;         //源SHEET未提供                      
    const MERGESHEETS_SRCSHEETNOEXIST = 1007;    //源SHEET不存在                      
    const MERGESHEETS_DESTSHEETDUPLICATE = 1008; //目标SHEET重复                      

    //构造函数中可以指定用户默认的Excel文件存放目录
    public function __construct($userImportDir="",$userExportDir="")
	{
		$this->logger = \Logger::getLogger( __CLASS__ );

		//如果用户没有指定源Excel目录，则使用系统默认的源Excel目录
		if(""==$userImportDir)
		{
			$this->defImportDir = "C:/TPSSAPP/Excel/";
		}
		else
		{
			$this->defImportDir = $userImportDir;
		}

		//如果用户没有指定源Excel目录，则使用系统默认的源Excel目录
		if(""==$userExportDir)
		{
			$this->defExportDir = "C:/TPSSAPP/Excel/";
		}
		else
		{
			$this->defExportDir = $userExportDir;
		}

    }



    /**
	 * 将给定的多个文件中的sheet，合并到一个新的Excel文件中
	 * 实现思路：
     *  1 加载指定excel文件
     *  2 克隆excel文件下面指定的sheet
     *  3 创建一个excel表并删除默认的sheet
     *  4 获取克隆的sheet并写入到excel文件下
     *  5 给当前的sheet重命名
     *  6 保存文件
     *  调用参数：
	 *   1.destExcelName   输出的Excel文件名
	 *   2.srcSheets       输入ExcelSheet信息
	 *     [
	 *       ["filename"=>srcfilename_1 , "sheetname"=>srcsheetname_1, "newsheetname" =>destsheetname_1],
	 * 	     ...
	 *	     ["filename"=>srcfilename_n , "sheetname"=>srcsheetname_n, "newsheetname" =>destsheetname_n]
	 *     ]
	 *     注意：目标sheetname可以不提供，或者为空字符串，此时直接使用源sheet名作为目标sheet名
	 *           srcfilename可以为绝对路径或者相对路径，如果为相对路径，则系统自动在默认路径下查找文件
	 *   3.overWrite       如果目标Excel文件存在，是否覆盖
	 *
     *  返回值：
	 *     0-函数执行成功，生成指定的Excel文件
	 *     1-目标Excel文件已存在，请使用覆盖方式
	 *     2-指定的目标Excel路径错误
	 *     3-给定的源Excel文件未提供
	 *     4-给定的源Excel文件不存在
	 *     5-给定的源Excel文件格式不被支持
	 *     6-给定的源sheet名未提供
	 *     7-给定的源sheet名不存在
	 *     8-目标sheet名重复
	 *     9-其他错误
	 */
	public function mergeSheets($destExcelName,$srcSheets,$overWrite=true)
	{
		//格式化目标文件为绝对路径
		$destExcelName =$this->autofullfilepath($destExcelName);
		//检查目标文是否存在
		if(is_file($destExcelName))
		{
			if(false == $overWrite)
			{
		        //输出目标Excel文件已存在，需要可能被覆盖
				$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方未指定覆盖模式，目标Excel文件('.iconv("gb2312","utf-8",$destExcelName).')已存在。'); 

				return  self::MERGESHEETS_DESTHASEXIST;

			}
		}
		//检查目标文是否有效
		elseif(!$this->filenameIsValid($destExcelName))
		{
		    //输出目标Excel文件路径错误
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方给定的目标Excel文件名('.iconv("gb2312","utf-8",$destExcelName).')无效。'); 
			return  self::MERGESHEETS_DESTFILEERROR;
		}


		//检查来源Excel文件名，来源Sheet名是否存在，以及目标sheet名是否存在及重复
		$sheetNum= count($srcSheets);
		$destSheetNameArray = array(); 
		for($i=0;$i<$sheetNum;$i++)
		{
			if(!array_key_exists ('filename',  $srcSheets[$i]))
			{
			    //输出第i条记录未提供文件名到日志
			    $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方未给定源Excel文件名,数组索引为('.($i+1).')。'); 
				return  self::MERGESHEETS_NOSRCFILE;
			}
			else
			{
				//格式化源文件为绝对路径
				$srcSheets[$i]["filename"] =$this->autofullfilepath($srcSheets[$i]["filename"] );
				if(!is_file($srcSheets[$i]["filename"]))
				{
					//输出第i条记录指定的源文件不存在到日志
			        $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方给定源的Excel文件名('.iconv("gb2312","utf-8",$srcSheets[$i]["filename"]).')不存在。'); 
					return  self::MERGESHEETS_SRCFILENOEXIST;
				}

			}

			if(!array_key_exists ('sheetname',  $srcSheets[$i]))
			{
			    //输出第i条记录未提供sheet名到日志
			    $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方未给定源Sheet名,数组索引为('.($i+1).')。'); 
				return  self::MERGESHEETS_NOSRCSHEET;
			}
			if(!array_key_exists ('newsheetname',  $srcSheets[$i]) || ""==$srcSheets[$i]["newsheetname"])
			{
				$srcSheets[$i]["newsheetname"]=$srcSheets[$i]["sheetname"];
			}
			array_push($destSheetNameArray,strtolower($srcSheets[$i]["newsheetname"]));
		}
		$retArray = array_unique ($destSheetNameArray);
		if(count($retArray)!=$sheetNum)
		{
			//输出传入的目标sheet名到日志
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方未给定目标Sheet重名,sheet数组为('.var_export($destSheetNameArray,true).')。'); 
			return  self::MERGESHEETS_DESTSHEETDUPLICATE;
		}


		$destPHPExcel=new \PHPExcel();        //创建新的excel表同时会生成一个默认的sheet表
		$destPHPExcel->removeSheetByIndex(0); //删除默认生成的sheet

		//$destFileType=\PHPExcel_IOFactory::identify($destExcelName);//获取文件的类型
        if(strtolower(substr($destExcelName,-5))==".xlsx")
		{
			$destFileType ="Excel2007";
		}
		elseif(strtolower(substr($destExcelName,-4))==".xls")
		{
			$destFileType ="Excel5";
		}
		else
		{
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方给定的目标Excel文件名('.iconv("gb2312","utf-8",$destExcelName).')无效,必须以xls或xlsx为后缀。'); 
			return  self::MERGESHEETS_DESTFILEERROR;
		}
        
		$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'准备合并其他Excel文件的Sheet到文件('.iconv("gb2312","utf-8",$destExcelName).')。'); 

		for($i=0;$i<$sheetNum;$i++)
		{
			$filename=$srcSheets[$i]['filename'];   //获取当前文件
			$sheetname=$srcSheets[$i]['sheetname']; //获取当前的sheet名
			$newsheetname=$srcSheets[$i]['newsheetname']; //获取目标sheet名

			$fileType=\PHPExcel_IOFactory::identify($filename);//获取文件的类型

			$objReader=\PHPExcel_IOFactory::createReader($fileType);//根据文件类型创建读文件对象
			$objPHPExcel=$objReader->load($filename);//加载指定的文件
			
			$copySheet=$objPHPExcel->getSheetByName($sheetname);
			//如果当前excel中不存在sheet
			if(!$copySheet)
			{
                //输出不存在的源sheet名到日志
			    $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'调用方给定源的Excel文件('.iconv("gb2312","utf-8",$srcSheets[$i]["filename"]).')中的源Sheet('.$srcSheets[$i]["sheetname"].')不存在。'); 
				return self::MERGESHEETS_SRCSHEETNOEXIST;
			}
			$copySheet->setTitle($newsheetname);        //给当前的工作表命名
			$objClonedWorksheet = clone $copySheet;              //克隆指定文件中指定sheet
		    $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'开始克隆Excel文件('.iconv("gb2312","utf-8",$srcSheets[$i]["filename"]).')的sheet('.$srcSheets[$i]["sheetname"].')。'); 
			$destPHPExcel->addExternalSheet($objClonedWorksheet);//把克隆的sheet添加到外部表
		}
		$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'开始创建Excel文件('.iconv("gb2312","utf-8",$destExcelName).')'); 
		$objWriter=\PHPExcel_IOFactory::createWriter($destPHPExcel,$destFileType);//生成指定格式的excel文件
		$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'开始保存Excel文件('.iconv("gb2312","utf-8",$destExcelName).')'); 
		$objWriter->save($destExcelName);//保存文件
		$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'合并Excel文件('.iconv("gb2312","utf-8",$destExcelName).')完成'); 
		return self::MERGESHEETS_SUCCESS;			
	}

	/*
	 * 如果传入文件名是相对路径，则给文件加上指定的路径，目前支持输入文件路径和输出文件路径
	 * 此处代码用判断是否带盘符判断windows文件，是否用/开头判断类unix文件是否为绝对路径
	 */
	private function autofullfilepath($filename,$type="IMP")
	{
		$fullname = $filename;
		if(! (substr($filename,1,1)==":" or substr($filename,0,1)=="/"))
		{
			if("IMP"==$type)
			{
				$fullname = $this->defImportDir.$filename;
			}
			elseif("EXP"==$type)
			{
				$fullname = $this->defExportDir.$filename;
			}
		}
		
		//因为操作系统的文件名是GB2312格式，而程序中传递的是UTF8编码的格式，因此应进行编码转换
		$fullname=iconv("utf-8","gb2312",$fullname);
		return $fullname;
	}

	/**
	 * 判断文件名是否合法
	 * 判断文件名合法主要是检查文件路径是否存在，对文件名中的特殊字符暂不考虑
	 **/
	private function filenameIsValid($filename)
	{
		$path = dirname($filename);
		$basename = basename($filename);
		if(""==$basename || "."==$basename || ".."==$basename)
		{
			return false;
		}

		if(!file_exists($path))
		{
			return false;
		}
		return true;
	}
}

?>