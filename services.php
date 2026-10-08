<?php require './Config.php'; ?>
<?php require_once './services_helper.php'; ?>
<?php
$services_result = services_get_active($conn);
$services = $services_result ? mysqli_fetch_all($services_result, MYSQLI_ASSOC) : array();
?>
<?php require './header.php'; ?>

<style>
    /* ── Hero (same banner style as the Plugins page) ── */
    .services-hero {
        background: linear-gradient(135deg, #2b84d1 0%, #2b84d1 50%, #4a4fe0 100%);
        padding: 60px 20px 70px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .services-hero::before,
    .services-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .services-hero::before { width: 320px; height: 320px; top: -80px; left: -80px; }
    .services-hero::after  { width: 260px; height: 260px; bottom: -80px; right: -60px; }
    .services-hero h1 {
        font-weight: 500;
        color: #fff;
        font-size: 3rem;
        line-height: 1.2;
        position: relative;
    }
    .services-hero p {
        font-weight: 400;
        max-width: 760px;
        margin: 1.5rem auto 0;
        color: #fff;
        font-size: 1.25rem;
        position: relative;
    }

    /* ── Grid ── */
    .services-section {
        background: #f4f6fb;
        padding: 50px 20px 70px;
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 28px;
        max-width: 1180px;
        margin: 0 auto;
    }
    @media (max-width: 1100px) {
        .services-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 700px) {
        .services-hero { padding: 45px 16px 55px; }
        .services-hero h1 { font-size: 2.2rem; }
        .services-hero p { font-size: 1.05rem; }
        .services-section { padding: 35px 16px 50px; }
        .services-grid { grid-template-columns: minmax(0, 1fr); max-width: 460px; }
    }

    /* ── Service image / fallback icon (shared markup from services_thumb_html()) ── */
    .service-thumb {
        position: relative;
        flex-shrink: 0;
        border-radius: 50%;
        background: #edf1fb;
        color: #2b84d1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .service-thumb img { width: 70%; height: 70%; object-fit: cover; display: block; }
    .service-thumb.has-image i { display: none; }

    /* ── Card ── */
    .svc-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.6rem 1.5rem 1.5rem;
        box-shadow: 0 4px 24px rgba(30,60,120,.10);
        display: flex;
        flex-direction: column;
        min-width: 0;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .svc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(30,60,120,.15);
    }
    .svc-card-head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    .svc-card-thumb { width: 72px; height: 72px; font-size: 28px; }
    .svc-card-title { min-width: 0; }
    .svc-card-title h2 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1a1f36;
        margin: 0 0 4px;
        line-height: 1.35;
        word-wrap: break-word;
    }
    .svc-price {
        font-size: 1rem;
        font-weight: 700;
        color: #2c6ecb;
    }
    .svc-price.is-on-request { color: #6b7a90; font-weight: 600; }
    .svc-card-desc {
        color: #555b66;
        font-size: .95rem;
        line-height: 1.65;
        margin: 0 0 10px;
        flex-grow: 1;
        word-wrap: break-word;
    }
    .svc-details-link {
        align-self: flex-start;
        background: none;
        border: 0;
        padding: 0;
        margin: 0 0 18px;
        color: #2c6ecb;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
    }
    .svc-details-link:hover,
    .svc-details-link:focus { text-decoration: underline; outline: none; }
    .svc-details-link .fa { margin-right: 4px; }

    .svc-buy-btn {
        width: 100%;
        border: 0;
        border-radius: 10px;
        padding: 12px 16px;
        background: #2c6ecb;
        color: #fff;
        font-size: .95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }
    .svc-buy-btn:hover { background: #1e5ab5; }
    .svc-buy-btn:active { transform: scale(.98); }
    .svc-buy-btn .fa { margin-right: 6px; }

    .services-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b6a6a;
        max-width: 700px;
        margin: 0 auto;
    }

    /* ── Popup ── */
    .svc-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,20,50,.55);
        z-index: 100000;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .svc-overlay.open { display: flex; animation: svcFadeIn .2s ease; }
    @keyframes svcFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes svcSlideUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

    .svc-modal {
        background: #fff;
        border-radius: 20px;
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
        animation: svcSlideUp .25s ease;
    }
    .svc-modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #ddd;
        color: #555;
        font-size: 16px;
        cursor: pointer;
        z-index: 2;
    }
    .svc-modal-close:hover { background: #f5f5f5; }
    .svc-modal-head {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 24px 64px 20px 28px;
        border-bottom: 1px solid #f0f0f0;
    }
    .svc-modal-thumb { width: 88px; height: 88px; font-size: 34px; }
    .svc-modal-head h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1f36;
        margin: 0 0 6px;
        word-wrap: break-word;
    }
    .svc-modal-head .svc-price { font-size: 1.15rem; }
    .svc-modal-body {
        padding: 22px 28px;
        overflow-y: auto;
        color: #444;
        font-size: .97rem;
        line-height: 1.75;
        word-wrap: break-word;
    }
    .svc-modal-body img { max-width: 100%; height: auto; border-radius: 8px; }
    .svc-modal-body table { max-width: 100%; }
    .svc-modal-body .is-empty { color: #8a939d; }
    .svc-modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px 22px;
        border-top: 1px solid #f0f0f0;
    }
    .svc-modal-foot .svc-buy-btn { width: auto; min-width: 160px; }
    .svc-btn-secondary {
        border: 1.5px solid #2c6ecb;
        border-radius: 10px;
        background: transparent;
        color: #2c6ecb;
        padding: 10px 22px;
        font-weight: 600;
        cursor: pointer;
    }
    .svc-btn-secondary:hover { background: #f0f6ff; }
    @media (max-width: 575px) {
        .svc-overlay { padding: 0; align-items: flex-end; }
        .svc-modal { max-height: 92vh; border-radius: 18px 18px 0 0; }
        .svc-modal-head { padding: 20px 56px 16px 18px; gap: 14px; }
        .svc-modal-thumb { width: 64px; height: 64px; font-size: 26px; }
        .svc-modal-head h3 { font-size: 1.2rem; }
        .svc-modal-body { padding: 18px; }
        .svc-modal-foot { padding: 14px 18px 18px; flex-direction: column-reverse; }
        .svc-modal-foot .svc-buy-btn, .svc-modal-foot .svc-btn-secondary { width: 100%; }
    }
    body.svc-modal-open { overflow: hidden; }

    .svc-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        max-width: calc(100% - 40px);
        background: #2d4156;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 100001;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    @media (max-width: 575px) { .svc-toast { left: 20px; right: 20px; bottom: 20px; } }

    button {
        outline: none !important;
    }
</style>

<section class="services-hero">
    <h1>Our Services</h1>
    <p>Professional services from the WebHut team to help you launch, run and grow your community.</p>
</section>

<div class="services-section">
    <?php if ($services) { ?>
        <div class="services-grid">
            <?php foreach ($services as $service) {
                $service_id = (int) $service['id'];
                $title = (string) $service['title'];
                $image_url = services_image_url($conn, $service['image']);
                $price_label = services_format_price($service['price'], $service['price_type']);
                $price_class = services_has_price($service['price']) ? 'svc-price' : 'svc-price is-on-request';
                $description_html = services_render_description($service['description']);
                ?>
                <div class="svc-card"
                     data-service-id="<?php echo $service_id; ?>"
                     data-service-slug="<?php echo htmlspecialchars($service['slug']); ?>"
                     data-service-title="<?php echo htmlspecialchars($title); ?>"
                     data-service-price="<?php echo htmlspecialchars($price_label); ?>"
                     data-service-price-class="<?php echo $price_class; ?>"
                     data-service-image="<?php echo htmlspecialchars($image_url); ?>">
                    <div class="svc-card-head">
                        <?php echo services_thumb_html($image_url, $title, 'svc-card-thumb'); ?>
                        <div class="svc-card-title">
                            <h2><?php echo htmlspecialchars($title); ?></h2>
                            <div class="<?php echo $price_class; ?>"><?php echo htmlspecialchars($price_label); ?></div>
                        </div>
                    </div>

                    <div class="svc-card-desc">
                        <?php echo $service['description']; ?>
                    </div>

                    <button type="button" class="svc-buy-btn" onclick="window.location.href='/store-admin/index.php/Frontend_services/checkout/<?php echo $service_id; ?>'">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>Buy Now
                    </button>

                    <template id="svc-desc-<?php echo $service_id; ?>"><?php echo $description_html !== '' ? $description_html : '<p class="is-empty">No further details have been added for this service yet.</p>'; ?></template>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="services-empty">
            <h3>No services are available right now.</h3>
            <p>Please check back soon, or <a href="contact.php">contact us</a> to ask about what we can do for you.</p>
        </div>
    <?php } ?>
</div>

<!-- Service details popup -->
<div class="svc-overlay" id="svcOverlay" aria-hidden="true">
    <div class="svc-modal" role="dialog" aria-modal="true" aria-labelledby="svcModalTitle">
        <button type="button" class="svc-modal-close" id="svcModalClose" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
        <div class="svc-modal-head">
            <div class="service-thumb svc-modal-thumb" id="svcModalThumb"><i class="fa <?php echo SERVICES_DEFAULT_ICON; ?>" aria-hidden="true"></i></div>
            <div>
                <h3 id="svcModalTitle"></h3>
                <div class="svc-price" id="svcModalPrice"></div>
            </div>
        </div>
        <div class="svc-modal-body" id="svcModalBody"></div>
        <div class="svc-modal-foot">
            <button type="button" class="svc-btn-secondary" id="svcModalCancel">Close</button>
            <button type="button" class="svc-buy-btn" id="svcModalBuy"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Buy Now</button>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('svcOverlay');
    var thumb = document.getElementById('svcModalThumb');
    var activeCard = null;
    var lastFocus = null;

    function openServiceModal(card) {
        activeCard = card;
        lastFocus = document.activeElement;

        document.getElementById('svcModalTitle').textContent = card.getAttribute('data-service-title');
        var price = document.getElementById('svcModalPrice');
        price.textContent = card.getAttribute('data-service-price');
        price.className = card.getAttribute('data-service-price-class');

        var oldImg = thumb.querySelector('img');
        if (oldImg) oldImg.remove();
        thumb.classList.remove('has-image');
        var imageUrl = card.getAttribute('data-service-image');
        if (imageUrl) {
            var img = document.createElement('img');
            img.src = imageUrl;
            img.alt = card.getAttribute('data-service-title');
            img.onerror = function () { thumb.classList.remove('has-image'); img.remove(); };
            thumb.insertBefore(img, thumb.firstChild);
            thumb.classList.add('has-image');
        }

        //full description, already sanitized on the server
        var body = document.getElementById('svcModalBody');
        body.innerHTML = '';
        var tpl = document.getElementById('svc-desc-' + card.getAttribute('data-service-id'));
        if (tpl) body.appendChild(tpl.content.cloneNode(true));
        body.scrollTop = 0;

        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('svc-modal-open');
        document.getElementById('svcModalClose').focus();
    }

    function closeServiceModal() {
        if (!overlay.classList.contains('open')) return;
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('svc-modal-open');
        activeCard = null;
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    //Buy Now is a placeholder until service ordering is built; wire the real flow here
    function servicesBuyNow(card) {
        var name = card ? card.getAttribute('data-service-title') : 'this service';
        var old = document.querySelector('.svc-toast');
        if (old) old.remove();
        var toast = document.createElement('div');
        toast.className = 'svc-toast';
        toast.setAttribute('role', 'status');
        toast.textContent = 'Online ordering for "' + name + '" is coming soon.';
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 3500);
    }

    document.addEventListener('click', function (e) {
        var openBtn = e.target.closest('[data-service-open]');
        if (openBtn) {
            openServiceModal(openBtn.closest('.svc-card'));
            return;
        }
        var buyBtn = e.target.closest('[data-service-buy]');
        if (buyBtn) {
            servicesBuyNow(buyBtn.closest('.svc-card'));
        }
    });

    document.getElementById('svcModalBuy').addEventListener('click', function () { servicesBuyNow(activeCard); });
    document.getElementById('svcModalClose').addEventListener('click', closeServiceModal);
    document.getElementById('svcModalCancel').addEventListener('click', closeServiceModal);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeServiceModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeServiceModal(); });
})();
</script>

<?php require './footer.php'; ?>
