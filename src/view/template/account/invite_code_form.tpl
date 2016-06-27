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

      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>邀请码</td>
            <td><?= !empty($code)? $code: ''; ?></td>
          </tr>
          <tr>
            <td>帐号类型</td>
            <td>
                <?= $class=="ent"?'企业版':'个人版'; ?>
            </td>
          </tr>
          <tr>
            <td>邀请码注册链接</td>
            <td><?php if ($class=='ent') { 
                echo "http://u2top.cn/enterprise/login?invite_code=" . $code;
              } else {
                echo "http://u2top.cn/pub?invite_code=" . $code;
              }
              ?>
            </td>
          </tr>
          <tr>
            <td>创建时间</td>
            <td><?= $created_at; ?></td>
          </tr>
          <tr>
            <td>已注册用户ID</td>
            <td><?= $assignee; ?></td>
          </tr>
          <tr>
            <td>注册时间</td>
            <td><?= $assigned_at; ?></td>
          </tr>
          <tr>
            <td>邀请码申请ID</td>
            <td><a href="index.php?route=account/invite_code_request/update&request_id=<?= $request_id; ?>" ><?=$request_id ?></td>
          </tr>
          <tr>
            <td>更新时间</td>
            <td><?= $updated_at; ?></td>
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
