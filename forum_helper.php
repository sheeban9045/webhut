<?php
// Shared helpers for the Forum pages (forum*.php) and the Forum AJAX endpoint (forum_action.php).
// The Forum data is managed by the store-admin backend, this frontend only reads/writes the crm_forum_* tables.

//Config.php outputs trailing whitespace, discard it so sessions, redirects and JSON responses stay clean
ob_start();
require_once __DIR__ . '/Config.php';
ob_end_clean();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const FORUM_PER_PAGE = 15;
const FORUM_LOGIN_URL = '/store-admin/index.php/signin';
const FORUM_REGISTER_URL = '/store-admin/index.php/signup';
const FORUM_REPLY_MAX_LENGTH = 10000;
const FORUM_TOPIC_MAX_LENGTH = 100000;
const FORUM_ASSET_VERSION = '1.0';

/* ---------------------------------------------------------------------------
 * General
 * ------------------------------------------------------------------------- */

function forum_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function forum_utc_now() {
    return gmdate('Y-m-d H:i:s');
}

function forum_get_int($key) {
    return isset($_GET[$key]) && is_scalar($_GET[$key]) ? max(0, (int) $_GET[$key]) : 0;
}

function forum_get_string($key) {
    return isset($_GET[$key]) && is_string($_GET[$key]) ? trim($_GET[$key]) : '';
}

function forum_post_string($key) {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function forum_current_page() {
    return max(1, forum_get_int('page'));
}

/* ---------------------------------------------------------------------------
 * Database (prepared statements on the shared $conn from Config.php)
 * ------------------------------------------------------------------------- */

function forum_db_query($sql, $types = '', array $params = array()) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result === false ? $stmt : $result;
}

function forum_db_all($sql, $types = '', array $params = array()) {
    return forum_db_query($sql, $types, $params)->fetch_all(MYSQLI_ASSOC);
}

function forum_db_one($sql, $types = '', array $params = array()) {
    $row = forum_db_query($sql, $types, $params)->fetch_assoc();
    return $row ?: null;
}

/* ---------------------------------------------------------------------------
 * Logged in user (the session is shared with store-admin, which sets $_SESSION['uid'])
 * ------------------------------------------------------------------------- */

function forum_current_user() {
    static $user = false;
    if ($user !== false) {
        return $user;
    }

    $user = null;
    $uid = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
    if ($uid > 0) {
        $user = forum_db_one(
            "SELECT id, first_name, last_name, is_admin, TRIM(CONCAT(IFNULL(first_name, ''), ' ', IFNULL(last_name, ''))) AS full_name
             FROM crm_users
             WHERE id = ? AND deleted = 0 AND status = 'active' AND disable_login = 0",
            'i',
            array($uid)
        );
    }
    return $user;
}

function forum_user_id() {
    $user = forum_current_user();
    return $user ? (int) $user['id'] : 0;
}

//must be called before any output
function forum_require_login() {
    if (!forum_current_user()) {
        header('Location: ' . FORUM_LOGIN_URL);
        exit;
    }
}

/* ---------------------------------------------------------------------------
 * CSRF + flash messages
 * ------------------------------------------------------------------------- */

function forum_csrf_token() {
    if (empty($_SESSION['forum_csrf'])) {
        $_SESSION['forum_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['forum_csrf'];
}

function forum_check_csrf($token) {
    return !empty($_SESSION['forum_csrf']) && is_string($token) && hash_equals($_SESSION['forum_csrf'], $token);
}

function forum_set_flash($type, $message) {
    $_SESSION['forum_flash'] = array('type' => $type, 'message' => $message);
}

function forum_render_flash() {
    if (empty($_SESSION['forum_flash'])) {
        return '';
    }
    $flash = $_SESSION['forum_flash'];
    unset($_SESSION['forum_flash']);
    $type = $flash['type'] === 'success' ? 'success' : 'danger';
    $icon = $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    return '<div class="alert alert-' . $type . ' forum-alert alert-dismissible fade show" role="alert">'
        . '<i class="fa ' . $icon . '"></i> ' . forum_e($flash['message'])
        . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

/* ---------------------------------------------------------------------------
 * Date / time (stored in UTC by the backend, displayed in the app timezone from crm_settings)
 * ------------------------------------------------------------------------- */

function forum_timezone() {
    static $timezone = null;
    if ($timezone === null) {
        $timezone = new DateTimeZone('UTC');
        $row = forum_db_one("SELECT setting_value FROM crm_settings WHERE setting_name = 'timezone' LIMIT 1");
        if ($row && $row['setting_value']) {
            try {
                $timezone = new DateTimeZone($row['setting_value']);
            } catch (Exception $e) {
                //keep UTC
            }
        }
    }
    return $timezone;
}

function forum_format_date($utc, $format = 'M j, Y \a\t g:i A') {
    if (!$utc) {
        return '-';
    }
    try {
        $date = new DateTime($utc, new DateTimeZone('UTC'));
    } catch (Exception $e) {
        return '-';
    }
    $date->setTimezone(forum_timezone());
    return $date->format($format);
}

function forum_time_ago($utc) {
    $timestamp = $utc ? strtotime($utc . ' UTC') : false;
    if (!$timestamp) {
        return '-';
    }

    $diff = time() - $timestamp;
    if ($diff < 60) {
        return 'just now';
    }
    if ($diff >= 2592000) {
        return forum_format_date($utc, 'M j, Y');
    }

    $units = array(604800 => 'week', 86400 => 'day', 3600 => 'hour', 60 => 'minute');
    foreach ($units as $seconds => $name) {
        if ($diff >= $seconds) {
            $count = (int) floor($diff / $seconds);
            return $count . ' ' . $name . ($count > 1 ? 's' : '') . ' ago';
        }
    }
    return 'just now';
}

function forum_time_html($utc) {
    if (!$utc) {
        return '<span>-</span>';
    }
    return '<time datetime="' . forum_e(forum_format_date($utc, 'c')) . '" title="' . forum_e(forum_format_date($utc)) . '">' . forum_e(forum_time_ago($utc)) . '</time>';
}

/* ---------------------------------------------------------------------------
 * Topic HTML (WYSIWYG) cleaning - allowlist based, same tags as the backend editor
 * ------------------------------------------------------------------------- */

function forum_has_editor_markup($text) {
    return (bool) preg_match('/<\/?(p|br|div|span|strong|b|em|i|u|s|strike|sub|sup|font|ul|ol|li|h[1-6]|blockquote|pre|code|table|thead|tbody|tr|th|td|hr|a|img)\b[^>]*>/i', (string) $text);
}

function forum_clean_url($url, $is_image = false) {
    $url = trim($url);
    $check = preg_replace('/[\x00-\x20]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($check === '') {
        return '';
    }
    if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $check, $matches)) {
        $allowed_schemes = $is_image ? array('http', 'https') : array('http', 'https', 'mailto');
        if (!in_array(strtolower($matches[1]), $allowed_schemes, true)) {
            return '';
        }
    }
    return $url;
}

function forum_clean_style($style) {
    $allowed_properties = array('color', 'background-color', 'text-align', 'font-weight', 'font-style', 'text-decoration', 'font-size', 'font-family', 'line-height', 'margin-left', 'padding-left', 'text-indent', 'vertical-align', 'list-style-type');
    $clean = array();
    foreach (explode(';', $style) as $declaration) {
        if (strpos($declaration, ':') === false) {
            continue;
        }
        list($property, $value) = array_map('trim', explode(':', $declaration, 2));
        $property = strtolower($property);
        if ($value === '' || !in_array($property, $allowed_properties, true) || preg_match('/url\s*\(|expression|javascript|behavior|@import|[<>\\\\{}]/i', $value)) {
            continue;
        }
        $clean[] = $property . ': ' . $value;
    }
    return implode('; ', $clean);
}

function forum_clean_node(DOMNode $node) {
    static $allowed = array(
        'p' => array(), 'br' => array(), 'div' => array(), 'span' => array(), 'strong' => array(), 'b' => array(),
        'em' => array(), 'i' => array(), 'u' => array(), 's' => array(), 'strike' => array(), 'sub' => array(), 'sup' => array(),
        'font' => array('color', 'face', 'size'), 'ul' => array(), 'ol' => array(), 'li' => array(),
        'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
        'blockquote' => array(), 'pre' => array(), 'code' => array(), 'hr' => array(),
        'table' => array(), 'thead' => array(), 'tbody' => array(), 'tr' => array(),
        'th' => array('colspan', 'rowspan'), 'td' => array('colspan', 'rowspan'),
        'a' => array('href', 'title', 'target'), 'img' => array('src', 'alt', 'title', 'width', 'height'),
    );
    static $removed_with_content = array('script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'svg', 'math', 'form', 'input', 'button', 'textarea', 'select', 'option', 'link', 'meta', 'base', 'noscript', 'template', 'audio', 'video', 'source', 'title', 'head');

    foreach (iterator_to_array($node->childNodes) as $child) {
        if ($child instanceof DOMText) {
            continue;
        }
        if (!($child instanceof DOMElement)) {
            $node->removeChild($child); //comments, processing instructions etc.
            continue;
        }

        $tag = strtolower($child->nodeName);
        if (in_array($tag, $removed_with_content, true)) {
            $node->removeChild($child);
            continue;
        }

        forum_clean_node($child);

        if (!isset($allowed[$tag])) {
            //unknown tag: keep its (already cleaned) content only
            while ($child->firstChild) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }

        foreach (iterator_to_array($child->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = $attribute->value;
            $keep = false;

            if ($name === 'style') {
                $value = forum_clean_style($value);
                $keep = $value !== '';
            } else if (in_array($name, $allowed[$tag], true)) {
                if ($name === 'href' || $name === 'src') {
                    $value = forum_clean_url($value, $name === 'src');
                    $keep = $value !== '';
                } else if ($name === 'target') {
                    $keep = $value === '_blank';
                } else if (in_array($name, array('width', 'height', 'colspan', 'rowspan', 'size'), true)) {
                    $value = preg_replace('/[^0-9%]/', '', $value);
                    $keep = $value !== '';
                } else if ($name === 'color') {
                    $keep = (bool) preg_match('/^#?[0-9a-z]{1,20}$/i', $value);
                } else if ($name === 'face') {
                    $keep = (bool) preg_match('/^[\w\s,\-\'"]{1,100}$/u', $value);
                } else {
                    $keep = true; //title, alt
                }
            }

            $child->removeAttributeNode($attribute);
            if ($keep) {
                $child->setAttribute($name, $value);
            }
        }

        if ($tag === 'img' && !$child->hasAttribute('src')) {
            $node->removeChild($child);
            continue;
        }
        if ($tag === 'a' && $child->hasAttribute('href')) {
            $child->setAttribute('rel', 'nofollow noopener noreferrer');
        }
        if ($tag === 'table') {
            $child->setAttribute('class', 'table table-bordered');
        }
    }
}

function forum_clean_html($html) {
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    $previous = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $encoded = mb_encode_numericentity($html, array(0x80, 0x10FFFF, 0, 0x1FFFFF), 'UTF-8');
    $document->loadHTML('<div>' . $encoded . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $wrapper = $document->documentElement;
    forum_clean_node($document);

    //output the content of the wrapper div (and anything after it, when the content closed the wrapper early)
    $output = '';
    foreach ($document->childNodes as $child) {
        if ($wrapper && $child->isSameNode($wrapper)) {
            foreach ($wrapper->childNodes as $inner) {
                $output .= $document->saveHTML($inner);
            }
        } else {
            $output .= $document->saveHTML($child);
        }
    }

    //restore the multibyte characters only (entities below 0x80 like &lt; stay encoded)
    return trim(mb_decode_numericentity($output, array(0x80, 0x10FFFF, 0, 0x1FFFFF), 'UTF-8'));
}

function forum_html_is_empty($html) {
    $text = html_entity_decode(strip_tags((string) $html, '<img>'), ENT_QUOTES, 'UTF-8');
    return preg_replace('/[\s\x{00A0}]+/u', '', $text) === '';
}

//topic descriptions are html from the editor, or plain text when the backend editor is disabled
function forum_render_description($description) {
    if (!forum_has_editor_markup($description)) {
        return nl2br(forum_e($description));
    }
    return forum_clean_html($description);
}

function forum_render_reply_text($text) {
    return nl2br(forum_e($text));
}

function forum_excerpt($html, $length = 180) {
    $text = html_entity_decode(strip_tags(str_replace('<', ' <', (string) $html)), ENT_QUOTES, 'UTF-8');
    $text = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
    if (mb_strlen($text) > $length) {
        $text = rtrim(mb_substr($text, 0, $length)) . '...';
    }
    return forum_e($text);
}

//excerpt of plain text content (replies)
function forum_text_excerpt($text, $length = 220) {
    $text = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', (string) $text));
    if (mb_strlen($text) > $length) {
        $text = rtrim(mb_substr($text, 0, $length)) . '...';
    }
    return forum_e($text);
}

/* ---------------------------------------------------------------------------
 * Queries
 * ------------------------------------------------------------------------- */

function forum_author_sql($alias) {
    return "TRIM(CONCAT(IFNULL($alias.first_name, ''), ' ', IFNULL($alias.last_name, '')))";
}

function forum_get_categories($category_id = 0) {
    $sql = "SELECT c.id, c.title, c.description, c.sort,
                (SELECT COUNT(t.id) FROM crm_forum_topics t WHERE t.category_id = c.id AND t.deleted = 0) AS total_topics,
                (SELECT COUNT(r.id) FROM crm_forum_replies r INNER JOIN crm_forum_topics rt ON rt.id = r.topic_id AND rt.deleted = 0 WHERE rt.category_id = c.id AND r.deleted = 0) AS total_replies,
                (SELECT MAX(COALESCE(t.last_activity_at, t.created_at)) FROM crm_forum_topics t WHERE t.category_id = c.id AND t.deleted = 0) AS last_activity_at
            FROM crm_forum_categories c
            WHERE c.deleted = 0";
    if ($category_id) {
        return forum_db_one($sql . " AND c.id = ?", 'i', array((int) $category_id));
    }
    return forum_db_all($sql . " ORDER BY c.sort ASC, c.title ASC");
}

function forum_get_stats() {
    return forum_db_one(
        "SELECT
            (SELECT COUNT(c.id) FROM crm_forum_categories c WHERE c.deleted = 0) AS total_categories,
            (SELECT COUNT(t.id) FROM crm_forum_topics t INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0 WHERE t.deleted = 0) AS total_topics,
            (SELECT COUNT(r.id) FROM crm_forum_replies r
                INNER JOIN crm_forum_topics t ON t.id = r.topic_id AND t.deleted = 0
                INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0
             WHERE r.deleted = 0) AS total_replies"
    );
}

//$filter_sql is appended to the WHERE clause and may use the aliases t (topic) and c (category)
function forum_get_topics($filter_sql, $types, array $params, $limit, $offset = 0) {
    $sql = "SELECT t.id, t.category_id, t.title, t.created_by, t.created_at, COALESCE(t.last_activity_at, t.created_at) AS last_activity_at,
                LEFT(t.description, 1500) AS description_snippet,
                c.title AS category_title, " . forum_author_sql('u') . " AS author_name,
                (SELECT COUNT(r.id) FROM crm_forum_replies r WHERE r.topic_id = t.id AND r.deleted = 0) AS total_replies,
                (SELECT COUNT(l.id) FROM crm_forum_topic_likes l WHERE l.topic_id = t.id AND l.deleted = 0) AS total_likes
            FROM crm_forum_topics t
            INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0
            LEFT JOIN crm_users u ON u.id = t.created_by
            WHERE t.deleted = 0 $filter_sql
            ORDER BY COALESCE(t.last_activity_at, t.created_at) DESC, t.id DESC
            LIMIT ? OFFSET ?";
    return forum_db_all($sql, $types . 'ii', array_merge($params, array((int) $limit, (int) $offset)));
}

function forum_count_topics($filter_sql, $types, array $params) {
    $row = forum_db_one(
        "SELECT COUNT(t.id) AS total FROM crm_forum_topics t
         INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0
         WHERE t.deleted = 0 $filter_sql",
        $types,
        $params
    );
    return (int) $row['total'];
}

function forum_get_topic($topic_id) {
    return forum_db_one(
        "SELECT t.id, t.category_id, t.title, t.description, t.created_by, t.created_at, COALESCE(t.last_activity_at, t.created_at) AS last_activity_at,
            c.title AS category_title, " . forum_author_sql('u') . " AS author_name
         FROM crm_forum_topics t
         INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0
         LEFT JOIN crm_users u ON u.id = t.created_by
         WHERE t.deleted = 0 AND t.id = ?",
        'i',
        array((int) $topic_id)
    );
}

function forum_get_topic_replies($topic_id) {
    return forum_db_all(
        "SELECT r.id, r.topic_id, r.description, r.created_by, r.created_at, " . forum_author_sql('u') . " AS author_name
         FROM crm_forum_replies r
         LEFT JOIN crm_users u ON u.id = r.created_by
         WHERE r.deleted = 0 AND r.topic_id = ?
         ORDER BY r.created_at ASC, r.id ASC",
        'i',
        array((int) $topic_id)
    );
}

function forum_count_topic_replies($topic_id) {
    $row = forum_db_one("SELECT COUNT(id) AS total FROM crm_forum_replies WHERE deleted = 0 AND topic_id = ?", 'i', array((int) $topic_id));
    return (int) $row['total'];
}

//a reply is visible only when its topic and category are visible too
function forum_replies_base_sql() {
    return "FROM crm_forum_replies r
            INNER JOIN crm_forum_topics t ON t.id = r.topic_id AND t.deleted = 0
            INNER JOIN crm_forum_categories c ON c.id = t.category_id AND c.deleted = 0
            LEFT JOIN crm_users u ON u.id = r.created_by
            WHERE r.deleted = 0";
}

function forum_get_reply($reply_id) {
    return forum_db_one(
        "SELECT r.id, r.topic_id, r.description, r.created_by, r.created_at, " . forum_author_sql('u') . " AS author_name,
            t.title AS topic_title, t.created_by AS topic_created_by " . forum_replies_base_sql() . " AND r.id = ?",
        'i',
        array((int) $reply_id)
    );
}

function forum_get_user_replies($user_id, $limit, $offset) {
    return forum_db_all(
        "SELECT r.id, r.topic_id, r.description, r.created_at, t.title AS topic_title, t.category_id, c.title AS category_title
         " . forum_replies_base_sql() . " AND r.created_by = ?
         ORDER BY r.created_at DESC, r.id DESC
         LIMIT ? OFFSET ?",
        'iii',
        array((int) $user_id, (int) $limit, (int) $offset)
    );
}

function forum_count_user_replies($user_id) {
    $row = forum_db_one("SELECT COUNT(r.id) AS total " . forum_replies_base_sql() . " AND r.created_by = ?", 'i', array((int) $user_id));
    return (int) $row['total'];
}

function forum_get_like_info($topic_id, $user_id) {
    $row = forum_db_one(
        "SELECT COUNT(id) AS total_likes, COALESCE(SUM(created_by = ?), 0) AS liked
         FROM crm_forum_topic_likes WHERE deleted = 0 AND topic_id = ?",
        'ii',
        array((int) $user_id, (int) $topic_id)
    );
    return array('total_likes' => (int) $row['total_likes'], 'liked' => $user_id && $row['liked'] > 0);
}

//escape the LIKE wildcards of a search term
function forum_like_term($search) {
    return '%' . addcslashes($search, '\\%_') . '%';
}

/* ---------------------------------------------------------------------------
 * UI helpers
 * ------------------------------------------------------------------------- */

function forum_avatar($name, $size = '') {
    $name = trim((string) $name);
    $initials = '?';
    if ($name !== '') {
        $parts = preg_split('/\s+/u', $name);
        $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));
    }
    $palette = array('#2b84d1', '#12407a', '#1f9d8b', '#e0892b', '#7b61ff', '#d9546a', '#3a9d4f', '#5a6b7b');
    $color = $palette[abs(crc32($name)) % count($palette)];
    return '<span class="forum-avatar ' . ($size ? 'forum-avatar-' . $size : '') . '" style="background-color:' . $color . '" aria-hidden="true">' . forum_e($initials) . '</span>';
}

function forum_author_name($name) {
    $name = trim((string) $name);
    return $name !== '' ? $name : 'Unknown user';
}

function forum_category_icon($title) {
    $title = strtolower((string) $title);
    $map = array(
        'announce' => 'fa-bullhorn', 'news' => 'fa-newspaper-o', 'help' => 'fa-life-ring', 'support' => 'fa-life-ring',
        'tip' => 'fa-lightbulb-o', 'tutorial' => 'fa-graduation-cap', 'guide' => 'fa-book', 'feedback' => 'fa-commenting-o',
        'suggest' => 'fa-commenting-o', 'idea' => 'fa-lightbulb-o', 'plugin' => 'fa-puzzle-piece', 'bug' => 'fa-bug',
        'off' => 'fa-coffee', 'general' => 'fa-comments-o', 'show' => 'fa-star-o', 'introduc' => 'fa-hand-paper-o',
    );
    foreach ($map as $keyword => $icon) {
        if (strpos($title, $keyword) !== false) {
            return $icon;
        }
    }
    return 'fa-folder-open-o';
}

function forum_category_color($category_id) {
    $palette = array('#2b84d1', '#1f9d8b', '#e0892b', '#7b61ff', '#d9546a', '#12407a', '#3a9d4f', '#5a6b7b');
    return $palette[((int) $category_id) % count($palette)];
}

function forum_topic_url($topic_id, $reply_id = 0) {
    return 'forum-topic.php?id=' . (int) $topic_id . ($reply_id ? '#reply-' . (int) $reply_id : '');
}

function forum_category_url($category_id) {
    return 'forum-category.php?id=' . (int) $category_id;
}

function forum_new_topic_url($category_id = 0) {
    return 'forum-new-topic.php' . ($category_id ? '?category_id=' . (int) $category_id : '');
}

function forum_pagination($total, $page, $per_page, array $params = array()) {
    $pages = (int) ceil($total / $per_page);
    if ($pages <= 1) {
        return '';
    }

    $link = function ($target, $label, $disabled = false, $active = false, $aria = '') use ($params) {
        $params['page'] = $target;
        $class = 'page-item' . ($disabled ? ' disabled' : '') . ($active ? ' active' : '');
        $aria = $aria ? ' aria-label="' . $aria . '"' : '';
        if ($disabled || $active) {
            return '<li class="' . $class . '"><span class="page-link"' . $aria . '>' . $label . '</span></li>';
        }
        return '<li class="' . $class . '"><a class="page-link" href="?' . forum_e(http_build_query($params)) . '"' . $aria . '>' . $label . '</a></li>';
    };

    $html = '<nav class="forum-pagination" aria-label="Pagination"><ul class="pagination justify-content-center flex-wrap">';
    $html .= $link(max(1, $page - 1), '<i class="fa fa-angle-left"></i>', $page <= 1, false, 'Previous');

    $start = max(1, $page - 2);
    $end = min($pages, $page + 2);
    if ($start > 1) {
        $html .= $link(1, '1');
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        $html .= $link($i, (string) $i, false, $i === $page);
    }
    if ($end < $pages) {
        if ($end < $pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        $html .= $link($pages, (string) $pages);
    }

    $html .= $link(min($pages, $page + 1), '<i class="fa fa-angle-right"></i>', $page >= $pages, false, 'Next');
    return $html . '</ul></nav>';
}

//returns array(current page, offset, total pages) with the page clamped to the available pages
function forum_paginate($total, $per_page = FORUM_PER_PAGE) {
    $pages = max(1, (int) ceil($total / $per_page));
    $page = min(forum_current_page(), $pages);
    return array($page, ($page - 1) * $per_page, $pages);
}

function forum_render_hero($title, $subtitle = '', array $breadcrumbs = array(), $extra_html = '', $modifier = '') {
    $html = '<section class="forum-hero ' . $modifier . '"><div class="container">';
    if ($breadcrumbs) {
        $html .= '<nav aria-label="breadcrumb"><ol class="forum-breadcrumb">';
        foreach ($breadcrumbs as $crumb) {
            $html .= $crumb[1]
                ? '<li><a href="' . forum_e($crumb[1]) . '">' . forum_e($crumb[0]) . '</a></li>'
                : '<li aria-current="page">' . forum_e($crumb[0]) . '</li>';
        }
        $html .= '</ol></nav>';
    }
    $html .= '<h1 class="forum-hero-title">' . forum_e($title) . '</h1>';
    if ($subtitle !== '') {
        $html .= '<p class="forum-hero-subtitle">' . forum_e($subtitle) . '</p>';
    }
    return $html . $extra_html . '</div></section>';
}

function forum_render_search_box($search = '', $modifier = '') {
    return '<form class="forum-search ' . $modifier . '" action="forum-search.php" method="get" role="search" autocomplete="off">'
        . '<i class="fa fa-search forum-search-icon" aria-hidden="true"></i>'
        . '<input type="search" name="q" class="forum-search-input js-forum-search-input" value="' . forum_e($search) . '" placeholder="Search discussions..." aria-label="Search forum topics" maxlength="100">'
        . '<button type="submit" class="btn btn-primary forum-search-btn">Search</button>'
        . '<div class="forum-search-suggestions js-forum-suggestions" role="listbox"></div>'
        . '</form>';
}

//secondary forum navigation shown under the hero
function forum_render_subnav($active, $action_html = '') {
    $items = array('home' => array('Forum Home', 'forum.php', 'fa-home'));
    if (forum_current_user()) {
        $items['my-topics'] = array('My Topics', 'forum-my-topics.php', 'fa-file-text-o');
        $items['my-replies'] = array('My Replies', 'forum-my-replies.php', 'fa-comments-o');
    }
    $html = '<div class="forum-subnav"><div class="container"><div class="forum-subnav-inner"><ul class="forum-subnav-links">';
    foreach ($items as $key => $item) {
        $html .= '<li><a href="' . $item[1] . '" class="' . ($key === $active ? 'active' : '') . '"><i class="fa ' . $item[2] . '"></i> ' . $item[0] . '</a></li>';
    }
    $html .= '</ul>';
    if ($action_html) {
        $html .= '<div class="forum-subnav-actions">' . $action_html . '</div>';
    }
    return $html . '</div></div></div>';
}

function forum_new_topic_button($category_id = 0) {
    if (forum_current_user()) {
        return '<a href="' . forum_new_topic_url($category_id) . '" class="btn btn-primary forum-btn"><i class="fa fa-plus"></i> New Topic</a>';
    }
    return '<a href="' . FORUM_LOGIN_URL . '" class="btn btn-outline-primary forum-btn"><i class="fa fa-sign-in"></i> Log in to post</a>';
}

//sidebar card: welcome for members, join prompt for visitors
function forum_render_member_card() {
    $user = forum_current_user();
    $html = '<div class="forum-card forum-cta"><div class="forum-card-body">';
    if ($user) {
        $html .= '<h3 class="forum-card-title">Welcome back, ' . forum_e($user['first_name'] ?: $user['full_name']) . '</h3>'
            . '<p>Keep track of the discussions you started and the replies you shared.</p>'
            . '<a href="forum-my-topics.php" class="btn btn-light forum-btn"><i class="fa fa-file-text-o"></i> My Topics</a>'
            . '<a href="forum-my-replies.php" class="btn btn-outline-light forum-btn"><i class="fa fa-comments-o"></i> My Replies</a>';
    } else {
        $html .= '<h3 class="forum-card-title">Join the conversation</h3>'
            . '<p>Log in to start topics, reply to discussions and like helpful posts.</p>'
            . '<a href="' . FORUM_LOGIN_URL . '" class="btn btn-light forum-btn"><i class="fa fa-sign-in"></i> Log in</a>'
            . '<a href="' . FORUM_REGISTER_URL . '" class="btn btn-outline-light forum-btn">Register</a>';
    }
    return $html . '</div></div>';
}

function forum_render_empty($icon, $title, $text = '', $action_html = '') {
    return '<div class="forum-empty"><div class="forum-empty-icon"><i class="fa ' . $icon . '"></i></div>'
        . '<h3>' . forum_e($title) . '</h3>' . ($text !== '' ? '<p>' . forum_e($text) . '</p>' : '') . $action_html . '</div>';
}

function forum_render_topic_item(array $topic, array $options = array()) {
    $show_category = !isset($options['show_category']) || $options['show_category'];
    $show_author = !isset($options['show_author']) || $options['show_author'];
    $show_excerpt = !empty($options['show_excerpt']);
    $author = forum_author_name($topic['author_name']);

    $html = '<div class="forum-topic-item">';
    if ($show_author) {
        $html .= '<div class="forum-topic-avatar">' . forum_avatar($author) . '</div>';
    }
    $html .= '<div class="forum-topic-main">'
        . '<a class="forum-topic-title" href="' . forum_topic_url($topic['id']) . '">' . forum_e($topic['title']) . '</a>';
    if ($show_excerpt) {
        $excerpt = forum_excerpt($topic['description_snippet'], 160);
        if ($excerpt !== '') {
            $html .= '<p class="forum-topic-excerpt">' . $excerpt . '</p>';
        }
    }
    $html .= '<div class="forum-topic-meta">';
    if ($show_category) {
        $html .= '<a class="forum-badge" href="' . forum_category_url($topic['category_id']) . '" style="--forum-badge-color:' . forum_category_color($topic['category_id']) . '">' . forum_e($topic['category_title']) . '</a>';
    }
    if ($show_author) {
        $html .= '<span><i class="fa fa-user-o"></i> ' . forum_e($author) . '</span>';
    }
    $html .= '<span><i class="fa fa-calendar-o"></i> ' . forum_e(forum_format_date($topic['created_at'], 'M j, Y')) . '</span>'
        . '<span class="forum-topic-meta-mobile"><i class="fa fa-comment-o"></i> ' . (int) $topic['total_replies'] . '</span>'
        . '<span class="forum-topic-meta-mobile"><i class="fa fa-heart-o"></i> ' . (int) $topic['total_likes'] . '</span>'
        . '</div></div>'
        . '<div class="forum-topic-stats">'
        . '<div class="forum-stat"><strong>' . (int) $topic['total_replies'] . '</strong><span>' . ((int) $topic['total_replies'] === 1 ? 'Reply' : 'Replies') . '</span></div>'
        . '<div class="forum-stat"><strong>' . (int) $topic['total_likes'] . '</strong><span>' . ((int) $topic['total_likes'] === 1 ? 'Like' : 'Likes') . '</span></div>'
        . '</div>'
        . '<div class="forum-topic-activity"><span>Last activity</span>' . forum_time_html($topic['last_activity_at']) . '</div>'
        . '</div>';
    return $html;
}

function forum_render_reply(array $reply, $current_user_id, $topic_author_id = 0) {
    $author = forum_author_name($reply['author_name']);
    $is_owner = $current_user_id && (int) $reply['created_by'] === (int) $current_user_id;

    $html = '<article class="forum-reply" id="reply-' . (int) $reply['id'] . '" data-reply-id="' . (int) $reply['id'] . '">'
        . '<div class="forum-reply-avatar">' . forum_avatar($author) . '</div>'
        . '<div class="forum-reply-main">'
        . '<div class="forum-reply-head"><div class="forum-reply-who">'
        . '<span class="forum-reply-author">' . forum_e($author) . '</span>';
    if ($topic_author_id && (int) $reply['created_by'] === (int) $topic_author_id) {
        $html .= '<span class="forum-tag">Author</span>';
    }
    if ($is_owner) {
        $html .= '<span class="forum-tag forum-tag-you">You</span>';
    }
    $html .= '<a class="forum-reply-time" href="#reply-' . (int) $reply['id'] . '">' . forum_time_html($reply['created_at']) . '</a></div>';
    if ($is_owner) {
        $html .= '<div class="forum-reply-actions">'
            . '<button type="button" class="forum-icon-btn js-forum-edit-reply" title="Edit reply" aria-label="Edit reply"><i class="fa fa-pencil"></i></button>'
            . '<button type="button" class="forum-icon-btn forum-icon-btn-danger js-forum-delete-reply" title="Delete reply" aria-label="Delete reply"><i class="fa fa-trash-o"></i></button>'
            . '</div>';
    }
    $html .= '</div><div class="forum-reply-body">' . forum_render_reply_text($reply['description']) . '</div>';
    if ($is_owner) {
        $html .= '<textarea class="forum-reply-raw" hidden>' . forum_e($reply['description']) . '</textarea>';
    }
    return $html . '</div></article>';
}

function forum_render_like_button($topic_id, array $like_info) {
    $liked = $like_info['liked'];
    return '<button type="button" class="forum-like-btn js-forum-like' . ($liked ? ' is-liked' : '') . '" data-topic-id="' . (int) $topic_id . '" data-liked="' . ($liked ? 1 : 0) . '" aria-pressed="' . ($liked ? 'true' : 'false') . '">'
        . '<i class="fa ' . ($liked ? 'fa-heart' : 'fa-heart-o') . '" aria-hidden="true"></i>'
        . '<span class="forum-like-label">' . ($liked ? 'Liked' : 'Like') . '</span>'
        . '<span class="forum-like-count">' . (int) $like_info['total_likes'] . '</span>'
        . '</button>';
}

function forum_render_styles() {
    return '<link rel="stylesheet" href="css/forum.css?v=' . FORUM_ASSET_VERSION . '">';
}

//loaded after footer.php (jQuery and Bootstrap are loaded there)
function forum_render_scripts() {
    return '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
        . forum_js_config()
        . '<script src="js/forum.js?v=' . FORUM_ASSET_VERSION . '"></script>';
}

//data used by js/forum.js
function forum_js_config() {
    return '<script>window.forumConfig = ' . json_encode(array(
        'endpoint' => 'forum_action.php',
        'csrfToken' => forum_csrf_token(),
        'loggedIn' => (bool) forum_current_user(),
        'loginUrl' => FORUM_LOGIN_URL,
        'registerUrl' => FORUM_REGISTER_URL,
        'replyMaxLength' => FORUM_REPLY_MAX_LENGTH,
    ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
}
