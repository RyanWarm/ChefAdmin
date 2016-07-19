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
            <td>ID</td>
            <td><?= !empty($id)? $id: ''; ?></td>
          </tr>
          <tr>
            <td>用户ID</td>
            <td><?= !empty($youzan_id)? $youzan_id: ''; ?></td>
          </tr>
          <tr>
            <td>菜品数量</td>
            <td><?= !empty($order_num)? $order_num: ''; ?></td>
          </tr>
          <tr>
            <td>支付类型</td>
            <td><?= !empty($pay_type)? $pay_type: ''; ?></td>
          </tr>
          <tr>
            <td>送餐费用</td>
            <td><?= !empty($post_fee)? $post_fee: ''; ?></td>
          </tr>
          <tr>
            <td>支付金额</td>
            <td><?= !empty($payment)? $payment: ''; ?></td>
          </tr>
          <tr>
            <td>折扣</td>
            <td><?= !empty($discount)? $discount: ''; ?></td>
          </tr>
          <tr>
            <td>总价</td>
            <td><?= !empty($total_fee)? $total_fee: ''; ?></td>
          </tr>
          <tr>
            <td>交易时间</td>
            <td><?= !empty($consign_time)? $consign_time: ''; ?></td>
          </tr>
          <tr>
            <td>订单状态</td>
            <td><?= !empty($status)? $status: ''; ?></td>
          </tr>
          <tr>
            <td>送餐时间</td>
            <td><input type="text" name="deliver_time" size="50" value="<?= $deliver_time; ?>" /></td>
          </tr>
          <tr>
            <td>用户留言</td>
            <td><textarea rows="5" cols="100" name="message"><?= $message; ?></textarea>
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
<script type="text/javascript"><!--
function image_upload(field, thumb) {
        $('#dialog').remove();
        
        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
        
        $('#dialog').dialog({
                title: 'Image Management',
		close: function (event, ui) {
			if ($('#' + field).attr('value') && $('#' + field).attr('value').indexOf('uc01') != -1) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(text) {
						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
                bgiframe: false,
                width: 800,
                height: 400,
                resizable: false,
                modal: false
        });
};
//--></script>
<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
