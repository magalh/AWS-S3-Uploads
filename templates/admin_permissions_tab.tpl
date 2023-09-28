<div id="about_c">
    <div class="pageoverflow">
    <h3>Permissions overview</h3>
    <p>The plugin will not work until all the configs are completed</p><br>
    
    <h3>Configuring block public access settings for your S3 buckets</h3>
    <p>Follow these steps if you need to change the public access settings for a single S3 bucket.</p>
    <div class="procedure">
  <ol><li><p>Sign in to the AWS Management Console and open the Amazon S3 console at
         <a href="https://console.aws.amazon.com/s3/" rel="noopener noreferrer" target="_blank"><span>https://console.aws.amazon.com/s3/</span></a>.</p></li><li>
        <p>In the <b>Bucket name</b> list, choose the name of the bucket that you
          want.</p>
      </li><li>
        <p>Choose <b>Permissions</b>.</p>
      </li><li>
        <p>Choose <b>Edit</b> to change the public access settings for the bucket.
          For more information about the four Amazon S3 Block Public Access Settings, see <a href="https://docs.aws.amazon.com/AmazonS3/latest/userguide/access-control-block-public-access.html#access-control-block-public-access-options">Block public access
				settings</a>.</p>
      </li><li>
        <p>Choose the setting that you want to change, and then choose
          <b>Save</b>.</p>
      </li><li>
        <p>When you're asked for confirmation, enter <code class="userinput">confirm</code>. Then choose
            <b>Confirm</b> to save your changes.</p>
      </li></ol></div>
    <br>
    
    <h3>Bucket policy</h3>
    <p>The bucket policy, written in JSON, provides access to the objects stored in the bucket. Bucket policies don't apply to objects owned by other accounts</p>
    <p>Please attach the provided policy to your Bucket policy or if you already have a policy for this bucket, use the policy provided as an example to be merged with your existing setup.</p>
    <br>
    
    <pre>{$bucket_policy}</pre>
    </div>
</div>