<?php

namespace Framework;

abstract class AccessControl{
    
    abstract public function isAllow($controller,$action);

}

?>