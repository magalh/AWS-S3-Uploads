<?php
if( !defined('CMS_VERSION') ) exit;
$this->RemovePermission(MANAGE_PERM);

// Remove all preferences for this module
$this->RemovePreference();
?>