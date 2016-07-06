<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/category.png" alt="" />当前交易</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">ID</td>
              <td class="left">用户ID</td>
              <td class="left"><a href="index.php?route=trade/trade&sort=order_num">菜品数量↓</a></td>
              <td class="left">支付类型</td>
              <td class="left"><a href="index.php?route=trade/trade&sort=post_fee">送餐费用↓</a></td>
              <td class="left"><a href="index.php?route=trade/trade&sort=payment">支付金额↓</a></td>
              <td class="left"><a href="index.php?route=trade/trade&sort=discount">折扣↓</a></td>
              <td class="left"><a href="index.php?route=trade/trade&sort=total_fee">总价↓</a></td>
              <td class="left"><a href="index.php?route=trade/trade&sort=consign_time">交易时间↓</a></td>
              <td class="left">留言</td>
              <td class="left">订单状态</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 40px;" name="filter_id" value="<?php echo $filter_id; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_youzan_id" value="<?php echo $filter_youzan_id; ?>" /></td>
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_pay_type" value="<?php echo $filter_pay_type; ?>" /></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_message" value="<?php echo $filter_message; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_status" value="<?php echo $filter_status; ?>" /></td>
              <td align="right"><a onclick="filter();" class="button">筛选</a></td>
            </tr>
            <?php if ($list) { ?>
            <?php foreach ($list as $item) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($item['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $item['id']; ?></td>
              <td class="left"><a target="_blank" href="index.php?route=customer/customer&filter_id=<?php echo $item['youzan_id']; ?>"><?php echo $item['youzan_id']; ?></a></td>
              <td class="left"><?php echo $item['order_num']; ?></td>
              <td class="left"><?php echo $item['pay_type']; ?></td>
              <td class="left"><?php echo $item['post_fee']; ?></td>        
              <td class="left"><?php echo $item['payment']; ?></td>
              <td class="left"><?php echo $item['discount']; ?></td>
              <td class="left"><?php echo $item['total_fee']; ?></td>
              <td class="left"><?php echo $item['consign_time']; ?></td>
              <td class="left"><?php echo $item['message']; ?></td>
              <td class="left"><?php echo $item['status']; ?></td>
              <td class="right"><?php foreach ($item['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script>
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});

function filter() {
	url = 'index.php?route=trade/trade';
	
	var filter_id = $('input[name=\'filter_id\']').attr('value');
	if (filter_id) {
		url += '&filter_id=' + encodeURIComponent(filter_id);
	}

	var filter_youzan_id = $('input[name=\'filter_youzan_id\']').attr('value');
	if (filter_youzan_id) {
		url += '&filter_youzan_id=' + encodeURIComponent(filter_youzan_id);
	}

	var filter_pay_type = $('input[name=\'filter_pay_type\']').attr('value');
	if (filter_pay_type) {
		url += '&filter_pay_type=' + encodeURIComponent(filter_pay_type);
	}

	var filter_message = $('input[name=\'filter_message\']').attr('value');
	if (filter_message) {
		url += '&filter_message=' + encodeURIComponent(filter_message);
	}

	var filter_status = $('input[name=\'filter_status\']').attr('value');
	if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

  location = url;
}

</script>
<?php echo $footer; ?>
