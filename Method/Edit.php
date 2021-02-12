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
use GDO\DB\GDT_String;
use GDO\Date\Time;
use GDO\Core\Website;
use GDO\UI\GDT_Redirect;
use GDO\Core\GDT_Response;
use GDO\Form\GDT_DeleteButton;
/**
 * Edit a comment.
 * 
 * @author gizmore
 * @see Comments_List
 * @see Comments_Write
 * @see GDT_Message
 * @see GDT_File
 */
class Edit extends MethodForm
{
	public function gdoParameters()
	{
		return [
			GDT_String::make('id')->notNull(),
		];
	}
	
	public function execute()
	{
	    if (isset($_REQUEST['delete']))
	    {
	        if ($this->comment->canEdit(GDO_User::current()))
	        {
	            $this->comment->delete();
    	        return
        	        $this->message('msg_crud_deleted', [$this->comment->gdoHumanName()])->
        	        add($this->redirectToList());
	        }
	    }
	    return parent::execute();
	}
	
	private function redirectToList()
	{
	    
	}
	
	/**
	 * @var GDO_Comment
	 */
	protected $comment;
	
	public function init()
	{
		$user = GDO_User::current();
		$this->comment = GDO_Comment::table()->find(Common::getRequestString('id'));
		if ($this->comment->isDeleted())
		{
		    throw new GDOError('err_is_deleted');
		}
		if (!$this->comment->canEdit($user))
		{
			throw new GDOError('err_no_permission');
		}
	}
	
	public function afterExecute()
	{
	    if (!$this->comment->isDeleted())
	    {
	        return GDT_Response::makeWithHTML($this->comment->renderCard());
	    }
	    else
	    {
	        return Website::redirectBack(6);
	    }
	}
	
	public function createForm(GDT_Form $form)
	{
		$form->addFields([
// 			$this->comment->gdoColumn('comment_title'),
			$this->comment->gdoColumn('comment_message'),
			$this->comment->gdoColumn('comment_file'),
			$this->comment->gdoColumn('comment_top'),
			GDT_AntiCSRF::make(),
		]);
		$form->actions()->addFields([
			GDT_Submit::make(),
			GDT_DeleteButton::make(),
		]);
		
		if (!$this->comment->isApproved())
		{
			$form->actions()->addField(GDT_Submit::make('approve'));
		}
		
		$form->withGDOValuesFrom($this->comment);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->comment->saveVars($form->getFormData());
		return $this->message('msg_comment_edited')->add($this->renderPage());
	}
	
	public function onSubmit_delete(GDT_Form $form)
	{
		if ($file = $this->comment->getFile())
		{
			$file->delete();
		}
		$this->comment->delete();
		return $this->message('msg_comment_deleted')->add(GDT_Response::makeWith(GDT_Redirect::make()->href($this->hrefBack())));
	}
	
	public function hrefBack()
	{
		return Website::hrefBack();
	}
	
	public function onSubmit_approve(GDT_Form $form)
	{
		if ($this->comment->isApproved())
		{
			return $this->error('err_comment_already_approved');
		}
		
		$this->comment->saveVars(array(
			'comment_approved' => Time::getDate(),
			'comment_approvor' => GDO_User::current()->getID(),
		));
		
		Approve::make()->sendEmail($this->comment);
		
		Website::redirect(href('Comment', 'Admin', 12));
		return $this->message('msg_comment_approved')->add($this->renderPage());
	}
}
