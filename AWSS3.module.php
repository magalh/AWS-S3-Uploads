<?php
class AWSS3 extends CMSModule
{
	const MANAGE_PERM = 'manage_AWSS3';	
	
	public function GetVersion() { return '1.0'; }
	public function GetFriendlyName() { return $this->Lang('friendlyname'); }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function GetHeaderHTML() { return $this->_output_header_javascript(); }
	public function VisibleToAdminUser() { return $this->CheckPermission(self::MANAGE_PERM); }
	public function GetAuthor() { return 'Magal Hezi'; }
	public function GetAuthorEmail() { return 'h_magal@hotmail.com'; }
	public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }
	public function GetAdminSection() { return 'extentions'; }
	
	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
        $this->RestrictUnknownParams();

        $this->RegisterRoute('/[Aa]wss3\/[Ss]\/(?P<key>.*?)$/', array('action'=>'signed'));
        $this->RegisterRoute('/[Aa]wss3\/[Pp]age]\/(?P<pagenumber>.*?)$/', array('action'=>'default'));

        $this->SetParameterType('id',CLEAN_STRING);
        $this->SetParameterType('prefix',CLEAN_STRING);
        $this->SetParameterType('key',CLEAN_STRING);
        $this->SetParameterType('key2',CLEAN_STRING);
        $this->SetParameterType('key3',CLEAN_STRING);
        $this->SetParameterType('title',CLEAN_STRING);
        $this->SetParameterType('inline',CLEAN_INT);
        $this->SetParameterType('pagenum',CLEAN_INT);
        $this->SetParameterType('pagelimit',CLEAN_INT);
        $this->SetParameterType('summarytemplate',CLEAN_STRING);
        $this->SetParameterType('sortby',CLEAN_STRING);
        $this->SetParameterType('sortorder',CLEAN_STRING);
        $this->SetParameterType('showall',CLEAN_INT);
        $this->SetParameterType('detailpage',CLEAN_STRING);
        $this->SetParameterType('detailtemplate',CLEAN_STRING);

	}

	 public function InitializeAdmin() {
		 $this->SetParameters();
	 }
	
	public function GetHelp() { return @file_get_contents(__DIR__.'/doc/help.inc'); }
	public function GetChangeLog() { return @file_get_contents(__DIR__.'/doc/changelog.inc'); }

	protected function _output_header_javascript()
    {
        $out = '';
        $urlpath = $this->GetModuleURLPath()."/js";
        $jsfiles = array('jquery-file-upload/jquery.iframe-transport.js');
        $jsfiles[] = 'jquery-file-upload/jquery.fileupload.js';
        $jsfiles[] = 'jqueryrotate/jQueryRotate-2.2.min.js';
        $jsfiles[] = 'jrac/jquery.jrac.js';

        $fmt = '<script type="text/javascript" src="%s/%s"></script>';
        foreach( $jsfiles as $one ) {
            $out .= sprintf($fmt,$urlpath,$one)."\n";
        }

        $urlpath = $this->GetModuleURLPath();
        $fmt = '<link rel="stylesheet" type="text/css" href="%s/%s"/>';
        $cssfiles = array('js/jrac/style.jrac.css');
        $cssfiles[] = 'css/style.css';
        foreach( $cssfiles as $one ) {
            $out .= sprintf($fmt,$urlpath,$one);
        }

        return $out;
    }

	public function is_developer_mode() {
		$config = \cms_config::get_instance();
		if( isset($config['developer_mode']) && $config['developer_mode'] ) {
			return true;
		} else {
			return false;
		}
	}

	public function GetFileIcon($extension,$isdir=false) {
        if (empty($extension)) $extension = '---'; // hardcode extension to something.
        if ($extension[0] == ".") $extension = substr($extension,1);
        $config = \cms_config::get_instance();
        $iconsize=$this->GetPreference("iconsize","32px");
        $iconsizeHeight=str_replace("px","",$iconsize);

        $result="";
        if ($isdir) {
            $result="<img height=\"".$iconsizeHeight."\" style=\"border:0;\" src=\"".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/dir.png\" ".
                "alt=\"directory\" ".
                "align=\"middle\" />";
            return $result;
        }

        if (file_exists($config["root_path"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/".strtolower($extension).".png")) {
            $result="<img height='".$iconsizeHeight."' style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/".strtolower($extension).".png' ".
                "alt='".$extension."-file' ".
                "align='middle' />";
        } else {
            $result="<img height='".$iconsizeHeight."' style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/0.png' ".
                "alt=".$extension."-file' ".
                "align='middle' />";
        }
        return $result;
    }

    final public function GetSettingsValues()
    {
        $prefix = $this->GetName().'_mapi_pref_';
        $list = cms_siteprefs::list_by_prefix($prefix);
        if( !$list || !count($list) ) return [];
        $out = [];
        foreach( $list as $prefname ) {
            $tmp = cms_siteprefs::get($prefname);
            if( !$tmp ) continue;
            $out[substr($prefname, strlen($prefix))] = $tmp;
        }

        if( count($out) ) return $out;

    }
    
    final public function GetOptionValue($key, $default = '')
    {
        $value = $this->GetPreference($key);
        if(isset($value) && $value !== '') {
            return $value;
        } else {
            return $default;
        }
        
    }
    
    final public function SetOptionValue($key, $value) : void
    {
      $this->SetPreference($key,$value);
    }

    public function getHelpers()
	{
	  return \AWSS3\Helpers::getInstance();
	}

    public function _DisplayErrorMessage($errors, string $class = 'alert alert-danger')
	{
	  $smarty = cmsms()->GetSmarty();
	  $tpl = $smarty->CreateTemplate($this->GetTemplateResource('error.tpl'),null,null,$smarty);
	  $tpl->assign('errorclass', $class);
	  $tpl->assign('errors', $errors);
	  $tpl->display();
	}

    public function _DisplayMessage($message,$type="alert-danger",$fetch=null)
	{
      //helpers::_DisplayAdminMessage($error,$class);
      $mod = \cms_utils::get_module("AWSS3");
	  $smarty = cmsms()->GetSmarty();
	  $tpl = $smarty->CreateTemplate($mod->GetTemplateResource('message.tpl'),null,null,$smarty);

      switch ($type) {
            case ($type == "alert-info" || $type == "info"):
                $class = "alert alert-info";
                break;
            case ($type == "alert-success" || $type == "success" || $type == 200):
                $class = "alert alert-success";
                break;
            case ($type == "alert-warning" || $type == "warning"):
                $class = "alert alert-warning";
                break;
            case ($type == "alert-danger" || $type == "error" || $type == 500):
                $class = "alert alert-danger";
                audit('', 'AWSS3 Error', substr( $message,0 ,200 ) );
                break;
            case "slide-danger":
                $class = "message pageerrorcontainer";
                audit('', 'AWSS3 Error', substr( $message,0 ,200 ) );
                break;
            case "slide-success":
                $class = "message pagemcontainer";
                break;
        }

        $tpl->assign('errorclass', $class);
        $tpl->assign('message', $message);
	  
        if(isset($fetch)){
            $out = $tpl->fetch();
            return $out;
        } else {
            $tpl->display();
        }
      
	}


    public static function page_type_lang_callback($str)
    {
        $mod = cms_utils::get_module('AWSS3');
        if( is_object($mod) ) return $mod->Lang('type_'.$str);
    }

    public static function template_help_callback($str)
    {
        $str = trim($str);
        $mod = cms_utils::get_module('AWSS3');
        if( is_object($mod) ) {
            $file = $mod->GetModulePath().'/doc/help.inc';
            if( is_file($file) ) return file_get_contents($file);
        }
    }

    public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        if( $type->get_originator() != 'AWSS3' ) throw new CmsLogicException('Cannot reset contents for this template type');

        $fn = null;
        switch( $type->get_name() ) {
        case 'summary':
            $fn = 'orig_summary_template.tpl';
            break;

        case 'detail':
            $fn = 'orig_detail_template.tpl';
            break;

        case 'form':
            $fn = 'orig_form_template.tpl';
            break;

        case 'browsecat':
            $fn = 'browsecat.tpl';
        }

        $fn = cms_join_path(__DIR__,'templates',$fn);
        if( file_exists($fn) ) return @file_get_contents($fn);
    }

    public function CreateSignedLink($name)
    {
        $base_url = CMS_ROOT_URL;
        $name = $this->encodefilename($name);
        $out = $base_url."/awss3/s/".$name;

        return $out;
    }

    public function CreatePrettyLink($page,$prefix)
    {
        $base_url = CMS_ROOT_URL;
        //$name = $this->encodefilename($name);
        $out = $base_url."/awss3/".$page."/".$prefix;

        return $out;
    }

    public function getCacheFile($bucket_id,$prefix)
    {
        $config = \cms_config::get_instance();
        $json_file_name = 'awss3_'.$this->encodefilename($bucket_id.'_'.str_replace('/', '_', $prefix)).'.cms';
        $json_file_Path = $config['tmp_cache_location'].'/'.$json_file_name;

        return $json_file_Path;
    }

    protected function encodefilename($filename) {
        return base64_encode(sha1($this->config['dbpassword'].__FILE__.$filename).'|'.$filename);
    }

    protected function decodefilename($encodedfilename) {
        list($sig,$filename) = explode('|',base64_decode($encodedfilename),2);
        if( sha1($this->config['dbpassword'].__FILE__.$filename) == $sig ) return $filename;
    }

    
    

}

?>