<?php
namespace GDO\Comment;

use GDO\Core\GDO;
use GDO\Table\MethodQueryCards;
use GDO\Util\Common;
use GDO\Table\GDT_List;
use GDO\Core\GDT_Response;
use GDO\Session\GDO_Session;

abstract class Comments_List extends MethodQueryCards
{
    const LAST_LIST_KEY = 'comments_list_last';
    
    public function setLastList()
    {
        GDO_Session::set(self::LAST_LIST_KEY, $_SERVER['REQUEST_URI']);
    }
    
    public function getLastList()
    {
        return GDO_Session::set(self::LAST_LIST_KEY);
    }
        
	public function showInSitemap() { return false; }
	
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
	
	public function getQuery()
	{
		$query = $this->gdoTable()->select('gdo_comment.*')->
		  where("comment_deleted is NULL")->
		  where("comment_object=".$this->object->getID());
		$query->joinObject('comment_id');
		$query->where("comment_approved IS NOT NULL");
		return $query->fetchTable(GDO_Comment::table());
	}
	
	/**
	 * @return GDT_Response
	 */
	public function execute()
	{
		return $this->object->responseCard()->addField(parent::execute());
	}

	public function gdoDecorateList(GDT_List $list)
	{
		$count = $this->object->getCommentCount();
		$list->title(t('list_comments', [$this->object->displayName(), $count]));
	}

}
