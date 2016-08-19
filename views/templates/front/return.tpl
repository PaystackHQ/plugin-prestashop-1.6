<div class="conf confirmation">
{foreach from=$vogURedirection item=value}
{if $value.value eq 'Approved'}
<h3>{l s='Congratulations! Your payment for the order is successful!' mod='voguepay'}</h3>
{elseif $value.value eq 'FAILED'}
<h3>{l s='Your payment for the order is Failed!' mod='voguepay'}</h3>
{elseif $value.value eq 'Pending'}
<h3>{l s='Congratulations! Your payment for the order is Pending!' mod='voguepay'}</h3>
{/if}
{/foreach}
<h4><u>Order Details</u> :</h4>
<p>&nbsp;</p>

{foreach from=$vogURedirection item=value}
{if $value.name neq 'return_url'}
<p><b>{$value.name}</b> : {$value.value}</p>
{/if}
{/foreach}

{foreach from=$vogURedirection item=value}
{if $value.name eq 'return_url'}
<a href="{$value.value}" name="Continue" class="button_large" style="float:right;">Continue</a>
{/if}
{/foreach}
</div>
