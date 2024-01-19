<div class="holidayWrapper">
 {foreach $items as $item}
 <div class="holiday">
 <div class="row">
 <div class="col-sm-6">
 <a href="{cms_action_url action='detail' hid=$item->id}">{$item->name}</a>
</div>
<div class="col-sm-6 text-right">{$item->the_date|date_format:'%x'}</div>
 </div>
 </div>
 {foreachelse}
 <div class=”alert alert-danger”>{$mod->Lang('sorry_nofiles')}</div>
 {/foreach}
</div>