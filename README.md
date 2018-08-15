# gdo6-comment
Reusable comments module for gdo6.

In Your GDO do use the trait CommentedObject and specify the comments table.
	
	class GDO_Ticket extends GDO
	{
		use \GDO\Comment\CommentedObject;
		public function gdoCommentTable() { return GDO_TicketMessage::table(); }
	}
	
Also create a GDO for your comment entries.

	class GDO_TicketMessage extends \GDO\Comment\GDO_CommentTable
	{
		public function gdoCommentedObjectTable() { return GDO_Ticket::table(); }
		public function gdoAllowFiles() { return Module_Helpdesk::instance()->cfgAttachments(); }
	}
	
Create methods to handle commenting.

	final class WriteComment extends Comments_Write
	{
		public function gdoCommentsTable() { return GDO_TicketMessage::table(); }
		public function hrefList() { return href('Helpdesk', 'Tickets', '&id='.$this->object->getID()); }
		
	}
	   
	final class Comments extends Comments_List
	{
		public function gdoCommentsTable() { return GDO_TicketMessage::table(); }
		public function hrefAdd() { return href('Helpdesk', 'WriteComment', 'id='.$this->object->getID()); }
	}
