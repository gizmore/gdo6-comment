<?php
namespace GDO\Comment;

use GDO\DB\GDO;
use GDO\Table\MethodQueryCards;
use GDO\Util\Common;
use GDO\Template\Response;

abstract class Comments_List extends MethodQueryCards
{
	/**
	 * @return CommentTable
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
		$query = $this->gdoTable()->select('gwf_comment.*')->where("comment_object=".$this->object->getID());
		$query->joinObject('comment_id');
		return $query->fetchTable(Comment::table());
	}
	
	public function execute()
	{
		return $this->object->renderCard()->add(parent::execute());
	}
}
