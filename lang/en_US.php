<?php
#A
$lang['admindescription'] = 'Store uploads in AWS S3';
$lang['ask_uninstall'] = 'Are you sure you want to uninstall the AWS S3 Uploads module?';
$lang['admin_save'] = "Save";
$lang['access_key'] = 'Access keys';
$lang['allowed'] = 'Allowed';
$lang['author_label'] = 'Posted by:';
$lang['access_region'] = 'Region';
$lang['access_key'] = 'AWS Access Key ID';
$lang['access_secret_key'] = 'AWS Secret Access Key';
#B
$lang['bucket_name'] = 'Bucket Name';
#C
$lang['category_label'] = 'Category:';
$lang['cancel'] = 'Cancel';
$lang['choose_file'] = 'Choose file';
$lang['config'] = 'AWS S3 Module Configuration';
$lang['custom_url'] = 'Custom URL';
$lang['captcha_title'] = 'Enter the text displayed in this image';
#D
$lang['date'] = 'Date';
$lang['delete'] = 'Delete';
$lang['default_options'] = 'Module defaults';
$lang['deleteselected'] = 'Delete the selected files/directories';
$lang['deleteselectedcancelled'] = 'Deletion of selected files canceled';
$lang['deletesuccess'] = 'Items successfully deleted';
$lang['desc_filemanager_settings'] = 'Edit File Manager Settings';
$lang['dirchmodfailmulti'] = 'Changing permissions on the directory failed, some of it\'s content may have gotten new permissions, though.';
$lang['dirchmodfailure'] = 'Changing permissions of the directory failed';
$lang['dirchmodsuccess'] = 'The directory successfully got new permissions';
$lang['dirchmodsuccessmulti'] = 'Changing permissions on the directory and it\'s content was successful';
$lang['direxists'] = 'The directory already exists';
$lang['dirnotemptyconfirm'] = 'The directory isn\'t empty! Do you really want to delete it, including all content and subdirectories?';
$lang['dirtreedeletecancelled'] = 'Deletion of directory canceled';
$lang['dirtreedeletefail'] = 'An error occurred when deleting this directory. Some of the contents may have been deleted, however.';
$lang['dirtreedeletesuccess'] = 'The directory including content was successfully deleted.';
#E
$lang['error'] = 'Error';
$lang['error_captchamismatch'] = 'Captch text entered did not match the image displayed';
$lang['error_setup'] = 'Please complete the AWS SDK Module Setup <a href="%s">here</a>';
$lang['expiry_interval'] = 'Remove cache files that are older than the specified number of minutes';
#F
$lang['file'] = 'File';
$lang['fielddef_allow_help'] = 'Specify a comma separated list of file extensions that are allowed. For example: pdf,gif,jpeg,jpg (keep lowercase)';
$lang['firstpage'] = '&lt;&lt;';
$lang['friendlyname'] = 'AWSS3 Module';
#G
$lang['getstarted'] = 'Get Started with AWS S3 Module';
#H
$lang['help_param_action'] = '
    Override the default action. Possible values are:
    <ul>
        <li>&quot;default&quot; - displays the summary view.</li>
        <li>&quot;detail&quot; - displays a specified entry in detail mode.</li>
        <li>&quot;search&quot; - displays the search form. Optional parameters that affect to this action only: <em>filter.</em></li>
        <li>&quot;category&quot; - displays the categories. Optional parameters that affect to this action only: <em>show_items, collapse, number_of_levels.</em></li>
        <li>&quot;archive&quot; - displays the archives. </li>
    </ul>';
$lang['help_param_prefix'] = 'A string of characters that can be the complete path in front of the object name. Project/WordFiles/...';
$lang['help_param_detailtemplate'] = 'The detail template you wish to use.';
$lang['help_param_summarytemplate'] = 'The summary template you wish to use.';
$lang['help_param_pagelimit'] = 'Maximum number of items to display (per page).  If this parameter is not supplied all matching items will be displayed.  If it is, and there are more items available than specified in the parameter, text and links will be supplied to allow scrolling through the results';
$lang['help_param_detailpage'] = 'Page to display item details in. Must be a page alias/id. Used to allow details to be displayed in a different page than summary.';
$lang['help_param_summarypage'] = 'Page to display item summary in. Must be a page alias/id. Used to allow summaries to be displayed in a different page than initiator.';

$lang['help_access_key'] = <<<EOT
<h3>Managing access keys (console)</h3>
<p>You can use the AWS Management Console to manage the access keys of an IAM user.<br></p>
<ul>
<li><a href="https://console.aws.amazon.com/iam" rel="noopener noreferrer" target="_blank"><span>IAM console</span></a></span>
<li>Use your AWS account ID or account alias, your IAM user name, and your password to sign in to the IAM console.</li>
<li>In the navigation bar on the upper right, choose your user name, and then choose Security credentials.</li>
</ul>

<h3>To create an access key</h3>
<ul>
<li>In the Access keys section, choose Create access key. If you already have two access keys, this button is deactivated and you must delete an access key before you can create a new one.</li>
<li>On the Access key best practices & alternatives page, choose your use case to learn about additional options which can help you avoid creating a long-term access key. If you determine that your use case still requires an access key, choose Other and then choose Next.</li>
<li>(Optional) Set a description tag value for the access key. This adds a tag key-value pair to your IAM user. This can help you identify and update access keys later. The tag key is set to the access key id. The tag value is set to the access key description that you specify. When you are finished, choose Create access key.</li>
<li>On the Retrieve access keys page, choose either Show to reveal the value of your user's secret access key, or Download .csv file. This is your only opportunity to save your secret access key. After you've saved your secret access key in a secure location, choose Done.</li>
</ul>
EOT;

$lang['help_opt_expiry_interval'] = 'Specify the amount of time (in minutes) that browsers should cache queries for. Setting this value to 0 disables the functionality. In most circumstances you should specify a value greater than 30';
$lang['help_bucket_name'] = 'This module will not create any S3 buckets. You have to create the bucket with public read access.';
$lang['help_use_custom_url'] = 'Enable this if you want to use custom URL';
$lang['help_custom_url'] = 'Custom URL can be a Cloudfront URL or if any url mapped on your bucket.';
#L
$lang['last_refreshed'] = 'Last refreshed';
$lang['logfilepath'] = 'Please provide your full Log File path, including filename';
$lang['line'] = 'Line';
$lang['label_size'] = 'Size';
$lang['label_url_original'] = 'Original Url';
$lang['label_url'] = 'Url';
$lang['label_url_presigned'] = 'Presigned Url';
$lang['label_mime'] = 'Mime';
$lang['lastpage'] = '&gt;&gt;';
#M
$lang['message'] = 'Message';
$lang['msg_vrfy_integrityverified'] = 'Connection Established';
$lang['msg_vrfy_noconn'] = 'Connection Not Established';
#N
$lang['nextpage'] = '&gt;';
#P
$lang['page'] = 'Page';
$lang['prompt_email'] = 'Email Address';
$lang['postinstall'] = 'AWS S3 Module was installed';
$lang['postrotate'] = 'Action for after image rotation';
$lang['predefined'] = 'Predefined Angles';
$lang['prompt_copy'] = 'Copy one or more Items';
$lang['prompt_dropfiles'] = 'Drop files here to upload';
$lang['prompt_move'] = 'Move Items to Another Directory';
$lang['prompt_use_custom_url'] = 'Use Custom URL';
$lang['prompt_custom_url'] = 'Custom URL';
$lang['prompt_detailpage'] = 'Detail page';
$lang['prompt_summarypage'] = 'Summary page';
$lang['prompt_itemsdisplayed'] = 'Number of items to display in the admin page';
$lang['prompt_go'] = 'Go';
$lang['prevpage'] = '&lt;';
$lang['prompt_of'] = 'of';
$lang['prompt_page'] = 'Page';
$lang['prompt_pagelimit'] = 'Page Limit';
#R
$lang['reviews'] = 'Reviews';
$lang['region'] = 'Region';
$lang['returntomodule'] = 'Return to Module';
$lang['required'] = 'Required';
$lang['refreshsuccess'] = 'Items successfully refreshed';
#S
$lang['submit'] = 'Submit';
$lang['sorry_nofiles'] = 'Sorry, we could not find any files';
$lang['successful_upload'] = 'Your file was successfully uploaded.  In the end, the filename we used was %s';
#T
$lang['type'] = 'Type';
$lang['type_AWSS3'] = 'AWSS3';
$lang['type_detail'] = 'Detail';
$lang['type_summary'] = 'Summary';
$lang['type_Reviews'] = 'Reviews';
$lang['title_changedir'] = 'Change working directory to this directory';
$lang['title_changeupdir'] = 'Go to the parent folder';
$lang['title_dropzone'] = 'You can drop files here from your file manager to automatically begin uploading';
$lang['title_refresh'] = 'Refresh Cached Data';
#U
$lang['upload'] = 'Upload';
#V
$lang['VALIDATION_ERROR'] = 'Please provide your Log File path';
#W
$lang['warn_delete_directory'] = 'A folder is selected for deletion, all objects in the folder will be deleted, and any new objects added while the delete action is in progress might also be deleted. If an object is selected for deletion, any new objects with the same name that are uploaded before the delete action is completed will also be deleted.';
$lang['warn_file_not_allowed'] = 'Filetype not allowed';
$lang['warn_file_missing'] = 'Select a file to upload';
?>