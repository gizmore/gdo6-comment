<?php
namespace GDO\Comment\Method;
use GDO\Table\MethodQueryTable;
use GDO\Comment\GDO_Comment;
use GDO\UI\GDT_EditButton;
/**
 * @author gizmore
 * @version 6.08
 */
final class Admin extends MethodQueryTable
{
	public function getPermission() { return 'staff'; }
	
	public function getHeaders()
	{
		return array_merge(array(
			GDT_EditButton::make(),
		), parent::getHeaders());
	}
	
	public function getQuery()
	{
		return GDO_Comment::table()->select()->orderDESC('comment_created');
	}

}
