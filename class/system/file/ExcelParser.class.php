<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
//namespace file;
require_once __DIR__."/../utils/PHPExcel_1.8.0/Classes/PHPExcel.php";

/**
 * 解析Excel的类，对PHPExcel的简单封装
 *
 * @property String $_fileName Excel文件名称
 * @property String $_sheetName Excel文件当前sheet名称
 * @property String $_sheetIndex Excel文件当前sheet索引
 * @property PHPExcel_Sheet $_curSheet Excel文件当前sheet对象
 *
 * @author lifq
 * @since 1.0
 */
class ExcelParser{
    private $_fileName;
    private $_sheetName;
    private $_sheetIndex;
    private $_curSheet;
    
    public function __construct($fileName){
        $this->_sheetIndex = 0;
        $this->_sheetName = '';
        $this->_curSheet = null;
        //$this->_fileName = $fileName;
		//因为操作系统的文件名是GB2312格式，而程序中传递的是UTF8编码的格式，因此应进行编码转换
		$this->_fileName=iconv("utf-8","gb2312",$fileName);//获得需要读取的excel文件名称	
    }
    
    /**
     * 设置当前sheet名称函数.
     * @param string $sheetName sheet名
     * @return 
     * @throws 
     */     
    public function setCurSheetName($sheetName){
        $this->_curSheet = null;
        $this->_sheetName = $sheetName;
    }
    
    /**
     * 设置当前sheet索引函数.
     * @param string $sheetIndex sheet索引
     * @return 
     * @throws 
     */     
    public function setCurSheetIndex($sheetIndex){
        $this->_sheetIndex = $sheetIndex;
    }

    /**
     * 获取Excel当前sheet的数据,sheet对象保存到类属性$_curSheet中.
     * @param 
     * @return 
     * @throws 
     */     
	public function load()
	{
        //文件名自动判断文件类型
        $inputFileType = \PHPExcel_IOFactory::identify($this->_fileName); 
        
        //指定Excel类型，创建一个reader 
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType); 

        //设置只读取数据，不包括公式和格式 
        $objReader->setReadDataOnly(true); 

        //只读取指定的sheet 
        if($this->_sheetName != ''){
            $objReader->setLoadSheetsOnly($this->_sheetName); 
        }
        
/*        
        //缓存：采用内存压缩方式
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;  
        $cacheSettings = array();  
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);  
*/        
        $objPHPExcel = $objReader->load($this->_fileName); 
        $this->_curSheet = $objPHPExcel->getSheet($this->_sheetIndex); 
     }

    /**
     * 获取Excel当前sheet的所有数据.
     * @param 
     * @return array 二维数组数据
     * @throws 
     */     
	public function getData()
	{
        if(null == $this->_curSheet)
            return null;
        return $this->_curSheet->toArray(); 
    }

    /**
     * 获取Excel当前sheet的行数据.
     * @param int $row 行号
     * @return array 行数据
     * @throws 
     */     
	public function getRowData($row)
	{
        $ret = array();
        $maxRow = $this->getMaxRow();
        $maxCol = $this->getMaxCol();
        if($row > $maxRow)
            return $ret;
        
        for($col = 'A';$col<=$maxCol;$col++){
            $value = $this->getCell($col,$row);
            array_push($ret,$value);
        }
        return $ret;
    }

    /**
     * 获取Excel当前sheet的列数据.
     * @param String $col 列号，如：A,B,C,D
     * @return array 列数据
     * @throws 
     */     
	public function getColData($col)
	{
        $ret = array();
        $maxRow = $this->getMaxRow();
        $maxCol = $this->getMaxCol();
        if($col > $maxCol)
            return $ret;
        
        for($row = 1;$row<=$maxRow;$row++){
            $value = $this->getCell($col,$row);
            array_push($ret,$value);
        }
        return $ret;
    }
    
    /**
     * 获取表格数据.
     * @param string $col Excel的列名，如A,B,C,D...
     * @param string $col Excel的行名，如1,2,3,4... 
     * @return string 表格的数据
     * @throws 
     */ 
    public function getCell($col,$row)
    {
        if(null == $this->_curSheet)
            return null;
        //return $this->_curSheet->getCell($col.$row)->getValue();
		return $this->_curSheet->getCell($col.$row)->getCalculatedValue();
    }    

    /**
     * 获取当前sheet的最大行数.
     * @param 
     * @return int 最大行数 
     * @throws 
     */ 
    public function getMaxRow()
    {
        if(null == $this->_curSheet)
            return -1;
            
        return $this->_curSheet->getHighestRow();
    } 

    /**
     * 获取当前sheet的最大列数.
     * @param string 
     * @return int 最大列数
     * @throws 
     */ 
    public function getMaxCol()
    {
        if(null == $this->_curSheet)
            return -1;
            
        return  $this->_curSheet->getHighestColumn();
    } 
        
    /**
     * 中文转换函数，防止中文出现乱码.
     * @param string $str gb2312编码的中文
     * @return string utf8编码的中文
     * @throws 
     */ 
    public function convertGB2312($str)
    {
       if(empty($str)) return '';
       return  iconv('utf-8','gb2312//IGNORE',$str);
    }
    
    /**
     * 中文转换函数，防止中文出现乱码.
     * @param string $str gb2312编码的中文
     * @return string utf8编码的中文
     * @throws 
     */ 
    public function convertUTF8($str)
    {
       if(empty($str)) return '';
       return  iconv('gb2312','utf-8//IGNORE',$str);
    }

    /**
     * 日期转换函数.
     * @param string $str Excel日期
     * @return string 显示格式为 “月/日/年”
     * @throws 
     */ 
     function getDateTime($val){ 
        $jd = GregorianToJD(1, 1, 1970); 
        $gregorian = JDToGregorian($jd+intval($val)-25569); 
        list($month, $day, $year) = explode('/', $gregorian);
        return sprintf('%04d-%02d-%02d', $year, $month, $day); 
    } 
     
}

?>