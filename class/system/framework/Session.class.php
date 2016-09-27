<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace framework;

/**
 * Session类管理
 *
 * @property string $id session id.
 *
 * @author lifq
 * @since 1.0
 */
class Session{
    private static $_instance = null;  
    private $id = null;
    
    /**
     * 返回当前对象实例，静态函数，保证只创建一个实例，只能通过该函数生成Session对象，不能通过构造函数创建.
     * @return 唯一的PdoDB实例
     * @throws 
     */      
    public static function getInstance()       
    {       
        if (null == self::$_instance)       
        {       
            self::$_instance = new self();       
        }       
        return self::$_instance;       
    }       

    private function __construct(){
        $this->start();
    }
    
    /**
     * 启动一个新SESSION，如果原有的SESSION存在，则先清空，再创建.
     * @param 
     * @return 
     * @throws 
     */    
    public function start(){
        if(null == $this->id){
            session_start();
            $this->id = session_id();
        }
    }
    
    /**
     * 销毁一个SESSION.
     * @param 
     * @return
     * @throws 
     */    
    public function destroy(){
        if(null != $this->id){
            session_destroy();
            $this->id = null;
            self::$_instance = null;
        }
    }
    
    /**
     * 重写PHP的魔术函数__get，当访问属性的时候，自动从对象的本地数据中获取.
     * @param string $propName 需要访问的属性名
     * @return 属性值
     * @throws 
     */    
    public function __get($propName){
        if('id' == $propName){
            return $this->id;
        }
            
        if(isset($_SESSION[$propName])){
            return $_SESSION[$propName];
        }
        
        return null;        
    }
    
    /**
     * 重写PHP的魔术函数__set，当访问属性的时候，自动设置对象的本地数据中获取.
     * @param string $propName 属性名
     * @param string $propValue 属性名
     * @return 属性值
     * @throws 
     */    
    public function __set($propName,$propValue){
        $_SESSION[$propName] = $propValue;
    }

    /**
     * 重写PHP的魔术函数__isset，判断属性是否设置.
     * @param string $propName 属性名
     * @return boolean 是否set
     * @throws 
     */    
    public function __isset($propName){
        return isset($_SESSION[$propName]);
    }

    /**
     * 重写PHP的魔术函数__unset，清空属性.
     * @param string $propName 属性名
     * @return boolean 是否set
     * @throws 
     */    
    public function __unset($propName){
        unset($_SESSION[$propName]);
    }
        
}

?>