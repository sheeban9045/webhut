<?php
require_once './forum_helper.php';
forum_require_login();

$userId = forum_user_id();
$filter = ' AND t.created_by = ?';
$totalTopics = forum_count_topics($filter, 'i', array($userId));
list($page, $offset) = forum_paginate($totalTopics);
$topics = forum_get_topics($filter, 'i', array($userId), FORUM_PER_PAGE, $offset);

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('My Topics', 'The discussions you have started in the community.', array(array('Forum', 'forum.php'), array('My Topics', null)), '', 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav('my-topics', forum_new_topic_button()); ?>

    <div class="container forum-body">
        <?php echo forum_render_flash(); ?>
        <div class="forum-card forum-topic-list">
            <div class="forum-topic-list-head">
                <h2>Your topics</h2>
                <?php if ($totalTopics) { ?>
                    <span>Showing <?php echo $offset + 1; ?>&ndash;<?php echo $offset + count($topics); ?> of <?php echo $totalTopics; ?></span>
                <?php } ?>
            </div>
            <?php
            if ($topics) {
                foreach ($topics as $topic) {
                    echo forum_render_topic_item($topic, array('show_author' => false));
                }
            } else {
                echo forum_render_empty('fa-file-text-o', 'You have not started any topics yet', 'Ask a question or share an idea with the community.', forum_new_topic_button());
            }
            ?>
            <?php $pagination = forum_pagination($totalTopics, $page, FORUM_PER_PAGE); ?>
            <?php if ($pagination) { ?>
                <div class="forum-list-footer"><?php echo $pagination; ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
