<?php
namespace GDO\Comment;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_DeletedBy;
use GDO\DB\GDT_EditedAt;
use GDO\DB\GDT_EditedBy;
use GDO\File\GDO_File;
use GDO\File\GDT_File;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Message;
use GDO\User\GDO_User;
use GDO\Vote\GDT_LikeCount;
use GDO\Vote\WithLikes;
use GDO\DB\GDT_Checkbox;
use GDO\Date\GDT_DateTime;
use GDO\User\GDT_User;
use GDO\Date\Time;

/**
 * A comment.
 * Comments can be attached to objects by use CommentedObject.
 * Comments can be liked.
 * 
 * @see CommentedObject
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 6.00
 */
final class GDO_Comment extends GDO
{
	use WithLikes;
	public function gdoLikeTable() { return GDO_CommentLike::table(); }
	
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('comment_id'),
// 			GDT_String::make('comment_title')->notNull(),
			GDT_Message::make('comment_message')->notNull(),
			GDT_File::make('comment_file'),
			GDT_LikeCount::make('comment_likes'),
			GDT_Checkbox::make('comment_top')->editable(false)->initial('0'),
			GDT_CreatedAt::make('comment_created'),
			GDT_CreatedBy::make('comment_creator'),
			GDT_DateTime::make('comment_approved'),
			GDT_User::make('comment_approvor')->label('comment_approvor'),
			GDT_EditedAt::make('comment_edited'),
			GDT_EditedBy::make('comment_editor'),
			GDT_DeletedAt::make('comment_deleted'),
			GDT_DeletedBy::make('comment_deletor'),
		);		
	}
	
	public function getID() { return $this->getVar('comment_id'); }
	
	/**
	 * @return GDO_File
	 */
	public function getFile() { return $this->getValue('comment_file'); }
	public function hasFile() { return $this->getFileID() !== null; }
	public function getFileID() { return $this->getVar('comment_file'); }
	/**
	 * @return GDO_User
	 */
	public function getCreator() { return $this->getValue('comment_creator'); }
	public function getCreatorID() { return $this->getVar('comment_creator'); }
	public function getCreateDate() { return $this->getVar('comment_created'); }
	
// 	public function getTitle() { return $this->getVar('comment_title');  }
	public function getMessage() { return $this->getVar('comment_message');  }
	public function displayMessage() { return $this->gdoColumn('comment_message')->renderCell();  }
	
	public function isApproved() { return $this->getVar('comment_approved') !== null; }
	public function isDeleted() { return $this->getVar('comment_deleted') !== null; }
	
	public function renderCard()
	{
		return GDT_Template::php('Comment', 'card/comment.php', ['gdo' => $this]);
	}
	
	public function canEdit(GDO_User $user)
	{
		return $user->hasPermission('staff');
	}
	
	public function canDelete(GDO_User $user)
	{
		return $user->hasPermission('staff');
	}
	
	public function hrefEdit()
	{
		return href('Comment', 'Edit', '&id='.$this->getID());
	}
	
	public function href_edit()
	{
		return $this->hrefEdit();
	}
	
	public function urlApprove()
	{
		return url('Comment', 'Approve', '&id='.$this->getID().'&token='.$this->gdoHashcode());
	}
	
	public function urlDelete()
	{
		return url('Comment', 'Delete', '&id='.$this->getID().'&token='.$this->gdoHashcode());
	}
	
	public function delete()
	{
		return $this->saveVars(array(
			'comment_deleted' => Time::getDate(),
			'comment_deletor' => GDO_User::current()->getID(),
		));
	}
}
