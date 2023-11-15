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

use AWSS3\utils;
use AWSS3\helpers;
use AWSSDK\encrypt;

final class bucket_query
{
    private $s3Client = null;
    private $_data = [ 
        'prefix'=>null, 
        'bucket'=>null, 
        'delimiter'=>null, 
        'maxkeys'=>1000, 
        'startafter'=>0,
        'returnid' => null,
        'detailpage' => null
    ];

    public function __construct($params = array())
    {
        //print_r($params);
        $this->s3Client = utils::getS3Client();

        foreach( $params as $key => $val ) {
            if( !array_key_exists($key,$this->_data) ) continue;
            // $this[$key] = $val;
            $this->_data[$key] = $val;
        }
    }

    public function __get($key)
    {
        if( isset($this->_data[$key]) ) return $this->_data[$key];
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'Prefix':
        case 'Bucket':
        case 'Delimiter':
        case 'Marker':
        case 'ContinuationToken':
            if( !is_null($val) ) $val = trim($val);
            $this->_data[$key] = $val;
            break;
        case 'MaxKeys':
            $this->_data[$key] = (int) max(1,min(1000,(int) $val));
            break;
        case 'StartAfter':
            $this->_data[$key] = (int) max(0,min(100000,(int) $val));
            break;
        case 'pagecount':
        case 'pagenum':
        case 'returnid':
            $this->_data[$key] = (int) $val;
            break;
        }
    }

    public function &execute_by_pages(){
        try {
            
            $resultPaginator = $this->s3Client->getPaginator('ListObjectsV2', $this->_data);
            $listing = $this->_data;
            $listing['date'] = time();
            $i = 1;
            $items = 0;
            foreach ($resultPaginator as $result) {
                //print_r($result);
                $listing['pages'][$i]['itemcount'] = $result['KeyCount'];
                $listing['pages'][$i]['items'] = $this->FetchAll($result);
                
                $i++;
                $items++;
            }

            foreach($listing['pages'] as $page){
                $listing['total'] += $page['itemcount'];
            }
        } catch (AwsException $e) {
            if($e->getStatusCode() == 404){
                $error_message = $bucket_id." ".$e->getAwsErrorMessage();
            } else {
                $error_message = $e->getMessage();
            }
            echo $error_message;
        }

        return $listing;
    }

    public function &execute()
    {

        try {

            $qparms = array();
            $qparms['Bucket'] = $this->bucket;
            $qparms['Delimiter'] = '/';
            $qparms['Prefix'] = $this->prefix;

            $resultPaginator = $this->s3Client->getPaginator('ListObjectsV2', $qparms);
            $listing = $this->_data;
            $listing['date'] = time();
            $listing['items'] = [];
            $items = 0;
            foreach ($resultPaginator as $result) {
                $listing['items'] = array_merge($listing['items'], $this->FetchAll($result));
                $items++;
            }
            utils::sortByProperty($listing['items'], 'name');

            $indicesToMove = [];
            for ($i = 0; $i < count($listing['items']); $i++) {
                if($listing['items'][$i]->dir == true)$indicesToMove[] = $i;
            }
            if (count($indicesToMove) > 0) {
                utils::moveItemsToBeginningByIndex($listing['items'], $indicesToMove);
            }
            $listing['total'] = count($listing['items']);  
            
        } catch (AwsException $e) {
            if($e->getStatusCode() == 404){
                $error_message = $bucket_id." ".$e->getAwsErrorMessage();
            } else {
                $error_message = $e->getMessage();
            }
            echo $error_message;
        }

        return $listing;        
    }

    /**
     * Fetch all of the records in this resultset as an array of objects.
     *
     * @return object[]
     */
    public function FetchAll($rs)
    {

        $__mod = \cms_utils::get_module('AWSS3');
        $config = cmsms()->GetConfig();
        $themeObject = \cms_utils::get_theme_object();
        $detailspage = $this->detailpage;
        $entryarray = [];
        $bucket_id = $this->bucket;

        //Directories
        foreach ($rs['CommonPrefixes'] as $dir) {

            $onerow = new \stdClass();
            $onerow->key = $dir['Prefix'];
            $onerow->name = basename($onerow->key);
            $onerow->dir = true;
            $onerow->mime = 'directory';
            $onerow->type = array('dir');
            $onerow->ext = '';
            $onerow->icon = $__mod->GetFileIcon($onerow->ext,true);
            $entryarray[]= $onerow;

        }

        foreach( $rs['Contents'] as $row ) {

                $onerow = new \stdClass();
                $onerow->key = $row['Key'];
                $onerow->key_encoded = urlencode($onerow->key);;
                $onerow->name = basename($onerow->key);
                $onerow->size = $row['Size'];
                $onerow->date = strtotime( $row['LastModified'] );
        
                $explodedfile = explode('.', $onerow->name); 
                $onerow->ext = array_pop($explodedfile);
                $onerow->type = array('file');

                $head = $this->s3Client->headObject([
                    'Bucket' => $bucket_id,
                    'Key' => $onerow->key
                ]);

                $onerow->mime = $head['ContentType'];

                if( strpos($onerow->mime,'text') !== FALSE ) {
                    $onerow->type[] = 'text';
                  }
                
                if($__mod->GetOptionValue("custom_url") !== '' && $__mod->GetOptionValue("use_custom_url") == true ){
                    $base_url = $__mod->GetOptionValue("custom_url").'/';
                } else {
                    $base_url = "https://".$bucket_id.".s3.".$__mod->GetPreference('access_region').".amazonaws.com/";
                }
                
                $onerow->url = $base_url.$onerow->key;
                $onerow->url_link = "<a href='" . $onerow->url . "' target='_blank' class=\"card-link\">" . $onerow->name . "</a>";
                $onerow->icon = $__mod->GetFileIcon($onerow->ext);
                $onerow->icon_link = "<a href='" . $onerow->url . "' target='_blank' class=\"card-link\">".$onerow->icon."</a>";
                $onerow->presigned_url = $__mod->CreateSignedLink($onerow->key);
                $onerow->presigned_link = "<a href='" . $onerow->presigned_url . "' class=\"card-link\">" . $onerow->name . "</a>";
                $onerow->presigned_icon_link = "<a href='" . $onerow->presigned_url . "' class=\"card-link\">" . $onerow->icon . "</a>";
                $onerow->detail_link = $__mod->create_url($id, 'detail', $detailspage, array('prefix' => $onerow->key_encoded));

                if( $config['url_rewriting'] == 'mod_rewrite')
                {
                    $string_array   = [];
                    $string_array[] = 'S3';
                    $string_array[] = 'file';
                    $string_array[] = $onerow->key_encoded;
                    $string_array[] = $detailspage;
                    $prettyurl = implode('/', $string_array);    
                    $item_detailurl = $__mod->create_url($id, 'detail', $detailspage, array('prefix' => $onerow->key_encoded), false, false, $prettyurl);
                    //needs fix
                    //$onerow->detail_link = $item_detailurl;
                }
                
                $entryarray[] = $onerow;
        
        }
        return $entryarray;
    }

    public function objectCount()
    {
        $result = $this->s3Client->listObjectsV2([
            'Bucket' => $this->Bucket,
            'Prefix' => $this->Prefix,
        ]);
        
        $objectCount = count($result['Contents']);
        return $objectCount;

    }

    public static function cache_query($qparms,$json_file_Path){

        $query = new bucket_query($qparms);
        $data = $query->execute();
        $json = json_encode($data);
        $tmp = encrypt::encrypt($json);
        file_put_contents($json_file_Path, $tmp);

        //$json = file_get_contents($json_file_Path);
        //$tmp = encrypt::decrypt($json);
        $data = json_decode($json, false);

        //print_r($data);

        return $data;

    }

    /** internal */
    public function fill_from_array($row)
    {
        foreach( $row as $key => $val ) {
            if( array_key_exists($key,$this->_data) ) {
                $this->_data[$key] = $val;
            }
        }
    }



} // end of class
