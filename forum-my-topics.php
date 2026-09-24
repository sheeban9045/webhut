<?php
require_once './forum_helper.php';
forum_require_login();

$userId = forum_user_id();
$categories = forum_get_categories();

$filters = array(
    'category_id' => forum_get_int('category_id'),
    'title' => forum_get_string('title'),
    'date_from' => forum_get_string('date_from'),
    'date_to' => forum_get_string('date_to'),
);
$hasFilters = $filters['category_id'] || $filters['title'] !== '' || $filters['date_from'] !== '' || $filters['date_to'] !== '';

//own topics only, filters are appended on top of this fixed condition
$filterSql = ' AND t.created_by = ?';
$types = 'i';
$params = array($userId);
forum_filter_add_equals_int($filterSql, $types, $params, 't.category_id', $filters['category_id']);
forum_filter_add_like($filterSql, $types, $params, 't.title', $filters['title']);
forum_filter_add_date_range($filterSql, $types, $params, 't.created_at', $filters['date_from'], $filters['date_to']);

$totalTopics = forum_count_topics($filterSql, $types, $params);
list($page, $offset) = forum_paginate($totalTopics);
$topics = forum_get_topics($filterSql, $types, $params, FORUM_PER_PAGE, $offset);

//pagination links must keep the selected filters
$paginationParams = forum_filter_query_params($filters);

require './header.php';
?>
<?php echo forum_render_styles(); ?>

<div class="forum-page">
    <?php echo forum_render_hero('My Topics', 'The discussions you have started in the community.', array(array('Forum', 'forum.php'), array('My Topics', null)), '', 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav('my-topics', forum_new_topic_button()); ?>

    <div class="container forum-body">
        <?php echo forum_render_flash(); ?>

        <div class="forum-card forum-filter-card">
            <div class="forum-card-body">
                <form method="get" action="forum-my-topics.php" class="forum-filter-form" role="search">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-3">
                            <label for="filter-category">Category</label>
                            <select id="filter-category" name="category_id" class="form-control">
                                <option value="">All categories</option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo (int) $category['id']; ?>" <?php echo $filters['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>><?php echo forum_e($category['title']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3">
                            <label for="filter-title">Topic name</label>
                            <input type="text" id="filter-title" name="title" class="form-control" placeholder="Search by title" maxlength="255" value="<?php echo forum_e($filters['title']); ?>">
                        </div>
                        <div class="form-group col-6 col-md-3 col-lg-3">
                            <label for="filter-date-from">From date</label>
                            <input type="date" id="filter-date-from" name="date_from" class="form-control" value="<?php echo forum_e($filters['date_from']); ?>">
                        </div>
                        <div class="form-group col-6 col-md-3 col-lg-3">
                            <label for="filter-date-to">To date</label>
                            <input type="date" id="filter-date-to" name="date_to" class="form-control" value="<?php echo forum_e($filters['date_to']); ?>">
                        </div>
                    </div>
                    <div class="forum-filter-actions">
                        <button type="submit" class="btn btn-primary forum-btn"><i class="fa fa-filter"></i> Apply Filters</button>
                        <?php if ($hasFilters) { ?>
                            <a href="forum-my-topics.php" class="btn btn-light forum-btn"><i class="fa fa-times"></i> Clear Filters</a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

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
            } else if ($hasFilters) {
                echo forum_render_empty('fa-search', 'No topics match your filters', 'Try different filters or clear them to see all your topics.', '<a href="forum-my-topics.php" class="btn btn-primary forum-btn forum-login-btn"><i class="fa fa-times"></i> Clear Filters</a>');
            } else {
                echo forum_render_empty('fa-file-text-o', 'You have not started any topics yet', 'Ask a question or share an idea with the community.', forum_new_topic_button());
            }
            ?>
            <?php $pagination = forum_pagination($totalTopics, $page, FORUM_PER_PAGE, $paginationParams); ?>
            <?php if ($pagination) { ?>
                <div class="forum-list-footer"><?php echo $pagination; ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
