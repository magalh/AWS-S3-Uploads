<?php
namespace AWSS3;

final class helpers
{

    private function __construct() {}

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

    public static function _DisplayAdminMessage($errors = array(), $messages=array())
	{
      //helpers::_DisplayAdminMessage($errors,$messages);
      $mod = \cms_utils::get_module("AWSS3");
	  $smarty = cmsms()->GetSmarty();
	  $tpl = $smarty->CreateTemplate($mod->GetTemplateResource('messages.tpl'),null,null,$smarty);
	  $tpl->assign('errors', $errors);
	  $tpl->assign('messages', $messages);
	  $tpl->display();
	}
  
}
?>