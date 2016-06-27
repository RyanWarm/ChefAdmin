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
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>

    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="right"><?php if ($sort == 'o.order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left">跟踪码</td>
              <td class="left">送礼人</td>
              <td class="left">收礼人</td>
              <td class="left"><?php if ($sort == 'participants_count') { ?>
                <a href="<?php echo $sort_participants_count; ?>" class="<?php echo strtolower($order); ?>">参加人数</a>
                <?php } else { ?>
                <a href="<?php echo $sort_participants_count; ?>">参加人数</a>
                <?php } ?>
              </td>
              <td class="left" style="width: 100px;" width="100" >产品</td>
              <td class="left">
                活动状态
              </td>
              <td class="left">送礼状态</td>
              <td class="left">收礼地址</td>
              <td class="left"><?php if ($sort == 'o.date_added') { ?>
                <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.activity_end_date') { ?>
                <a href="<?php echo $sort_activity_end_date; ?>" class="<?php echo strtolower($order); ?>">结束日起</a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_modified; ?>">结束日期</a>
                <?php } ?>
              </td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td align="left"><input type="text" name="filter_track_code" value="<?php echo $filter_track_code; ?>" size="8" /></td>
              <td><input type="text" name="filter_sender_name" value="<?php echo $filter_sender_name; ?>" size="8" /></td>
              <td><input type="text" name="filter_recv_name" value="<?php echo $filter_recv_name; ?>" size="8" /></td>
              <td></td>
              <td></td>
              <td><select name="filter_activity_status">
                  <option value="*"></option>
                  <?php foreach ($all_activity_status as $status_name) { ?>
                  <?php if ($status_name == $filter_activity_status) { ?>
                  <option value="<?php echo $status_name; ?>" selected="selected"><?php echo $status_name; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $status_name; ?>"><?php echo $status_name; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </td>
              <td>
                <select name="filter_ship_status">
                  <option value="*"></option>
                  <?php foreach ($all_ship_status as $status_name) { ?>
                  <?php if ($status_name == $filter_ship_status) { ?>
                  <option value="<?php echo $status_name; ?>" selected="selected"><?php echo $status_name; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $status_name; ?>"><?php echo $status_name; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </td>
              <td></td>
              <td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
              <td><input type="text" name="filter_activity_end_date" value="<?php echo $filter_activity_end_date; ?>" size="12" class="date" /></td>
              <td align="right"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
            </tr>
            <?php if ($orders) { ?>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                <?php } ?></td>
              <td class="right"><?php echo $order['order_id']; ?></td>
              <td class="left"><a target="_blank" href="http://apps.weibo.com/lesongweb/order/<?php echo $order['track_code'] ?>" ><?php echo $order['track_code']; ?></a></td>
              <td class="left"><a target="_blank" href="http://weibo.com/u/<?php echo $order['sender_profile_id']; ?>"><?php echo $order['sender_name']; ?></a></td>
              <td class="left"><?php echo $order['recv_name']; ?></td>
              <td class="left"><span style="color:red"><?php echo $order['participants_count'] . '</span>/' . $order['group_count']; ?></td>
              <td class="left" style=""><a href="index.php?route=catalog/product/update&product_id=<?php echo $order['product_id']; ?>" ><?php echo $order['product_name']; ?></a></td>
              <td class="left"><?php echo $order['activity_status']; ?></td>
              <td class="left"><?php echo $order['ship_status']; ?></td>
              <td class="left"><?php echo $order['address_id']; ?></td>              
              <td class="left"><?php echo $order['date_added']; ?></td>
              <td class="left"><?php echo $order['activity_end_date']; ?></td>
              <td class="right">
                [ <a target="_blank" href="http://apps.weibo.com/lesongweb/order/<?php echo $order['track_code'] ?>" >查看</a> ]

              <?php foreach ($order['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=sale/group_order&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}

	var filter_track_code = $('input[name=\'filter_track_code\']').attr('value');
	
	if (filter_track_code) {
		url += '&filter_track_code=' + encodeURIComponent(filter_track_code);
	}

	var filter_sender_name = $('input[name=\'filter_sender_name\']').attr('value');
	
	if (filter_sender_name) {
		url += '&filter_sender_name=' + encodeURIComponent(filter_sender_name);
	}

	var filter_recv_name = $('input[name=\'filter_recv_name\']').attr('value');
	
	if (filter_recv_name) {
		url += '&filter_recv_name=' + encodeURIComponent(filter_recv_name);
	}

	var filter_activity_status = $('select[name=\'filter_activity_status\']').attr('value');
	
	if (filter_activity_status != '*') {
		url += '&filter_activity_status=' + encodeURIComponent(filter_activity_status);
	}	

	var filter_ship_status = $('select[name=\'filter_ship_status\']').attr('value');
	
	if (filter_ship_status != '*') {
		url += '&filter_ship_status=' + encodeURIComponent(filter_ship_status);
	}	

	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_activity_end_date = $('input[name=\'filter_activity_end_date\']').attr('value');
	
	if (filter_activity_end_date) {
		url += '&filter_activity_end_date=' + encodeURIComponent(filter_activity_end_date);
	}
				
	location = url;
}
//--></script>  
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<script type="text/javascript"><!--
$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=\'filter_sender_name\']').catcomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						category: item.customer_group,
						label: item.name,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_sender\']').val(ui.item.label);
						
		return false;
	}
});
//--></script> 
<?php echo $footer; ?>