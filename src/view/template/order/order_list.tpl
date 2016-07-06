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
      <h1><img src="view/image/category.png" alt="" />当前订单</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">id</td>
              <td class="left">菜品名称</td>
              <td class="left"><a href="index.php?route=customer/customer&sort=price">单价↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=num">数量↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=payment">合计↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=discount">折扣↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=total">总价↓</a></td>
              <td class="left">状态</td>
              <td class="left">用户留言</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 40px;" name="filter_id" value="<?php echo $filter_id; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_state" value="<?php echo $filter_state; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_message" value="<?php echo $filter_message; ?>" /></td>
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
              <td class="left"><?php echo $item['oid']; ?></td>
              <td class="left"><?php echo $item['name']; ?></td>
              <td class="left"><?php echo $item['price']; ?></td>
              <td class="left"><?php echo $item['num']; ?></td>
              <td class="left"><?php echo $item['payment']; ?></td>        
              <td class="left"><?php echo $item['discount']; ?></td>
              <td class="left"><?php echo $item['total']; ?></td>
              <td class="left"><?php echo $item['state']; ?></td>
              <td class="left"><?php echo $item['message']; ?></td>
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
	url = 'index.php?route=customer/customer';
	
	var filter_id = $('input[name=\'filter_id\']').attr('value');
	if (filter_id) {
		url += '&filter_id=' + encodeURIComponent(filter_id);
	}

	var filter_name = $('input[name=\'filter_name\']').attr('value');
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_state = $('input[name=\'filter_state\']').attr('value');
	if (filter_state) {
		url += '&filter_state=' + encodeURIComponent(filter_state);
	}

	var filter_message = $('input[name=\'filter_message\']').attr('value');
	if (filter_message) {
		url += '&filter_message=' + encodeURIComponent(filter_message);
	}

  location = url;
}

</script>
<?php echo $footer; ?>
