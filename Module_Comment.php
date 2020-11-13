<?php
namespace GDO\Comment;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_User;

/**
 * Abstract comments. Reused in news, forum, helpdesk etc.
 * 
 * @author gizmore@wechall.net
 * @version 6.10
 * @since 5.0
 */
final class Module_Comment extends GDO_Module
{
	##############
	### Module ###
	##############
	public $module_priority = 30;
	public function getDependencies() { return ['Vote', 'File']; }
	public function getClasses() { return [GDO_Comment::class, GDO_CommentLike::class]; }
	public function onLoadLanguage() { $this->loadLanguage('lang/comments'); }
	public function href_administrate_module()  { return href('Comment', 'Admin', 'File'); }
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDT_Checkbox::make('comment_email')->initial('1'),
			GDT_Checkbox::make('comment_approval_guest')->initial('1'),
			GDT_Checkbox::make('comment_approval_member')->initial('0'),
			GDT_Checkbox::make('comment_captcha_guest')->initial('1'),
			GDT_Checkbox::make('comment_captcha_member')->initial('0'),
		);
	}
	public function cfgEmail() { return $this->getConfigValue('comment_email'); }
	public function cfgApprovalGuest() { return $this->getConfigValue('comment_approval_guest'); }
	public function cfgApprovalMember() { return $this->getConfigValue('comment_approval_member'); }
	public function cfgCaptchaGuest() { return $this->getConfigValue('comment_captcha_guest'); }
	public function cfgCaptchaMember() { return $this->getConfigValue('comment_captcha_member'); }
	public function cfgCaptcha()
	{
		return GDO_User::current()->isMember() ?
		$this->cfgCaptchaMember() :
		$this->cfgCaptchaGuest();
	}
	public function cfgApproval()
	{
		return GDO_User::current()->isMember() ?
		$this->cfgApprovalMember() :
		$this->cfgApprovalGuest();
	}
	
}
