<?php require './Config.php'; ?>
<?php require './header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; margin: 0; padding: 0;font-family: "roboto", sans-serif; }

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
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 28px;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── Card ── */
.plugin-card {
    background: #fff;
    border: 1px solid #e6e9f4;
    border-radius: 14px;
    padding: 32px 24px 28px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    position: relative;
    overflow: hidden;
}

.plugin-card:hover {
    box-shadow: 0 12px 36px rgba(61, 63, 204, 0.14);
    transform: translateY(-4px);
}

/* Subtle bottom-right blob like in screenshot */
.plugin-card::after {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(74, 79, 224, 0.07);
    bottom: -30px;
    right: -20px;
}

/* ── Icon circle ── */
.plugin-icon-wrap {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 2px dashed #b8bdf5;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f2ff;
    margin-bottom: 6px;
}

.plugin-icon-wrap img {
    width: 56px;
    height: 56px;
    object-fit: contain;
}

/* ── Name ── */
.plugin-name {
    font-size: 28px;
    font-weight: 600;
    color: #1a1d3b;
    line-height: 1.4;
}

/* ── Price ── */
.plugin-price-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.plugin-price-now {
    font-size: 20px;
    font-weight: 800;
    color: #2b84d1;
}

.plugin-price-old {
    font-size: 14px;
    color: #aaa;
    text-decoration: line-through;
}

.plugin-discount-badge {
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
}

/* ── Button ── */
.plugin-btn {
    display: inline-block;
    margin-top: 6px;
    background: #2b84d1;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 10px 28px;
    border-radius: 8px;
    text-decoration: none;
    letter-spacing: 0.3px;
    transition: background 0.2s ease;
    position: relative;
    z-index: 1;
}

.plugin-btn:hover {
    background: #1e2290;
    color: #fff;
}
</style>

<!-- Hero Banner -->
<div class="plugins-hero">
    <h1>Our Plugins</h1>
    <p>Upgrade your <strong>website builder software</strong> with our <strong>Plugins</strong> — crafted to enhance, increase and extend its functionality.</p>
</div>

<!-- Plugin Cards -->
<div class="plugins-section">
    <div class="plugins-grid">
    <?php
        $sql = 'SELECT * FROM crm_webhut_plugins WHERE status = "active" AND deleted = 0';
        $result = mysqli_query($conn, $sql);

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
        <div class="plugin-card">
            <div class="plugin-icon-wrap">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            </div>

            <div class="plugin-name"><?php echo htmlspecialchars($row['name']); ?></div>

            <div class="plugin-price-row">
                <span class="plugin-price-now">$<?php echo number_format($price, 2); ?></span>
                <?php if ($discount_amount > 0): ?>
                    <span class="plugin-price-old">$<?php echo number_format($old_price, 2); ?></span>
                    <span class="plugin-discount-badge"><?php echo htmlspecialchars($discount_label); ?></span>
                <?php endif; ?>
            </div>

            <a href="plugin-details.php?id=<?php echo (int)$row['id']; ?>" class="plugin-btn">
                Learn More
            </a>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<?php require './footer.php'; ?>