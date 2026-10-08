<?php
// Shared helpers for the public-facing Services pages (services.php, service-details.php)
// and the "Our Services" homepage preview section (index.php).
// The Services module is managed by the store-admin backend (crm_services table),
// this frontend only reads active, non-deleted rows.

require_once __DIR__ . '/forum_helper.php'; //reuses forum_clean_html() for the rich-text description

// Font Awesome 4.7 icon shown in place of the image when a service has no (valid) image.
define('SERVICES_DEFAULT_ICON', 'fa-briefcase');

// Where store-admin saves uploaded files (RISE "timeline_file_path" setting), relative to store-admin/
define('SERVICES_DEFAULT_FILE_PATH', 'files/timeline_files/');

//active, non-deleted services in the same order the admin list uses; $limit=0 means no limit
function services_get_active($conn, $limit = 0) {
    $sql = "SELECT * FROM crm_services WHERE status = 'active' AND deleted = 0 ORDER BY sort_order ASC, id ASC";
    if ($limit > 0) {
        $sql .= " LIMIT " . (int) $limit;
    }
    return mysqli_query($conn, $sql);
}

//a single active service by its slug, or null when not found/inactive/deleted
function services_get_by_slug($conn, $slug) {
    $slug = trim((string) $slug);
    if ($slug === '') {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM crm_services WHERE slug = ? AND status = 'active' AND deleted = 0 LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}

//store-admin's file folder for uploads, read once from its settings
function services_file_path($conn) {
    static $path = null;
    if ($path === null) {
        $path = SERVICES_DEFAULT_FILE_PATH;
        $result = mysqli_query($conn, "SELECT setting_value FROM crm_settings WHERE setting_name = 'timeline_file_path' AND deleted = 0 LIMIT 1");
        $row = $result ? mysqli_fetch_assoc($result) : null;
        if ($row && trim((string) $row['setting_value']) !== '') {
            $path = rtrim(trim($row['setting_value']), '/') . '/';
        }
    }
    return $path;
}

//public URL of a service image, or '' when there is none.
//crm_services.image holds the serialized file info written by store-admin (file_name, file_id, service_type)
function services_image_url($conn, $image) {
    $info = $image ? @unserialize((string) $image, array('allowed_classes' => false)) : null;
    if (!is_array($info)) {
        return '';
    }

    $file_name = (string) ($info['file_name'] ?? '');
    $file_id = (string) ($info['file_id'] ?? '');

    if (($info['service_type'] ?? '') === 'google') {
        return $file_id !== '' ? 'https://drive.google.com/thumbnail?id=' . rawurlencode($file_id) . '&sz=s700' : '';
    }

    if ($file_name === '' || $file_name !== basename($file_name)) {
        return '';
    }

    return '/store-admin/' . services_file_path($conn) . rawurlencode($file_name);
}

//price + the admin's free-text price type: "$25.00 USD per hour", "$200.00 USD",
//just the price type when there is no price (e.g. "Custom Quote"), otherwise "Price on request"
function services_format_price($price, $price_type = '') {
    $price_type = trim((string) $price_type);
    if ($price === null || $price === '' || !is_numeric($price)) {
        return $price_type !== '' ? $price_type : 'Price on request';
    }
    return trim('$' . number_format((float) $price, 2) . ' USD ' . $price_type);
}

//false when there is no numeric price (the label is then shown in a muted style)
function services_has_price($price) {
    return $price !== null && $price !== '' && is_numeric($price);
}

//short plain-text preview of the (html) description, html-escaped and ready to echo
function services_excerpt($html, $length = 140) {
    $text = str_replace('<', ' <', (string) $html); //keep words from separate paragraphs apart
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));

    if (mb_strlen($text) > $length) {
        $text = rtrim(mb_substr($text, 0, $length), " \t\n\r\0\x0B.,;:-") . '...';
    }
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

//the full description is saved as html from the backend's rich text editor, clean it the same way the Forum does
function services_render_description($html) {
    return forum_clean_html((string) $html);
}

function services_detail_url($slug) {
    return 'service-details.php?slug=' . urlencode($slug);
}

//links to the existing Contact Us form, pre-filling the message with this service (see contact.php)
function services_contact_url($slug) {
    return 'contact.php?service=' . urlencode($slug);
}

//service image inside a wrapper with the default icon as fallback (no image, or the image fails to load).
//used by the cards, the popup, the detail page and the homepage; style it with ".service-thumb" + $class
function services_thumb_html($image_url, $title, $class = '') {
    $html = '<div class="service-thumb ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . ($image_url !== '' ? ' has-image' : '') . '">';
    if ($image_url !== '') {
        $html .= '<img src="' . htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="lazy" onerror="this.parentNode.classList.remove(\'has-image\');this.remove();">';
    }
    return $html . '<i class="fa ' . SERVICES_DEFAULT_ICON . '" aria-hidden="true"></i></div>';
}
