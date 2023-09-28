<div class="{$errorclass}">
<p class="message">
{if isset($errors) && $errors[0] != ''}
    {foreach from=$errors item='error'}
        {if $error}
        <p>{$error}</p>
        {/if}
    {/foreach}
{/if}
</div>