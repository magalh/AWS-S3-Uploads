
{*if $pagecount > 1}
  <p>
{if $pagenumber > 1}
{$firstpage}&nbsp;{$prevpage}&nbsp;
{/if}
{$pagetext}&nbsp;{$pagenumber}&nbsp;{$oftext}&nbsp;{$pagecount}
{if $pagenumber < $pagecount}
&nbsp;{$nextpage}&nbsp;{$lastpage}
{/if}
</p>
{/if*}

<div class="card">
<ul class="list-group list-group-flush">
{foreach from=$items item=entry}
  <li class="list-group-item">{$entry->presigned_icon_link} {$entry->presigned_link}</li>
{/foreach}
</ul>
</div>
{if $pagecount > 1}
<nav aria-label="Page navigation example">
  <ul class="pagination">
  {if $pagenumber > 1}
    <li class="page-item"><a class="page-link" href="{$prevurl}">Previous</a></li>
    {/if}
    {if $pagenumber < $pagecount}
      <a class="page-link" href="{$lasturl}">Next</a>
      {/if}
  </ul>
</nav>
{/if}
<!-- End AWSS3 Display Template -->
{get_template_vars}