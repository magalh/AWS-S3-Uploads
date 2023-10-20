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

use \AWSS3\aws_s3_utils;
use \AWSS3\bucket_query;
use \AWSS3\encrypt;

$mod_fm = \cms_utils::get_module('FileManager');

    $bucket_id = $this->GetOptionValue('bucket_name');
    $nminutes     = (int)$this->GetPreference('expiry_interval', 180);
    if ($nminutes == '') $nminutes = 0;

    if(!isset($bucket_id) || empty($bucket_id)) {
        //throw new CmsException('no bucket selected');
        $this->_DisplayMessage('no bucket selected',500);
        return;
    }

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

    $template = 'orig_summary_template.tpl';
    $tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);

    $pagelimit = 10;
    if( isset( $params['pagelimit'] ) ) {
        $pagelimit = (int) ($params['pagelimit']);
    }
    $pagelimit = max(1,min(1000,$pagelimit)); // maximum of 1000 entries.
    $pagecount = -1;
    $startelement = 0;
    $pagenumber = 1;
    $prefix = $params['prefix']?$params['prefix']:'';

    $explodedprefix=explode('/', $prefix); 
    $path_parts = [];
    for( $i = 0; $i < count($explodedprefix); $i++ ) {
        $obj = new \StdClass;
        if( !$explodedprefix[$i] ) continue;
        $obj->name = $explodedprefix[$i];
        $obj->prefix = implode('/',array_slice($explodedprefix,0,$i+1)).'/';
        if( $i < count($explodedprefix) - 1 ) {
            $params['prefix'] = $obj->prefix;
            $obj->url = $this->CreateFrontendLink($id,$returnid,'default','',$params, '', true);
        } else {
            // the last entry... no link
        }
        $path_parts[] = $obj;
    }


    if( isset( $params['pagenumber'] ) && $params['pagenumber'] !== '' ) {
        $pagenumber = (int)$params['pagenumber'];
        $startelement = ($pagenumber-1) * $pagelimit;
    }
    $endelement = $startelement + $pagelimit - 1;

    if( $path_parts[1] ) {
        $counter = count($path_parts);
        $updir = $path_parts[$counter-2]->prefix;
        $icon = $mod_fm->GetModuleURLPath().'/icons/themes/default/actions/dir_up.gif';
        $img_tag = '<img src="'.$icon.'" width="32" title="'.$this->Lang('title_changeupdir').'"/>';
        $params['prefix'] = $updir;
        $diriconlink = $this->CreateFrontendLink($id,$returnid, 'default', $img_tag, $params);
        $dirlink = "<a class=\"dirlink\" href=\"{$url}\" title=\"{$this->Lang('title_changeupdir')}\">..</a>";

        $tpl_ob->assign('updir',$updir);
        $tpl_ob->assign('dirlink',$dirlink);
        $tpl_ob->assign('diriconlink',$diriconlink);
    }

    $qparms = array();
    $qparms['Bucket'] = $bucket_id;
    $qparms['Prefix'] = $prefix;
    $qparms['Delimiter'] = '/';
    //$qparms['MaxKeys'] = $pagelimit;

    $tpl_ob->assign('qparms',$qparms);

    $json_file_Path = $this->getCacheFile($bucket_id,$prefix);

    if($nminutes == 0) {
        $data = bucket_query::cache_query($qparms,$json_file_Path);
    } else {
        
        if (!file_exists($json_file_Path)) {
            $data = bucket_query::cache_query($qparms,$json_file_Path);
        } else {

            $json = file_get_contents($json_file_Path);
            $tmp = encrypt::decrypt($json);
            $data = json_decode($tmp, false);
        
            $enddate  = strtotime(sprintf("+%d minutes", $nminutes), $data->date);
            if (time() >= $enddate) {
                $data = bucket_query::cache_query($qparms,$json_file_Path);
            }

        }
    }

    $count = isset($data->total) ? $data->total : '0';
    $pagecount = (int)($count / $pagelimit);
    if( ($count % $pagelimit) != 0 ) $pagecount++;
    
    // Assign some pagination variables to smarty
    if( $pagenumber == 1 ) {
        $tpl_ob->assign('prevpage',$this->Lang('prevpage'));
        $tpl_ob->assign('firstpage',$this->Lang('firstpage'));
    }
    else {
        $params['pagenumber']=$pagenumber-1;
        $tpl_ob->assign('prevpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('prevpage'),$params));
        $tpl_ob->assign('prevurl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
        $params['pagenumber']=1;
        $tpl_ob->assign('firstpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('firstpage'),$params));
        $tpl_ob->assign('firsturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
    }

    if( $pagenumber >= $pagecount ) {
        $tpl_ob->assign('nextpage',$this->Lang('nextpage'));
        $tpl_ob->assign('lastpage',$this->Lang('lastpage'));
    }
    else {
        $params['pagenumber']=$pagenumber+1;
        $tpl_ob->assign('nextpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('nextpage'),$params));
        $tpl_ob->assign('nexturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
        $params['pagenumber']=$pagecount;
        $tpl_ob->assign('lastpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('lastpage'),$params));
        $tpl_ob->assign('lasturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
    }

    $tpl_ob->assign('bucket',$bucket_id);
    $tpl_ob->assign('cachefile',$json_file_Path);
    $tpl_ob->assign('lastupdate', $data->date);
    $tpl_ob->assign('count', $count);
    $tpl_ob->assign('pagenumber',$pagenumber);
    $tpl_ob->assign('startelement',$startelement);

    if($endelement >= $count)$endelement=$count-1;

    $tpl_ob->assign('endelement', $endelement);
    $tpl_ob->assign('pagecount',$pagecount);
    $tpl_ob->assign('oftext',$this->Lang('prompt_of'));
    $tpl_ob->assign('pagetext',$this->Lang('prompt_page'));

    
    if ($startelement < 0 || $startelement > $endelement) {
        echo "Invalid indexes.";
    } else {
        // Slice the array to extract elements between the indexes
        $selectedItems = array_slice($data->items, $startelement, $endelement - $startelement + 1);

        foreach( $selectedItems as $row ) {

            $onerow = $row;
            if($onerow->dir){
                $sendtodetail = array('prefix'=>$onerow->key);
                if( isset( $params['pagelimit'] ) ) {
                    $sendtodetail = array_merge($sendtodetail,array('pagelimit'=>$pagelimit));
                }
                $onerow->link = $this->CreateLink($id, 'default', $returnid, '', $sendtodetail,'', true, false, '', true,$prettyurl);
                $onerow->icon_link = "<a href='" . $onerow->link . "' class=\"card-link\">".$onerow->icon."</a>";
            }


            $entryarray[]= $onerow;
        }

        //print_r($selectedItems);
    }

    $tpl_ob->assign('items', $entryarray);
    
    $tpl_ob->assign('startdate', $data->date);
    $tpl_ob->assign('enddate', $enddate);
    $tpl_ob->assign('cachetime', $nminutes);

    $smarty->assign('path_parts',$path_parts);


    $tpl_ob->display();

?>