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
      <?php if (!empty($request_id) && empty($issued_at)) { ?>
        <a onclick="issueInviteCode(false);" class="button">生成邀请码并手动通知</a>
        <a onclick="issueInviteCode(true);" class="button">生成邀请码并邮件通知</a>
        <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>

      <?php } else if (!empty($request_id) && empty($notify_method)) { ?>
        <a onclick="sendNotifyEmail()" class="button">发送通知邮件</a>
        <a onclick="manualNotify()" class="button">手动通知</a>
        <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a> 
     <?php } else { ?>
            <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
      <?php } ?>

      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>Request ID</td>
            <td><?= !empty($request_id)? $request_id: ''; ?></td>
          </tr>
          <tr>
            <td>申请帐号类型</td>
            <td>
              <?php if (empty($request_id)) { ?>
              <select name="class">
                <option value="basic" selected="selected">个人版</option>
                <option value="ent" selected="selected">企业版</option>
              </selected>
              <?php } else { 
                echo $class=="ent"?'企业版':'个人版';
               } ?>
            </td>
          </tr>
          <tr>
            <td>email</td>
            <td><input type="text" name="email" size="100" value="<?php echo $email; ?>" />
          </tr>
          <tr>
            <td><span class="required">*</span>姓名</td>
            <td><input type="text" name="name" size="100" value="<?= $name; ?>" />
          </tr>
          <tr>
            <td>公司</td>
            <td><input type="text" name="company" size="100" value="<?= $company; ?>" />
          </tr>
          <tr>
            <td>职位</td>
            <td><input type="text" name="position" size="100" value="<?= $position; ?>" />
          </tr>
          <tr>
            <td>申请时间</td>
            <td><?= $created_at; ?></td>
          </tr>
          <tr>
            <td>已分配邀请码</td>
            <td><?= $issued_code; ?></td>
          </tr>
          <tr>
            <td>邀请码注册链接</td>
            <td><?php if (!empty($issued_code)) {
              if ($class=='ent') { 
                echo "http://u2top.cn/enterprise/login?invite_code=" . $issued_code;
              } else {
                echo "http://u2top.cn/pub?invite_code=" . $issued_code;
              }
              }             
              ?>
            </td>
          </tr>
          <tr>
            <td>分配邀时间</td>
            <td><?= $issued_at; ?></td>
          </tr>
          <tr>
            <td>通知方式</td>
            <td><?= $notify_method; ?></td>
          </tr>
          <tr>
            <td>通知时间</td>
            <td><?= $notify_time; ?></td>
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

function manualNotify() {

    $('#form').attr('action', '<?=$action_manual_notify; ?>');

    $('#form').submit();
}


</script>

<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
