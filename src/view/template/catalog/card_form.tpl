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
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
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
              <input type="hidden" name="bg_color" value="<?php echo $bg_color; ?>" />
              <!-- tr>
                <td><?php echo $this->language->get('entry_bgcolor'); ?></td>
                <td><input class="color" id="bg_color" name="bg_color" onchange="this.value=this.color.rgb" value=<?php echo $bg_color; ?> />
                </td>
              </tr -->
              <tr>
                <td>Theme ID</td>
                <?php if ($theme_id <= 1 || $theme_id > 5) $theme_id = 1; ?>
                <td>
                   <input type="radio" name="theme_id" value="1" <?php echo ($theme_id == 1)?'checked':''; ?> >1:&nbsp;<img align="top" src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage("1"); ?>" /></input><br />
                   <input type="radio" name="theme_id" value="2" <?php echo ($theme_id == 2)?'checked':''; ?> >2:&nbsp;<img align="top" src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage("2"); ?>" /></input><br />
                   <input type="radio" name="theme_id" value="3" <?php echo ($theme_id == 3)?'checked':''; ?> >3:&nbsp;<img align="top" src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage("3"); ?>" /></input></br />
                   <input type="radio" name="theme_id" value="4" <?php echo ($theme_id == 4)?'checked':''; ?> >4:&nbsp;<img align="top" src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage("4"); ?>" /></input><br />
                   <input type="radio" name="theme_id" value="5" <?php echo ($theme_id == 5)?'checked':''; ?> >5:&nbsp;<img align="top" src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage("5"); ?>" /></input><br />

                   
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_image; ?></td>
                <td><div class="image"><img src="<?php echo HTTP_IMAGE . $orig_image; ?>" alt="" id="thumb" /><br />
                  <input type="hidden" name="orig_image" value="<?php echo $orig_image; ?>" id="image" />
                  <a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a></div></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get('entry_default_body'); ?></td>
                <td><textarea rows="15" cols="80" name="default_body" id="default_body"><?php echo !empty($default_body) ? $default_body : ''; ?></textarea></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get('entry_category'); ?></td>
                <td><div class="scrollbox" style="width:500px;height:300px">
                  <?php $class = 'odd'; ?>
                  <?php foreach ($card_categories as $card_category) { ?>
                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                  <div class="<?php echo $class; ?>">
                    <?php if (in_array($card_category['card_category_id'], $the_card_category)) { ?>
                    <input type="checkbox" name="the_card_category[]" value="<?php echo $card_category['card_category_id']; ?>" checked="checked" />
                    <?php echo $card_category['name']; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="the_card_category[]" value="<?php echo $card_category['card_category_id']; ?>" />
                    <?php echo $card_category['name']; ?>
                    <?php } ?>
                  </div>
                  <?php } ?>
                </div>
                <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $this->language->get('text_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $this->language->get('text_unselect_all'); ?></a>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_sort_order; ?></td>
                <td><input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="1" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="status">
                  <?php if ($status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $this->language->get('text_disabled'); ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $this->language->get('text_enabled'); ?></option>
                  <option value="0" selected="selected"><?php echo $this->language->get('text_disabled'); ?></option>
                  <?php } ?>
                </select>
                </td>
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
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
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
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
$('#languages a').tabs();
//--></script> 
<?php echo $footer; ?>