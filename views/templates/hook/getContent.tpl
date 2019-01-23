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
<fieldset>
  {if isset($confirmation)}
    <div class="alert alert-success">Settings updated</div>
  {/if}
  <div class="vogu-module-inner-wrap"  style="width:50%;margin:0 auto;margin-bottom:30px;">



		<img src="../modules/prestapaystack/views/img/paystack_logo.png" alt="logo" class="vogu-logo" style="height:60px;display:inline-block" />

    {* <h2  style="display:inline-block" >Configuration</h2> *}


	</div>
  <div class="panel">
    <div class="panel-heading">
      <legend><img src="../img/admin/cog.gif" alt="" width="16" />Configuration</legend>
    </div>
    <form action="" method="post">

      <div class="form-group clearfix">
        <label class="col-lg-3">Toggle Mode:</label>
        <div class="col-lg-9">
          <img src="../img/admin/enabled.gif" alt="" />
          <input type="radio" id="enable_comments_1" name="mode" value="live" {if $mode eq 'live'}checked{/if} />
          <label class="t" for="enable_comments_1">Live</label>
          <img src="../img/admin/disabled.gif" alt="" />
          <input type="radio" id="enable_comments_0" name="mode" value="test" {if $mode eq 'test'}checked{/if} />
          <label class="t" for="enable_comments_0">Test</label>
        </div>
      </div>
       <div class="form-group clearfix">
        <label class="col-lg-3">Test Secret Key:</label>
        <div class="col-lg-9">
          <input type="text" name="test_secretkey"  value="{$test_secretkey|escape:'htmlall':'UTF-8'}"  />

        </div>
      </div>
      <div class="form-group clearfix">
        <label class="col-lg-3">Test Public Key:</label>
        <div class="col-lg-9">
          <input type="text" name="test_publickey" value="{$test_publickey|escape:'htmlall':'UTF-8'}" />

        </div>
      </div>
      <div class="form-group clearfix">
        <label class="col-lg-3">Live Secret Key:</label>
        <div class="col-lg-9">
          <input type="text" name="live_secretkey"  value="{$live_secretkey|escape:'htmlall':'UTF-8'}"  />

        </div>
      </div>
      <div class="form-group clearfix">
        <label class="col-lg-3">Live Public Key:</label>
        <div class="col-lg-9">
          <input type="text" name="live_publickey" value="{$live_publickey|escape:'htmlall':'UTF-8'}" />

        </div>
      </div>
     
      <div class="form-group clearfix">
        <label class="col-lg-3">Payment style:</label>
        <div class="col-lg-9">
          <input type="radio" id="enable_comments_1" name="style" value="inline" {if $style eq 'inline'}checked{/if} />
          <label class="t" for="enable_comments_1">Inline</label>
          <input type="radio" id="enable_comments_0" name="style" value="lazy" {if $style eq 'lazy'}checked{/if} />
          <label class="t" for="enable_comments_0">Lazy Inline</label>
        </div>
      </div>

      <div class="panel-footer">
        <input class="btn btn-default pull-right" type="submit" name="save_settings" value="Save" />
      </div>
    </form>
  </div>
</fieldset>
