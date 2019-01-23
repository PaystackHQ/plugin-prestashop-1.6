{*
* 2016 Paystack
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/MIT
*
*  @author Paystack Payments <support@paystack.com>
*  @copyright  2016 Paystack
*  @license    https://opensource.org/licenses/MIT  MIT License
*}
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
    <div class="box">

  {else}
  <p class="alert alert-danger">Your payment for the order failed.</p>
  <div class="conf confirmation">

  {/if}

  <h4><u>Order Details</u> :</h4>
  <p>&nbsp;</p>

  {foreach from=$params item=value}
  {if $value.name neq 'return_url'}
  <p><b>{$value.name|escape:'htmlall':'UTF-8'}</b> :
    {if $value.name eq 'Total'}
     {displayPrice price=$value.value}
    {else}
     {$value.value|escape:'htmlall':'UTF-8'}
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
        href="{$return_url|escape:'htmlall':'UTF-8'}">
      <i class="icon-chevron-left"></i>{l s='Go to your order history page' mod='prestapaystack'}
    </a>

    {/if}
  </p>
{/if}
