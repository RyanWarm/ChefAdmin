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
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">zid</td>
              <td class="left">email</td>
              <td class="left">帐号类型</td>
              <td class="left">邀请码</td>
              <td class="left">社交网络</td>
              <td class="left">社交帐号</td>
              <td class="left">邮箱验证</td>
              <td class="left">注册时间</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 40px;" name="filter_zid" value="<?php echo $filter_zid; ?>" /></td>
              <td><input type="text" style="width: 200px;" name="filter_email" value="<?php echo $filter_email; ?>" /></td>
              <td>
		<?php if ($classTypes) { ?>
		<select name="filter_class">
			<?php foreach ($classTypes as $classType) { ?>
				<option value=<?php echo $classType; ?> <?php if($filter_class==$classType){ ?> selected<?php }?>><?php echo $classType; ?></option>
			<?php } ?>
			<option value="all" <?php if(empty($filter_class) || $filter_class=='all'){ ?> selected<?php }?>>all</option>
		</select>
		<?php } ?>
	      </td>
              <td><input type="text" style="width: 100px;" name="filter_code" value="<?php echo $filter_code; ?>" /></td>
              <td></td>
              <td><input type="text" style="width: 100px;" name="filter_profile" value="<?php echo $filter_profile; ?>" /></td>
              <td></td>
              <td></td>
              <td align="right"><a onclick="filter();" class="button">筛选</a></td>
            </tr>
            <?php if ($list) { ?>
            <?php foreach ($list as $item) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($item['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['zid']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $item['zid']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $item['zid']; ?></td>
              <td class="left"><?php echo $item['email']; ?></td>
              <td class="left"><?php echo $item['class']; ?></td>
              <td class="left"><?php echo $item['code']; ?></td>
              <td class="left"><?php echo $item['network']; ?></td>        
	      <td class="left"><?php echo $item['profile_id']; ?></td>
              <td class="left"><?php echo $item['verified'] ? '已验证':''; ?></td>
              <td class="left"><?php echo $item['created_at']; ?></td>
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
	url = 'index.php?route=account/account';
	
	var filter_code = $('input[name=\'filter_code\']').attr('value');
	if (filter_code) {
		url += '&filter_code=' + encodeURIComponent(filter_code);
	}

	var filter_class = $('select[name=\'filter_class\']').attr('value');
	if (filter_class) {
		url += '&filter_class=' + encodeURIComponent(filter_class);
	}

	var filter_zid = $('input[name=\'filter_zid\']').attr('value');
	if (filter_zid) {
		url += '&filter_zid=' + encodeURIComponent(filter_zid);
	}

	var filter_email = $('input[name=\'filter_email\']').attr('value');
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_profile = $('input[name=\'filter_profile\']').attr('value');
	if (filter_profile) {
		url += '&filter_profile=' + encodeURIComponent(filter_profile);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
