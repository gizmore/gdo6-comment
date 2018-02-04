<?php
namespace GDO\Comment;

use GDO\Core\GDO;
use GDO\Table\MethodQueryCards;
use GDO\Util\Common;

abstract class Comments_List extends MethodQueryCards
{
	/**
	 * @return GDO_CommentTable
	 */
	public abstract function gdoCommentsTable();
	public function gdoTable() { return $this->gdoCommentsTable(); }
	
	public abstract function hrefAdd();
	
	/**
	 * @var GDO
	 */
	protected $object;
	
	public function init()
	{
		$this->object = $this->gdoCommentsTable()->gdoCommentedObjectTable()->find(Common::getRequestString('id'));
	}
	
	public function gdoQuery()
	{
		$query = $this->gdoTable()->select('gdo_comment.*')->where("comment_object=".$this->object->getID());
		$query->joinObject('comment_id');
		return $query->fetchTable(GDO_Comment::table());
	}
	
	public function execute()
	{
	    return $this->object->responseCard()->add(parent::execute());
	}
}
