<?php echo $header; ?>
<?php $lang = $this->language; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button">返回</a>
        <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a> 
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>ID</td>
            <td><?= !empty($id)? $id: ''; ?></td>
          </tr>
          <tr>
            <td>公司名称</td>
            <td><input type="text" name="name" size="50" value="<?php echo $name; ?>" />
          </tr>
          <tr>
            <td>公司名称(Normalized)</td>
            <td><input type="test" name="normalized_name" size="50" value="<?php echo $normalized_name; ?>" /></td>
          </tr>
          <tr>
            <td>雇员数量</td>
            <td><input type="text" name="employer_count" size="50" value="<?php echo $employer_count; ?>" />
          </tr>
          <tr>
            <td>URL</td>
            <td><input type="text" name="url" size="50" value="<?= $url; ?>" />
          </tr>
          <tr>
            <td>Email</td>
            <td><input type="text" name="email" size="50" value="<?= $email; ?>" />
          </tr>
          <tr>
            <td>所在地区</td>
            <td><input type="text" name="location" size="50" value="<?= $location; ?>" />
          </tr>
          <tr>
            <td>领域</td>
            <td><input type="text" name="category" size="50" value="<?= $category; ?>" />
          </tr>
          <tr>
            <td>所属类别</td>
            <td><input type="text" name="property" size="50" value="<?= $property; ?>" />
          </tr>
          <tr>
            <td>公司规模</td>
	    <td>
		<select name="scale">
		    <?php $matched=0; ?>
		    <option value=<?php $tmp="1-49人"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="50-99人"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="100-499人"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="500-999人"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="1000-9999人"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="10000人以上"; echo $tmp; ?> <?php if($scale==$tmp){ $matched=1; ?> selected<?php }?>><?php echo $tmp; ?></option>
		    <option value=<?php $tmp="未填写"; echo $tmp; ?> <?php if($matched==0 || $scale==$tmp){ ?> selected<?php }?>><?php echo $tmp; ?></option>
                </select>
	    </td>
          </tr>
          <tr>
            <td>所在城市</td>
            <td><input type="text" name="cities" size="50" value="<?= $cities; ?>" />
          </tr>
          <tr>
            <td>公司简介</td>
            <td><textarea rows="10" cols="100" name="overview"><?= $overview; ?></textarea>
          </tr>
          <tr>
            <td>公司简介(Abstract)</td>
            <td><textarea rows="10" cols="100" name="overview_abstract"><?= $overview_abstract; ?></textarea>
          </tr>
	  <tr>
            <td>Company Logo</td>
            <td><div class="image"><img src="<?php echo HTTP_IMAGE . $image; ?>" alt="" id="thumb" /><br />
              <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
              <a onclick="image_upload('image', 'thumb');">Browse</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo LOCAL_IMAGE. 'recruit/' . $no_image; ?>'); $('#image').attr('value', '');">Clear</a></div></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript">
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m'
});
$('.time').timepicker({timeFormat: 'h:m'});
</script> 
<script type="text/javascript">

function issueInviteCode(send_email) {

    $('#form').attr('action', send_email?'<?=$action_issue_invite_code_with_email; ?>':'<?=$action_issue_invite_code; ?>');

    $('#form').submit();
}

function sendNotifyEmail() {

    $('#form').attr('action', '<?=$action_send_notify_email; ?>');

    $('#form').submit();
}

function manualNotify() {

    $('#form').attr('action', '<?=$action_manual_notify; ?>');

    $('#form').submit();
}


</script>
<script type="text/javascript"><!--
function image_upload(field, thumb) {
        $('#dialog').remove();
        
        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
        
        $('#dialog').dialog({
                title: 'Image Management',
		close: function (event, ui) {
			if ($('#' + field).attr('value') && $('#' + field).attr('value').indexOf('uc01') != -1) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(text) {
						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
                bgiframe: false,
                width: 800,
                height: 400,
                resizable: false,
                modal: false
        });
};
//--></script>
<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
