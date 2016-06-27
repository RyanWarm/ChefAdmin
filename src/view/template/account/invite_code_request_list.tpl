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
      <h1><img src="view/image/category.png" alt="" />帐号</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a><a onclick="$('#form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">Request ID</td>
              <td class="left">帐号类型</td>
              <td class="left">email</td>
              <td class="left">姓名</td>
              <td class="left">公司</td>
              <td class="left">职位</td>
              <td class="left">申请时间</td>
              <td class="left">邀请码</td>
              <td class="left">通知方式</td>
              <td class="left">通知时间</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td></td>
              <td></td>
              <td><input type="text" style="width: 200px;" name="filter_email" value="<?php echo $filter_email; ?>" /></td>
              <td><input type="text" style="width: 80px;" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td><input type="text" style="width: 160px;" name="filter_company" value="<?php echo $filter_company; ?>" /></td>
              <td><input type="text" style="width: 80px;" name="filter_position" value="<?php echo $filter_position; ?>" /></td>
              <td></td>
              <td><input type="text"style="width: 60px;"  name="filter_code" value="<?php echo $filter_code; ?>" /></td>
              <td></td>
              <td></td>
              <td align="right"><a onclick="filter();" class="button">筛选</a></td>
            </tr>
            <?php if ($list) { ?>
            <?php foreach ($list as $item) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($item['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['request_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['request_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $item['request_id']; ?></td>
              <td class="left"><?php echo $item['class']; ?></td>
              <td class="left"><?php echo $item['email']; ?></td>
              <td class="left"><?php echo $item['name']; ?></td>
              <td class="left"><?php echo $item['company']; ?></td>
              <td class="left"><?php echo $item['position']; ?></td>        
              <td class="left"><?php echo $item['created_at']; ?></td>
              <td class="left"><?php if (!empty($item['issued_code'])) { ?>
                <a href="index.php?route=account/invite_code/update&code=<?=$item['issued_code']; ?>"><?=$item['issued_code'] ?></a>
                <?php } ?>
              </td>
              <td class="left"><?php echo $item['notify_method']; ?></td>
              <td class="left"><?php echo $item['notify_time']; ?></td>
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
	url = 'index.php?route=account/invite_code_request';
	
	var filter_requestId = $('input[name=\'filter_requestId\']').attr('value');
	if (filter_requestId) {
		url += '&filter_requestId=' + encodeURIComponent(filter_requestId);
	}

	var filter_class = $('input[name=\'filter_class\']').attr('value');
	if (filter_class) {
		url += '&filter_class=' + encodeURIComponent(filter_class);
	}

	var filter_name = $('input[name=\'filter_name\']').attr('value');
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_email = $('input[name=\'filter_email\']').attr('value');
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_company = $('input[name=\'filter_company\']').attr('value');
	if (filter_company) {
		url += '&filter_company=' + encodeURIComponent(filter_company);
	}

	var filter_position = $('input[name=\'filter_position\']').attr('value');
	if (filter_position) {
		url += '&filter_position=' + encodeURIComponent(filter_position);
	}

	var filter_code = $('input[name=\'filter_code\']').attr('value');
	if (filter_code) {
		url += '&filter_code=' + encodeURIComponent(filter_code);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
