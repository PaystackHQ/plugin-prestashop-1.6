{capture name=path}
    {l s='Order Status' mod='prestapaystack'}
{/capture}
<div class="conf confirmation">
  {if $status eq 'approved'}
  <h3>{l s='Congratulations! Your payment for the order is successful!' mod='prestapaystack'}</h3>
  {else}
  <h3>{l s='Your payment for the order Failed!' mod='prestapaystack'}</h3>
  {/if}
<h4><u>Order Details</u> :</h4>
<p>&nbsp;</p>

{foreach from=$vogURedirection item=value}
{if $value.name neq 'return_url'}
<p><b>{$value.name}</b> :
  {if $value.name eq 'Total'}
   {displayPrice price=$value.value}
  {else}
   {$value.value}
  {/if}

</p>
{/if}
{/foreach}

</div>
{if $status neq 'approved'}
<p class="cart_navigation clearfix" id="cart_navigation" style="display:inline-block;">
  <a
      class="button-exclusive btn btn-default"
      href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
    <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='prestapaystack'}
  </a>
</p>
{else}
<a href="{$return_url}" name="Continue" class="btn btn-default" style="float:right;">Continue</a>
{/if}
