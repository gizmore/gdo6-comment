<?php
use GDO\Comment\GDO_Comment;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_User;
use GDO\UI\GDT_Card;

$gdo instanceof GDO_Comment;
$user = GDO_User::current();

$card = GDT_Card::make()->gdo($gdo);
$card->withCreator();
$card->title($gdo->getCreator()->displayNameLabel());
$card->subtitle(tt($gdo->getCreateDate()));

$card->addFields(array(
	$gdo->gdoColumn('comment_message'),
));

if ($gdo->hasFile())
{
	$card->addFields(array(
		$gdo->gdoColumn('comment_file'),
	));
}

$card->actions()->addFields(array(
	GDT_EditButton::make()->href($gdo->hrefEdit())->writable($gdo->canEdit($user)),
));

echo $card->render();
