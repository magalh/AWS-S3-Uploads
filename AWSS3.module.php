<?php
#---------------------------------------------------------------------------------------------------
# Module: AWSS3
# Authors: Magal Hezi, with CMS Made Simple Foundation.
# Copyright: (C) 2023 Magal Hezi, h_magal@hotmail.com
# Licence: GNU General Public License version 3. See http://www.gnu.org/licenses/  
#---------------------------------------------------------------------------------------------------
# CMS Made Simple(TM) is (c) CMS Made Simple Foundation 2004-2020 (info@cmsmadesimple.org)
# Project's homepage is: http://www.cmsmadesimple.org
# Module's homepage is: http://dev.cmsmadesimple.org/projects/AWSS3
#---------------------------------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify it under the terms of the GNU
# General Public License as published by the Free Software Foundation; either version 3 of the 
# License, or (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
# See the GNU General Public License for more details.
#---------------------------------------------------------------------------------------------------

use \AWSS3\aws_s3_utils;

class AWSS3 extends CMSModule
{
	const MANAGE_PERM = 'manage_AWSS3';	
	
	public function GetVersion() { return '1.0.0'; }
	public function GetFriendlyName() { return $this->Lang('friendlyname'); }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function GetHeaderHTML() { return $this->_output_header_assets(); }
	public function VisibleToAdminUser() { return $this->CheckPermission(self::MANAGE_PERM); }
	public function GetAuthor() { return 'Magal Hezi'; }
	public function GetAuthorEmail() { return 'h_magal@hotmail.com'; }
	public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }
	public function GetAdminSection() { return 'extentions'; }
    public function MinimumCMSVersion() { return '2.2.1'; }
    public function GetDependencies() { return ['AWSSDK' => '1.0.0']; }

    public function __construct(){
        parent::__construct();

        \spl_autoload_register([$this, 'autoload']);

        $smarty = \CmsApp::get_instance()->GetSmarty();
        if( !$smarty ) return;

        $sdk = cms_utils::get_module( 'AWSSDK' );
        $fn = $this->GetModulePath().'/lib/class.aws_s3_utils.php'; require_once($fn);
        $fn = $sdk->GetModulePath().'/lib/class.aws_sdk_utils.php'; require_once($fn);
        
        $smarty->registerClass('s3_utils','\AWSS3\aws_s3_utils');
        $smarty->registerClass('sdk_utils','\AWSSDK\aws_sdk_utils');
        
        $smarty->assign('sdk',$sdk);

        if ($sdk->is_developer_mode()){
            $smarty->assign('isdev',true);
        }
    
  }

  public function autoload($classname) : bool
  {
      $sdk_mod = cms_utils::get_module( 'AWSSDK' );
      $fn = $sdk_mod->GetModulePath().'/lib/SDK/aws-autoloader.php'; require_once($fn);
      $fn = $this->GetModulePath().'/lib/SDK/aws-autoloader.php'; require_once($fn);
      return TRUE;
  }
	
	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
        $this->RestrictUnknownParams();
        
        $this->SetParameterType('action', CLEAN_STRING);
        $this->SetParameterType('prefix',CLEAN_STRING);
        $this->SetParameterType('pagenum',CLEAN_INT);
        $this->SetParameterType('pagelimit',CLEAN_INT);
        $this->SetParameterType('summarytemplate',CLEAN_STRING);
        $this->SetParameterType('detailpage',CLEAN_STRING);
        $this->SetParameterType('detailtemplate',CLEAN_STRING);
        $this->SetParameterType('junk',CLEAN_STRING);

        $this->RegisterRoute('/[Ss]3\/[Ss]\/(?P<prefix>.*?)$/', array('action'=>'signed'));
        $this->RegisterRoute('/[Ss]3\/[Ff]ile\/(?P<prefix>.*)\/(?P<returnid>[0-9]+)$/', array('action'=>'detail'));
        $this->RegisterRoute('/[Ss]3\/[Ff]ile\/(?P<prefix>.*)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)$/', array('action'=>'detail'));

	}

	 public function InitializeAdmin() {
		 $this->SetParameters();
         $this->CreateParameter('prefix',null,$this->Lang('help_param_prefix'));
         $this->CreateParameter('pagelimit',null,$this->Lang('help_param_pagelimit'));
         $this->CreateParameter('summarypage',null,$this->Lang('help_param_summarypage'));
         $this->CreateParameter('summarytemplate',null,$this->Lang('help_param_summarytemplate'));
         $this->CreateParameter('detailpage',null,$this->Lang('help_param_detailpage'));
         $this->CreateParameter('detailtemplate',null,$this->Lang('help_param_detailtemplate'));
         #$this->CreateParameter('detailpage',null,$this->Lang('help_param_detailpage'));
	 }
	
	public function GetHelp() { return @file_get_contents(__DIR__.'/README.md'); }
	public function GetChangeLog() { return @file_get_contents(__DIR__.'/doc/changelog.inc'); }

	protected function _output_header_assets()
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

    protected function _output_frontend_css()
    {
        $out = '';
        $urlpath = $this->GetModuleURLPath();
        $fmt = '<link rel="stylesheet" type="text/css" href="%s/%s"/>';
        $cssfiles = array('css/style.css');
        //$cssfiles[] = 'css/style.css';
        foreach( $cssfiles as $one ) {
            $out .= sprintf($fmt,$urlpath,$one);
        }
        return $out;
    }

    /**
	 * DoEvent methods
	 */
	function DoEvent($originator, $eventname, &$params) {
		if ($originator == 'Core' && $eventname == 'ContentPostRender')
		{
			$pos_top = stripos($params["content"], "</head");
			if ($pos_top !== FALSE && !empty($this->FrontEndCSS))
			{
				$params["content"] = substr($params["content"], 0, $pos_top) . $this->FrontEndCSS . substr($params["content"], $pos_top);
			}
			$pos_btm = strripos($params["content"], "</body");
			if ($pos_btm !== FALSE && !empty($this->FrontEndJS))
			{
				$params["content"] = substr($params["content"], 0, $pos_btm) . $this->FrontEndJS . substr($params["content"], $pos_btm);
			}
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
        }

        $fn = cms_join_path(__DIR__,'templates',$fn);
        if( file_exists($fn) ) return @file_get_contents($fn);
    }

    public function CreateSignedLink($name)
    {
        $mod_sdk = \cms_utils::get_module('AWSSDK');
        $base_url = CMS_ROOT_URL;
        $name = $mod_sdk->encodefilename($name);
        $out = $base_url."/S3/s/".$name;

        return $out;
    }

    public function CreatePrettyLink($page,$prefix)
    {
        $base_url = CMS_ROOT_URL;
        $out = $base_url."/S3/".$page."/".$prefix;

        return $out;
    }

    public function GetPrettyUrl($prefix=null)
    {
        $use_custom_url = $this->GetOptionValue("use_custom_url");
        $custom_url = $this->GetOptionValue("custom_url");
        if( $use_custom_url && $custom_url ) {
            $url = $custom_url;
        } else {
            $url = "https://".$this->GetOptionValue('bucket_name').".s3.".$this->GetOptionValue('access_region').".amazonaws.com";
        }
        if(isset($prefix)){
            return $url.'/'.$prefix;
        } else {
            return $url;
        }
        
    }

    public function getCacheFile($qparms)
    {
        $bucket_id = $qparms['bucket'];
        $prefix = $qparms['prefix'];
        $mod_sdk = \cms_utils::get_module('AWSSDK');
        $config = \cms_config::get_instance();
        $json_file_name = 'awss3_'.$mod_sdk->encodefilename($bucket_id.'_'.str_replace('/', '_', $prefix)).'.cms';
        $json_file_Path = $config['tmp_cache_location'].'/'.$json_file_name;

        return $json_file_Path;
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

}

?>