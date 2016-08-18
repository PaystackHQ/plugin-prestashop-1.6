<fieldset>
  {if isset($confirmation)}
    <div class="alert alert-success">Settings updated</div>
  {/if}
  <h2>Paystack Configuration</h2>
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
        <label class="col-lg-3">Test Public Key:</label>
        <div class="col-lg-9">
          <input type="text" name="test_publickey" value="{$test_publickey}" />

        </div>
      </div>
      <div class="form-group clearfix">
        <label class="col-lg-3">Test Secret Key:</label>
        <div class="col-lg-9">
          <input type="text" name="test_secretkey"  value="{$test_secretkey}"  />

        </div>
      </div>

      <div class="panel-footer">
        <input class="btn btn-default pull-right" type="submit" name="save_settings" value="Save" />
      </div>
    </form>
  </div>
</fieldset>
