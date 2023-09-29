{strip}
<div class="{$class}" role="alert">
    {if is_array($options) && $options[0] != ''}
    {foreach from=$options item='option'}
        {if $option}
        <p>{$option}</p>
        {/if}
    {/foreach}
    {else}
        <p>{$options}</p>
    {/if}
    </div>	
{/strip}
    