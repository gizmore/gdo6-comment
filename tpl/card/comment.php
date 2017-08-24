<?php
use GDO\Comment\Comment;
use GDO\UI\GDO_EditButton;
use GDO\UI\GDO_Link;
use GDO\User\User;
$gdo instanceof Comment;
$user = User::current();
?>
<md-card>
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
      <div><?= GDO_Link::make()->icon('file_download')->href(href('Comment', 'Attachment', '&file='.$gdo->getFileID()))->renderCell(); ?></div>
      <div><?= $gdo->getFile()->renderCell(); ?></div>
    </div>
    <?php endif; ?>
  </md-card-content>
  <gdo-div></gdo-div>
  <md-card-actions layout="row" layout-align="end center">
    <?= GDO_EditButton::make()->href($gdo->hrefEdit())->writable($gdo->canEdit($user))->renderCell(); ?>
  </md-card-actions>

</md-card>
