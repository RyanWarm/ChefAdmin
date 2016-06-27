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
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">code</td>
              <td class="left">类型</td>
              <td class="left">生成时间</td>
              <td class="left">注册帐号</td>
              <td class="left">注册时间</td>
              <td class="left">申请ID</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" name="filter_code" value="<?php echo $filter_code; ?>" /></td>
              <td></td>
              <td></td>
              <td><input type="text" name="filter_regAccount" value="<?php echo $filter_regAccount; ?>" /></td>
              <td></td>
              <td><input type="text" name="filter_reqId" value="<?php echo $filter_reqId; ?>" /></td>
              <td align="right"><a onclick="filter();" class="button">筛选</a></td>
            </tr>
            <?php if ($list) { ?>
            <?php foreach ($list as $item) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($item['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['code']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['code']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $item['code']; ?></td>
              <td class="left"><?php echo $item['class']; ?></td>
              <td class="left"><?php echo $item['created_at']; ?></td>
              <td class="left"><?php echo $item['assignee']; ?></td>
              <td class="left"><?php echo $item['assigned_at']; ?></td>        
              <td class="left"><?php if (!empty($item['request_id'])) { ?>
                  <a href="index.php?route=account/invite_code_request/update&request_id=<?= $item['request_id'] ?>"><?php echo $item['request_id']; ?></a></a>
                  <?php } ?>
              </td>             
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
	url = 'index.php?route=account/invite_code';
	
	var filter_code = $('input[name=\'filter_code\']').attr('value');
	if (filter_code) {
		url += '&filter_code=' + encodeURIComponent(filter_code);
	}

	var filter_regAccount = $('input[name=\'filter_regAccount\']').attr('value');
	if (filter_regAccount) {
		url += '&filter_regAccount=' + encodeURIComponent(filter_regAccount);
	}

	var filter_reqId = $('input[name=\'filter_reqId\']').attr('value');
	if (filter_reqId) {
		url += '&filter_reqId=' + encodeURIComponent(filter_reqId);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
