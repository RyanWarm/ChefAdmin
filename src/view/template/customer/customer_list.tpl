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
      <h1><img src="view/image/category.png" alt="" />当前客户</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">id</td>
              <td class="left">别名</td>
              <td class="left">电话</td>
              <td class="left"><a href="index.php?route=customer/customer&sort=traded_num">订单数量↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=traded_money">订单总额↓</a></td>
              <td class="left"><a href="index.php?route=customer/customer&sort=unit">单价↓</a></td>
              <td class="left">小区</td>
              <td class="left">地址</td>
              <td class="left"><a href="index.php?route=customer/customer&sort=join_time">加入时间↓</a></td>
              <td class="left">性别</td>
              <td class="left">Trades</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_id" value="<?php echo $filter_id; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_alias" value="<?php echo $filter_alias; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_mobile" value="<?php echo $filter_mobile; ?>" /></td>
              <td></td>
              <td></td>
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_community" value="<?php echo $filter_community; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_address" value="<?php echo $filter_address; ?>" /></td>
              <td></td>
              <td><input type="text" style="width: 20px;" name="filter_sex" value="<?php echo $filter_sex; ?>" /></td>
              <td></td>
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
              <td class="left"><?php echo $item['youzan_id']; ?></td>
              <td class="left"><?php echo $item['alias']; ?></td>
              <td class="left"><?php echo $item['mobile']; ?></td>
              <td class="left"><?php echo $item['traded_num']; ?></td>
              <td class="left"><?php echo $item['traded_money']; ?></td>        
              <td class="left"><?php echo number_format($item['unit'], 2); ?></td>
              <td class="left"><?php echo $item['community']; ?></td>
              <td class="left"><?php echo $item['address']; ?></td>
              <td class="left"><?php echo $item['join_time']; ?></td>
              <td class="left"><?php echo $item['sex']; ?></td>
              <td class="left"><a target="_blank" href="index.php?route=trade/trade&filter_youzan_id=<?php echo $item['youzan_id']; ?>">All Trades</a></td>
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

	var filter_alias = $('input[name=\'filter_alias\']').attr('value');
	if (filter_alias) {
		url += '&filter_alias=' + encodeURIComponent(filter_alias);
	}

	var filter_mobile = $('input[name=\'filter_mobile\']').attr('value');
	if (filter_mobile) {
		url += '&filter_mobile=' + encodeURIComponent(filter_mobile);
	}

	var filter_community = $('input[name=\'filter_community\']').attr('value');
	if (filter_community) {
		url += '&filter_community=' + encodeURIComponent(filter_community);
	}

	var filter_address = $('input[name=\'filter_address\']').attr('value');
	if (filter_address) {
		url += '&filter_address=' + encodeURIComponent(filter_address);
	}

	var filter_sex = $('input[name=\'filter_sex\']').attr('value');
	if (filter_sex) {
		url += '&filter_sex=' + encodeURIComponent(filter_sex);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
