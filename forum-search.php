<?php
require_once './forum_helper.php';

$search = mb_substr(forum_get_string('q'), 0, 100);
$totalTopics = 0;
$topics = array();
$page = 1;
$offset = 0;

if ($search !== '') {
    $term = forum_like_term($search);
    $filter = ' AND (t.title LIKE ? OR t.description LIKE ?)';
    $totalTopics = forum_count_topics($filter, 'ss', array($term, $term));
    list($page, $offset) = forum_paginate($totalTopics);
    $topics = forum_get_topics($filter, 'ss', array($term, $term), FORUM_PER_PAGE, $offset);
} else {
    $categories = forum_get_categories();
}

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('Search the Forum', 'Find answers and discussions from the Webhut community.', array(array('Forum', 'forum.php'), array('Search', null)), forum_render_search_box($search), 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav(''); ?>

    <div class="container forum-body">
        <?php if ($search === '') { ?>
            <div class="forum-card">
                <?php echo forum_render_empty('fa-search', 'What are you looking for?', 'Type a keyword above to search topic titles and descriptions, or browse a category below.'); ?>
            </div>
            <?php if ($categories) { ?>
                <div class="forum-section-head mt-4">
                    <h2 class="forum-section-title">Browse Categories</h2>
                </div>
                <div class="forum-card forum-topic-list">
                    <?php foreach ($categories as $category) { ?>
                        <div class="forum-topic-item">
                            <span class="forum-category-icon" style="--forum-cat-color: <?php echo forum_category_color($category['id']); ?>"><i class="fa <?php echo forum_category_icon($category['title']); ?>"></i></span>
                            <div class="forum-topic-main">
                                <a class="forum-topic-title" href="<?php echo forum_category_url($category['id']); ?>"><?php echo forum_e($category['title']); ?></a>
                                <div class="forum-topic-meta"><span><?php echo (int) $category['total_topics']; ?> topics</span></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="forum-card forum-topic-list">
                <div class="forum-topic-list-head">
                    <h2><?php echo $totalTopics; ?> <?php echo $totalTopics === 1 ? 'result' : 'results'; ?> for &ldquo;<?php echo forum_e($search); ?>&rdquo;</h2>
                    <?php if ($totalTopics) { ?>
                        <span>Showing <?php echo $offset + 1; ?>&ndash;<?php echo $offset + count($topics); ?></span>
                    <?php } ?>
                </div>
                <?php
                if ($topics) {
                    foreach ($topics as $topic) {
                        echo forum_render_topic_item($topic, array('show_excerpt' => true));
                    }
                } else {
                    echo forum_render_empty('fa-search', 'No topics found', 'Try different or fewer keywords, or browse the categories on the Forum Home.', '<a href="forum.php" class="btn btn-primary forum-btn"><i class="fa fa-home"></i> Forum Home</a>');
                }
                ?>
                <?php $pagination = forum_pagination($totalTopics, $page, FORUM_PER_PAGE, array('q' => $search)); ?>
                <?php if ($pagination) { ?>
                    <div class="forum-list-footer"><?php echo $pagination; ?></div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
