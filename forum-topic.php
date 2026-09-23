<?php
require_once './forum_helper.php';

$topicId = forum_get_int('id');
$topic = $topicId ? forum_get_topic($topicId) : null;
$currentUser = forum_current_user();
$currentUserId = forum_user_id();

if ($topic) {
    $replies = forum_get_topic_replies($topicId);
    $likeInfo = forum_get_like_info($topicId, $currentUserId);
    $moreTopics = forum_get_topics(' AND t.category_id = ? AND t.id <> ?', 'ii', array((int) $topic['category_id'], $topicId), 5);
    $author = forum_author_name($topic['author_name']);
} else {
    http_response_code(404);
}

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php if (!$topic) { ?>
        <?php echo forum_render_hero('Topic not found', '', array(array('Forum', 'forum.php'), array('Not found', null)), '', 'forum-hero-compact'); ?>
        <?php echo forum_render_subnav(''); ?>
        <div class="container forum-body">
            <div class="forum-card">
                <?php echo forum_render_empty('fa-comments-o', 'This topic is not available', 'It may have been removed or the link is incorrect.', '<a href="forum.php" class="btn btn-primary forum-btn"><i class="fa fa-arrow-left"></i> Back to Forum</a>'); ?>
            </div>
        </div>
    <?php } else { ?>
        <?php
        $heroMeta = '<div class="forum-hero-meta">'
            . '<span><i class="fa fa-user-o"></i>' . forum_e($author) . '</span>'
            . '<span><i class="fa fa-calendar-o"></i>' . forum_e(forum_format_date($topic['created_at'], 'M j, Y')) . '</span>'
            . '<span><i class="fa fa-comments-o"></i><span class="js-forum-reply-count">' . count($replies) . '</span>&nbsp;replies</span>'
            . '</div>';
        echo forum_render_hero($topic['title'], '', array(array('Forum', 'forum.php'), array($topic['category_title'], forum_category_url($topic['category_id'])), array('Topic', null)), $heroMeta, 'forum-hero-compact');
        echo forum_render_subnav('', '<a href="' . forum_category_url($topic['category_id']) . '" class="btn btn-light forum-btn"><i class="fa fa-arrow-left"></i> ' . forum_e($topic['category_title']) . '</a>');
        ?>

        <div class="container forum-body">
            <?php echo forum_render_flash(); ?>
            <div class="row">
                <div class="col-lg-8">
                    <article class="forum-card forum-post">
                        <div class="forum-post-head">
                            <?php echo forum_avatar($author, 'lg'); ?>
                            <div>
                                <span class="forum-post-author"><?php echo forum_e($author); ?> <?php if ($currentUserId && (int) $topic['created_by'] === $currentUserId) { ?><span class="forum-tag forum-tag-you">You</span><?php } ?></span>
                                <span class="forum-post-date">Posted <?php echo forum_time_html($topic['created_at']); ?> &middot; <?php echo forum_e(forum_format_date($topic['created_at'])); ?></span>
                            </div>
                        </div>

                        <div class="forum-content">
                            <?php echo forum_render_description($topic['description']); ?>
                        </div>

                        <div class="forum-post-foot">
                            <?php echo forum_render_like_button($topicId, $likeInfo); ?>
                            <div class="forum-post-foot-meta">
                                <span><i class="fa fa-comments-o"></i><span class="js-forum-reply-count"><?php echo count($replies); ?></span> replies</span>
                                <span><i class="fa fa-clock-o"></i>Last activity <?php echo forum_time_html($topic['last_activity_at']); ?></span>
                            </div>
                        </div>
                    </article>

                    <div class="forum-replies-head">
                        <h2>Replies <span class="forum-replies-count js-forum-reply-count"><?php echo count($replies); ?></span></h2>
                    </div>

                    <div id="forum-reply-list">
                        <?php
                        foreach ($replies as $reply) {
                            echo forum_render_reply($reply, $currentUserId, $topic['created_by']);
                        }
                        ?>
                    </div>
                    <div class="forum-card js-forum-no-replies mb-3" <?php echo $replies ? 'style="display:none"' : ''; ?>>
                        <?php echo forum_render_empty('fa-comment-o', 'No replies yet', 'Be the first to share your thoughts on this topic.'); ?>
                    </div>

                    <?php if ($currentUser) { ?>
                        <div class="forum-card">
                            <form id="forum-reply-form" class="forum-reply-form" data-topic-id="<?php echo $topicId; ?>" novalidate>
                                <?php echo forum_avatar($currentUser['full_name']); ?>
                                <div class="forum-reply-form-main">
                                    <label for="forum-reply-text" class="sr-only">Write a reply</label>
                                    <textarea id="forum-reply-text" name="description" class="form-control forum-textarea" placeholder="Write a reply..." maxlength="<?php echo FORUM_REPLY_MAX_LENGTH; ?>" required></textarea>
                                    <div class="forum-reply-form-foot">
                                        <span class="forum-char-count js-forum-char-count"></span>
                                        <button type="submit" class="btn btn-primary forum-btn"><i class="fa fa-paper-plane"></i> Post Reply</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="forum-card forum-login-prompt">
                            <h3>Join the discussion</h3>
                            <p>Log in to reply to this topic and like helpful posts.</p>
                            <a href="<?php echo FORUM_LOGIN_URL; ?>" class="btn btn-primary forum-btn forum-login-btn"><i class="fa fa-sign-in"></i> Log in</a>
                            <a href="<?php echo FORUM_REGISTER_URL; ?>" class="btn btn-outline-primary forum-btn">Register</a>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-lg-4 forum-sidebar">
                    <div class="forum-card">
                        <div class="forum-card-body">
                            <h3 class="forum-card-title">About this topic</h3>
                            <ul class="forum-side-facts">
                                <li><span>Category</span><strong><a class="forum-badge" href="<?php echo forum_category_url($topic['category_id']); ?>" style="--forum-badge-color: <?php echo forum_category_color($topic['category_id']); ?>"><?php echo forum_e($topic['category_title']); ?></a></strong></li>
                                <li><span>Started by</span><strong><?php echo forum_e($author); ?></strong></li>
                                <li><span>Created</span><strong><?php echo forum_e(forum_format_date($topic['created_at'], 'M j, Y')); ?></strong></li>
                                <li><span>Last activity</span><strong><?php echo forum_time_html($topic['last_activity_at']); ?></strong></li>
                                <li><span>Replies</span><strong class="js-forum-reply-count"><?php echo count($replies); ?></strong></li>
                                <li><span>Likes</span><strong class="js-forum-like-total"><?php echo $likeInfo['total_likes']; ?></strong></li>
                            </ul>
                        </div>
                    </div>

                    <?php if ($moreTopics) { ?>
                        <div class="forum-card">
                            <div class="forum-card-body">
                                <h3 class="forum-card-title">More in <?php echo forum_e($topic['category_title']); ?></h3>
                                <ul class="forum-side-list">
                                    <?php foreach ($moreTopics as $item) { ?>
                                        <li>
                                            <a href="<?php echo forum_topic_url($item['id']); ?>">
                                                <span><?php echo forum_e($item['title']); ?></span>
                                                <span class="forum-side-count"><i class="fa fa-comment-o"></i> <?php echo (int) $item['total_replies']; ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!$currentUser) { echo forum_render_member_card(); } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
