<?php
// Shared helpers for the public-facing Services pages (services.php, service-details.php)
// and the "Our Services" homepage preview section (index.php).
// The Services module is managed by the store-admin backend (crm_services table),
// this frontend only reads active, non-deleted rows.

require_once __DIR__ . '/forum_helper.php'; //reuses forum_clean_html() for the rich-text description

// A generic "tools/services" icon, used whenever a service has no (valid) icon of its own.
// Must be an icon that actually exists in Font Awesome 4.7 (the version loaded by header.php) -
// newer-only icon names (e.g. "fa-concierge-bell") silently render as an empty/broken square.
define('SERVICES_DEFAULT_ICON', 'fa-wrench');

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

//returns a safe-to-render Font Awesome class for a service icon.
//NULL, empty, missing or anything that isn't a plausible "fa-..." class falls back to
//SERVICES_DEFAULT_ICON, so a card/detail page never shows an empty or broken icon.
function services_icon_class($icon) {
    $icon = trim((string) $icon);
    if ($icon !== '' && preg_match('/^(fa\s+)?fa-[a-z0-9]+(-[a-z0-9]+)*(\s+fa-[a-z0-9]+(-[a-z0-9]+)*)*$/i', $icon)) {
        return $icon;
    }
    return SERVICES_DEFAULT_ICON;
}

//"$200.00", "Starting from $49.00" or "Custom Quote" depending on price_type
function services_format_price($price, $price_type) {
    if ($price_type === 'custom_quote' || $price === null || $price === '') {
        return 'Custom Quote';
    }

    $amount = '$' . number_format((float) $price, 2);
    return $price_type === 'starting_from' ? 'Starting from ' . $amount : $amount;
}

//short plain-text excerpt of short_description, html-escaped and ready to echo
function services_excerpt($text, $length = 140) {
    $text = trim((string) $text);
    if (mb_strlen($text) > $length) {
        $text = rtrim(mb_substr($text, 0, $length)) . '...';
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
