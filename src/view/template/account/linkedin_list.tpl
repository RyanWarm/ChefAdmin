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
              <td class="left">score</td>
              <td class="left">帐号链接</td>
              <td class="left">类别</td>
              <td class="left">状态</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" style="width: 150px;" name="filter_zid" value="<?php echo $filter_zid; ?>" /></td>
              <td><input type="text" style="width: 50px;" name="filter_score" value="<?php echo $filter_score; ?>" /></td>
              <td></td>
              <td><input type="text" style="width: 300px;" name="filter_category" value="<?php echo $filter_category; ?>" /></td>
              <td>
		<?php if ($statusTypes) { ?>
		<select name="filter_status">
			<?php foreach ($statusTypes as $statusType) { ?>
				<option value=<?php echo $statusType['status_value']; ?> <?php if($filter_status==$statusType['status_value']){ ?> selected<?php }?>><?php echo $statusType['status_name']; ?></option>
			<?php } ?>
			<option value="all" <?php if(empty($filter_status) || $filter_status=='all'){ ?> selected<?php }?>>所有</option>
		</select>
		<?php } ?>
	      </td>
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
              <td class="left"><?php echo $item['score']; ?></td>
              <!--<td class="left"><a href="<?php echo $item['link']; ?>" target="_blank">链接</a></td>-->
	      <td class="left">
		<?php if( $item['status'] > 1 ) { ?>
		    已发送 
		<?php } else { ?>
		    <a onclick="changeStatus('<?php echo $item['changeStatus']; ?>', '<?php echo $item['link']; ?>')" href="javascript:void(0)">链接</a>
		<?php } ?>
	      </td>
              <td class="left"><?php echo $item['category']; ?></td>
              <td class="left"><?php echo $item['status_name']; ?></td>        
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
	url = 'index.php?route=account/linkedin';
	
	var filter_score = $('input[name=\'filter_score\']').attr('value');
	if (filter_score) {
		url += '&filter_score=' + encodeURIComponent(filter_score);
	}

	var filter_status = $('select[name=\'filter_status\']').attr('value');
	if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	var filter_zid = $('input[name=\'filter_zid\']').attr('value');
	if (filter_zid) {
		url += '&filter_zid=' + encodeURIComponent(filter_zid);
	}

	var filter_category = $('input[name=\'filter_category\']').attr('value');
	if (filter_category) {
		url += '&filter_category=' + encodeURIComponent(filter_category);
	}

    	location = url;
}

function changeStatus(url, link) {
	window.open(link);
	window.location = url;
}

</script>
<?php echo $footer; ?>
