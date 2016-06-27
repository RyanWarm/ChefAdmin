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
      <!-- div class="buttons"><a onclick="$('#form').attr('action', '<?php echo $invoice; ?>'); $('#form').attr('target', '_blank'); $('#form').submit();" class="button"><?php echo $button_invoice; ?></a><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a><a onclick="$('#form').attr('action', '<?php echo $delete; ?>'); $('#form').attr('target', '_self'); $('#form').submit();" class="button"><?php echo $button_delete; ?></a></div -->
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="right">
                订单跟踪码
                </td>
              <td class="left"><?php if ($sort == 'sender_name') { ?>
                <a href="<?php echo $sort_sender_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_sender_name; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_sender_name; ?>"><?php echo $column_sender_name; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'recipient_name') { ?>
                <a href="<?php echo $sort_recipient_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $this->language->get('column_recipient_name'); ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_recipient_name; ?>"><?php echo $this->language->get('column_recipient_name'); ?></a>
                <?php } ?></td>
              <td class="left">
                 供应商状态
              </td>
              <td class="left"><?php if ($sort == 'supplier_error') { ?>
                <a href="<?php echo $sort_supplier_error; ?>" class="<?php echo strtolower($order); ?>">订单异常</a>
                <?php } else { ?>
                <a href="<?php echo $sort_supplier_error; ?>">订单异常</a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'o.total') { ?>
                <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                <?php } ?></td>
              <td class="right">物流费</td>
              <td class="left"><?php if ($sort == 'o.date_added') { ?>
                <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.date_modified') { ?>
                <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                <?php } ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td></td>
              <td><input type="text" name="filter_sender_name" value="<?php echo $filter_sender_name; ?>" /></td>
              <td><input type="text" name="filter_recipient_name" value="<?php echo $filter_recipient_name; ?>" /></td>
              <td><select name="filter_supplier_status">
                  <option value="*"></option>
                  <?php foreach ($all_supplier_status as $status_name) { ?>
                  <?php if ($status_name == $filter_supplier_status) { ?>
                  <option value="<?php echo $status_name; ?>" selected="selected"><?php echo $this->language->get('text_supplier_' . $status_name); ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $status_name; ?>"><?php echo $this->language->get('text_supplier_' . $status_name); ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
              <td><select name="filter_supplier_error">
                  <option value="*"></option>
                  <?php if ($filter_supplier_error === "1") { ?>
                  <option value="1" selected="selected">异常</option>
                  <?php } else { ?>
                  <option value="1" >异常</option>
                  <?php } ?>

                  <?php if ($filter_supplier_error === "0") { ?>
                  <option value="0" selected="selected">正常</option>
                  <?php } else { ?>
                  <option value="0" >正常</option>
                  <?php } ?>

</td>
              <td align="right"></td>
              <td></td>
              <td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
              <td><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" size="12" class="date" /></td>
              <td align="right"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
            </tr>
            <?php if ($orders) { ?>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['short_track_code']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['short_track_code']; ?>" />
                <?php } ?></td>
              <td class="right"><?php echo $order['short_track_code']; ?></td>
              <td class="left"><?php echo $order['sender_name']; ?></td>
              <td class="left"><?php echo $order['recipient_name']; ?></td>
              <td class="left"><?php echo $this->language->get('text_supplier_' . $order['supplier_status']); ?></td>
              <td class="left"><?php echo $order['supplier_error']?"<font color='#ff0000'>异常</font>":"正常"; ?></td>
              <td class="right"><?php echo $order['price']; ?></td>
              <td class="right"><?php echo $order['shipping_cost']; ?></td>
              <td class="left"><?php echo $order['date_added']; ?></td>
              <td class="left"><?php echo $order['date_modified']; ?></td>
              <td class="right"><?php foreach ($order['action'] as $action) { ?>
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
	url = 'index.php?route=supplier/order&token=<?php echo $token; ?>';	
	
	var filter_sender_name = $('input[name=\'filter_sender_name\']').attr('value');
	
	if (filter_sender_name) {
		url += '&filter_sender_name=' + encodeURIComponent(filter_sender_name);
	}

	var filter_recipient_name = $('input[name=\'filter_recipient_name\']').attr('value');
	
	if (filter_recipient_name) {
		url += '&filter_recipient_name=' + encodeURIComponent(filter_recipient_name);
	}

	/*
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	
*/
/*
	var filter_order_status_summary = $('select[name=\'filter_order_status_summary\']').attr('value');
	
	if (filter_order_status_summary != '*') {
		url += '&filter_order_status_summary=' + encodeURIComponent(filter_order_status_summary);
	}	

	var filter_order_status = $('select[name=\'filter_order_status\']').attr('value');
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}	
*/

	var filter_supplier_status = $('select[name=\'filter_supplier_status\']').attr('value');
	
	if (filter_supplier_status != '*') {
		url += '&filter_supplier_status=' + encodeURIComponent(filter_supplier_status);
	}	

	var filter_supplier_error = $('select[name=\'filter_supplier_error\']').attr('value');
	
	if (filter_supplier_error != '*') {
		url += '&filter_supplier_error=' + encodeURIComponent(filter_supplier_error);
	}	

	var filter_price = $('input[name=\'filter_price\']').attr('value');

	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
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
