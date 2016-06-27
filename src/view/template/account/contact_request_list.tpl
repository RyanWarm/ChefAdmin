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
      <h1><img src="view/image/category.png" alt="" />委托请求</h1>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">request_id</td>
              <td class="left">requester_zid</td>
              <td class="left">job_id</td>
              <td class="left">target_network</td>
              <td class="left">latest_status</td>
              <td class="left">created_at</td>
              <td class="left">updated_at</td>
              <td class="left">subject</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 50px;" name="filter_request_id" value="<?php echo $filter_request_id; ?>" /></td>
              <td><input type="text" style="width: 50px;" name="filter_requester_zid" value="<?php echo $filter_requester_zid; ?>" /></td>
              <td><input type="text" style="width: 50px;" name="filter_job_id" value="<?php echo $filter_job_id; ?>" /></td>
              <td><input type="text" style="width: 80px;" name="filter_target_network" value="<?php echo $filter_target_network; ?>" /></td>
              <td>
		<?php if ($statusTypes) { ?>
		<select name="filter_status">
			<?php foreach ($statusTypes as $statusType) { ?>
				<option value=<?php echo $statusType; ?> <?php if($filter_status==$statusType){ ?> selected<?php }?>><?php echo $statusType; ?></option>
			<?php } ?>
			<option value="all" <?php if(empty($filter_status) || $filter_status=='all'){ ?> selected<?php }?>>所有</option>
		</select>
		<?php } ?>
	      </td>
              <td><input type="text" style="width: 120px;" name="filter_created_at" value="<?php echo $filter_created_at; ?>" /></td>
              <td><input type="text" style="width: 120px;" name="filter_updated_at" value="<?php echo $filter_updated_at; ?>" /></td>
              <td><input type="text" style="width: 100px;" name="filter_subject" value="<?php echo $filter_subject; ?>" /></td>
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
              <td class="left"><?php echo $item['request_id']; ?></td>
              <td class="left"><?php echo $item['requester_zid']; ?></td>
              <td class="left"><?php echo $item['requester_job_id']; ?></td>
              <td class="left"><?php echo $item['target_network']; ?></td>
              <td class="left"><?php echo $item['latest_status']; ?></td>        
              <td class="left"><?php echo $item['created_at']; ?></td>        
              <td class="left"><?php echo $item['updated_at']; ?></td>        
              <td class="left"><?php echo $item['requester_subject']; ?></td>        
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
	url = 'index.php?route=account/contact_request';
	
	var filter_request_id = $('input[name=\'filter_request_id\']').attr('value');
	if (filter_request_id) {
		url += '&filter_request_id=' + encodeURIComponent(filter_request_id);
	}

	var filter_requester_zid = $('input[name=\'filter_requester_zid\']').attr('value');
	if (filter_requester_zid) {
		url += '&filter_requester_zid=' + encodeURIComponent(filter_requester_zid);
	}

	var filter_job_id = $('input[name=\'filter_job_id\']').attr('value');
	if (filter_job_id) {
		url += '&filter_job_id=' + encodeURIComponent(filter_job_id);
	}

	var filter_target_network = $('input[name=\'filter_target_network\']').attr('value');
	if (filter_target_network) {
		url += '&filter_target_network=' + encodeURIComponent(filter_target_network);
	}

	var filter_status = $('select[name=\'filter_status\']').attr('value');
	if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	var filter_created_at = $('input[name=\'filter_created_at\']').attr('value');
	if (filter_created_at) {
		url += '&filter_created_at=' + encodeURIComponent(filter_created_at);
	}

	var filter_updated_at = $('input[name=\'filter_updated_at\']').attr('value');
	if (filter_updated_at) {
		url += '&filter_updated_at=' + encodeURIComponent(filter_updated_at);
	}

	var filter_subject = $('input[name=\'filter_subject\']').attr('value');
	if (filter_subject) {
		url += '&filter_subject=' + encodeURIComponent(filter_subject);
	}

    	location = url;
}

</script>
<?php echo $footer; ?>
