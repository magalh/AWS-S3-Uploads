<?php
class AWS_S3_Uploads extends CMSModule
{
	const MANAGE_PERM = 'manage_AWS_S3_Uploads';
	const S3_UPLOADS_BUCKET = 'hmn-uploads';
	const S3_UPLOADS_KEY = 'key';
	const S3_UPLOADS_REGION = 'us-east-1';
	const S3_UPLOADS_BUCKET_URL = 'https://localhost';
	const S3_UPLOADS_OBJECT_ACL = 'public';
	
	public function GetVersion() { return '1.0'; }
	public function GetFriendlyName() { return $this->Lang('friendlyname'); }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function VisibleToAdminUser() { return $this->CheckPermission(self::MANAGE_PERM); }
	public function GetAuthor() { return 'Magal Hezi'; }
	public function GetAuthorEmail() { return 'h_magal@hotmail.com'; }
	public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }
	public function GetAdminSection() { return 'extentions'; }
	
	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
		$this->SetParameterType('biz',CLEAN_STRING);
	}

	 public function InitializeAdmin() {
		 $this->SetParameters();
	 }
	
	public function GetHelp() { return @file_get_contents(__DIR__.'/doc/help.inc'); }
	public function GetChangeLog() { return @file_get_contents(__DIR__.'/doc/changelog.inc'); }

	public function is_developer_mode() {
		$config = \cms_config::get_instance();
		if( isset($config['developer_mode']) && $config['developer_mode'] ) {
			return true;
		} else {
			return false;
		}
	}

}

?>