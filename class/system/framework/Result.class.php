<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace framework;

/**
 * 返回给客户端的对象基类
 *
 * @property boolean $status 返回的结果.
 * @property string $messages 返回的结果提示信息.
 * @property string $code 返回的错误码.
 * @property string $data 返回的数据对象.
 *
 * @author lifq
 * @since 1.0
 */
class Result{
    public $status = TRUE;
    public $message;
    public $code;
    public $data;

    public function __construct($status,$msg,$cd=0,$data=null){
        $this->status = $status;
        $this->message = $msg;
        $this->code = $cd;
        $this->data = $data;
    }
    
    /**
     * 类静态函数，直接返回TRUE的快键方式.
     * @param string $msg 提示信息
     * @param Object $data 实际的数据
     * @return Result Result对象
     * @throws 
     */    
    public static function OK($msg='OK',$data=null,$cd=0){
        return new \Framework\Result(TRUE,$msg,$cd,$data);
    }

    /**
     * 类静态函数，直接返回FALSE的快键方式.
     * @param string $msg 提示信息
     * @param Object $data 实际的数据
     * @return Result Result对象
     * @throws 
     */    
    public static function Error($msg='Error',$data=null,$cd=0){
        return new \Framework\Result(FALSE,$msg,$cd,$data);
    }

}

?>