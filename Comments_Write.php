<?php
namespace GDO\Comment;

use GDO\Core\Website;
use GDO\Core\GDO;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Core\Application;
use GDO\Date\Time;
use GDO\Mail\Mail;
use GDO\Core\GDT_Template;

abstract class Comments_Write extends MethodForm
{
	/**
	 * @return GDO_CommentTable
	 */
	public abstract function gdoCommentsTable();

	public abstract function hrefList();

	/**
	 * @var GDO
	 */
	protected $object;
	
	/**
	 * @var GDO_Comment
	 */
	protected $oldComment;
	
	public function createForm(GDT_Form $form)
	{
		$gdo = GDO_Comment::table();
// 		$form->addField($gdo->gdoColumn('comment_title'));
		$form->addField($gdo->gdoColumn('comment_message'));
		if ($this->gdoCommentsTable()->gdoAllowFiles())
		{
			$form->addField($gdo->gdoColumn('comment_file'));
		}
		$form->addFields(array(
			GDT_Submit::make(),
			GDT_AntiCSRF::make(),
		));
		
		if (1 === $this->gdoCommentsTable()->gdoMaxComments(GDO_User::current()))
		{
			$form->withGDOValuesFrom($this->oldComment);
		}
	}
	
	public function init()
	{
		$this->object = $this->gdoCommentsTable()->gdoCommentedObjectTable()->find(Common::getRequestString('id'));
		if (1 === $this->gdoCommentsTable()->gdoMaxComments(GDO_User::current()))
		{
			$this->oldComment = $this->object->getUserComment();
		}
	}
	
	public function execute()
	{
		$response = parent::execute();
		return $this->object->responseCard()->add($response);
	}
	
	public function successMessage()
	{
		return $this->message('msg_comment_added');
	}
	
	public function formValidated(GDT_Form $form)
	{
		if ($this->oldComment)
		{
			$this->oldComment->saveVars($form->getFormData());
		}
		else
		{
			# Insert comment
			$comment = GDO_Comment::blank($form->getFormData());
			if (!Module_Comment::instance()->cfgApproval())
			{
				$comment->setVars(array(
					'comment_approved' => Time::getDate(),
					'comment_approver' => GDO_User::system()->getID(),
				));
			}
			$comment->insert();
			
			# Relation entry
			$entry = $this->gdoCommentsTable()->blank(array(
				'comment_object' => $this->object->getID(),
				'comment_id' => $comment->getID(),
			));
			$entry->insert();
			
			if (Module_Comment::instance()->cfgEmail())
			{
				$this->sendEmail($comment);
			}
		}
		
		$response = $this->successMessage();
		if (!Application::instance()->isAjax())
		{
			$response->add(Website::redirectMessage($this->hrefList()));
		}
		return $response;
	}
	
	private function sendEmail(GDO_Comment $comment)
	{
		foreach (GDO_User::staff() as $user)
		{
			$this->sendEmailTo($user, $comment);
		}
	}
	
	private function sendEmailTo(GDO_User $user, GDO_Comment $comment)
	{
		$mail = new Mail();
		$mail->setSubject(tusr($user, 'mail_new_comment_title', [sitename()]));
		$tVars = array(
			'user' => $user,
			'comment' => $comment,
			'href_approve' => $comment->urlApprove(),
			'href_delete' => $comment->urlDelete(),
		);
		$mail->setBody(GDT_Template::phpUser($user, 'Comment', 'mail/new_comment', $tVars));
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->sendToUser($user);
	}
}
