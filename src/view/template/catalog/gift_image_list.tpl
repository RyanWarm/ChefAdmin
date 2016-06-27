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
      <h1><img src="view/image/category.png" alt="" />图片</h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a><a onclick="$('#form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?php echo $this->language->get('column_id'); ?></td>
              <td class="left">名称</td>
              <td class="left">图片</td>
              <td class="left">GIF动画</td>
              <td class="left">分类</td>
              <td class="left">Tag</td>
              <td class="left">祝福语</td>
              <td class="right">Score</td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td></td>
              <td><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td></td>
              <td></td>
              <td>
                <select name="filter_category">
                  <option value="*"></option>
                  <?php
                    foreach ($gift_image_categories as $category) {
                      if ($filter_category == $category['category_id']) { ?>
                      <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                   <?php } else { ?>
                      <option value="<?php echo $category['category_id']; ?>" ><?php echo $category['name']; ?></option>
                   <?php } 
                    }
                   ?>                      

                </select>
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td align="right"><a onclick="filter();" class="button">筛选</a></td>
            </tr>
            <?php if ($gift_images) { ?>
            <?php foreach ($gift_images as $gift_image) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($gift_image['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $gift_image['image_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $gift_image['image_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $gift_image['image_id']; ?></td>
              <td class="left"><?php echo $gift_image['name']; ?></td>
              <td class="center"><img src="<?php echo HTTP_IMAGE . $gift_image['image']; ?>" alt="<?php echo $gift_image['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
              <td class="center"><img src="<?php echo HTTP_IMAGE . $gift_image['animate']; ?>" alt="" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
              <td class="left"><?php echo $gift_image['category_name']; ?></td>        
              <td class="left"><?php echo $gift_image['tag_name']; ?></td>
              <td class="left" style="width: 100px;overflow:hidden; text-wrap:ellips;"><?php echo $gift_image['wish']; ?></td>
              
              <td class="right"><?php echo $gift_image['score']; ?></td>
              <td class="right"><?php foreach ($gift_image['action'] as $action) { ?>
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
	url = 'index.php?route=catalog/gift_image';
	
	var filter_name = $('input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_category = $('select[name=\'filter_category\']').attr('value');
	
	if (filter_category) {
		url += '&filter_category=' + encodeURIComponent(filter_category);
	}


    location = url;
}

</script>
<?php echo $footer; ?>