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
            <td>score</td>
            <td><?= !empty($score)? $score: ''; ?></td>
          </tr>
          <tr>
            <td>帐号链接</td>
            <td><a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></td>
          </tr>
          <tr>
            <td>类别</td>
            <td><?= $category; ?></td>
          </tr>
          <tr>
            <td>状态</td>
            <td>
		<select name="status">
                        <?php foreach ($statusTypes as $statusType) { ?>
                                <option value=<?php echo $statusType['status_value']; ?> <?php if($status==$statusType['status_value']){ ?> selected<?php }?>><?php echo $statusType['status_name']; ?></option>
                        <?php } ?>
                </select>
	    </td>
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
