<?php
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$plugin_id = $_POST['plugin_id'] ?? '';
$plugin_name = $_POST['plugin_name'] ?? '';
$plugin_price = $_POST['plugin_price'] ?? 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    if (isset($_SESSION['cart'][$plugin_id])) {
        $_SESSION['cart'][$plugin_id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$plugin_id] = [
            'id'    => $plugin_id,
            'name'  => $plugin_name,
            'price' => $plugin_price,
            'qty'   => 1
        ];
    }
} elseif ($action === 'remove') {
    unset($_SESSION['cart'][$plugin_id]);
} elseif ($action === 'count') {
    echo json_encode(['cart_count' => array_sum(array_column($_SESSION['cart'], 'qty'))]);
    exit;
}

// Cart HTML aur total calculate karo
$cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
$total = 0;
$items_html = '';

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += (float)$item['price'] * $item['qty'];
        $items_html .= '<div class="cart-drop-item" data-id="'.$item['id'].'" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid #f0f0f0;">
            <div style="flex:1;">
                <div style="font-size:13px; font-weight:600; color:#333;">'.htmlspecialchars($item['name']).'</div>
                <div style="font-size:12px; color:#888; margin-top:2px;">₹'.number_format((float)$item['price']).' &times; '.$item['qty'].'</div>
            </div>
            <button onclick="removeFromCart('.$item['id'].')" style="background:none; border:none; cursor:pointer; color:#aaa; font-size:18px; padding:4px;" title="Remove">&#x2715;</button>
        </div>';
    }
} else {
    $items_html = '<div style="padding:30px; text-align:center; color:#aaa; font-size:13px;">🛒 Cart is empty</div>';
}

echo json_encode([
    'status'      => 'success',
    'cart_count'  => $cart_count,
    'items_html'  => $items_html,
    'total'       => number_format((float)$total),
    'has_items'   => !empty($_SESSION['cart'])
]);
exit;
?>