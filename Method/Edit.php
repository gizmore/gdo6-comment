<?php
namespace GDO\Comment\Method;

use GDO\Comment\GDO_Comment;
use GDO\Core\GDOError;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;
use GDO\Util\Common;
/**
 * Edit a comment.
 * 
 * @author gizmore
 * @see Comments_List
 * @see Comments_Write
 * @see GDT_Message
 * @see GDT_File
 */
final class Edit extends MethodForm
{
	public function getPermission() { return 'staff'; }
	
	/**
	 * @var GDO_Comment
	 */
	private $comment;
	
	public function init()
	{
		$user = GDO_User::current();
		$this->comment = GDO_Comment::table()->find(Common::getRequestString('id'));
		if (!$this->comment->canEdit($user))
		{
			throw new GDOError('err_no_permission');
		}
	}
	
	public function execute()
	{
		return parent::execute()->add($this->comment->renderCard());
	}
	
	public function createForm(GDT_Form $form)
	{
		$form->addFields(array(
// 			$this->comment->gdoColumn('comment_title'),
			$this->comment->gdoColumn('comment_message'),
			$this->comment->gdoColumn('comment_file'),
			GDT_AntiCSRF::make(),
			GDT_Submit::make(),
			GDT_Submit::make('delete'),
		));
		$form->withGDOValuesFrom($this->comment);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->comment->saveVars($form->getFormData());
		return $this->message('msg_comment_edited');
	}
	
	public function onSubmit_delete(GDT_Form $form)
	{
		if ($file = $this->comment->getFile())
		{
			$file->delete();
		}
		$this->comment->delete();
		return $this->message('msg_comment_deleted');
	}
}
