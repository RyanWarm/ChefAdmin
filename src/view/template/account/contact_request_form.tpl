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
            <td>request_id</td>
            <td><?= !empty($request_id)? $request_id: ''; ?></td>
          </tr>
          <tr>
            <td>请求者zid</td>
            <td><?= !empty($requester_zid)? $requester_zid: ''; ?></td>
          </tr>
          <tr>
            <td>请求消息主题</td>
            <td><?= !empty($requester_subject)? $requester_subject: ''; ?></td>
          </tr>
          <tr>
            <td>请求消息内容</td>
            <td><?= !empty($requester_comment)? $requester_comment: ''; ?></td>
          </tr>
          <tr>
            <td>工作id</td>
            <td><?= $requester_job_id; ?></td>
          </tr>
          <tr>
            <td>目标网络</td>
            <td><?= $target_network; ?></td>
          </tr>
          <tr>
            <td>目标profile id</td>
            <td><?= $target_profile_id; ?></td>
          </tr>
          <tr>
            <td>目标链接</td>
            <td><a href="<?= $target_url; ?>" target="_blank">link</a></td>
          </tr>
          <tr>
            <td>目标邮箱</td>
            <td><input type="text" name="target_email" value="<?= $target_email; ?>"></td>
          </tr>
          <tr>
            <td>目标电话</td>
            <td><input type="text" name="target_phone" value="<?= $target_phone; ?>"></td>
          </tr>
          <tr>
            <td>目标留言</td>
            <td><textarea rows="10" cols="100" name="target_comment"><?= $target_comment; ?></textarea>
          </tr>
          <!--<tr>
            <td>目标简历</td>
            <td><textarea rows="10" cols="100" name="target_resume"><?= $target_resume; ?></textarea>
          </tr>-->
          <tr>
            <td>联系过程纪录</td>
            <td><textarea rows="10" cols="100" name="target_transactions"><?= $target_transactions; ?></textarea>
          </tr>
          <tr>
            <td>请求创建时间</td>
            <td><?= $created_at; ?></td>
          </tr>
          <tr>
            <td>请求更新时间</td>
            <td><?= $updated_at; ?></td>
          </tr>
          <tr>
            <td>track_code</td>
            <td><?= $track_code; ?></td>
          </tr>
          <tr>
            <td>目标响应链接</td>
            <td><a href="<?php echo "http://u2top.cn/job/contact?track_code=" . $track_code; ?>" target="_blank"><?php echo "http://u2top.cn/job/contact?track_code=" . $track_code; ?></a></td>
          </tr>
          <tr>
            <td>请求状态</td>
            <td>
		<table id="request_statuses" class="list"> 
		  <?php $status_code = -1; ?>
		  <?php foreach ($request_statuses as $request_status) { ?>
		    <?php if($status_code == -1 && $request_status['status'] == 'admin_process') {$status_code=0; ?>
		    <?php } elseif($status_code < 2 && $request_status['status']=='target_fill') {$status_code=1; ?>
		    <?php } elseif($status_code < 3 && $request_status['status']=='admin_response') { if($request_status['complete_reason']=='expire'){$status_code=2;} else {$status_code=3;} ?>
		    <?php } elseif($request_status['status']=='close') {$status_code=4;} ?>
		    <tr>
		      <td colspan="2">
		      <hr />
		      </td>
		    </tr>
		    <tr>
		      <td>状态</td>
		      <td><?php echo $request_status['status']; ?></td>
		    </tr>
		    <tr>
		      <td>开始时间</td>
		      <td><?php echo $request_status['start_time']; ?></td>
		    </tr>
		    <tr>
		      <td>结束时间</td>
		      <td><?php echo $request_status['complete_time']; ?></td>
		    </tr>
		    <tr>
		      <td>结束原因</td>
		      <td><?php echo $request_status['complete_reason']; ?></td>
		    </tr>
		    <tr>
		      <td>状态日志</td>
		      <td><textarea cols="80" readonly=1 ><?php echo $request_status['comment']; ?></textarea></td>
		    </tr>
		    </tbody>
		  <?php } ?>
		</table>
	    </td>
          </tr>
	  <tr>
	    <td>状态管理</td>
	    <td>
		<table>
		    <?php if( $status_code == 0 ) { ?>
                        <tr>
                            <td id="result_ap">设置状态: admin_process</td>
                            <td id="status_ap">
                                <textarea cols=80 id="st_pro_text" name="st_admin_process_comment" onclick="javascript:document.getElementById('st_pro_text').value='';">状态日志</textarea>
                            </td>
                            <td width="160"><a id="a_ap" onclick="changeStatus('ap')" class="button">Process状态置成超时</a></td>
                        </tr>
		    <?php } elseif( $status_code == 1 ) { ?>
			<tr>
			    <td id="result_ar">添加状态: admin_response</td>
			    <td id="status_ar">
				<textarea cols=80 id="st_res_text" name="st_admin_response_comment" onclick="javascript:document.getElementById('st_res_text').value='';">状态日志</textarea>
			    </td>
			    <td width="200"><a id="a_ar" onclick="changeStatus('ar')" class="button">添加状态并向HR发送反馈邮件</a></td>
			</tr>
			<tr>
			    <td id="result_ap">设置状态: admin_process</td>
			    <td id="status_ap">
				<textarea cols=80 id="st_pro_text" name="st_admin_process_comment" onclick="javascript:document.getElementById('st_pro_text').value='';">状态日志</textarea>
			    </td>
			    <td width="160"><a id="a_ap" onclick="changeStatus('ap')" class="button">Process状态置成超时</a></td>
			</tr>
		    <?php } elseif( $status_code == 2 ) { ?>
			<tr>
			    <td id="result_ar">添加状态: admin_response</td>
			    <td id="status_ar">
				<textarea cols=80 id="st_res_text" name="st_admin_response_comment" onclick="javascript:document.getElementById('st_res_text').value='';">状态日志</textarea>
			    </td>
			    <td width="200"><a id="a_ar" onclick="changeStatus('ar_r')" class="button">重新发送反馈邮件</a></td>
			</tr>
		    <?php } elseif( $status_code == 3 ) { ?>
			<tr>
			    <td id="result_cl">添加状态: close</td>
			    <td id="status_cl">
				<textarea cols=80 id="st_clo_text" name="st_close_comment" onclick="javascript:document.getElementById('st_clo_text').value='';">状态日志</textarea>
			    </td>
			    <td width="160"><a id="a_cl" onclick="changeStatus('cl')" class="button">置成结束状态</a></td>
			</tr>
		    <?php } ?>
		</table>
	    </td>
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

function changeStatus(type) {
	if( type == 'ar' ){
		ele_td = document.getElementById('status_ar');
		ele_in = document.createElement('input');
		ele_in.setAttribute('type', 'hidden');
		ele_in.setAttribute('value', 1);
		ele_in.setAttribute('name', 'st_admin_response');
		ele_td.appendChild(ele_in);
	}

	if( type == 'ar_r' ){
		ele_td = document.getElementById('status_ar');
		ele_in = document.createElement('input');
		ele_in.setAttribute('type', 'hidden');
		ele_in.setAttribute('value', 1);
		ele_in.setAttribute('name', 'st_admin_response_retry');
		ele_td.appendChild(ele_in);
	}
	
	if( type == 'ap' ){
                ele_td = document.getElementById('status_ap');
                ele_in = document.createElement('input');
                ele_in.setAttribute('type', 'hidden');
                ele_in.setAttribute('value', 1);
                ele_in.setAttribute('name', 'st_admin_process');
                ele_td.appendChild(ele_in);
        }

	if( type == 'cl' ){
                ele_td = document.getElementById('status_cl');
                ele_in = document.createElement('input');
                ele_in.setAttribute('type', 'hidden');
                ele_in.setAttribute('value', 1);
                ele_in.setAttribute('name', 'st_close');
                ele_td.appendChild(ele_in);
        }

    	$('#form').attr('action', '<?=$action_change_status; ?>');

    	$('#form').submit();
}


</script>

<script type="text/javascript">
$('.vtabs a').tabs();
</script> 
<?php echo $footer; ?>
