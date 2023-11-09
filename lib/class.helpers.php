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