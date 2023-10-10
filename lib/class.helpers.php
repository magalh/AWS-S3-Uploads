<?php
namespace AWSS3;

final class helpers
{

    public function __construct() {}

    public static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    public static function find_layout_template($params, $paramname, $typename)
    {
        $mod = \cms_utils::get_module("AWSS3");
        $paramname = (string) $paramname;
        $typename = (string) $typename;
        if ( !is_array($params) || !($thetemplate = \xt_param::get_string($params,$paramname)) ) {
            $tpl = \CmsLayoutTemplate::load_dflt_by_type($typename);
            if ( !is_object($tpl) ) {
                $mod->_DisplayErrorPage ($id, $params, $returnid, 'No default '.$typename.' template found');
                audit('', 'AWSS3', 'No default '.$typename.' template found');
                return;
            }
            $thetemplate = $tpl->get_name();
            unset($tpl);
        }
        return $thetemplate;
    }
    
  
}
?>