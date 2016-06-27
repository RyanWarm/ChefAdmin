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
      <h1><img src="view/image/category.png" alt="" />工作</h1>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">id</td>
              <td class="left">职位名称</td>
              <td class="left">公司名称</td>
              <td class="left">发布日期</td>
              <td class="left">最低学历</td>
              <td class="left">工作性质</td>
              <td class="left">职位类别</td>
              <td class="left">工作地点</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 50px;" name="filter_id" value="<?php echo $filter_id; ?>" /></td>
              <td><input type="text" style="width: 150px;" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td><input type="text" style="width: 80px;" name="filter_companyName" value="<?php echo $filter_companyName; ?>" /></td>
              <td><input type="text" style="width: 120px;" name="filter_date" value="<?php echo $filter_date; ?>" /></td>
              <td><input type="text" style="width: 50px;" name="filter_edu" value="<?php echo $filter_edu; ?>" /></td>
              <td><input type="text" style="width: 50px;" name="filter_type" value="<?php echo $filter_type; ?>" /></td>
              <td><input type="text" style="width: 120px;" name="filter_role" value="<?php echo $filter_role; ?>" /></td>
              <td><input type="text" style="width: 60px;" name="filter_location" value="<?php echo $filter_location; ?>" /></td>
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
              <td class="left"><?php echo $item['title']; ?></td>
              <td class="left"><?php echo $item['normalized_name']; ?></td>
              <td class="left"><?php echo $item['create_date']; ?></td>
              <td class="left"><?php echo $item['edu_background']; ?></td>        
              <td class="left"><?php echo $item['job_type']; ?></td>
              <td class="left"><?php echo $item['role_name']; ?></td>
              <td class="left"><?php echo $item['location']; ?></td>
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
	url = 'index.php?route=company/job';
	
	var filter_id = $('input[name=\'filter_id\']').attr('value');
	if (filter_id) {
		url += '&filter_id=' + encodeURIComponent(filter_id);
	}

	var filter_name = $('input[name=\'filter_name\']').attr('value');
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_companyName = $('input[name=\'filter_companyName\']').attr('value');
	if (filter_companyName) {
		url += '&filter_companyName=' + encodeURIComponent(filter_companyName);
	}

	var filter_location = $('input[name=\'filter_location\']').attr('value');
	if (filter_location) {
		url += '&filter_location=' + encodeURIComponent(filter_location);
	}

	var filter_date = $('input[name=\'filter_date\']').attr('value');
	if (filter_date) {
		url += '&filter_date=' + encodeURIComponent(filter_date);
	}

	var filter_edu = $('input[name=\'filter_edu\']').attr('value');
	if (filter_edu) {
		url += '&filter_edu=' + encodeURIComponent(filter_edu);
	}

	var filter_type = $('input[name=\'filter_type\']').attr('value');
	if (filter_type) {
		url += '&filter_type=' + encodeURIComponent(filter_type);
	}

	var filter_role = $('input[name=\'filter_role\']').attr('value');
	if (filter_role) {
		url += '&filter_role=' + encodeURIComponent(filter_role);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
