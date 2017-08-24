<?php
namespace GDO\Comment\Method;
use GDO\Core\Method;
use GDO\GWF\Module_GWF;

final class Attachment extends Method
{
	public function execute()
	{
		return Module_GWF::instance()->getMethod('GetFile')->execute();
	}
}
