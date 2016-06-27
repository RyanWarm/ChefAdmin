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
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a><a onclick="$('#form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?php echo $this->language->get('column_id'); ?></td>
              <td class="left"><?php echo $column_name; ?></td>
              <td class="left">图片</td>
              <td class="center">Theme</td>
              <td class="left">分类</td>
              <td class="left">祝福语</td>
              <td class="right"><?php echo $this->language->get('column_status'); ?></td>
              <td class="right"><?php echo $column_sort_order; ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($cards) { ?>
            <?php foreach ($cards as $card) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($card['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $card['card_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $card['card_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $card['card_id']; ?></td>
              <td class="left"><?php echo $card['name']; ?></td>
              <td class="center"><img src="<?php echo $card['image']; ?>" alt="<?php echo $card['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
              <td class="center"><img src="<?php echo HTTP_IMAGE . ControllerCatalogCard::getCardThemeImage($card['theme_id']); ?>" style="padding: 1px; border: 0; width:80px; height: 30px" /></td>
              <td class="left"><?php echo $card['category']; ?></td>
              <td class="left"><?php echo $card['default_body']; ?></td>
              
              <td class="right"><?php echo $card['status']; ?></td>
              <td class="right"><?php echo $card['sort_order']; ?></td>
              <td class="right"><?php foreach ($card['action'] as $action) { ?>
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
    </div>
  </div>
</div>
<?php echo $footer; ?>