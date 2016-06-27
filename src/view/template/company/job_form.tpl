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
            <td>公司ID</td>
            <td><?= !empty($company_id)? $company_id: ''; ?><input type="hidden" name="company_id" value="<?php echo $company_id; ?>" /></td>
          </tr>
          <tr>
            <td>职位名称</td>
            <td><input type="text" name="title" size="50" value="<?php echo $title; ?>" />
          </tr>
          <tr>
            <td>工作地点</td>
            <td><input type="text" name="location" size="50" value="<?= $location; ?>" />
          </tr>
          <tr>
            <td>发布日期</td>
            <td><?= !empty($create_date)? $create_date: ''; ?></td>
          </tr>
          <tr>
            <td>工作经验</td>
            <td><input type="text" name="work_exp" size="50" value="<?= $work_exp; ?>" />
          </tr>
          <tr>
            <td>管理经验</td>
            <td><input type="text" name="management_exp" size="50" value="<?= $management_exp; ?>" />
          </tr>
          <tr>
            <td>最低学历</td>
            <td><input type="text" name="edu_background" size="50" value="<?= $edu_background; ?>" />
          </tr>
          <tr>
            <td>招聘人数</td>
            <td><input type="text" name="headcount" size="50" value="<?= $headcount; ?>" />
          </tr>
          <tr>
            <td>职位类别</td>
            <td><input type="text" name="category" size="50" value="<?= $category; ?>" />
          </tr>
          <tr>
            <td>工作性质</td>
            <td><input type="text" name="job_type" size="50" value="<?= $job_type; ?>" />
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
            <td>最低工作年限</td>
            <td><input type="text" name="exp_year_min" size="50" value="<?= $exp_year_min; ?>" />
          </tr>
          <tr>
            <td>最低薪酬</td>
            <td><input type="text" name="salary_min" size="50" value="<?= $salary_min; ?>" />
          </tr>
          <tr>
            <td>最高薪酬</td>
            <td><input type="text" name="salary_max" size="50" value="<?= $salary_max; ?>" />
          </tr>
          <tr>
            <td>所属部门</td>
            <td><input type="text" name="department" size="50" value="<?= $department; ?>" />
          </tr>
          <tr>
            <td>状态</td>
	    <td>
		<select name="status">
			<?php foreach ($statusTypes as $statusType) { ?>
				<option value=<?php echo $statusType; ?> <?php if($status==$statusType){ ?> selected<?php }?>><?php echo $statusType; ?></option>
			<?php } ?>
		</select>
	    </td>
          </tr>
          <tr>
            <td>职位描述</td>
            <td><textarea rows="10" cols="100" name="description"><?= $description; ?></textarea>
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

<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
