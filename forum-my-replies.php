<?php
require_once './forum_helper.php';
forum_require_login();

$userId = forum_user_id();
$totalReplies = forum_count_user_replies($userId);
list($page, $offset) = forum_paginate($totalReplies);
$replies = forum_get_user_replies($userId, FORUM_PER_PAGE, $offset);

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('My Replies', 'Everything you have contributed to community discussions.', array(array('Forum', 'forum.php'), array('My Replies', null)), '', 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav('my-replies'); ?>

    <div class="container forum-body">
        <?php echo forum_render_flash(); ?>
        <div class="forum-card forum-topic-list">
            <div class="forum-topic-list-head">
                <h2>Your replies</h2>
                <?php if ($totalReplies) { ?>
                    <span>Showing <?php echo $offset + 1; ?>&ndash;<?php echo $offset + count($replies); ?> of <?php echo $totalReplies; ?></span>
                <?php } ?>
            </div>
            <?php if ($replies) { ?>
                <?php foreach ($replies as $reply) { ?>
                    <a class="forum-myreply" href="<?php echo forum_topic_url($reply['topic_id'], $reply['id']); ?>">
                        <p class="forum-myreply-text"><?php echo forum_text_excerpt($reply['description']); ?></p>
                        <div class="forum-topic-meta">
                            <span>on <span class="forum-myreply-topic"><?php echo forum_e($reply['topic_title']); ?></span></span>
                            <span class="forum-badge" style="--forum-badge-color: <?php echo forum_category_color($reply['category_id']); ?>"><?php echo forum_e($reply['category_title']); ?></span>
                            <span><i class="fa fa-clock-o"></i> <?php echo forum_time_html($reply['created_at']); ?></span>
                        </div>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <?php echo forum_render_empty('fa-comments-o', 'You have not replied to any topics yet', 'Browse the latest discussions and share your thoughts.', '<a href="forum.php" class="btn btn-primary forum-btn"><i class="fa fa-compass"></i> Explore the Forum</a>'); ?>
            <?php } ?>
            <?php $pagination = forum_pagination($totalReplies, $page, FORUM_PER_PAGE); ?>
            <?php if ($pagination) { ?>
                <div class="forum-list-footer"><?php echo $pagination; ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
