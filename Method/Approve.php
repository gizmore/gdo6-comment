<?php
namespace GDO\Comment\Method;
use GDO\Core\GDT_Template;
use GDO\Core\Method;
use GDO\Comment\GDO_Comment;
use GDO\Util\Common;
use GDO\Date\Time;
use GDO\User\GDO_User;
use GDO\Mail\Mail;
/**
 * Comment attachment download.
 * @author gizmore
 * @version 6.05
 */
final class Approve extends Method
{
	public function execute()
	{
		$comment = GDO_Comment::table()->find(Common::getRequestString('id'));
		if ($comment->isApproved())
		{
			return $this->error('err_comment_already_approved');
		}
		if ($comment->gdoHashcode() !== Common::getRequestString(''))
		{
			return $this->error('err_token');
		}
		
		$comment->saveVars(array(
			'comment_approved' => Time::getDate(),
			'comment_approvor' => GDO_User::current()->getID(),
		));
		
		$this->sendEmail($comment);
		
		return $this->message('msg_comment_approved');
	}

	public function sendEmail(GDO_Comment $comment)
	{
		foreach (GDO_User::staff() as $user)
		{
			$this->sendEmailTo($user, $comment);
		}
	}
	
	private function sendEmailTo(GDO_User $user, GDO_Comment $comment)
	{
		$mail = new Mail();
		$mail->setSubject(tusr($user, 'mail_approved_comment_title', [sitename()]));
		$tVars = array(
			'user' => $user,
			'comment' => $comment,
		);
		$mail->setBody(GDT_Template::phpUser($user, 'Comment', 'mail/approved_comment', $tVars));
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->sendToUser($user);
	}
}
