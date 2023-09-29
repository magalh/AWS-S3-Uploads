<?php
namespace AWSS3;

class Exception extends \Exception
{

    private $_options;

    public function __construct($options,$type="alert-danger") 
    {
        parent::__construct($type);

        $this->_options = $options; 
        $this->_type = $type;
    }

    public function GetOptions() { return $this->_options; }

    public function GetType() { return $this->_type; }
}

?>