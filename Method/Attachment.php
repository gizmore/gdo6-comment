<?php
namespace GDO\Comment\Method;
use GDO\Core\Method;
use GDO\DB\GDT_String;
use GDO\File\Method\GetFile;
/**
 * Comment attachment download.
 * @author gizmore
 * @version 6.05
 */
final class Attachment extends Method
{
	public function gdoParameters()
	{
		return array(
			GDT_String::make('id')->notNull(),
		);
	}
	public function execute()
	{
		return GetFile::make()->execute();
	}
}
