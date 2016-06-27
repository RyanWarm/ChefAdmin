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
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/category.png" alt="" />图片</h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <div id="languages" class="htabs">
            <?php foreach ($languages as $language) { ?>
            <a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
            <?php } ?>
          </div>
          <div>
            <table class="form">
              <tr>
                <td><span class="required">*</span> <?php echo $entry_name; ?></td>
                <td><input type="text" name="name" size="100" value="<?php echo $name; ?>" />
                  <span class="error"><?php echo $error_name; ?></span>
              </tr>
              <!-- tr>
                <td><?php echo $this->language->get('entry_bgcolor'); ?></td>
                <td><input class="color" id="bg_color" name="bg_color" onchange="this.value=this.color.rgb" value=<?php echo $bg_color; ?> />
                </td>
              </tr -->

              <tr>
                <td><?php echo $entry_image; ?></td>
                <td><div class="image"><img src="<?php echo HTTP_IMAGE . $image; ?>" alt="" id="thumb" /><br />
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                  <a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a></div></td>
              </tr>
              <tr>
                <td>GIF动画</td>
                <td><div class="image"><img src="<?php echo HTTP_IMAGE . $animate; ?>" alt="" id="animate_thumb" /><br />
                  <input type="hidden" name="animate" value="<?php echo $animate; ?>" id="animate" />
                  <a onclick="image_upload('animate', 'animate_thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#animate_thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a></div></td>
              </tr>

              <tr>
                <td><?php echo $this->language->get('entry_default_body'); ?></td>
                <td><textarea rows="15" cols="80" name="wish" id="wish"><?php echo !empty($wish) ? $wish : ''; ?></textarea></td>
              </tr>
              <tr>
                <td>Score</td>
                <td><input type="text" name="score" value="<?php echo $score; ?>" size="1" /></td>
              </tr>
              <tr>
              <td>Tag</td>
              <td><div class="scrollbox" style="width:500px;height:300px">
                  <?php $class = 'odd'; ?>
                  <?php foreach ($tags as $tag) { ?>
                  <?php if ($tag['status'] == 1) { ?>
                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                  <div class="<?php echo $class; ?>">
                    <?php if (in_array($tag['tag_id'], $product_tag)) { ?>
                    <input type="checkbox" name="product_tag[]" value="<?php echo $tag['tag_id']; ?>" checked="checked" />
                    <?php echo $tag['name']; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="product_tag[]" value="<?php echo $tag['tag_id']; ?>" />
                    <?php echo $tag['name']; ?>
                    <?php } ?>
                  </div>
                  <?php } ?>
                  <?php } ?>
                </div>
                <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">全选</a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">取消选择</a></td>
            </tr>

              <tr>
              <td>图片分类</td>
              <td><div class="scrollbox" style="width:500px;height:300px">
                  <?php $class = 'odd'; ?>
                  <?php foreach ($gift_image_categories as $category) { ?>
                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                  <div class="<?php echo $class; ?>">
                    <?php if (in_array($category['category_id'], $gift_image_category)) { ?>
                    <input type="checkbox" name="gift_image_category[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                    <?php echo $category['name']; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="gift_image_category[]" value="<?php echo $category['category_id']; ?>" />

                    <?php echo $category['name']; ?>
                    <?php } ?>
                  </div>
                  <?php } ?>
                </div>
                <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">全选</a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">取消选择</a></td>
            </tr>

            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
CKEDITOR.replace('description<?php echo $language['language_id']; ?>', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
<?php } ?>
//--></script> 
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
                $('#' + thumb).replaceWith('<img src="<?php echo HTTP_IMAGE; ?>' + $('#' + field).val() + '" alt="" id="' + thumb + '" />');
/*
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
*/

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
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
$('#languages a').tabs();
//--></script> 
<?php echo $footer; ?>