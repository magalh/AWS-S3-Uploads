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

    public function getOptions() { return $this->_options; }

    public function getText() { 

        if (is_array($this->_options)) {
            return implode("<br>",$this->_options);
        } else {
            return $this->_options; 
        }
    }

    public function getType() { return $this->_type; }
}

?>