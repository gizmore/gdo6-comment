<?php
namespace GDO\Comment;

use GDO\Core\Website;
use GDO\DB\GDO;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\User;
use GDO\Util\Common;

abstract class Comments_Write extends MethodForm
{
	/**
	 * @return CommentTable
	 */
	public abstract function gdoCommentsTable();

	public abstract function hrefList();

	/**
	 * @var GDO
	 */
	protected $object;
	
	/**
	 * @var Comment
	 */
	protected $oldComment;
	
	public function createForm(GDT_Form $form)
	{
		$gdo = Comment::table();
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
		
		if (1 === $this->gdoCommentsTable()->gdoMaxComments(User::current()))
		{
		    $form->withGDOValuesFrom($this->oldComment);
		}
	}
	
	public function init()
	{
		$this->object = $this->gdoCommentsTable()->gdoCommentedObjectTable()->find(Common::getRequestString('id'));
		if (1 === $this->gdoCommentsTable()->gdoMaxComments(User::current()))
		{
		    $this->oldComment = $this->object->getUserComment();
		}
	}
	
	public function execute()
	{
		$response = parent::execute();
		return $this->object->renderCard()->add($response);
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
    		$comment = Comment::blank($form->getFormData())->insert();
    		$entry = $this->gdoCommentsTable()->blank(array(
    			'comment_object' => $this->object->getID(),
    			'comment_id' => $comment->getID(),
    		))->insert();
	    }
		return $this->successMessage()->add(Website::redirectMessage($this->hrefList()));
	}
}
