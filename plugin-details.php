<?php require './Config.php'; ?>
<?php require './header.php'; ?>

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

// echo "<pre>";
// print_r($row);
// die;
?>
<title>Event Plugin — SocialCMS Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Fraunces:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --bg:#f4f5f8;
  --surface:#ffffff;
  --surface2:#f8f9fc;
  --border:rgba(0,0,0,0.07);
  --border-md:rgba(0,0,0,0.12);
  --text:#0c0e14;
  --text-2:#5a5f72;
  --text-3:#9198ae;
  --blue:#1757e8;
  --blue-light:#eef2ff;
  --blue-mid:#3b7eff;
  --green:#059669;
  --green-bg:#ecfdf5;
  --amber:#d97706;
  --amber-bg:#fffbeb;
  --rad:14px;
  --rad-sm:8px;
  --rad-pill:100px;
}

body{font-family:"roboto", sans-serif;background:var(--bg);color:var(--text);min-height:100vh;font-size:15px}

/* ── NAV ── */
nav{background:#0c0e14;padding:0 2.5rem;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200}
.nav-logo{font-family:"roboto", sans-serif;font-size:20px;color:#fff;letter-spacing:-0.5px}
.nav-logo em{font-style:italic;color:#3b7eff}
.nav-links{display:flex;gap:24px}
.nav-links a{font-size:13px;color:rgba(255,255,255,0.5);text-decoration:none;transition:color .15s}
.nav-links a:hover,.nav-links a.active{color:#fff}
.nav-actions{display:flex;gap:8px}
.btn-ghost{background:transparent;border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.75);padding:6px 16px;border-radius:var(--rad-pill);font-size:13px;font-family:"roboto", sans-serif;cursor:pointer;transition:all .15s}
.btn-ghost:hover{background:rgba(255,255,255,0.08);color:#fff}
.btn-nav-buy{background:var(--blue-mid);color:#fff;border:none;padding:7px 18px;border-radius:var(--rad-pill);font-size:13px;font-family:"roboto", sans-serif;font-weight:600;cursor:pointer;transition:opacity .15s}
.btn-nav-buy:hover{opacity:.88}

/* ── BREADCRUMB ── */
.breadcrumb{padding:.9rem 2.5rem;font-size:12px;color:var(--text-3);display:flex;align-items:center;gap:6px}
.breadcrumb a{color:var(--text-3);text-decoration:none}
.breadcrumb a:hover{color:var(--blue)}
.breadcrumb i{font-size:12px}

/* ── LAYOUT ── */
.layout{display:grid;grid-template-columns:1fr 320px;gap:24px;padding:0 2.5rem 3rem;max-width:1280px;margin:0 auto;align-items:start}

/* ── HERO CARD ── */
.hero-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;animation:fadeUp .4s ease both}

.hero-top{padding:2rem 2rem 1.5rem;display:flex;gap:1.5rem;align-items:flex-start;border-bottom:1px solid var(--border)}

.plugin-icon-wrap{width:80px;height:80px;border-radius:18px;background:linear-gradient(135deg,#1757e8 0%,#3b7eff 100%);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.plugin-icon-wrap i{font-size:36px;color:#fff}

.hero-meta{flex:1}
.hero-meta h1{font-family:"roboto", sans-serif;font-size:28px;font-weight:600;color:var(--text);letter-spacing:-0.5px;line-height:1.15;margin-bottom:.4rem}
.hero-meta .tagline{font-size:14px;color:var(--text-2);line-height:1.5;margin-bottom:1rem}

.price-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.price-now{font-size:26px;font-weight:700;color:var(--blue)}
.price-was{font-size:14px;color:var(--text-3);text-decoration:line-through}
.disc-pill{font-size:11px;font-weight:600;padding:3px 10px;border-radius:var(--rad-pill);background:#fef3c7;color:#92400e}

.hero-badges{display:flex;gap:8px;margin-top:.9rem;flex-wrap:wrap}
.badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:4px 10px;border-radius:var(--rad-pill);border:1px solid var(--border)}
.badge-active{background:var(--green-bg);color:var(--green);border-color:rgba(5,150,105,.15)}
.badge-version{background:var(--blue-light);color:var(--blue);border-color:rgba(23,87,232,.15)}
.badge-updated{background:var(--surface2);color:var(--text-2);border-color:var(--border)}

/* ── TABS ── */
.tabs{display:flex;border-bottom:1px solid var(--border)}
.tab{padding:.85rem 1.75rem;font-size:14px;font-weight:500;color:var(--text-3);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:color .15s,border-color .15s;background:none;border-top:none;border-left:none;border-right:none;font-family:"roboto", sans-serif}
.tab:hover{color:var(--text)}
.tab.active{color:var(--blue);border-bottom-color:var(--blue)}

/* ── TAB CONTENT ── */
.tab-content{padding:2rem;display:none}
.tab-content.active{display:block;animation:fadeIn .2s ease}

/* Description */
.desc-section h2{font-family:"roboto", sans-serif;font-size:20px;font-weight:600;margin-bottom:.75rem;color:var(--text)}
.desc-section p{font-size:14px;color:var(--text-2);line-height:1.75;margin-bottom:1.25rem}
.feature-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:1.25rem}
.feature-item{display:flex;align-items:flex-start;gap:10px;padding:.9rem;background:var(--surface2);border-radius:var(--rad-sm);border:1px solid var(--border)}
.feature-icon{width:32px;height:32px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.feature-icon i{font-size:16px;color:var(--blue)}
.feature-text strong{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:2px}
.feature-text span{font-size:12px;color:var(--text-3);line-height:1.4}

/* Screenshots */
.screenshots-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.screenshot{border-radius:var(--rad-sm);overflow:hidden;border:1px solid var(--border);cursor:pointer;transition:transform .15s,border-color .2s;background:#f0f1f5;aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;position:relative}
.screenshot:hover{transform:translateY(-2px);border-color:var(--blue-mid)}
.screenshot-placeholder{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--text-3)}
.screenshot-placeholder i{font-size:28px}
.screenshot-placeholder span{font-size:11px}
.screenshot-label{position:absolute;bottom:6px;left:8px;font-size:10px;font-weight:600;background:rgba(12,14,20,.65);color:#fff;padding:2px 7px;border-radius:4px}

/* Changelog */
.changelog-item{padding:1rem 0;border-bottom:1px solid var(--border)}
.changelog-item:last-child{border-bottom:none}
.cl-header{display:flex;align-items:center;gap:10px;margin-bottom:.5rem}
.cl-version{font-size:12px;font-weight:700;padding:2px 8px;background:var(--blue-light);color:var(--blue);border-radius:4px}
.cl-date{font-size:12px;color:var(--text-3)}
.cl-tag{font-size:10px;font-weight:600;padding:2px 7px;border-radius:4px}
.cl-tag.major{background:#fef3c7;color:#92400e}
.cl-tag.minor{background:var(--green-bg);color:var(--green)}
.cl-list{list-style:none;display:flex;flex-direction:column;gap:4px}
.cl-list li{font-size:13px;color:var(--text-2);display:flex;align-items:flex-start;gap:7px}
.cl-list li::before{content:'→';color:var(--blue);font-size:12px;margin-top:1px;flex-shrink:0}

/* Reviews */
.reviews-summary{display:flex;gap:2rem;align-items:center;padding:1.25rem;background:var(--surface2);border-radius:var(--rad-sm);margin-bottom:1.5rem;border:1px solid var(--border)}
.big-rating{font-family:"roboto", sans-serif;font-size:48px;font-weight:600;color:var(--text);line-height:1}
.rating-stars{color:var(--amber);font-size:18px}
.rating-count{font-size:12px;color:var(--text-3);margin-top:3px}
.bars{flex:1;display:flex;flex-direction:column;gap:5px}
.bar-row{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-3)}
.bar-track{flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden}
.bar-fill{height:100%;border-radius:3px;background:var(--amber)}
.review-card{padding:1rem 0;border-bottom:1px solid var(--border)}
.review-card:last-child{border-bottom:none}
.review-header{display:flex;align-items:center;gap:10px;margin-bottom:.5rem}
.avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}
.review-meta strong{font-size:13px;display:block}
.review-meta span{font-size:11px;color:var(--text-3)}
.review-stars{color:var(--amber);font-size:13px;margin-bottom:5px}
.review-text{font-size:13px;color:var(--text-2);line-height:1.6}

/* ── RIGHT SIDEBAR ── */
.sidebar{display:flex;flex-direction:column;gap:16px;animation:fadeUp .4s .1s ease both}

.buy-card{background:var(--blue);border-radius:20px;padding:1.75rem;color:#fff;text-align:center}
.buy-price-label{font-size:13px;opacity:.75;margin-bottom:.25rem}
.buy-price{font-family:"roboto", sans-serif;font-size:38px;font-weight:600;line-height:1;margin-bottom:.25rem}
.buy-original{font-size:13px;opacity:.6;text-decoration:line-through;margin-bottom:1.25rem}
.btn-buy{display:block;width:100%;padding:13px 0;background:#fff;color:var(--blue);border:none;border-radius:var(--rad-sm);font-size:15px;font-weight:700;font-family:"roboto", sans-serif;cursor:pointer;transition:opacity .15s;margin-bottom:10px}
.btn-buy:hover{opacity:.9}
.btn-demo{display:block;width:100%;padding:12px 0;background:rgba(255,255,255,0.12);color:#fff;border:1.5px solid rgba(255,255,255,0.25);border-radius:var(--rad-sm);font-size:14px;font-weight:500;font-family:"roboto", sans-serif;cursor:pointer;transition:background .15s;text-decoration:none;text-align:center}
.btn-demo:hover{background:rgba(255,255,255,0.2)}
.buy-note{font-size:11px;opacity:.55;margin-top:.75rem;line-height:1.5}

.info-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem}
.info-card h3{font-size:14px;font-weight:600;color:var(--text);margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid var(--border);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-row .label{color:var(--text-3)}
.info-row .value{font-weight:500;color:var(--text)}
.info-row .value.blue{color:var(--blue)}
.info-row .value.green{color:var(--green)}

.support-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem}
.support-card h3{font-size:14px;font-weight:600;margin-bottom:.75rem}
.support-hours{display:flex;flex-direction:column;gap:8px;margin-bottom:1rem}
.hour-row{display:flex;justify-content:space-between;font-size:12px}
.hour-row .day{color:var(--text-3)}
.hour-row .time{font-weight:500;color:var(--text)}
.closed{color:var(--text-3) !important;font-style:italic}
.support-btn{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:9px 0;background:var(--surface2);border:1px solid var(--border);border-radius:var(--rad-sm);font-size:13px;color:var(--text-2);font-family:"roboto", sans-serif;cursor:pointer;transition:background .15s}
.support-btn:hover{background:#eef2ff;color:var(--blue);border-color:rgba(23,87,232,.2)}

.trust-row{display:flex;flex-direction:column;gap:6px}
.trust-item{display:flex;align-items:center;gap:9px;font-size:13px;color:var(--text-2)}
.trust-item i{font-size:16px;color:var(--green)}

/* ── ANIMATIONS ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .layout{grid-template-columns:1fr;padding:0 1rem 3rem}
  .feature-grid{grid-template-columns:1fr}
  .screenshots-grid{grid-template-columns:1fr 1fr}
  .nav-links{display:none}
}
@media(max-width:600px){
  .hero-top{flex-direction:column}
  .screenshots-grid{grid-template-columns:1fr}
  .breadcrumb{padding:.75rem 1rem}
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
.lb-close { position: absolute; top: -40px; right: 0; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
.lb-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.15); border: none; color: #fff; font-size: 28px; cursor: pointer; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; padding-bottom: 7px; }
.lb-nav:hover { background: rgba(255,255,255,0.3); }
.lb-prev { left: -56px; } .lb-next { right: -56px; }
.lb-counter { position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,0.7); font-size: 13px; }

</style>

<div class="breadcrumb">
  <a href="/">Home</a>
  <i class="ti ti-chevron-right"></i>
  <a href="/list-plugins.php">Plugins</a>
  <i class="ti ti-chevron-right"></i>
  <span><?php echo htmlspecialchars($row['name']); ?></span>
</div>

<div class="layout">

  <!-- LEFT: MAIN CONTENT -->
  <div>
    <div class="hero-card">

      <div class="hero-top">
        <div class="plugin-icon-wrap">
          <img src="<?php echo htmlspecialchars($icon_url); ?>" alt="Plugin Icon" style="width:48px;height:48px;border-radius:12px">
          <!-- <i class="ti ti-calendar-event" aria-hidden="true"></i> -->
        </div>
        <div class="hero-meta">
          <h1><?php echo htmlspecialchars($row['name']); ?></h1>
          <?php
            $description = strip_tags($row['description']);
            $words = explode(' ', $description);
            $short_description = implode(' ', array_slice($words, 0, 20));
          ?>
          <!-- <p class="tagline"><?php echo htmlspecialchars($short_description); ?>...</p> -->
          <div class="price-row">
            <span class="price-now">$<?php echo number_format($price, 2); ?></span>
            <span class="price-was">$<?php echo number_format($old_price, 2); ?></span>
            <span class="disc-pill"><?php echo $discount_type == 'percentage' ? $row['discount_value'] . '% off' : '$' . $row['discount_value'] . ' off'; ?></span>
          </div>
          <div class="hero-badges">
            <span class="badge badge-active"><i class="ti ti-circle-check" style="font-size:13px"></i> <?php echo ucfirst($row['status']); ?></span>
            <span class="badge badge-version"><i class="ti ti-tag" style="font-size:13px"></i> v<?php echo htmlspecialchars($row['version']); ?></span>
            <span class="badge badge-updated"><i class="ti ti-clock" style="font-size:13px"></i> Updated <?php echo date('M d, Y', strtotime($row['updated_at'])); ?></span>
          </div>
        </div>
      </div>

      <div class="tabs">
        <button class="tab active" onclick="switchTab('description',this)">Description</button>
        <button class="tab" onclick="switchTab('screenshots',this)">Screenshots</button>
        <button class="tab" onclick="switchTab('changelog',this)">Changelog</button>
        <button class="tab" onclick="switchTab('reviews',this)">Reviews (28)</button>
      </div>

      <!-- DESCRIPTION TAB -->
      <div class="tab-content active" id="tab-description">
        <div class="desc-section">
          <h2>About this plugin</h2>
          <p><?php echo htmlspecialchars($row['description']); ?></p>
          
        </div>
      </div>

      <!-- SCREENSHOTS TAB -->
      <div class="tab-content" id="tab-screenshots">
        <div class="screenshots-grid">
          <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $idx => $photo): 
              $photo_url = $baseURL . "/store-admin/uploads/plugins/photos/" . $photo;
            ?>
              <div class="screenshot">
                <div class="screenshot-placeholder">
                  <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Screenshot" onclick="openLB(<?php echo $idx; ?>)">
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
            <!-- <span class="screenshot-label">Dashboard</span> -->
        </div>
      </div>

      <!-- CHANGELOG TAB -->
      <div class="tab-content" id="tab-changelog">
        <div class="changelog-item">
          <div class="cl-header">
            <span class="cl-version">v1.0.1</span>
            <span class="cl-date">May 08, 2026</span>
            <span class="cl-tag minor">Patch</span>
          </div>
          <ul class="cl-list">
            <li>Fixed timezone offset issue in recurring event scheduler</li>
            <li>Improved RSVP confirmation email template rendering</li>
            <li>Minor performance improvement on dashboard load</li>
          </ul>
        </div>
        <div class="changelog-item">
          <div class="cl-header">
            <span class="cl-version">v1.0.0</span>
            <span class="cl-date">Apr 15, 2026</span>
            <span class="cl-tag major">Launch</span>
          </div>
          <ul class="cl-list">
            <li>Initial public release of Event Plugin</li>
            <li>Full event creation with recurring schedule support</li>
            <li>Ticketing system with free and paid tiers</li>
            <li>Social auto-share on event publish</li>
            <li>Attendance analytics dashboard</li>
          </ul>
        </div>
      </div>

      <!-- REVIEWS TAB -->
      <div class="tab-content" id="tab-reviews">
        <div class="reviews-summary">
          <div style="text-align:center">
            <div class="big-rating">4.8</div>
            <div class="rating-stars">★★★★★</div>
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
          <div class="review-header">
            <div class="avatar" style="background:#dbeafe;color:#1d4ed8">AK</div>
            <div class="review-meta"><strong>Arjun Kapoor</strong><span>Apr 29, 2026</span></div>
          </div>
          <div class="review-stars">★★★★★</div>
          <div class="review-text">Exactly what I needed. The RSVP flow is super smooth and attendees haven't had any issues at all. Saves us hours every week.</div>
        </div>
        <div class="review-card">
          <div class="review-header">
            <div class="avatar" style="background:#d1fae5;color:#065f46">SR</div>
            <div class="review-meta"><strong>Sara Reeves</strong><span>May 02, 2026</span></div>
          </div>
          <div class="review-stars">★★★★★</div>
          <div class="review-text">The social auto-share feature alone is worth the price. We used to manually post events everywhere — now it just happens automatically on publish.</div>
        </div>
        <div class="review-card">
          <div class="review-header">
            <div class="avatar" style="background:#fef3c7;color:#92400e">TM</div>
            <div class="review-meta"><strong>Tom Müller</strong><span>May 05, 2026</span></div>
          </div>
          <div class="review-stars">★★★★</div>
          <div class="review-text">Great plugin overall. Would love to see Google Calendar sync in a future update, but the core features are solid and support was fast to respond.</div>
        </div>
      </div>

    </div>
  </div>

  <!-- RIGHT SIDEBAR -->
  <div class="sidebar">

    <div class="buy-card">
      <!-- <div class="buy-price-label">One-time purchase</div> -->
      <div class="buy-price">$<?php echo number_format($price, 2); ?></div>
      <div class="buy-original">Regular price $<?php echo number_format($old_price, 2); ?></div>
      <button class="btn-buy" onclick="window.location.href='/store-admin/index.php/Webhut_plugins/checkout/<?php echo $row['id']; ?>'">
        Buy Now — $<?php echo number_format($price, 2); ?>
      </button>
      <a href="javascript:void(0);" class="btn-demo">
        <i class="ti ti-player-play" style="font-size:14px;vertical-align:-2px"></i> View live demo
      </a>
      <!-- <div class="buy-note">One-time payment · Lifetime access · Free updates for 1 year</div> -->
    </div>

    <div class="info-card">
      <h3>Plugin info</h3>
      <div class="info-row"><span class="label">Version</span><span class="value blue"><?php echo htmlspecialchars($row['version']); ?></span></div>
      <div class="info-row"><span class="label">Plugin code</span><span class="value blue"><?php echo htmlspecialchars($row['code']); ?></span></div>
      <div class="info-row"><span class="label">Status</span><span class="value green"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></span></div>
      <div class="info-row"><span class="label">Last updated</span><span class="value"><?php echo date('M d, Y', strtotime($row['updated_at'])); ?></span></div>
      <!-- <div class="info-row"><span class="label">License</span><span class="value">Single site</span></div> -->
      <!-- <div class="info-row"><span class="label">Category</span><span class="value">Events</span></div> -->
    </div>

    <div class="support-card">
      <h3>Support hours</h3>
      <div class="support-hours">
        <div class="hour-row"><span class="day">Monday – Friday</span><span class="time">9am – 6pm</span></div>
        <div class="hour-row"><span class="day">Saturday</span><span class="time closed">Closed</span></div>
        <div class="hour-row"><span class="day">Sunday</span><span class="time closed">Closed</span></div>
        <div class="hour-row"><span class="day">Public holidays</span><span class="time closed">Closed</span></div>
      </div>
      <button class="support-btn" onclick="window.location.href='/contact.php'">
        <i class="ti ti-mail" style="font-size:16px"></i> Contact support
      </button>
    </div>

    <div class="info-card">
      <h3>Why buy with us</h3>
      <div class="trust-row">
        <div class="trust-item"><i class="ti ti-shield-check" aria-hidden="true"></i> Secure checkout</div>
        <div class="trust-item"><i class="ti ti-refresh" aria-hidden="true"></i> 30-day money back guarantee</div>
        <div class="trust-item"><i class="ti ti-download" aria-hidden="true"></i> Instant download after purchase</div>
        <div class="trust-item"><i class="ti ti-headset" aria-hidden="true"></i> Dedicated support team</div>
        <div class="trust-item"><i class="ti ti-code" aria-hidden="true"></i> Clean, documented code</div>
      </div>
    </div>

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
function switchTab(id, el) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  el.classList.add('active');
}
</script>
