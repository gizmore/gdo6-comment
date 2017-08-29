<?php
namespace GDO\Comment;

use GDO\Core\GDOError;
use GDO\DB\GDO;
use GDO\DB\GDT_Object;
use GDO\User\GDO_User;

class GDO_CommentTable extends GDO
{
	################
	### Comments ###
	################
	public function gdoCommentedObjectTable() {}

	public function gdoEnabled() { return true; }
	public function gdoAllowTitle() { return true; }
	public function gdoAllowFiles() { return true; }
	public function gdoMaxComments(GDO_User $user) { return 1; }
	###########
	### GDO ###
	###########
	/**
	 * @return GDO
	 */
	public function gdoAbstract() { return $this->gdoCommentedObjectTable() === null; }
	public function gdoColumns()
	{
		return array(
		    GDT_Object::make('comment_id')->primary()->table(GDO_Comment::table()),
			GDT_Object::make('comment_object')->primary()->table($this->gdoCommentedObjectTable()),
		);
	}
	
	### 
	/**
	 * @param string $className
	 * @return GDO_CommentTable
	 */
	public static function getInstance(string $className)
	{
		$table = GDO::tableFor($className);
		if (!($table instanceof GDO_CommentTable))
		{
			throw new GDOError('err_comment_table', [html($className)]);
		}
		return $table;
	}
}
