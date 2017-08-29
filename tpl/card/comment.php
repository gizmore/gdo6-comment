<?php
use GDO\Comment\GDO_Comment;
use GDO\UI\GDT_EditButton;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
$gdo instanceof GDO_Comment;
$user = GDO_User::current();
?>
<md-card layout-fill flex>
  <md-card-title>
    <md-card-title-text>
      <span class="md-headline">
        <div><?= $gdo->getCreator()->renderCell(); ?></div>
        <div class="gdo-card-date"><?= t('commented_at', [tt($gdo->getCreateDate())]); ?></div>
      </span>
    </md-card-title-text>
  </md-card-title>
  <gdo-div></gdo-div>
  <md-card-content>
    <?= $gdo->displayMessage(); ?>
    <?php if ($gdo->hasFile()) : ?>
    <div class="gdo-attachment" layout="row" flex layout-fill layout-align="left center">
      <div><?= GDT_Link::make()->icon('file_download')->href(href('Comment', 'Attachment', '&file='.$gdo->getFileID()))->renderCell(); ?></div>
      <div><?= $gdo->getFile()->renderCell(); ?></div>
    </div>
    <?php endif; ?>
  </md-card-content>
  <gdo-div></gdo-div>
  <md-card-actions layout="row" layout-align="end center">
    <?= GDT_EditButton::make()->href($gdo->hrefEdit())->writable($gdo->canEdit($user))->renderCell(); ?>
  </md-card-actions>

</md-card>
