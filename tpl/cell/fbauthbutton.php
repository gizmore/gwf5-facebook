<?php $field instanceof GDO_FBAuthButton; ?>
<?php $icon = sprintf('<img src="/module/Facebook/img/fb-btn.png" title="%s" style="width: 300px;" />', t('btn_continue_with_fb')); ?>
<?= GDO_Button::make()->noLabel()->href($field->href)->rawIcon($icon); ?>
