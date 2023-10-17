<div class="bd-example">
{$startform}
{if $message}{$message}{/if}

<div class="form-group">
  <div class="custom-file">
    <input type="file" class="custom-file-input" id="{$actionid}customFile" name="{$actionid}input_browse">
    <label class="custom-file-label" for="{$actionid}customFile">{$mod->Lang('choose_file')}</label>
    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
</div>
{* captcha ?? *}
{if isset($captcha)}
  <div class="form-group">
    {$captcha}<br/>
    <input type="text" class="form-control mt-3" name="{$actionid}input_captcha" size="10" maxlength="10"/>
    <small id="captchaHelp" class="form-text text-muted">{$mod->Lang('captcha_title')}</small>
  </div>
{/if}
<button type="submit" name="{$actionid}input_submit" class="btn btn-primary">{$mod->Lang('submit')}</button>
{$endform}
</div>

<!-- remove this if not using bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script>
$(document).ready(function () {
  bsCustomFileInput.init()
})
</script> 
<!-- end remove -->
{get_template_vars}