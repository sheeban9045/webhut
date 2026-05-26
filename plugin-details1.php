<?php require './Config.php'; ?>
<?php require './header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header('Location: list-plugins.php');
    exit;
}

$sql = 'SELECT * FROM crm_webhut_plugins WHERE id = ' . $id . ' AND status = "active" AND deleted = 0';
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: list-plugins.php');
    exit;
}

$row = mysqli_fetch_assoc($result);

// Price calculation
$old_price     = (float)$row['rate'];
$discount_type = $row['discount_type'];
if ($discount_type == 'percentage') {
    $discount_amount = ($old_price * (float)$row['discount_value']) / 100;
} else {
    $discount_amount = (float)$row['discount_value'];
}
$price  = $old_price - $discount_amount;

// Photos
$photos = json_decode($row['photos'], true) ?: [];

// Icon
$icon_url = $baseURL . "/store-admin/uploads/plugins/icons/" . $row['icon'];
?>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body { background: #f4f6fb; color: #1a1d3b;font-family: "roboto", sans-serif; }

/* ── Hero ── */
.pd-hero {
    background: linear-gradient(135deg, #2b84d1 0%, #2b84d1 50%, #4a4fe0 100%);
    padding: 48px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.pd-hero::before, .pd-hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
.pd-hero::before { width: 300px; height: 300px; top: -80px; left: -80px; }
.pd-hero::after  { width: 250px; height: 250px; bottom: -80px; right: -60px; }
.pd-hero h1 {
    font-weight: 500;
    color: #fff;
    font-size: 3rem;
    line-height: 1.2;
}

/* ── Layout ── */
.pd-container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 28px;
    align-items: start;
}

/* ── Left panel ── */
.pd-main { background: #fff; border: 1px solid #e6e9f4; border-radius: 14px; overflow: hidden; }

.pd-product-header {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 28px 28px 20px;
    border-bottom: 1px solid #f0f2ff;
}
.pd-product-header img {
    width: 72px;
    height: 72px;
    object-fit: contain;
    border-radius: 12px;
    background: #f0f2ff;
    padding: 6px;
}
.pd-product-header-info h2 {
    font-size: 24px;
    font-weight: 800;
    color: #2b84d1;
    margin-bottom: 6px;
}
.pd-price-now  { font-size: 18px; font-weight: 800; color: #2b84d1; }
.pd-price-old  { font-size: 14px; color: #aaa; text-decoration: line-through; margin-left: 8px; }
.pd-discount   { font-size: 12px; font-weight: 700; background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 20px; margin-left: 6px; }

/* ── Tabs ── */
.pd-tabs {
    display: flex;
    border-bottom: 1px solid #e6e9f4;
}
.pd-tab {
    flex: 1;
    padding: 14px;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    color: #888;
    background: none;
    border: none;
    transition: background 0.2s, color 0.2s;
}
.pd-tab.active {
    background: #2b84d1;
    color: #fff;
    outline: none !important;
}
.pd-tab:focus { outline: none !important; }
.pd-tab:not(.active):hover { background: #f4f6fb; color: #2b84d1; }

/* ── Tab content ── */
.pd-tab-content { display: none; padding: 28px; }
.pd-tab-content.active { display: block; }

.pd-description {
    font-size: 16px;
    line-height: 1.8;
    color: #444;
    white-space: pre-line;
}
.pd-description h3 {
    font-size: 16px;
    font-weight: 800;
    color: #1a1d3b;
    margin: 18px 0 8px;
}

/* ── Screenshots ── */
.pd-screenshots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 14px;
}
.pd-screenshots img {
    width: 100%;
    border-radius: 8px;
    border: 1px solid #e6e9f4;
    object-fit: cover;
    height: 130px;
    cursor: pointer;
    transition: transform 0.2s;
}
.pd-screenshots img:hover { transform: scale(1.03); }

/* ── Right sidebar ── */
.pd-sidebar { display: flex; flex-direction: column; gap: 20px; }

.pd-buy-btn {
    display: block;
    width: 100%;
    background: #2b84d1;
    color: #fff;
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    padding: 16px;
    border-radius: 10px;
    text-decoration: none;
    letter-spacing: 0.4px;
    transition: background 0.2s;
}
.pd-buy-btn:hover { background: #005dae; color: #fff; }

.pd-info-box {
    background: #fff;
    border: 1px solid #e6e9f4;
    border-radius: 14px;
    overflow: hidden;
}
.pd-info-box-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1d3b;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2ff;
}
.pd-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid #f4f6fb;
    font-size: 13px;
}
.pd-info-row:last-child { border-bottom: none; }
.pd-info-row span:first-child { color: #555; }
.pd-info-row span:last-child  { color: #2b84d1; font-weight: 700; }

.pd-support-box {
    background: #fff;
    border: 1px solid #e6e9f4;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
}
.pd-support-box h4 { font-size: 16px; font-weight: 600; color: #2b84d1; margin-bottom: 10px; }
.pd-support-box p  { font-size: 14px; color: #666; line-height: 1.7; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .pd-container { grid-template-columns: 1fr; }
}

/* Lightbox */
.lb-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.85); z-index: 9999;
    align-items: center; justify-content: center;
}
.lb-overlay.open { display: flex; }
.lb-inner { position: relative; max-width: 90vw; max-height: 90vh; }
.lb-inner img { max-width: 90vw; max-height: 85vh; border-radius: 10px; display: block; object-fit: contain; }
.lb-close { position: absolute; top: -40px; right: 0; background: none; border: none; color: #fff; font-size: 32px; cursor: pointer; }
.lb-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.15); border: none; color: #fff; font-size: 28px; cursor: pointer; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
.lb-nav:hover { background: rgba(255,255,255,0.3); }
.lb-prev { left: -56px; } .lb-next { right: -56px; }
.lb-counter { position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,0.7); font-size: 13px; }
</style>

<!-- Hero -->
<div class="pd-hero">
    <h1><?php echo htmlspecialchars($row['name']); ?></h1>
</div>

<!-- Main Layout -->
<div class="pd-container">

    <!-- LEFT: Main Content -->
    <div class="pd-main">

        <!-- Plugin Header -->
        <div class="pd-product-header">
            <img src="<?php echo htmlspecialchars($icon_url); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <div class="pd-product-header-info">
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <div>
                    <span class="pd-price-now">$<?php echo number_format($price, 2); ?></span>
                    <?php if ($discount_amount > 0): ?>
                        <span class="pd-price-old">$<?php echo number_format($old_price, 2); ?></span>
                        <span class="pd-discount">
                            <?php echo $discount_type == 'percentage' ? $row['discount_value'] . '% off' : '$' . $row['discount_value'] . ' off'; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="pd-tabs">
            <button class="pd-tab active" onclick="switchTab('description', this)">Description</button>
            <?php if (!empty($photos)): ?>
            <button class="pd-tab" onclick="switchTab('screenshots', this)">Screenshots</button>
            <?php endif; ?>
        </div>

        <!-- Description Tab -->
        <div class="pd-tab-content active" id="tab-description">
            <div class="pd-description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></div>
        </div>

        <!-- Screenshots Tab -->
        <?php if (!empty($photos)): ?>
        <div class="pd-tab-content" id="tab-screenshots">
            <div class="pd-screenshots">
                    <?php foreach ($photos as $idx => $photo): 
                        $photo_url = $baseURL . "/store-admin/uploads/plugins/photos/" . $photo;
                    ?>
                    <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Screenshot" onclick="openLB(<?php echo $idx; ?>)">
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT: Sidebar -->
    <div class="pd-sidebar">

        <!-- Buy Button -->
        <a href="/store-admin/index.php/Webhut_plugins/checkout/<?php echo $row['id']; ?>" class="pd-buy-btn">Buy Now — $<?php echo number_format($price, 2); ?></a>

        <!-- Info Box -->
        <div class="pd-info-box">
            <div class="pd-info-box-title">Plugin Info</div>
            <div class="pd-info-row">
                <span>Version</span>
                <span><?php echo htmlspecialchars($row['version']); ?></span>
            </div>
            <div class="pd-info-row">
                <span>Plugin Code</span>
                <span><?php echo htmlspecialchars($row['code']); ?></span>
            </div>
            <div class="pd-info-row">
                <span>Status</span>
                <span style="color:#2e7d32;">Active</span>
            </div>
            <div class="pd-info-row">
                <span>Last Updated</span>
                <span style="color:#555; font-weight:600;"><?php echo date('M d, Y', strtotime($row['updated_at'])); ?></span>
            </div>
        </div>

        <!-- Support Hours -->
        <div class="pd-support-box">
            <h4>Support Hours</h4>
            <p>Support is provided from Monday through Friday during normal business hours. Support is closed for major holidays.</p>
        </div>

    </div>

    <div class="lb-overlay" id="lbOverlay" onclick="if(event.target===this) closeLB()">
        <div class="lb-inner">
            <button class="lb-close" onclick="closeLB()">&#x2715;</button>
            <button class="lb-nav lb-prev" onclick="lbNav(-1)">&#8249;</button>
            <img id="lbImg" src="" alt="">
            <button class="lb-nav lb-next" onclick="lbNav(1)">&#8250;</button>
            <div class="lb-counter" id="lbCounter"></div>
        </div>
    </div>
</div>

<script>
<?php
$photos_json = json_encode(array_map(fn($p) => $baseURL . "/store-admin/uploads/plugins/photos/" . $p, $photos));
?>
const lbImgs = <?php echo $photos_json; ?>;
let lbCur = 0;
function openLB(i) {
    lbCur = i;
    document.getElementById('lbImg').src = lbImgs[i];
    document.getElementById('lbCounter').textContent = (i+1) + ' / ' + lbImgs.length;
    document.getElementById('lbOverlay').classList.add('open');
}
function closeLB() { document.getElementById('lbOverlay').classList.remove('open'); }
function lbNav(dir) {
    lbCur = (lbCur + dir + lbImgs.length) % lbImgs.length;
    document.getElementById('lbImg').src = lbImgs[lbCur];
    document.getElementById('lbCounter').textContent = (lbCur+1) + ' / ' + lbImgs.length;
}
document.addEventListener('keydown', e => {
    if (!document.getElementById('lbOverlay').classList.contains('open')) return;
    if (e.key === 'ArrowLeft') lbNav(-1);
    if (e.key === 'ArrowRight') lbNav(1);
    if (e.key === 'Escape') closeLB();
});
function switchTab(tab, btn) {
    document.querySelectorAll('.pd-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.pd-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}
</script>

<?php require './footer.php'; ?>