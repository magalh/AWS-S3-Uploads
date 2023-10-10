<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: AWSS3
# Authors: Magal Hezi, with CMS Made Simple Foundation able to assign new administrators.
# Copyright: (C) 2023 Magal Hezi, magal@pixelsolutions.biz
# AWSS3 is An addon module for CMS Made Simple to provide the ability to access and upload
# objects to Amazon (AWS) S3 Buckets.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !defined('CMS_VERSION') ) exit;

use \AWSS3\utils;
use \AWSS3\bucket_query;

$template = null;
if (isset($params['summarytemplate'])) {
    $template = trim($params['summarytemplate']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('AWSS3::summary');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default summary template found');
        return;
    }
    $template = $tpl->get_name();
}

    $bucket_id = $this->GetOptionValue('bucket_name');
    if(!isset($bucket_id) || empty($bucket_id)) {
        //throw new CmsException('no bucket selected');
        $this->_DisplayMessage('no bucket selected',500);
        return;
    }

    $template = 'orig_summary_template.tpl';
    $tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);

    $pagelimit = 10;
    if( isset( $params['pagelimit'] ) ) {
        $pagelimit = (int) ($params['pagelimit']);
    }
    else if( isset( $params['number'] ) ) {
        $pagelimit = (int) ($params['number']);
    }
    $pagelimit = max(1,min(1000,$pagelimit)); // maximum of 1000 entries.
    $pagecount = -1;
    $startelement = 0;
    $pagenumber = 1;
    $token_prev = '';
    $qparms = array();

    if( isset( $params['pagenumber'] ) && $params['pagenumber'] != '' ) {
        // if given a page number, determine a start element
        $pagenumber = (int)$params['pagenumber'];
        $startelement = ($pagenumber-1) * $pagelimit;

        //$qparms['StartAfter'] = $startelement;
    }

    $qparms['Bucket'] = $bucket_id;
    $qparms['Prefix'] = 'files/';
    $qparms['Delimiter'] = '/';
    $qparms['MaxKeys'] = $pagelimit;
    if(isset($params['ContinuationToken'])) {
        $qparms['ContinuationToken'] = $params['ContinuationToken'];
    }

    //print_r($qparms);

    $query = new bucket_query($qparms);
    $query_resp = $query->execute();

    $count = isset($query_resp['total']) ? $query_resp['total'] : '0';

    // determine a number of pages
    $pagecount = (int)($count / $pagelimit);
    if( ($count % $pagelimit) != 0 ) $pagecount++;
    
    if( $pagenumber >= $pagecount ) {
        $tpl_ob->assign('nextpage',$this->Lang('nextpage'));
        $tpl_ob->assign('lastpage',$this->Lang('lastpage'));
    }
    else {

        $params['pagenumber']=$pagenumber+1;
        $params['ContinuationToken'] = $query_resp['NextContinuationToken'];

        if( $pagenumber == 1 ) {
            $this->SetOptionValue('token_prev', '');
            $this->SetOptionValue('token_now', '');
            $this->SetOptionValue('token_next', $query_resp['NextContinuationToken']);
            $tpl_ob->assign('prevurl',$this->Lang('firstpage'));
            $tpl_ob->assign('lasturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
        }
        if( $pagenumber == 2 ) {
            $this->SetOptionValue('token_prev', '');
            $this->SetOptionValue('token_now', $query_resp['ContinuationToken']);
            $this->SetOptionValue('token_next', $query_resp['NextContinuationToken']);
            $params['pagenumber']=$pagenumber+1;
            $tpl_ob->assign('lasturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
            $params['pagenumber']=$pagenumber-1;
            unset($params['ContinuationToken']);
            $tpl_ob->assign('prevurl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
        } else {
            $this->SetOptionValue('token_prev', $this->GetOptionValue('token_now'));
            $this->SetOptionValue('token_now', $query_resp['ContinuationToken']);
            $this->SetOptionValue('token_next', $query_resp['NextContinuationToken']);
            $params['pagenumber']=$pagenumber+1;
            $tpl_ob->assign('lasturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
            $params['pagenumber']=$pagenumber-1;
            $params['ContinuationToken'] = $this->GetOptionValue('token_prev');
            $tpl_ob->assign('prevurl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
        }
        

    }
    $tpl_ob->assign('pagenumber',$pagenumber);
    $tpl_ob->assign('pagecount',$pagecount);
    $tpl_ob->assign('oftext',$this->Lang('prompt_of'));
    $tpl_ob->assign('pagetext',$this->Lang('prompt_page'));
    $tpl_ob->assign('token_prev',$this->GetOptionValue('token_prev'));
    $tpl_ob->assign('token_now',$this->GetOptionValue('token_now'));
    $tpl_ob->assign('token_next',$this->GetOptionValue('token_next'));

    $tpl_ob->assign('itemcount', $query_resp['itemcount']);
    $tpl_ob->assign('items', $query_resp['data']);
    $tpl_ob->assign('count', $count);
    $tpl_ob->assign('marker', $entry->key);

    $tpl_ob->display();

?>