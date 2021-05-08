<?php
namespace GDO\Comment\Method;

use GDO\Comment\GDO_Comment;
use GDO\Core\GDT_Template;
use GDO\Core\Method;
use GDO\DB\GDT_String;
use GDO\Date\Time;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Mail\Mail;
use GDO\Core\GDT_Hook;

/**
 * @author gizmore
 * @version 6.09
 */
final class Delete extends Method
{
	public function gdoParameters()
	{
		return array(
			GDT_String::make('id')->notNull(),
		);
	}
	
	public function execute()
	{
		$comment = GDO_Comment::table()->find(Common::getRequestString('id'));
		if ($comment->isDeleted())
		{
			return $this->error('err_comment_already_deleted');
		}
		if ($comment->gdoHashcode() !== Common::getRequestString('token'))
		{
			return $this->error('err_token');
		}
		
		$this->deleteComment($comment);

		return $this->message('msg_comment_deleted');
	}
	
	public function deleteComment(GDO_Comment $comment)
	{
	    $comment->saveVars(array(
	        'comment_deleted' => Time::getDate(),
	        'comment_deletor' => GDO_User::current()->getID(),
	    ));
	    
	    $this->sendEmail($comment);
	    
	    GDT_Hook::callWithIPC('CommentDeleted', $comment);
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
		$mail->setSubject(tusr($user, 'mail_deleted_comment_title', [sitename()]));
		$tVars = array(
			'user' => $user,
			'comment' => $comment,
		);
		$mail->setBody(GDT_Template::phpUser($user, 'Comment', 'mail/deleted_comment.php', $tVars));
		$mail->setSender(GDO_BOT_EMAIL);
		$mail->sendToUser($user);
	}
}
