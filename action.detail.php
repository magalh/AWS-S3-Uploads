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
#
# Amazon Web Services, AWS, and the Powered by AWS logo are trademarks of Amazon.com, Inc. or its affiliates
#---------------------------------------------------------------------------------------------------

namespace AWSS3;

if( !defined('CMS_VERSION') ) exit;

use \AWSSDK\Exception;

	// pass data to head section.
	$template_head = $this->_output_frontend_css();
	$templatetitle = '<!-- AWSS3 -->
	';
	if (!empty($template_head))
			$this->FrontEndCSS .= $templatetitle . $template_head . '
	';

	$debug = isset($params['debug']);
	$template = null;
	if (isset($params['detailtemplate'])) {
		$template = trim($params['detailtemplate']);
	}
	else {
		$tpl = \CmsLayoutTemplate::load_dflt_by_type('AWSS3::detail');
		if( !is_object($tpl) ) {
			audit('',$this->GetName(),'No default detail template found');
			return;
		}
		$template = $tpl->get_name();
	}

	try {
		if( !isset($params['prefix']) ) {
			throw new \AWSSDK\Exception('Key is not specified',500);
		}

		$key = urldecode($params['prefix']);
		$entry = utils::get_file_info($key);

	} catch (\AWSSDK\Exception $e) {
		utils::get_sdk()->_DisplayMessage($e->getText(),$e->getType());
	}


    $tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);
    $tpl_ob->assign('entry',$entry);
	//$tpl_ob->assign('settings',$this->GetSettingsValues());
    $tpl_ob->display();

	if($debug) 
	$tpl_ob->display('string:<pre>{get_template_vars}</pre>');

?>
