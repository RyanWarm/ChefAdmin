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
      <h1><img src="view/image/category.png" alt="" />公司</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">id</td>
              <td class="left">公司名称</td>
              <td class="left">所在地区</td>
              <td class="left">领域</td>
              <td class="left">类别</td>
              <td class="left">规模</td>
              <td class="left">城市</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 40px;" name="filter_id" value="<?php echo $filter_id; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_location" value="<?php echo $filter_location; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_domain" value="<?php echo $filter_domain; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_category" value="<?php echo $filter_category; ?>" /></td>
              <td></td>
              <td><input type="text" style="width: 140px;" name="filter_cities" value="<?php echo $filter_cities; ?>" /></td>
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
              <td class="left"><?php echo $item['name']; ?></td>
              <td class="left"><?php echo $item['location']; ?></td>
              <td class="left"><?php echo $item['category']; ?></td>
              <td class="left"><?php echo $item['property']; ?></td>        
              <td class="left"><?php echo $item['scale']; ?></td>
              <td class="left"><?php echo $item['cities']; ?></td>
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
	url = 'index.php?route=company/company';
	
	var filter_id = $('input[name=\'filter_id\']').attr('value');
	if (filter_id) {
		url += '&filter_id=' + encodeURIComponent(filter_id);
	}

	var filter_name = $('input[name=\'filter_name\']').attr('value');
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_location = $('input[name=\'filter_location\']').attr('value');
	if (filter_location) {
		url += '&filter_location=' + encodeURIComponent(filter_location);
	}

	var filter_domain = $('input[name=\'filter_domain\']').attr('value');
	if (filter_domain) {
		url += '&filter_domain=' + encodeURIComponent(filter_domain);
	}

	var filter_category = $('input[name=\'filter_category\']').attr('value');
	if (filter_category) {
		url += '&filter_category=' + encodeURIComponent(filter_category);
	}

	var filter_cities = $('input[name=\'filter_cities\']').attr('value');
	if (filter_cities) {
		url += '&filter_cities=' + encodeURIComponent(filter_cities);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
