{capture name=path}
    {l s='Order Status' mod='prestapaystack'}
{/capture}
{if $nb_products <= 0}
    <p class="alert alert-warning">
        {l s='Your shopping cart is empty.' mod='prestapaystack'}
    </p>
{else}
  {if $status eq 'approved'}
    <p class="alert alert-success">Congratulations! Your payment for the order is successful!</p>
  {else}
  <p class="alert alert-danger">Your payment for the order failed.</p>
  {/if}
  <div class="conf confirmation">

  <h4><u>Order Details</u> :</h4>
  <p>&nbsp;</p>

  {foreach from=$params item=value}
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
  <p class="cart_navigation clearfix" id="cart_navigation" style="display:inline-block;">
    {if $status neq 'approved'}
    <a
        class="button-exclusive btn btn-default"
        href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
      <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='prestapaystack'}
    </a>
    {else}
    <a
        class="button-exclusive btn btn-default"
        href="{$return_url}">
      <i class="icon-chevron-left"></i>{l s='Go to your order history page' mod='prestapaystack'}
    </a>

    {/if}
  </p>
{/if}
