<?php require './Config.php'; ?>
<?php require './header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0;font-family: "roboto", sans-serif; }

    body {
        font-family: 'roboto', sans-serif;
        background: #f0f4fa;

        min-height: 100vh;

    }
  
    /* ── Hero Banner ── */
    .plugins-hero {
        background: linear-gradient(135deg, #2b84d1 0%, #2b84d1 50%, #4a4fe0 100%);
        padding: 60px 20px 70px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .plugins-hero::before,
    .plugins-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .plugins-hero::before { width: 320px; height: 320px; top: -80px; left: -80px; }
    .plugins-hero::after  { width: 260px; height: 260px; bottom: -80px; right: -60px; }

    .plugins-hero h1 {
        font-weight: 500;
        color: #fff;
        font-size: 3rem;
        line-height: 1.2;
    }

    .plugins-hero p {
        font-weight: 400;
        width: 70%;
        margin: auto;
        color: #ffffff;
        margin-bottom: 1.5rem !important;
        margin-top: 1.5rem !important;
        font-size: 1.25rem;
    }

    .plugins-hero p strong { color: #fff; }

    /* ── Grid ── */
    .plugins-section {
        background: #f4f6fb;
        padding: 50px 40px 60px;
    }

    .plugins-grid {
        display: grid;
        grid-template-columns: repeat(3, 350px);
        justify-content: center;
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
        margin-bottom: 20px;
    }

    @media (max-width: 1200px) {
        .plugins-grid {
            grid-template-columns: repeat(2, 350px);
        }
    }

    @media (max-width: 768px) {
        .plugins-grid {
            grid-template-columns: 1fr;
        }

        .card {
            max-width: 350px;
            margin: auto;
        }
    }

    .plugin-filter-buttons{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:16px;
        margin-bottom:40px;
        flex-wrap:wrap;
    }

    .plugin-filter-btn{
        text-decoration:none;
        background:#fff;
        color:#2b84d1;
        border:2px solid #2b84d1;
        padding:10px 24px;
        border-radius:30px;
        font-size:15px;
        font-weight:700;
        transition:all 0.25s ease;
    }

    .plugin-filter-btn:hover{
        background:#2b84d1;
        color:#fff;
        transform:translateY(-2px);
    }

    .plugin-filter-btn.active{
        background:#2b84d1;
        color:#fff;
        box-shadow:0 8px 20px rgba(43,132,209,0.2);
    }

    .plugin-features {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 28px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }

    .plugin-features li {
        position: relative;
        color: #fff;
        font-size: 18px;
        font-weight: 500;
        padding-left: 16px;
        line-height: 1.5;
    }

    .plugin-features li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 9px;
        width: 6px;
        height: 6px;
        background: #fff;
        border-radius: 50%;
    }

    /* ── CARD ── */
    .card {
        background: #fff;
        border-radius: 20px;
        padding: 1.75rem 1.5rem 1.5rem;
        width: 350px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.85rem;
        position: relative;
        box-shadow: 0 4px 24px rgba(30,60,120,.10);
    }

    .badge-left {
        position: absolute; top: 14px; left: 14px;
        background: #FFF4E0; color: #A86000;
        font-size: 11px; font-weight: 600;
        padding: 4px 10px; border-radius: 20px;
        display: flex; align-items: center; gap: 4px;
    }
    .badge-right {
        position: absolute; top: 14px; right: 14px;
        background: #E6F1FB; color: #185FA5;
        font-size: 11px; font-weight: 600;
        padding: 4px 10px; border-radius: 20px;
        display: flex; align-items: center; gap: 4px;
    }

    .icon-wrap {
        margin-top: 1.4rem;
        width: 84px; height: 84px; border-radius: 50%;
        background: #edf1fb; border: 2px dashed #b5c3e8;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .icon-wrap img { width: 54px; height: auto; border-radius: 8px; }

    .card h2 { font-size: 20px; font-weight: 600; color: #1a1f36;margin: 0; }

    .pricing { display: flex; align-items: center; gap: 8px;padding:0;margin: 5px; }
    .price-new { font-size: 18px; font-weight: 600; color: #2c6ecb; }
    .price-old { font-size: 13px; color: #aaa; text-decoration: line-through; }
    .discount { font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 20px; font-weight: 500; }

    .stats-row {
        width: 100%; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
        padding: 10px 0; display: flex; justify-content: space-around; align-items: center;
    }
    .stat { display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .stat-stars { display: flex; gap: 2px; color: #f0a500; font-size: 13px; }
    .stat-label { font-size: 11px; color: #888; }
    .stat-val { font-size: 14px; font-weight: 600; color: #1a1f36; }
    .divider { width: 1px; height: 36px; background: #eee; }

    .btn-row { display: flex; gap: 8px; width: 100%; }
    .btn {
        flex: 1; border: none; border-radius: 10px;
        padding: 11px 0; font-size: 13px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;
        transition: opacity .15s, transform .1s;
    }
    .btn:hover { opacity: .88; }
    .btn:active { transform: scale(.97); }
    .btn-cart { background: #2c6ecb; color: #fff; }
    .btn-buy  { background: #12407a; color: #fff; }

    .learn-link {
        font-size: 12px; color: #2c6ecb; text-decoration: none;
        cursor: pointer; margin-top: -4px;
        display: flex; align-items: center; gap: 3px;
    }
    .learn-link:hover { text-decoration: underline; }

    /* ── LIGHTBOX OVERLAY ── */
    .overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(10,20,50,.55);
        backdrop-filter: blur(6px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn .2s ease;
    }
    .overlay.open { display: flex; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    /* ── MODAL ── */
    .modal1 {
        background: #fff;
        border-radius: 20px;
        width: 100%; max-width: 860px;
        max-height: 90vh; overflow-y: auto;
        position: relative;
        animation: slideUp .25s ease;
        scrollbar-width: thin;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .modal-close {
        position: sticky; top: 14px; left: 100%;
        width: 34px; height: 34px; border-radius: 50%;
        background: #fff; border: 1px solid #ddd;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 10; margin: 14px 14px -34px auto;
        font-size: 18px; color: #555; transition: background .15s;
    }
    .modal-close:hover { background: #f5f5f5; }

    /* ── MODAL HEADER ── */
    .modal-header {
        padding: 1.5rem 1.75rem 1.25rem;
        display: flex; gap: 1.25rem; align-items: flex-start;
        border-bottom: 1px solid #f0f0f0;
    }
    .modal-icon {
        width: 72px; height: 72px; border-radius: 14px;
        background: #edf1fb; border: 1px solid #d5ddf5;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        overflow: hidden;
    }
    .modal-icon img { width: 50px; }
    .modal-title-block { flex: 1; }
    .modal-title-block h1 { font-family: 'Playfair Display', serif; font-size: 24px; color: #1a1f36; margin-bottom: 6px; }
    .modal-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
    .meta-pill {
        font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 20px;
        display: flex; align-items: center; gap: 4px;
    }
    .pill-green  { background: #e8f5e9; color: #2e7d32; }
    .pill-blue   { background: #e3f0fb; color: #1565c0; }
    .pill-gray   { background: #f4f4f4; color: #555; }
    .pill-amber  { background: #fff8e1; color: #a86000; }
    .modal-pricing { display: flex; align-items: center; gap: 10px; }
    .modal-price { font-size: 22px; font-weight: 700; color: #2c6ecb; }
    .modal-price-old { font-size: 14px; color: #aaa; text-decoration: line-through; }
    .modal-discount { font-size: 12px; background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 20px; }

    /* ── MODAL BODY ── */
    .modal-body { display: grid; grid-template-columns: 1fr 280px; gap: 0; }
    @media (max-width: 640px) { .modal-body { grid-template-columns: 1fr; } }

    .modal-main { padding: 1.5rem 1.75rem; border-right: 1px solid #f0f0f0; }

    /* tabs */
    .tabs { display: flex; gap: 0; border-bottom: 1px solid #eee; margin-bottom: 1.25rem; }
    .tab {
        padding: 8px 16px; font-size: 13px; font-weight: 500; color: #888;
        cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px;
        transition: color .15s, border-color .15s;
    }
    .tab.active { color: #2c6ecb; border-bottom-color: #2c6ecb; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* description */
    .tab-content p { font-size: 14px; color: #444; line-height: 1.7; margin-bottom: .75rem; }
    .feature-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .feature-list li { font-size: 13px; color: #444; display: flex; gap: 8px; line-height: 1.5; }
    .feature-list li i { color: #2c6ecb; flex-shrink: 0; margin-top: 2px; }

    /* carousel */
    .carousel { position: relative; width: 100%; overflow: hidden; border-radius: 12px; background: #f5f7fc; }
    .carousel-track { display: flex; transition: transform .4s cubic-bezier(.4,0,.2,1); }
    .carousel-slide { min-width: 100%; position: relative; }
    .carousel-slide img { width: 100%; max-height: 300px; object-fit: contain; display: block; border-radius: 12px; }
    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(255,255,255,.92); border: 1px solid #dde3f0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 5; font-size: 18px; color: #2c6ecb;
        transition: background .15s, box-shadow .15s;
        box-shadow: 0 2px 8px rgba(30,60,120,.10);
    }
    .carousel-btn:hover { background: #fff; box-shadow: 0 4px 14px rgba(30,60,120,.18); }
    .carousel-prev { left: 10px; }
    .carousel-next { right: 10px; }
    .carousel-dots { display: flex; justify-content: center; gap: 6px; margin-top: 10px; }
    .carousel-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #cdd4e8; cursor: pointer; transition: background .2s, width .2s;
    }
    .carousel-dot.active { background: #2c6ecb; width: 22px; border-radius: 4px; }
    .carousel-counter { text-align: center; font-size: 12px; color: #999; margin-top: 6px; }
    .carousel-thumbs { display: flex; gap: 8px; margin-top: 10px; }
    .carousel-thumb {
        width: 60px; height: 44px; border-radius: 8px; overflow: hidden;
        border: 2px solid transparent; cursor: pointer; flex-shrink: 0; transition: border-color .2s;
    }
    .carousel-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .carousel-thumb.active { border-color: #2c6ecb; }

    /* changelog */
    .changelog-entry { margin-bottom: 1.25rem; }
    .cl-version { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .cl-ver { font-size: 13px; font-weight: 700; color: #1a1f36; }
    .cl-date { font-size: 12px; color: #888; }
    .cl-tag { font-size: 11px; padding: 2px 8px; border-radius: 20px; }
    .cl-patch  { background: #fff8e1; color: #a86000; }
    .cl-launch { background: #e8f5e9; color: #2e7d32; }
    .cl-list { padding-left: 1.2rem; }
    .cl-list li { font-size: 13px; color: #555; margin-bottom: 4px; line-height: 1.5; }

    /* reviews */
    .rating-summary { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0; }
    .rating-big { font-size: 48px; font-weight: 700; color: #1a1f36; line-height: 1; }
    .rating-stars-big { color: #f0a500; font-size: 18px; letter-spacing: 2px; }
    .rating-count { font-size: 12px; color: #888; }
    .bars { flex: 1; display: flex; flex-direction: column; gap: 4px; }
    .bar-row { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #888; }
    .bar-track { flex: 1; height: 6px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
    .bar-fill { height: 100%; background: #f0a500; border-radius: 3px; }
    .review-card { margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f8f8f8; }
    .reviewer { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .av-blue   { background: #e3f0fb; color: #1565c0; }
    .av-green  { background: #e8f5e9; color: #2e7d32; }
    .av-purple { background: #f3e8fb; color: #6a1b9a; }
    .reviewer-name { font-size: 13px; font-weight: 600; color: #1a1f36; }
    .reviewer-date { font-size: 11px; color: #aaa; }
    .review-stars { color: #f0a500; font-size: 12px; margin-bottom: 4px; }
    .review-text { font-size: 13px; color: #555; line-height: 1.6; }

    /* ── SIDEBAR ── */
    .modal-sidebar { padding: 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 1rem; }

    .btn-buy-lg {
        width: 100%; padding: 13px; border: none; border-radius: 12px;
        background: #2c6ecb; color: #fff; font-size: 15px; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: background .15s;
    }
    .btn-buy-lg:hover { background: #1e5ab5; }
    .btn-demo {
        width: 100%; padding: 10px; border: 1.5px solid #2c6ecb; border-radius: 12px;
        background: transparent; color: #2c6ecb; font-size: 13px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: background .15s;
    }
    .btn-demo:hover { background: #f0f6ff; }

    .info-block h4 { font-size: 12px; font-weight: 600; color: #aaa; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
    .info-row { display: flex; justify-content: space-between; font-size: 13px; color: #444; padding: 4px 0; border-bottom: 1px solid #f8f8f8; }
    .info-row span:last-child { font-weight: 500; color: #1a1f36; }

    .trust-list { list-style: none; display: flex; flex-direction: column; gap: 6px; }
    .trust-list li { font-size: 12px; color: #555; display: flex; gap: 7px; align-items: flex-start; }
    .trust-list li i { color: #2e7d32; flex-shrink: 0; margin-top: 1px; }

    .support-block p { font-size: 12px; color: #888; }
    .support-times { margin-top: 6px; }
    .support-row { display: flex; justify-content: space-between; font-size: 12px; padding: 3px 0; }
    .support-row span:first-child { color: #888; }
    .support-row span:last-child { color: #444; font-weight: 500; }
</style>

<!-- Hero Banner -->
<div class="plugins-hero">
    <h1>Our Plugins</h1>
    <!-- <p>Upgrade your <strong>website builder software</strong> with our <strong>Plugins</strong> — crafted to enhance, increase and extend its functionality.</p> -->
    <ul class="plugin-features">
        <li>Extend Your Social Platform Without Limits</li>
        <li>Everything Your Social Platform Needs — In One Place</li>
    </ul>
</div>


<!-- Plugin Cards -->
<div class="plugins-section">
    <!-- Filter Buttons -->
    <div class="plugin-filter-buttons">
        <a href="?type=all" 
           class="plugin-filter-btn <?php echo (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'active' : ''; ?>">
            All
        </a>

        <a href="?type=released" 
           class="plugin-filter-btn <?php echo (isset($_GET['type']) && $_GET['type'] == 'released') ? 'active' : ''; ?>">
            Released Plugin
        </a>

        <a href="?type=upcoming" 
           class="plugin-filter-btn <?php echo (isset($_GET['type']) && $_GET['type'] == 'upcoming') ? 'active' : ''; ?>">
            Upcoming Plugin
        </a>
    </div>
</div>
<div class="plugins-grid">
<?php
    $type = $_GET['type'] ?? 'all';

    $sql = 'SELECT * FROM crm_webhut_plugins 
            WHERE status = "active" 
            AND deleted = 0';

    if ($type == 'released') {
        $sql .= ' AND label = "released"';
    } elseif ($type == 'upcoming') {
        $sql .= ' AND label = "upcoming"';
    }

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
            $image         = $baseURL . "/store-admin/uploads/plugins/icons/" . $row['icon'];
            $old_price     = $row['rate'];
            $discount_type = $row['discount_type'];

            if ($discount_type == 'percentage') {
                $discount_amount = ($old_price * $row['discount_value']) / 100;
            } else {
                $discount_amount = $row['discount_value'];
            }

            $price = $old_price - $discount_amount;

            if ($discount_type == 'percentage') {
                $discount_label = $row['discount_value'] . "% off";
            } else {
                $discount_label = "$" . $row['discount_value'] . " off";
            }
    ?>
        <div class="card"
            data-id="<?php echo $row['id']; ?>"
            data-name="<?php echo htmlspecialchars($row['name']); ?>"
            data-description="<?php echo htmlspecialchars($row['description']); ?>"
            data-code="<?php echo htmlspecialchars($row['code']); ?>"
            data-version="<?php echo htmlspecialchars($row['version']); ?>"
            data-status="<?php echo htmlspecialchars($row['status']); ?>"
            data-price="<?php echo number_format($price,2); ?>"
            data-oldprice="<?php echo number_format($old_price,2); ?>"
            data-discount="<?php echo htmlspecialchars($discount_label); ?>"
            data-icon="<?php echo htmlspecialchars($image); ?>"
            data-updated="<?php echo date('M d, Y', strtotime($row['updated_at'])); ?>"
            data-best-sale="<?php echo $row['is_best_sale']; ?>"
            data-featured="<?php echo $row['is_featured']; ?>"
            data-photos='<?php echo $row['photos']; ?>'
        >
            <?php if($row['is_best_sale'] == 1) { ?>
                <div class="badge-left"><i class="ti ti-award" style="font-size:13px;"></i> Best Sale</div>
            <?php } ?>
            <?php if($row['is_featured'] == 1) { ?>
                <div class="badge-right"><i class="ti ti-star" style="font-size:13px;"></i> Featured</div>
            <?php } ?>

            <div class="icon-wrap">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Photo Album icon" onerror="this.style.display='none'"/>
            </div>

            <h2><?php echo htmlspecialchars($row['name']); ?></h2>

            <div class="pricing">
                <span class="price-new">$<?php echo number_format($price, 2); ?></span>
                <span class="price-old">$<?php echo number_format($old_price, 2); ?></span>
                <span class="discount"><?php echo htmlspecialchars($discount_label); ?></span>
            </div>

            <!-- <div class="stats-row">
                <div class="stat">
                <div class="stat-stars">
                    <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-half-filled"></i>
                </div>
                <span class="stat-label">4.8 (28 reviews)</span>
                </div>
                <div class="divider"></div>
                <div class="stat">
                <div style="display:flex;align-items:center;gap:4px;">
                    <i class="ti ti-download" style="font-size:15px;color:#2c6ecb;"></i>
                    <span class="stat-val">18.7k</span>
                </div>
                <span class="stat-label">Downloads</span>
                </div>
            </div> -->

            <div class="btn-row">
                <button class="btn btn-cart"><i class="ti ti-shopping-cart" style="font-size:14px;"></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="window.location.href='/store-admin/index.php/Webhut_plugins/checkout/<?php echo $row['id']; ?>'">
                    <i class="ti ti-bolt" style="font-size:14px;"></i> Buy Now
                </button>
            </div>

            <a class="learn-link" href="javascript:void(0)" onclick='openModal(<?= json_encode($row); ?>)'>
                <i class="ti ti-info-circle" style="font-size:14px;"></i> Learn More
            </a>
        </div>

    <?php endwhile; ?>
        
<?php else: ?>
    <div class="empty-plugin-message">
        <h3>No Plugins Found</h3>
    </div>

<?php endif; ?>
</div>

<!-- ══════════ LIGHTBOX OVERLAY ══════════ -->
<div class="overlay" id="overlay" onclick="handleOverlayClick(event)">
    
    <div class="modal1" id="modal">

        <button class="modal-close" onclick="closeModal()" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>

        <!-- Header -->
        <div class="modal-header">

            <div class="modal-icon">
                <img id="modalImage" src="" alt="Plugin icon" onerror="this.style.display='none'"/>
            </div>

            <div class="modal-title-block">

                <!-- NAME -->
                <h1 id="modalName">Plugin Name</h1>

                <!-- META -->
                <div class="modal-meta">

                    <!-- STATUS -->
                    <span class="meta-pill pill-green">
                        <i class="ti ti-circle-check" style="font-size:12px;"></i>
                        <span id="modalStatus">Active</span>
                    </span>

                    <!-- VERSION -->
                    <span class="meta-pill pill-blue" id="versionWrapper">
                        <i class="ti ti-code" style="font-size:12px;"></i>
                        v<span id="modalVersion">1.0.0</span>
                    </span>

                    <!-- UPDATED -->
                    <span class="meta-pill pill-gray" id="updatedWrapper">
                        <i class="ti ti-calendar" style="font-size:12px;"></i>
                        <span id="modalUpdated">May 01, 2026</span>
                    </span>

                    <!-- BEST SALE -->
                    <span class="meta-pill pill-amber" id="bestSaleWrapper" style="display:none;">
                        <i class="ti ti-award" style="font-size:12px;"></i>
                        Best Sale
                    </span>

                    <!-- FEATURED -->
                    <span class="meta-pill pill-purple" id="featuredWrapper" style="display:none;">
                        <i class="ti ti-star" style="font-size:12px;"></i>
                        Featured
                    </span>

                </div>

                <!-- PRICING -->
                <div class="modal-pricing">

                    <span class="modal-price" id="modalPrice">$0.00</span>

                    <span class="modal-price-old" id="modalOldPrice" style="display:none;">
                        $0.00
                    </span>

                    <span class="modal-discount" id="modalDiscount" style="display:none;">
                        10% off
                    </span>

                </div>

            </div>

        </div>

        <!-- Body -->
        <div class="modal-body">

            <!-- Main -->
            <div class="modal-main">

                <div class="tabs">
                    <div class="tab active" onclick="switchTab(event,'desc')">
                        Description
                    </div>

                    <div class="tab" onclick="switchTab(event,'screenshots')" id="screenshotTabBtn" style="display:none;">
                        Screenshots
                    </div>

                    <!-- <div class="tab" onclick="switchTab(event,'changelog')">Changelog</div>
                    <div class="tab" onclick="switchTab(event,'reviews')">Reviews (28)</div> -->
                </div>

                <!-- Description -->
                <div class="tab-content active" id="tab-desc">

                    <p id="modalDescription">
                        No description available.
                    </p>

                </div>

                <!-- Screenshots -->
                <div class="tab-content" id="tab-screenshots">

                    <div class="carousel" id="carousel">

                        <div class="carousel-track" id="carouselTrack">
                            <!-- Dynamic Images -->
                        </div>

                        <button class="carousel-btn carousel-prev" onclick="carouselMove(-1)" aria-label="Previous">
                            <i class="ti ti-chevron-left"></i>
                        </button>

                        <button class="carousel-btn carousel-next" onclick="carouselMove(1)" aria-label="Next">
                            <i class="ti ti-chevron-right"></i>
                        </button>

                    </div>

                    <div class="carousel-dots" id="carouselDots"></div>

                    <div class="carousel-counter" id="carouselCounter"></div>

                    <div class="carousel-thumbs" id="carouselThumbs"></div>

                </div>

                <!-- Changelog -->
                <div class="tab-content" id="tab-changelog">
                    <div class="changelog-entry">
                        <div class="cl-version">
                        <span class="cl-ver">v1.0.1</span>
                        <span class="cl-date">May 08, 2026</span>
                        <span class="cl-tag cl-patch">Patch</span>
                        </div>
                        <ul class="cl-list">
                        <li>Fixed timezone offset issue in recurring event scheduler</li>
                        <li>Improved RSVP confirmation email template rendering</li>
                        <li>Minor performance improvement on dashboard load</li>
                        </ul>
                    </div>
                    <div class="changelog-entry">
                        <div class="cl-version">
                        <span class="cl-ver">v1.0.0</span>
                        <span class="cl-date">Apr 15, 2026</span>
                        <span class="cl-tag cl-launch">Launch</span>
                        </div>
                        <ul class="cl-list">
                        <li>Initial public release of Photo Album Plugin</li>
                        <li>Full album creation with drag-and-drop ordering</li>
                        <li>Privacy settings with friends-only and custom options</li>
                        <li>Social sharing on album publish</li>
                        <li>Member tagging and comments system</li>
                        </ul>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="tab-content" id="tab-reviews">
                    <div class="rating-summary">
                        <div style="text-align:center;">
                        <div class="rating-big">4.8</div>
                        <div class="rating-stars-big">★★★★★</div>
                        <div class="rating-count">28 reviews</div>
                    </div>
                    <div class="bars">
                        <div class="bar-row">5★ <div class="bar-track"><div class="bar-fill" style="width:82%"></div></div> 23</div>
                        <div class="bar-row">4★ <div class="bar-track"><div class="bar-fill" style="width:14%"></div></div> 4</div>
                        <div class="bar-row">3★ <div class="bar-track"><div class="bar-fill" style="width:4%"></div></div> 1</div>
                        <div class="bar-row">2★ <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div> 0</div>
                        <div class="bar-row">1★ <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div> 0</div>
                    </div>
                </div>

                <div class="review-card">
                    <div class="reviewer">
                        <div class="avatar av-blue">AK</div>
                        <div><div class="reviewer-name">Arjun Kapoor</div><div class="reviewer-date">Apr 29, 2026</div></div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">Exactly what I needed. The RSVP flow is super smooth and attendees haven't had any issues at all. Saves us hours every week.</p>
                </div>

                <div class="review-card">
                    <div class="reviewer">
                    <div class="avatar av-green">SR</div>
                    <div><div class="reviewer-name">Sara Reeves</div><div class="reviewer-date">May 02, 2026</div></div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">The social auto-share feature alone is worth the price. We used to manually post events everywhere — now it just happens automatically on publish.</p>
                </div>

                <div class="review-card">
                    <div class="reviewer">
                    <div class="avatar av-purple">TM</div>
                    <div><div class="reviewer-name">Tom Müller</div><div class="reviewer-date">May 05, 2026</div></div>
                    </div>
                    <div class="review-stars">★★★★☆</div>
                    <p class="review-text">Great plugin overall. Would love to see Google Calendar sync in a future update, but the core features are solid and support was fast to respond.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="modal-sidebar">

                <!-- BUY BUTTON -->
                <button class="btn-buy-lg" id="modalBuyBtn">
                    <i class="ti ti-credit-card" style="font-size:16px;"></i>
                    Buy Now
                </button>

                <!-- DEMO BUTTON -->
                <button class="btn-demo" id="demoBtn">
                    <i class="ti ti-player-play" style="font-size:14px;"></i>
                    View Live Demo
                </button>

                <!-- INFO -->
                <div class="info-block">

                    <h4>Plugin Info</h4>

                    <div class="info-row">
                        <span>Version</span>
                        <span id="infoVersion">1.0.0</span>
                    </div>

                    <div class="info-row">
                        <span>Plugin code</span>
                        <span id="infoCode">plugin</span>
                    </div>

                    <div class="info-row">
                        <span>Status</span>
                        <span id="infoStatus" style="color:#2e7d32;">Active</span>
                    </div>

                    <div class="info-row">
                        <span>Last updated</span>
                        <span id="infoUpdated">May 01, 2026</span>
                    </div>

                </div>

                <div class="info-block support-block">
                    <h4>Support Hours</h4>
                    <div class="support-times">
                        <div class="support-row"><span>Mon – Fri</span><span>9am – 6pm</span></div>
                        <div class="support-row"><span>Saturday</span><span>Closed</span></div>
                        <div class="support-row"><span>Sunday</span><span>Closed</span></div>
                        <div class="support-row"><span>Public holidays</span><span>Closed</span></div>
                    </div>
                    </div>

                    <div class="info-block">
                    <h4>Why Buy With Us</h4>
                    <ul class="trust-list">
                        <li><i class="ti ti-shield-check" style="font-size:14px;"></i> Secure checkout</li>
                        <li><i class="ti ti-refresh" style="font-size:14px;"></i> 30-day money back guarantee</li>
                        <li><i class="ti ti-download" style="font-size:14px;"></i> Instant download after purchase</li>
                        <li><i class="ti ti-headset" style="font-size:14px;"></i> Dedicated support team</li>
                        <li><i class="ti ti-code" style="font-size:14px;"></i> Clean, documented code</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    let carouselTotal = 3;

    function openModal(plugin) {

        // IMAGE
        let image = plugin.icon
            ? '/store-admin/uploads/plugins/icons/' + plugin.icon
            : '';
        document.getElementById('modalImage').src = image;
        document.getElementById('modalImage').style.display = image ? 'block' : 'none';

        // NAME
        document.getElementById('modalName').innerText = plugin.name || 'Plugin';

        // DESCRIPTION
        document.getElementById('modalDescription').innerText =
            plugin.description || 'No description available';

        // STATUS
        document.getElementById('modalStatus').innerText = plugin.status || 'Inactive';
        document.getElementById('infoStatus').innerText  = plugin.status || 'Inactive';

        // VERSION
        if (plugin.version) {
            document.getElementById('modalVersion').innerText = plugin.version;
            document.getElementById('infoVersion').innerText  = plugin.version;
            document.getElementById('versionWrapper').style.display = 'inline-flex';
        } else {
            document.getElementById('versionWrapper').style.display = 'none';
        }

        // UPDATED DATE
        if (plugin.updated_at) {
            let date      = new Date(plugin.updated_at);
            let formatted = date.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
            document.getElementById('modalUpdated').innerText = formatted;
            document.getElementById('infoUpdated').innerText  = formatted;
        }

        // CODE
        document.getElementById('infoCode').innerText = plugin.code || '-';

        // BEST SALE / FEATURED badges
        document.getElementById('bestSaleWrapper').style.display =
            plugin.is_best_sale == 1 ? 'inline-flex' : 'none';
        document.getElementById('featuredWrapper').style.display =
            plugin.is_featured == 1  ? 'inline-flex' : 'none';

        // PRICE calculation
        let oldPrice       = parseFloat(plugin.rate || 0);
        let discountAmount = 0;

        if (plugin.discount_type == 'percentage') {
            discountAmount = (oldPrice * parseFloat(plugin.discount_value || 0)) / 100;
        } else {
            discountAmount = parseFloat(plugin.discount_value || 0);
        }

        let finalPrice = oldPrice - discountAmount;
        document.getElementById('modalPrice').innerText = '$' + finalPrice.toFixed(2);

        if (discountAmount > 0) {
            document.getElementById('modalOldPrice').style.display = 'inline-block';
            document.getElementById('modalDiscount').style.display = 'inline-block';
            document.getElementById('modalOldPrice').innerText     = '$' + oldPrice.toFixed(2);

            let label = plugin.discount_type == 'percentage'
                ? plugin.discount_value + '% off'
                : '$' + plugin.discount_value + ' off';

            document.getElementById('modalDiscount').innerText = label;
        } else {
            document.getElementById('modalOldPrice').style.display = 'none';
            document.getElementById('modalDiscount').style.display = 'none';
        }

        // BUY BUTTON
        document.getElementById('modalBuyBtn').onclick = function () {
            window.location.href = '/store-admin/index.php/Webhut_plugins/checkout/' + plugin.id;
        };

        // SCREENSHOTS
        let photos = [];
        console.log('Raw photos data:', plugin);
        if (plugin.photos && plugin.photos != 'null') {
            try {
                photos = JSON.parse(plugin.photos);
            } catch(e) {
                photos = [];
            }
        }

        let track   = document.getElementById('carouselTrack');
        let dots    = document.getElementById('carouselDots');
        let thumbs  = document.getElementById('carouselThumbs');
        let counter = document.getElementById('carouselCounter');

        track.innerHTML  = '';
        dots.innerHTML   = '';
        thumbs.innerHTML = '';

        console.log('Loaded photos:', photos);
        if (photos.length > 0) {
            document.getElementById('screenshotTabBtn').style.display = 'block';
            carouselTotal   = photos.length;
            carouselIndex   = 0;

            photos.forEach((photo, index) => {
                track.innerHTML += `
                    <div class="carousel-slide">
                        <img src="/store-admin/uploads/plugins/photos/${photo}" alt="Screenshot">
                    </div>`;

                dots.innerHTML += `
                    <div class="carousel-dot ${index == 0 ? 'active' : ''}"
                        onclick="carouselGoTo(${index})">
                    </div>`;

                thumbs.innerHTML += `
                    <div class="carousel-thumb ${index == 0 ? 'active' : ''}"
                        onclick="carouselGoTo(${index})">
                        <img src="/store-admin/uploads/plugins/photos/${photo}" alt="">
                    </div>`;
            });

            counter.innerText = `1 / ${photos.length}`;
            document.getElementById('carouselTrack').style.transform = 'translateX(0%)';

        } else {
            document.getElementById('screenshotTabBtn').style.display = 'none';
        }

        document.getElementById('overlay').classList.add('open');
        document.body.style.overflow = 'hidden';

        // First tab reset
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab')[0].classList.add('active');
        document.getElementById('tab-desc').classList.add('active');
    }

    function closeModal() {
        document.getElementById('overlay').classList.remove('open'); 
        document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
        if (e.target === document.getElementById('overlay')) closeModal();
    }

    function switchTab(e, id) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        e.target.classList.add('active');
        document.getElementById('tab-' + id).classList.add('active');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape')     closeModal();
        if (e.key === 'ArrowLeft')  carouselMove(-1);
        if (e.key === 'ArrowRight') carouselMove(1);
    });

    let carouselIndex = 0;

    function carouselGoTo(idx) {
        carouselIndex = ((idx % carouselTotal) + carouselTotal) % carouselTotal;
        document.getElementById('carouselTrack').style.transform = `translateX(-${carouselIndex * 100}%)`;
        document.querySelectorAll('.carousel-dot').forEach((d, i)  => d.classList.toggle('active', i === carouselIndex));
        document.querySelectorAll('.carousel-thumb').forEach((t, i) => t.classList.toggle('active', i === carouselIndex));
        document.getElementById('carouselCounter').textContent = `${carouselIndex + 1} / ${carouselTotal}`;
    }

    function carouselMove(dir) { carouselGoTo(carouselIndex + dir); }

    // Swipe support
    let touchStartX = 0;
    const carouselEl = document.getElementById('carousel');
    if (carouselEl) {
        carouselEl.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        carouselEl.addEventListener('touchend',   e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) carouselMove(diff > 0 ? 1 : -1);
        });
    }
</script>
<?php require './footer.php'; ?>