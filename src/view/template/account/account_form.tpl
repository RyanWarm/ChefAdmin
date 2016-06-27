<?php echo $header; ?>
<?php $lang = $this->language; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button">返回</a>
        <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a> 
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>zid</td>
            <td><?= !empty($zid)? $zid: ''; ?></td>
          </tr>
          <tr>
            <td>帐号类型</td>
            <td>
                <?= $class=="ent"?'企业版':'个人版'; ?>
            </td>
          </tr>
          <tr>
            <td>email</td>
            <td><?= !empty($email)? $email: ''; ?></td>
          </tr>
          <tr>
            <td>邀请码</td>
            <td><?= !empty($code)? $code: ''; ?></td>
          </tr>
          <tr>
            <td>邮箱验证</td>
            <td>
                <?= !empty($verified) ? '已验证' : '未验证'; ?>
	    </td>
          </tr>
          <tr>
            <td>注册时间</td>
            <td><?= $created_at; ?></td>
          </tr>
          <tr>
            <td>社交网络</td>
            <td><?= !empty($network) ? $network : ''; ?></td>
          </tr>
          <tr>
            <td>社交帐号</td>
            <td><a href="http://weibo.com/u/<?=$profile_id ?>" target="_blank"><?php echo $profile_id; ?></a></td>
          </tr>
	  <tr>
	    <td>角色类别</td>
	    <td><div class="scrollbox" style="width:300px;height:100px">
	      <?php $class = 'odd'; ?>
	      <?php foreach ($roleTypes as $roleType) { ?>
	      <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
	      <div class="<?php echo $class; ?>">
	        <?php if (in_array($roleType['role_id'], $account_roles)) { ?>
	        <input type="checkbox" name="account_roles[]" value="<?php echo $roleType['role_id']; ?>" checked="checked" />
	        <?php echo $roleType['role_name']; ?>
	        <?php } else { ?>
	        <input type="checkbox" name="account_roles[]" value="<?php echo $roleType['role_id']; ?>" />
	        <?php echo $roleType['role_name']; ?>
	        <?php } ?>
	      </div>
	      <?php } ?>
	    </div>
	    <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
	  </tr>
          <tr>
            <td>Hourly Search Count</td>
            <td><input type="text" name="hs_count" value="<?= $cachedCounters['hs']; ?>" /></td>
          </tr>
          <tr>
            <td>Hourly Profile Count</td>
            <td><input type="text" name="hp_count" value="<?= $cachedCounters['hp']; ?>" /></td>
          </tr>
          <tr>
            <td>Daily Search Count</td>
            <td><input type="text" name="ds_count" value="<?= $cachedCounters['ds']; ?>" /></td>
          </tr>
          <tr>
            <td>Daily Profile Count</td>
            <td><input type="text" name="dp_count" value="<?= $cachedCounters['dp']; ?>" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript">
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m'
});
$('.time').timepicker({timeFormat: 'h:m'});
</script> 
<script type="text/javascript">

function issueInviteCode(send_email) {

    $('#form').attr('action', send_email?'<?=$action_issue_invite_code_with_email; ?>':'<?=$action_issue_invite_code; ?>');

    $('#form').submit();
}

function sendNotifyEmail() {

    $('#form').attr('action', '<?=$action_send_notify_email; ?>');

    $('#form').submit();
}


</script>

<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
