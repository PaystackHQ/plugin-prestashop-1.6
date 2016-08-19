{*



* 2007-2013 PrestaShop



*



* NOTICE OF LICENSE



*



* This source file is subject to the Academic Free License (AFL 3.0)



* that is bundled with this package in the file LICENSE.txt.



* It is also available through the world-wide-web at this URL:



* http://opensource.org/licenses/afl-3.0.php



* If you did not receive a copy of the license and are unable to



* obtain it through the world-wide-web, please send an email



* to license@prestashop.com so we can send you a copy immediately.



*



* DISCLAIMER



*



* Do not edit or add to this file if you wish to upgrade PrestaShop to newer



* versions in the future. If you wish to customize PrestaShop for your



* needs please refer to http://www.prestashop.com for more information.



*



*  @author VoguePay <contact@voguepay.com>



*  @copyright  20012-2013 VoguePay



*  @version  Release: $Revision: 14011 $



*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)



*  VoguePay



*}


{capture name=path}
  <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='voguepay'}">{l s='Checkout' mod='voguepay'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='voguepay payment' mod='voguepay'}
{/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='voguepay'}</h2>
{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
  <p class="warning">{l s='Your shopping cart is empty.' mod='voguepay'}</p>
{/if}

<h3>{l s='VoguePay payment' mod='voguepay'}</h3>
<div style="text-align:center;">



{if isset($error)}



<p style="color:red">{l s='An error occured, please try again later.' mod='voguepay'}</p>



{else}

<p>
  <img src="{$modulePath}img/logo.png" alt="{l s='VoguepayPay' mod='voguepay'}" width="86" height="49" style="float:left; margin: 0px 10px 5px 0px;" />

  <br/><br />
  {l s='Here is a short summary of your order:' mod='voguepay'}
</p>

<p style="font-size:15px;">{l s='You are going to be redirected to VoguePay website for your payment.' mod='voguepay'}</p>



<p>
  <b>{l s='Please confirm your order by clicking "I confirm my order".' mod='voguepay'}</b>
</p>



<form action="{$formLink}" method="POST" id="formVogU">



  {foreach from=$vogURedirection item=value}



  <input type="hidden" value="{$value.value}" name="{$value.name}"/>



  {/foreach}


  <p class="cart_navigation" id="cart_navigation">
    <input class="exclusive_large" id="voguSubmit" type="submit" value="{l s='Please click here' mod='voguepay'}" />
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='voguepay'}</a>
  </p>



</form>



</div>



{literal}



<script type="text/javascript">
$('#voguSubmit').click(function(){
	  $('#formVogU').submit();
});
</script>
{/literal}



{/if}
