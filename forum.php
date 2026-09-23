<?php
require_once './forum_helper.php';

$categories = forum_get_categories();
$stats = forum_get_stats();
$latestTopics = forum_get_topics('', '', array(), 6);

$heroExtra = forum_render_search_box()
    . '<ul class="forum-hero-stats">'
    . '<li><strong>' . (int) $stats['total_categories'] . '</strong> Categories</li>'
    . '<li><strong>' . (int) $stats['total_topics'] . '</strong> Topics</li>'
    . '<li><strong>' . (int) $stats['total_replies'] . '</strong> Replies</li>'
    . '</ul>';

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('Webhut Community Forum', 'Ask questions, share ideas and learn from other Webhut users.', array(), $heroExtra, 'forum-hero-home'); ?>
    <?php echo forum_render_subnav('home'); ?>

    <div class="container forum-body">
        <?php echo forum_render_flash(); ?>

        <div class="forum-section-head">
            <div>
                <h2 class="forum-section-title">Browse Categories</h2>
                <p class="forum-section-subtitle">Find the right place for your question or idea.</p>
            </div>
        </div>

        <?php if ($categories) { ?>
            <div class="row forum-category-grid">
                <?php foreach ($categories as $category) { ?>
                    <div class="col-lg-4 col-md-6">
                        <a class="forum-category-card" href="<?php echo forum_category_url($category['id']); ?>" style="--forum-cat-color: <?php echo forum_category_color($category['id']); ?>">
                            <div class="forum-category-head">
                                <span class="forum-category-icon"><i class="fa <?php echo forum_category_icon($category['title']); ?>"></i></span>
                                <h3 class="forum-category-name"><?php echo forum_e($category['title']); ?></h3>
                            </div>
                            <p class="forum-category-desc"><?php echo $category['description'] ? forum_e($category['description']) : 'Discussions about ' . forum_e($category['title']) . '.'; ?></p>
                            <div class="forum-category-foot">
                                <div class="forum-category-counts">
                                    <span><strong><?php echo (int) $category['total_topics']; ?></strong><?php echo (int) $category['total_topics'] === 1 ? 'Topic' : 'Topics'; ?></span>
                                    <span><strong><?php echo (int) $category['total_replies']; ?></strong><?php echo (int) $category['total_replies'] === 1 ? 'Reply' : 'Replies'; ?></span>
                                </div>
                                <i class="fa fa-arrow-right forum-category-arrow"></i>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="forum-card mb-4"><?php echo forum_render_empty('fa-folder-open-o', 'No categories yet', 'The forum categories will appear here soon.'); ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="forum-card forum-topic-list">
                    <div class="forum-topic-list-head">
                        <h2><i class="fa fa-bolt text-primary"></i> Latest Discussions</h2>
                        <span>Recently active topics</span>
                    </div>
                    <?php
                    if ($latestTopics) {
                        foreach ($latestTopics as $topic) {
                            echo forum_render_topic_item($topic);
                        }
                    } else {
                        echo forum_render_empty('fa-comments-o', 'No discussions yet', 'Open a category to see its topics.');
                    }
                    ?>
                </div>
            </div>

            <div class="col-lg-4 forum-sidebar">
                <?php echo forum_render_member_card(); ?>

                <div class="forum-card">
                    <div class="forum-card-body">
                        <h3 class="forum-card-title"><i class="fa fa-shield text-primary"></i> Community Guidelines</h3>
                        <ul class="forum-tips">
                            <li>Search before posting, your question may already be answered.</li>
                            <li>Pick the category that fits your topic best.</li>
                            <li>Be respectful and keep discussions on topic.</li>
                            <li>Never share passwords or private account details.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
