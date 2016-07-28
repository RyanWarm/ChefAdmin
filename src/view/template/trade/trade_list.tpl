<?php echo $header; ?>
<style>
@media print{
  INPUT {
    display:none
  }
  .bgnoprint{
     background:display:none;
  }
  .noprint{
     display:none
  }
  .paging{
    page-break-after :always
  }
  .print_div{
    font-size:12px;color:#000000;
  }
}
@media screen
{
  .print_div{
    display:none
  }
}
</style>
<div id="content"  class="noprint">
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
      <div class="buttons"><a id="print_list" class="button">打印列表</a><a onclick="window.print()" class="button">打印订单</a><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
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
              <td class="left"><a href="index.php?route=trade/trade&sort=deliver_time">送餐时间↓</a></td>
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
              <!--td><input type="text" id="datetimepicker"></td-->
              <td><input type="text" style="width: 210px;" name="filter_deliver_time" value=""></td>
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
              <td class="left"><a target="_blank" href="index.php?route=order/order&filter_tid=<?php echo $item['tid']; ?>"><?php echo $item['order_num']; ?></a></td>
              <td class="left"><?php echo $item['pay_type']; ?></td>
              <td class="left"><?php echo $item['post_fee']; ?></td>        
              <td class="left"><?php echo $item['payment']; ?></td>
              <td class="left"><?php echo $item['discount']; ?></td>
              <td class="left"><?php echo $item['total_fee']; ?></td>
              <td class="left"><?php echo $item['consign_time']; ?></td>
              <td class="left"><?php echo $item['deliver_time']; ?></td>
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
<div id="order_detail" class="print_div">
  <?php if ($list) { ?>
    <?php foreach ($list as $item) { ?>
      姓名：<?php echo $item['alias']; ?><p>
      送餐时间：<?php echo $item['deliver_time']; ?><p>
      地址：<?php echo $item['address']; ?><p>
      电话：<?php echo $item['mobile']; ?><p>
      留言：<?php echo $item['message']; ?><p>
      菜品：<p>
	<?php foreach ($item['orders'] as $order) { ?>
		--- <?php echo $order['name']; ?> (<?php echo $order['num']; ?> 份)<p>
    	<?php } ?>
      <p class="paging"></p>
    <?php } ?>
  <?php } ?>
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

	var filter_deliver_time = $('input[name=\'filter_deliver_time\']').attr('value');
	if (filter_deliver_time) {
		url += '&filter_deliver_time=' + encodeURIComponent(filter_deliver_time) + '&sort=deliver_time';
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
<!--=script src="view/javascript/jquery.js"></script-->
<script src="view/javascript/jquery.datetimepicker.full.js"></script>
<script type="text/javascript">
$.datetimepicker.setLocale('zh');
$('#datetimepicker').datetimepicker({
	format:'Y-m-d H:i:00',
	dayOfWeekStart : 1,
	lang:'zh',
	step:10,
	startDate:'2016-07-21'
});
</script>
<script type="text/javascript">
$(function() {
    $('input[name="filter_deliver_time"]').daterangepicker({
	autoUpdateInput: false,
        timePicker: true,
        timePickerIncrement: 10,
        locale: {
	    autoApply: true,
	    separator: ' ~ ',
            format: 'YYYY-MM-DD H:mm'
        }
    });
});
  $('input[name="filter_deliver_time"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD H:mm') + ' ~ ' + picker.endDate.format('YYYY-MM-DD H:mm'));
  });

  $('input[name="filter_deliver_time"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });
</script>

<script type="text/javascript">
  $('#print_list').click(function(){
    $('#content').removeClass().addClass('print_div');
    $('#order_detail').removeClass().addClass('noprint');
    window.print();
  })
</script>
<?php echo $footer; ?>
