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
    {l s='Pay with Paystack' mod='prestapaystack'}
{/capture}


<h1 class="page-heading">
{l s='Order summary' mod='prestapaystack'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nb_products <= 0}
  <p class="alert alert-warning">
      {l s='Your shopping cart is empty.' mod='prestapaystack'}
  </p>
{else}
  <form action="{$link->getModuleLink('prestapaystack', 'confirm', [], true)|escape:'html'}" id="paystack_form" method="post">
  <div class="box cheque-box">
    <h3 class="page-subheading">
            {l s='PAYSTACK Payment' mod='prestapaystack'}
    </h3>
    <p class="cheque-indent">
      <strong class="dark">
                {l s='You have chosen to pay with Paystack.' mod='prestapaystack'} {l s='Here is a short summary of your order:' mod='prestapaystack'}
      </strong>
    </p>
    <p>
      - {l s='The total amount of your order is' mod='prestapaystack'}
      <span id="amount" class="price">{displayPrice price=$total_amount}</span>
          {if $use_taxes == 1}
              {l s='(tax incl.)' mod='prestapaystack'}
          {/if}
    </p>
    <p>
    -
      {if $currencies|@count > 1}
      {l s='We allow several currencies to be sent via MyMod Payment.' mod='prestapaystack'}
      <div class="form-group">
        <label>{l s='Choose one of the following:' mod='prestapaystack'}</label>
        <select id="currency_payment" class="form-control" name="currency_payment">
            {foreach from=$currencies item=currency}
            <option value="{$currency.id_currency|escape:'htmlall':'UTF-8'}" {if $currency.id_currency == $cart_currency}selected="selected"{/if}>
                {$currency.name|escape:'htmlall':'UTF-8'}
            </option>
            {/foreach}
        </select>
       </div>
      {else}
        {l s='We allow the following currency to be sent via Paystack:' mod='prestapaystack'}&nbsp;<b>{$currencies.0.name|escape:'htmlall':'UTF-8'}</b>
        <input type="hidden" name="currency_payment" value="{$currencies.0.id_currency|escape:'htmlall':'UTF-8'}" />
      {/if}
      
        <input type="hidden" name="amounttotal" value="{$total_amount|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="email" value="{$email|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="txn_code" value="{$code|escape:'htmlall':'UTF-8'}" />
    </p>
    <br />
    ITEMS:

    {foreach from=$products item=product}
    <p>
      {$product.name|escape:'htmlall':'UTF-8'} x <b>{$product.cart_quantity|escape:'htmlall':'UTF-8'}</b> -  {displayPrice price=$product.total_wt}
    </p>
    {/foreach}
  </div>

    <p class="cart_navigation clearfix" id="cart_navigation" style="display:inline-block;">
      <a
          class="button-exclusive btn btn-default"
          href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
        <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='prestapaystack'}
      </a>
    </p>
    {if $style == 'inline'}
      <script src="https://js.paystack.co/v1/inline.js"></script>
      <span class="cart_navigation">
      <a href="#" id="paystack_button" class="button btn btn-default standard-checkout button-medium pull-right" title="Pay now" style="">
				<span>Pay now<i class="icon-chevron-right right"></i></span>
			</a>
      </span>
          
    {else}
      <script
        src="https://js.paystack.co/v1/inline.js"
        data-key="{$key|escape:'htmlall':'UTF-8'}"
        data-email="{$email|escape:'htmlall':'UTF-8'}"
        data-amount="{$total_amount*100|escape:'htmlall':'UTF-8'}"
        data-currency="{$currency->iso_code|escape:'htmlall':'UTF-8'}"
        data-ref="{$code|escape:'htmlall':'UTF-8'}">
      </script>
    {/if}
   
    </form>
    <style>
      .paystack-trigger-btn{
        float:right;
      }
    </style>
    {if $style == 'inline'}
     <script>
        $('#paystack_button').on('click', function (e) {
            // e.preventDefault();
            $("#paystack_form").unbind("submit");
            var handler = PaystackPop.setup({
              key: "{$key|escape:'htmlall':'UTF-8'}",
              email: "{$email|escape:'htmlall':'UTF-8'}",
              amount: "{$total_amount*100|escape:'htmlall':'UTF-8'}",
              currency: "{$currency->iso_code|escape:'htmlall':'UTF-8'}",
              ref: "{$code|escape:'htmlall':'UTF-8'}", 
              metadata:{
                "custom_fields":[
              {
                "display_name":"Plugin",
                "variable_name":"plugin",
                "value":'presta-1.6'
              }
            ]
              },
              callback: function(response){
                  $( "#paystack_form" ).submit();
              },
              onClose: function(){
                  
              }
            });
            handler.openIframe();
          });
    </script>
     
    {/if}
    
{/if}
