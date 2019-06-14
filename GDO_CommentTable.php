<?php
namespace GDO\Comment;

use GDO\Core\GDOError;
use GDO\Core\GDO;
use GDO\DB\GDT_Object;
use GDO\User\GDO_User;

class GDO_CommentTable extends GDO
{
	################
	### Comments ###
	################
	/**
	 * @return GDO
	 */
	public function gdoCommentedObjectTable() {}

	public function gdoEnabled() { return true; }
	public function gdoAllowTitle() { return true; }
	public function gdoAllowFiles() { return true; }
	public function gdoMaxComments(GDO_User $user) { return 100; }
	
	public function canAddComment(GDO_User $user) { return true; }
	public function canEditComment(GDO_Comment $comment, GDO_User $user) { return $comment->canEdit($user); }
	public function canDeleteComment(GDO_Comment $comment, GDO_User $user) { return $comment->canDelete($user); }
	
	###########
	### GDO ###
	###########
	/**
	 * @return GDO
	 */
	public function gdoAbstract() { return !$this->gdoCommentedObjectTable(); }
	public function gdoColumns()
	{
		return array(
			GDT_Object::make('comment_id')->primary()->table(GDO_Comment::table())->cascade(),
			GDT_Object::make('comment_object')->primary()->table($this->gdoCommentedObjectTable())->cascade(),
		);
	}
	
	/**
	 * @return GDO
	 */
	public function getCommentedObject() { return $this->getValue('comment_object'); }
	
	public static function getCommentedObjectByComment(GDO_Comment $comment, $fetchAs=null)
	{
		$fetchAs = $fetchAs = null ?
			self::table()->gdoCommentedObjectTable() :
			$fetchAs;
		return self::table()->select()->
			fetchTable($fetchAs)->
			joinObject('comment_object')->
			where("comment_id={$comment->getID()}")->
			first()->exec()->fetchObject();
	}
	
	### 
	/**
	 * @param string $className
	 * @return GDO_CommentTable
	 */
	public static function getInstance($className)
	{
		$table = GDO::tableFor($className);
		if (!($table instanceof GDO_CommentTable))
		{
			throw new GDOError('err_comment_table', [html($className)]);
		}
		return $table;
	}
}
