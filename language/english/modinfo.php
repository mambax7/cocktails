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
 * @category        Module
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       2000-2026 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

// Module info
\define('_MI_COCKTAILS_NAME', 'Cocktails');
\define('_MI_COCKTAILS_DESC', 'Submit, rate and discover cocktail recipes — a modern, domain-driven recipe manager for XOOPS.');

// Front sub-menu
\define('_MI_COCKTAILS_SMNAME_BROWSE', 'Browse Recipes');
\define('_MI_COCKTAILS_SMNAME_ADD_RECIPE', 'Submit a Recipe');
\define('_MI_COCKTAILS_SMNAME_INGREDIENTS', 'Ingredients');
\define('_MI_COCKTAILS_SMNAME_FAVORITES', 'My Favorites');

// Blocks
\define('_MI_COCKTAILS_TOPRATED_BLOCK', 'Cocktails: Top Rated');
\define('_MI_COCKTAILS_NEWEST_BLOCK', 'Cocktails: Newest');
\define('_MI_COCKTAILS_RANDOM_BLOCK', 'Cocktails: Random Pick');
\define('_MI_COCKTAILS_BLOCK_COUNT', 'Number of cocktails to display');

// Help
\define('_MI_COCKTAILS_OVERVIEW', 'Overview');
\define('_MI_COCKTAILS_DISCLAIMER', 'Disclaimer');
\define('_MI_COCKTAILS_LICENSE', 'License');
\define('_MI_COCKTAILS_SUPPORT', 'Support');

// Editors
\define('_MI_COCKTAILS_EDITOR_ADMIN', 'Admin editor');
\define('_MI_COCKTAILS_EDITOR_DESC_ADMIN', 'Select the editor used by admins for the recipe method.');
\define('_MI_COCKTAILS_EDITOR_USER', 'User editor');
\define('_MI_COCKTAILS_EDITOR_DESC_USER', 'Select the editor offered to registered users.');

// Groups
\define('_MI_COCKTAILS_GROUPS', 'General access groups');
\define('_MI_COCKTAILS_GROUPS_DESC', 'Groups that may view the cocktails module.');
\define('_MI_COCKTAILS_ADMINGROUPS', 'Admin groups');
\define('_MI_COCKTAILS_ADMINGROUPS_DESC', 'Groups granted module administration rights.');

// Keywords
\define('_MI_COCKTAILS_KEYWORDS', 'Meta keywords');
\define('_MI_COCKTAILS_KEYWORDS_DESC', 'Comma-separated keywords for SEO meta tags.');

// Measurement system
\define('_MI_COCKTAILS_MEASURESYS', 'Default measurement system');
\define('_MI_COCKTAILS_MEASURESYS_DESC', 'Preferred unit system for displaying quantities.');
\define('_MI_COCKTAILS_METRIC', 'Metric (ml / cl)');
\define('_MI_COCKTAILS_IMPERIAL', 'Imperial (fl oz)');

// Moderation / rating
\define('_MI_COCKTAILS_MODERATE', 'Moderate user submissions');
\define('_MI_COCKTAILS_MODERATE_DESC', 'If enabled, recipes submitted by users stay offline until an admin approves them.');
\define('_MI_COCKTAILS_GUESTRATE', 'Allow guests to rate');
\define('_MI_COCKTAILS_GUESTRATE_DESC', 'Allow anonymous visitors to leave star ratings (tracked by session/IP).');

// Uploads
\define('_MI_COCKTAILS_MAXSIZE', 'Max image size (bytes)');
\define('_MI_COCKTAILS_MAXSIZE_DESC', 'Maximum allowed file size for uploaded images.');
\define('_MI_COCKTAILS_MIMETYPES', 'Allowed image types');
\define('_MI_COCKTAILS_MIMETYPES_DESC', 'MIME types accepted for image uploads.');
\define('_MI_COCKTAILS_THUMBW', 'Thumbnail width (px)');
\define('_MI_COCKTAILS_THUMBW_DESC', 'Target width for generated recipe thumbnails.');
\define('_MI_COCKTAILS_THUMBH', 'Thumbnail height (px)');
\define('_MI_COCKTAILS_THUMBH_DESC', 'Target height for generated recipe thumbnails.');

// Pagers
\define('_MI_COCKTAILS_ADMINPAGER', 'Admin items per page');
\define('_MI_COCKTAILS_ADMINPAGER_DESC', 'Number of rows per page in the admin lists.');
\define('_MI_COCKTAILS_USERPAGER', 'User items per page');
\define('_MI_COCKTAILS_USERPAGER_DESC', 'Number of cards per page in the public lists.');

// Bookmarks
\define('_MI_COCKTAILS_BOOKMARKS', 'Show social bookmarks');
\define('_MI_COCKTAILS_BOOKMARKS_DESC', 'Show share/bookmark links on the recipe page.');

// Shared config-title constants (also referenced by xoops_version.php)
\define('_CO_COCKTAILS_TRUNCATE_LENGTH', 'Summary truncate length');
\define('_CO_COCKTAILS_TRUNCATE_LENGTH_DESC', 'Maximum characters shown for a recipe summary in lists.');
\define('_CO_COCKTAILS_SHOW_SAMPLE_BUTTON', 'Show "Add sample data" button?');
\define('_CO_COCKTAILS_SHOW_SAMPLE_BUTTON_DESC', 'Display the sample/test-data buttons on the admin index.');
\define('_CO_COCKTAILS_SHOW_DEV_TOOLS', 'Show developer tools?');
\define('_CO_COCKTAILS_SHOW_DEV_TOOLS_DESC', 'Display developer/migration tools in the admin menu.');
