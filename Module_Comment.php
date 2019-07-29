<?php
namespace GDO\Comment;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;

/**
 * Abstract comments. Reused in news, forum, helpdesk etc.
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 5.0
 */
final class Module_Comment extends GDO_Module
{
	##############
	### Module ###
	##############
	public $module_priority = 30;
	public function getDependencies() { return ['Vote', 'File']; }
	public function getClasses() { return ['GDO\Comment\GDO_Comment', 'GDO\Comment\GDO_CommentLike']; }
	public function onLoadLanguage() { $this->loadLanguage('lang/comments'); }
	public function href_administrate_module()  { return href('Comment', 'Admin'); }
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDT_Checkbox::make('comment_email')->initial('1'),
			GDT_Checkbox::make('comment_approval')->initial('0'),
		);
	}
	public function cfgEmail() { return $this->getConfigValue('comment_email'); }
	public function cfgApproval() { return $this->getConfigValue('comment_approval'); }
}
