<?php
namespace GDO\Comment;

use GDO\DB\GDO;
use GDO\DB\GDO_AutoInc;
use GDO\DB\GDO_CreatedAt;
use GDO\DB\GDO_CreatedBy;
use GDO\DB\GDO_DeletedAt;
use GDO\DB\GDO_DeletedBy;
use GDO\DB\GDO_EditedAt;
use GDO\DB\GDO_EditedBy;
use GDO\File\File;
use GDO\File\GDO_File;
use GDO\Template\GDO_Template;
use GDO\Type\GDO_Message;
use GDO\User\User;
use GDO\Vote\GDO_LikeCount;
use GDO\Vote\WithLikes;

final class Comment extends GDO
{
	use WithLikes;
	public function gdoLikeTable() { return CommentLike::table(); }
	
	public function gdoColumns()
	{
		return array(
			GDO_AutoInc::make('comment_id'),
// 			GDO_String::make('comment_title')->notNull(),
			GDO_Message::make('comment_message')->notNull(),
			GDO_File::make('comment_file'),
		    GDO_LikeCount::make('comment_likes'),
			GDO_CreatedAt::make('comment_created'),
			GDO_CreatedBy::make('comment_creator'),
			GDO_EditedAt::make('comment_edited'),
			GDO_EditedBy::make('comment_editor'),
			GDO_DeletedAt::make('comment_deleted'),
			GDO_DeletedBy::make('comment_deletor'),
		);		
	}
	
	public function getID() { return $this->getVar('comment_id'); }
	
	/**
	 * @return File
	 */
	public function getFile() { return $this->getValue('comment_file'); }
	public function hasFile() { return $this->getFileID() !== null; }
	public function getFileID() { return $this->getVar('comment_file'); }
	/**
	 * @return User
	 */
	public function getCreator() { return $this->getValue('comment_creator'); }
	public function getCreatorID() { return $this->getVar('comment_creator'); }
	public function getCreateDate() { return $this->getVar('comment_created'); }
	
// 	public function getTitle() { return $this->getVar('comment_title');  }
	public function getMessage() { return $this->getVar('comment_message');  }
	public function displayMessage() { return $this->gdoColumn('comment_message')->renderCell();  }
	
	public function renderCard()
	{
	    return GDO_Template::responsePHP('Comment', 'card/comment.php', ['gdo' => $this]);
	}
	
	public function canEdit(User $user)
	{
		return $user->hasPermission('staff');
	}
	
	public function hrefEdit()
	{
		return href('Comment', 'Edit', '&id='.$this->getID());
	}
	
}
