<?php require './Config.php'; ?>
<?php require_once './services_helper.php'; ?>
<?php
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$service = $slug !== '' ? services_get_by_slug($conn, $slug) : null;

if (!$service) {
    header('Location: services.php');
    exit;
}
?>
<?php require './header.php'; ?>

<style>
    .service-detail-hero {
        background: linear-gradient(135deg, #2b84d1 0%, #2b84d1 50%, #4a4fe0 100%);
        padding: 60px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .service-detail-hero::before,
    .service-detail-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .service-detail-hero::before { width: 300px; height: 300px; top: -80px; left: -80px; }
    .service-detail-hero::after  { width: 240px; height: 240px; bottom: -80px; right: -60px; }

    .service-detail-breadcrumb {
        position: relative;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .service-detail-breadcrumb a { color: rgba(255,255,255,0.85); }
    .service-detail-breadcrumb a:hover { color: #fff; }
    .service-detail-breadcrumb span { color: rgba(255,255,255,0.6); margin: 0 6px; }
    .service-detail-breadcrumb strong { color: #fff; }

    .service-thumb.service-detail-thumb {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: 3px solid rgba(255,255,255,0.35);
        color: #fff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
    }
    .service-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .service-thumb.has-image i { display: none; }
    .service-detail-hero h1 {
        position: relative;
        color: #fff;
        font-weight: 500;
        font-size: 2.4rem;
        margin-bottom: 14px;
    }
    .service-detail-price {
        position: relative;
        display: inline-block;
        background: rgba(255,255,255,0.15);
        color: #fff;
        font-weight: 700;
        font-size: 1.15rem;
        padding: 8px 22px;
        border-radius: 30px;
    }

    .service-detail-body {
        max-width: 900px;
        margin: 0 auto;
        padding: 50px 20px 70px;
    }
    .service-detail-content {
        background: #fff;
        border-radius: 16px;
        padding: 36px;
        box-shadow: 0 4px 18px rgba(20, 30, 60, 0.07);
        color: #4f5660;
        font-size: 1rem;
        line-height: 1.8;
        word-wrap: break-word;
    }
    .service-detail-content img { max-width: 100%; height: auto; border-radius: 8px; }
    .service-detail-content.is-empty { color: #8a939d; }

    .service-detail-cta {
        text-align: center;
        margin-top: 36px;
    }
    .service-detail-cta .btn {
        margin: 6px;
        padding: 14px 32px;
    }
</style>

<section class="service-detail-hero">
    <div class="container">
        <div class="service-detail-breadcrumb">
            <a href="services.php">Services</a>
            <span>/</span>
            <strong><?php echo htmlspecialchars($service['title']); ?></strong>
        </div>
        <?php echo services_thumb_html(services_image_url($conn, $service['image']), $service['title'], 'service-detail-thumb'); ?>
        <h1><?php echo htmlspecialchars($service['title']); ?></h1>
        <div class="service-detail-price"><?php echo htmlspecialchars(services_format_price($service['price'], $service['price_type'])); ?></div>
    </div>
</section>

<div class="service-detail-body">
    <?php $description_html = services_render_description($service['description']); ?>
    <div class="service-detail-content<?php echo $description_html === '' ? ' is-empty' : ''; ?>">
        <?php echo $description_html !== '' ? $description_html : 'No further details have been added for this service yet.'; ?>
    </div>
    <div class="service-detail-cta">
        <a href="<?php echo htmlspecialchars(services_contact_url($service['slug'])); ?>" class="btn btn-xl btn-primary">Contact Us About This Service</a>
        <a href="services.php" class="btn btn-xl btn-outline-primary">Back to Services</a>
    </div>
</div>

<?php require './footer.php'; ?>
