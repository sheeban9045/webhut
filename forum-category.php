<?php
require_once './forum_helper.php';

$categoryId = forum_get_int('id');
$category = $categoryId ? forum_get_categories($categoryId) : null;

if ($category) {
    $filter = ' AND t.category_id = ?';
    $totalTopics = forum_count_topics($filter, 'i', array($categoryId));
    list($page, $offset) = forum_paginate($totalTopics);
    $topics = forum_get_topics($filter, 'i', array($categoryId), FORUM_PER_PAGE, $offset);
    $allCategories = forum_get_categories();
} else {
    http_response_code(404);
}

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php if (!$category) { ?>
        <?php echo forum_render_hero('Category not found', '', array(array('Forum', 'forum.php'), array('Not found', null)), '', 'forum-hero-compact'); ?>
        <?php echo forum_render_subnav(''); ?>
        <div class="container forum-body">
            <div class="forum-card">
                <?php echo forum_render_empty('fa-folder-open-o', 'This category is not available', 'It may have been removed or the link is incorrect.', '<a href="forum.php" class="btn btn-primary forum-btn"><i class="fa fa-arrow-left"></i> Back to Forum</a>'); ?>
            </div>
        </div>
    <?php } else { ?>
        <?php
        $heroMeta = '<div class="forum-hero-meta">'
            . '<span><i class="fa fa-file-text-o"></i>' . (int) $category['total_topics'] . ' ' . ((int) $category['total_topics'] === 1 ? 'topic' : 'topics') . '</span>'
            . '<span><i class="fa fa-comments-o"></i>' . (int) $category['total_replies'] . ' ' . ((int) $category['total_replies'] === 1 ? 'reply' : 'replies') . '</span>'
            . ($category['last_activity_at'] ? '<span><i class="fa fa-clock-o"></i>Active ' . forum_time_html($category['last_activity_at']) . '</span>' : '')
            . '</div>';
        echo forum_render_hero($category['title'], (string) $category['description'], array(array('Forum', 'forum.php'), array($category['title'], null)), $heroMeta, 'forum-hero-compact');
        echo forum_render_subnav('', forum_new_topic_button($categoryId));
        ?>

        <div class="container forum-body">
            <?php echo forum_render_flash(); ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="forum-card forum-topic-list">
                        <div class="forum-topic-list-head">
                            <h2>Topics</h2>
                            <?php if ($totalTopics) { ?>
                                <span>Showing <?php echo $offset + 1; ?>&ndash;<?php echo $offset + count($topics); ?> of <?php echo $totalTopics; ?></span>
                            <?php } ?>
                        </div>
                        <?php
                        if ($topics) {
                            foreach ($topics as $topic) {
                                echo forum_render_topic_item($topic, array('show_category' => false));
                            }
                        } else {
                            echo forum_render_empty('fa-comments-o', 'No topics yet', 'Be the first to start a discussion in this category.', forum_new_topic_button($categoryId));
                        }
                        ?>
                        <?php $pagination = forum_pagination($totalTopics, $page, FORUM_PER_PAGE, array('id' => $categoryId)); ?>
                        <?php if ($pagination) { ?>
                            <div class="forum-list-footer"><?php echo $pagination; ?></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-lg-4 forum-sidebar">
                    <div class="forum-card">
                        <div class="forum-card-body">
                            <h3 class="forum-card-title">Categories</h3>
                            <ul class="forum-side-list">
                                <?php foreach ($allCategories as $item) { ?>
                                    <li>
                                        <a href="<?php echo forum_category_url($item['id']); ?>" class="<?php echo (int) $item['id'] === $categoryId ? 'active' : ''; ?>">
                                            <span><span class="forum-side-dot" style="background: <?php echo forum_category_color($item['id']); ?>"></span><?php echo forum_e($item['title']); ?></span>
                                            <span class="forum-side-count"><?php echo (int) $item['total_topics']; ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php echo forum_render_member_card(); ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
