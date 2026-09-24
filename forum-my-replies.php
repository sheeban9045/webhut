<?php
require_once './forum_helper.php';
forum_require_login();

$userId = forum_user_id();
$categories = forum_get_categories();

$filters = array(
    'category_id' => forum_get_int('category_id'),
    'title' => forum_get_string('title'),
    'description' => forum_get_string('description'),
    'author' => forum_get_string('author'),
    'date_from' => forum_get_string('date_from'),
    'date_to' => forum_get_string('date_to'),
);
$hasFilters = $filters['category_id'] || $filters['title'] !== '' || $filters['description'] !== '' || $filters['author'] !== '' || $filters['date_from'] !== '' || $filters['date_to'] !== '';

//own replies only, filters are appended on top of this fixed condition
$filterSql = '';
$types = '';
$params = array();
forum_filter_add_equals_int($filterSql, $types, $params, 't.category_id', $filters['category_id']);
forum_filter_add_like($filterSql, $types, $params, 't.title', $filters['title']);
forum_filter_add_like($filterSql, $types, $params, 'r.description', $filters['description']);
forum_filter_add_like($filterSql, $types, $params, forum_author_sql('tu'), $filters['author']);
forum_filter_add_date_range($filterSql, $types, $params, 'r.created_at', $filters['date_from'], $filters['date_to']);

$totalReplies = forum_count_user_replies($userId, $filterSql, $types, $params);
list($page, $offset) = forum_paginate($totalReplies);
$replies = forum_get_user_replies($userId, $filterSql, $types, $params, FORUM_PER_PAGE, $offset);

//pagination links must keep the selected filters
$paginationParams = forum_filter_query_params($filters);

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('My Replies', 'Everything you have contributed to community discussions.', array(array('Forum', 'forum.php'), array('My Replies', null)), '', 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav('my-replies'); ?>

    <div class="container forum-body">
        <?php echo forum_render_flash(); ?>

        <div class="forum-card forum-filter-card">
            <div class="forum-card-body">
                <form method="get" action="forum-my-replies.php" class="forum-filter-form" role="search">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="filter-category">Category</label>
                            <select id="filter-category" name="category_id" class="form-control">
                                <option value="">All categories</option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo (int) $category['id']; ?>" <?php echo $filters['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>><?php echo forum_e($category['title']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="filter-title">Topic name</label>
                            <input type="text" id="filter-title" name="title" class="form-control" placeholder="Search by topic title" maxlength="255" value="<?php echo forum_e($filters['title']); ?>">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="filter-author">Topic author</label>
                            <input type="text" id="filter-author" name="author" class="form-control" placeholder="Author of the topic" maxlength="100" value="<?php echo forum_e($filters['author']); ?>">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="filter-description">Reply content</label>
                            <input type="text" id="filter-description" name="description" class="form-control" placeholder="Search within your replies" maxlength="255" value="<?php echo forum_e($filters['description']); ?>">
                        </div>
                        <div class="form-group col-6 col-md-3 col-lg-4">
                            <label for="filter-date-from">From date</label>
                            <input type="date" id="filter-date-from" name="date_from" class="form-control" value="<?php echo forum_e($filters['date_from']); ?>">
                        </div>
                        <div class="form-group col-6 col-md-3 col-lg-4">
                            <label for="filter-date-to">To date</label>
                            <input type="date" id="filter-date-to" name="date_to" class="form-control" value="<?php echo forum_e($filters['date_to']); ?>">
                        </div>
                    </div>
                    <div class="forum-filter-actions">
                        <button type="submit" class="btn btn-primary forum-btn"><i class="fa fa-filter"></i> Apply Filters</button>
                        <?php if ($hasFilters) { ?>
                            <a href="forum-my-replies.php" class="btn btn-light forum-btn"><i class="fa fa-times"></i> Clear Filters</a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

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
                            <?php if ($reply['topic_author_name']) { ?>
                                <span><i class="fa fa-user-o"></i> <?php echo forum_e($reply['topic_author_name']); ?></span>
                            <?php } ?>
                            <span class="forum-badge" style="--forum-badge-color: <?php echo forum_category_color($reply['category_id']); ?>"><?php echo forum_e($reply['category_title']); ?></span>
                            <span><i class="fa fa-clock-o"></i> <?php echo forum_time_html($reply['created_at']); ?></span>
                        </div>
                    </a>
                <?php } ?>
            <?php } else if ($hasFilters) { ?>
                <?php echo forum_render_empty('fa-search', 'No replies match your filters', 'Try different filters or clear them to see all your replies.', '<a href="forum-my-replies.php" class="btn btn-primary forum-btn forum-login-btn"><i class="fa fa-times"></i> Clear Filters</a>'); ?>
            <?php } else { ?>
                <?php echo forum_render_empty('fa-comments-o', 'You have not replied to any topics yet', 'Browse the latest discussions and share your thoughts.', '<a href="forum.php" class="btn btn-primary forum-btn"><i class="fa fa-compass"></i> Explore the Forum</a>'); ?>
            <?php } ?>
            <?php $pagination = forum_pagination($totalReplies, $page, FORUM_PER_PAGE, $paginationParams); ?>
            <?php if ($pagination) { ?>
                <div class="forum-list-footer"><?php echo $pagination; ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
