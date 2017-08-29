<?php
namespace GDO\Comment;

use GDO\Core\GDO_Module;

final class Module_Comment extends GDO_Module
{
	public $module_priority = 30;
	public function getClasses() { return ['GDO\Comment\GDO_Comment', 'GDO\Comment\GDO_CommentLike']; }
	public function onLoadLanguage() { $this->loadLanguage('lang/comments'); }
}
