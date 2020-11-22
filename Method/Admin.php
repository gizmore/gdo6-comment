<?php
namespace GDO\Comment\Method;

use GDO\Table\MethodQueryTable;
use GDO\Comment\GDO_Comment;
use GDO\UI\GDT_EditButton;

/**
 * @author gizmore
 * @version 6.10
 * @since 6.03
 */
final class Admin extends MethodQueryTable
{
	public function getPermission() { return 'staff'; }
	
    public function gdoTable()
    {
        return GDO_Comment::table();
    }

	public function gdoHeaders()
	{
		return array_merge(array(
			GDT_EditButton::make(),
		), parent::gdoHeaders());
	}
	
	public function getQuery()
	{
		return GDO_Comment::table()->select()->orderDESC('comment_created');
	}

}
