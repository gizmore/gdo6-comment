<?php
namespace GDO\Comment;

use GDO\Core\Module;

final class Module_Comment extends Module
{
	public $module_priority = 30;
	public function getClasses() { return ['GDO\Comment\Comment', 'GDO\Comment\CommentLike']; }
	public function onLoadLanguage() { $this->loadLanguage('lang/comments'); }
}
