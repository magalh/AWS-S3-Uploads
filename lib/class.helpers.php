<?php
namespace AWSS3;

final class helpers
{

    public function __construct() {}

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

    public static function _DisplayAdminMessage($options,$type="alert-danger",$fetch=null)
	{
      //helpers::_DisplayAdminMessage($error,$class);
      $mod = \cms_utils::get_module("AWSS3");
	  $smarty = cmsms()->GetSmarty();
	  $tpl = $smarty->CreateTemplate($mod->GetTemplateResource('messages.tpl'),null,null,$smarty);

      switch ($type) {
            case "alert-primary":
                $class = "alert ".$type;
                break;
            case "alert-success":
                $class = "alert ".$type;
                break;
            case "alert-warning":
                $class = "alert ".$type;
                break;
            case "alert-danger":
                $class = "alert ".$type;
                break;
            case "slide-danger":
                $class = "message pageerrorcontainer";
                break;
            case "slide-success":
                $class = "message pagemcontainer";
                break;
        }

        $tpl->assign('class', $class);
        $tpl->assign('options', $options);
	  
        if(isset($fetch)){
            $out = $tpl->fetch();
            return $out;
        } else {
            $tpl->display();
        }
      
	}
    
  
}
?>