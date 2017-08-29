<?php
namespace GDO\Comment;

use GDO\Vote\GDO_LikeTable;
/**
 * It is possible to like comments.
 * @author gizmore
 * @since 5.0
 * @see Module_Votes
 * @see GDO_LikeTable
 */
final class GDO_CommentLike extends GDO_LikeTable
{
    public function gdoLikeObjectTable() { return GDO_Comment::table(); }
}
