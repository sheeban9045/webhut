<?php require './Config.php'; ?>
<?php require_once './services_helper.php'; ?>
<?php require './header.php'; ?>

<style>
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
        width: 70%;
        margin: 1.5rem auto 0;
        color: #ffffff;
        font-size: 1.25rem;
        position: relative;
    }

    .services-section {
        background: #f4f6fb;
        padding: 50px 40px 60px;
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, 350px);
        justify-content: center;
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }
    @media (max-width: 1200px) {
        .services-grid { grid-template-columns: repeat(2, 350px); }
    }
    @media (max-width: 768px) {
        .services-grid { grid-template-columns: 1fr; }
        .service-card { max-width: 350px; margin: auto; }
    }

    .service-card {
        background: #fff;
        border-radius: 16px;
        padding: 32px 28px;
        box-shadow: 0 4px 18px rgba(20, 30, 60, 0.07);
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 30px rgba(20, 30, 60, 0.12);
    }
    .service-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: rgba(43, 132, 209, 0.1);
        color: #2b84d1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 20px;
    }
    .service-card h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e2321;
        margin-bottom: 10px;
    }
    .service-card p.service-desc {
        color: #6b6a6a;
        font-size: 0.95rem;
        line-height: 1.6;
        flex-grow: 1;
    }
    .service-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2b84d1;
        margin: 16px 0;
    }
    .service-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .service-actions .btn {
        flex: 1;
        white-space: nowrap;
    }

    .services-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b6a6a;
        max-width: 1400px;
        margin: 0 auto;
    }
</style>

<section class="services-hero">
    <h1>Our Services</h1>
    <p>Explore the services our team offers to help you launch, run and grow your community.</p>
</section>

<div class="services-section">
    <?php
    $services_result = services_get_active($conn);
    $services = $services_result ? mysqli_fetch_all($services_result, MYSQLI_ASSOC) : array();
    ?>
    <?php if ($services) { ?>
        <div class="services-grid">
            <?php foreach ($services as $service) { ?>
                <div class="service-card">
                    <div class="service-icon"><i class="fa <?php echo htmlspecialchars(services_icon_class($service['icon'])); ?>"></i></div>
                    <h2><?php echo htmlspecialchars($service['title']); ?></h2>
                    <p class="service-desc"><?php echo services_excerpt($service['short_description']); ?></p>
                    <div class="service-price"><?php echo htmlspecialchars(services_format_price($service['price'], $service['price_type'])); ?></div>
                    <div class="service-actions">
                        <a href="<?php echo htmlspecialchars(services_detail_url($service['slug'])); ?>" class="btn btn-outline-primary">Learn More</a>
                        <a href="<?php echo htmlspecialchars(services_contact_url($service['slug'])); ?>" class="btn btn-primary">Get a Quote</a>
                    </div>
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

<?php require './footer.php'; ?>
