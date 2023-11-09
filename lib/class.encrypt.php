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

final class encrypt
{
    /**
     * @ignore
     */
    protected function __construct() {}

    /**
     * Encrypt some data
     *
     * @param string $key The encryption key.  The longer and more unique this string is the more secure the encrypted data is.  The key should also be kept in a secure location.
     * @param string $data The data to encrypt.
     * @return string The encrypted data, or FALSE
     */
    static public function encrypt(string $data)
    {
        $key = CMS_VERSION.__FILE__;
        return self::openssl_encrypt( $key, $data );
    }

    /**
     * Encrypt some data using openssl libraries
     *
     * @param string $key
     * @param string $data
     * @return string
     */
    static protected function openssl_encrypt(string $key,string $data)
    {
        if( !function_exists('openssl_encrypt') ) return FALSE;

        $cipher = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        return $encrypted.'::'.$iv;
    }

    /**
     * Decrypt previously encrypted data
     *
     * @param string $key The key used for encrypting the data.
     * @param string $encdata The encrypted data"
     * @return string the decrypted data.  or FALSE
     */
    static public function decrypt(string $encdata)
    {
        // use openssl and see if we get any data
        // if openssl fails, try mcrypt.
        $key = CMS_VERSION.__FILE__;
        return self::openssl_decrypt( $key, $encdata );
    }

    /**
     * Decrypt some data using openssl
     *
     * @param string $key
     * @param string $encdata
     * @return string
     */
    static protected function openssl_decrypt(string $key,string $encdata)
    {
        list( $enc, $iv ) = explode('::', $encdata );
        return openssl_decrypt($enc, 'aes-256-cbc', $key, 0, $iv);
    }

} // end of class
