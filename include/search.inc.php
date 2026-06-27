<?php declare(strict_types=1);

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * Module: Cocktails
 *
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       2000-2026 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * XOOPS core search callback for cocktails recipes.
 *
 * @param array    $queryarray
 * @param string   $andor
 * @param int      $limit
 * @param int      $offset
 * @param int      $userid
 * @return array
 */
function cocktails_search($queryarray, $andor, $limit, $offset, $userid)
{
    $db    = $GLOBALS['xoopsDB'];
    $andor = \strtoupper((string)$andor);
    $andor = \in_array($andor, ['AND', 'OR'], true) ? $andor : 'OR';

    $sql = 'SELECT id, title, slug, summary, uid, created FROM ' . $db->prefix('cocktails_recipe') . ' WHERE online = 1';

    if ((int)$userid > 0) {
        $sql .= ' AND uid = ' . (int)$userid;
    }

    if (\is_array($queryarray) && $count = \count($queryarray)) {
        $term = $db->quote('%' . (string)$queryarray[0] . '%');
        $sql  .= ' AND ((title LIKE ' . $term . ' OR summary LIKE ' . $term . ' OR method LIKE ' . $term . ')';
        for ($i = 1; $i < $count; ++$i) {
            $term = $db->quote('%' . (string)$queryarray[$i] . '%');
            $sql  .= " $andor (title LIKE " . $term . ' OR summary LIKE ' . $term . ' OR method LIKE ' . $term . ')';
        }
        $sql .= ')';
    }

    $sql    .= ' ORDER BY created DESC';
    $result = $db->query($sql, (int)$limit, (int)$offset);
    $ret    = [];
    if (!$db->isResultSet($result) || !($result instanceof \mysqli_result)) {
        return $ret;
    }

    while (false !== ($row = $db->fetchArray($result))) {
        $ret[] = [
            'image' => 'assets/images/icons/32/logo.png',
            'link'  => 'recipe.php?op=view&id=' . (int)$row['id'],
            'title' => $row['title'],
            'time'  => (int)$row['created'],
            'uid'   => (int)$row['uid'],
        ];
    }

    return $ret;
}
