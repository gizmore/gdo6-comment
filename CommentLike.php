<?php
namespace GDO\Comment;

use GDO\Vote\LikeTable;
/**
 * It is possible to like comments.
 * @author gizmore
 * @since 5.0
 * @see Module_Votes
 * @see VoteTable
 */
final class CommentLike extends LikeTable
{
    public function gdoLikeObjectTable() { return Comment::table(); }
}
