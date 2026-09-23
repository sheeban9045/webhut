<?php
require_once './forum_helper.php';
forum_require_login();

$currentUser = forum_current_user();
$categories = forum_get_categories();
$categoryIds = array_map('intval', array_column($categories, 'id'));

$values = array('title' => '', 'category_id' => forum_get_int('category_id'), 'description' => '');
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['title'] = forum_post_string('title');
    $values['category_id'] = (int) forum_post_string('category_id');
    $values['description'] = forum_clean_html(forum_post_string('description'));

    if (!forum_check_csrf(forum_post_string('csrf_token'))) {
        $errors[] = 'Your session has expired. Please submit the form again.';
    }
    if ($values['title'] === '') {
        $errors[] = 'Please enter a title for your topic.';
    } else if (mb_strlen($values['title']) > 255) {
        $errors[] = 'The title can be at most 255 characters long.';
    }
    if (!in_array($values['category_id'], $categoryIds, true)) {
        $errors[] = 'Please choose a category.';
    }
    if (forum_html_is_empty($values['description'])) {
        $errors[] = 'Please write a description for your topic.';
    } else if (mb_strlen($values['description']) > FORUM_TOPIC_MAX_LENGTH) {
        $errors[] = 'The description is too long.';
    }

    if (!$errors) {
        $now = forum_utc_now();
        $stmt = forum_db_query(
            "INSERT INTO crm_forum_topics (category_id, title, description, created_by, created_at, last_activity_at, deleted) VALUES (?, ?, ?, ?, ?, ?, 0)",
            'ississ',
            array($values['category_id'], $values['title'], $values['description'], (int) $currentUser['id'], $now, $now)
        );
        forum_set_flash('success', 'Your topic has been published.');
        header('Location: ' . forum_topic_url($stmt->insert_id));
        exit;
    }
}

$selectedCategory = null;
foreach ($categories as $category) {
    if ((int) $category['id'] === $values['category_id']) {
        $selectedCategory = $category;
    }
}

$breadcrumbs = array(array('Forum', 'forum.php'));
if ($selectedCategory) {
    $breadcrumbs[] = array($selectedCategory['title'], forum_category_url($selectedCategory['id']));
}
$breadcrumbs[] = array('New Topic', null);

require './header.php';
?>
<?php echo forum_render_styles(); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css">

<div class="forum-page">
    <?php echo forum_render_hero('Start a New Topic', 'Share a question, an idea or something useful with the Webhut community.', $breadcrumbs, '', 'forum-hero-compact'); ?>
    <?php echo forum_render_subnav(''); ?>

    <div class="container forum-body">
        <div class="row">
            <div class="col-lg-8">
                <?php if ($errors) { ?>
                    <div class="alert alert-danger forum-alert" role="alert">
                        <strong><i class="fa fa-exclamation-circle"></i> Please fix the following:</strong>
                        <ul class="forum-error-list">
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo forum_e($error); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <div class="forum-card">
                    <div class="forum-card-body">
                        <?php if (!$categories) { ?>
                            <?php echo forum_render_empty('fa-folder-open-o', 'No categories available', 'Topics can be created once the forum categories are set up.', '<a href="forum.php" class="btn btn-primary forum-btn">Back to Forum</a>'); ?>
                        <?php } else { ?>
                            <form id="forum-topic-form" class="forum-form" method="post" action="forum-new-topic.php" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo forum_e(forum_csrf_token()); ?>">

                                <div class="form-group">
                                    <label for="topic-title">Title</label>
                                    <input type="text" id="topic-title" name="title" class="form-control" maxlength="255" required placeholder="What is your topic about?" value="<?php echo forum_e($values['title']); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="topic-category">Category</label>
                                    <select id="topic-category" name="category_id" class="form-control" required>
                                        <option value="">Choose a category</option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo (int) $category['id']; ?>" <?php echo (int) $category['id'] === $values['category_id'] ? 'selected' : ''; ?>><?php echo forum_e($category['title']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" id="topic-description-group">
                                    <label for="topic-description">Description</label>
                                    <textarea id="topic-description" name="description" class="form-control"><?php echo forum_e($values['description']); ?></textarea>
                                    <small class="form-text">Add details, steps you already tried, links or examples.</small>
                                </div>

                                <div class="forum-form-actions">
                                    <a href="<?php echo $selectedCategory ? forum_category_url($selectedCategory['id']) : 'forum.php'; ?>" class="btn btn-light forum-btn">Cancel</a>
                                    <button type="submit" class="btn btn-primary forum-btn"><i class="fa fa-paper-plane"></i> Publish Topic</button>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 forum-sidebar">
                <div class="forum-card">
                    <div class="forum-card-body">
                        <h3 class="forum-card-title"><i class="fa fa-lightbulb-o text-primary"></i> Tips for a great topic</h3>
                        <ul class="forum-tips">
                            <li>Use a short, descriptive title.</li>
                            <li>Choose the category that fits best.</li>
                            <li>Explain the context and what you expect.</li>
                            <li>Search first, your question might already be answered.</li>
                            <li>Never share passwords or private data.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require './footer.php'; ?>
<?php echo forum_render_scripts(); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
<script>
    (function ($) {
        var $description = $('#topic-description');
        if (!$description.length) {
            return;
        }

        $description.summernote({
            height: 280,
            placeholder: 'Describe your topic...',
            dialogsInBody: true,
            disableDragAndDrop: true,
            followingToolbar: false,
            styleTags: ['p', 'blockquote', 'pre', 'h2', 'h3', 'h4'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'hr']],
                ['history', ['undo', 'redo']]
            ],
            callbacks: {
                //images are not supported, ignore pasted/dropped image files
                onImageUpload: function () {}
            }
        });

        $('#forum-topic-form').on('submit', function (e) {
            var $form = $(this);
            var $title = $('#topic-title');
            var $category = $('#topic-category');
            var valid = true;

            $title.toggleClass('is-invalid', !$.trim($title.val()));
            $category.toggleClass('is-invalid', !$category.val());
            if (!$.trim($title.val()) || !$category.val()) {
                valid = false;
            }

            var editorText = $description.next('.note-editor').find('.note-editable').text();
            var empty = $description.summernote('isEmpty') || !$.trim(editorText.replace(/\u00a0/g, ' '));
            $('#topic-description-group').toggleClass('is-invalid-editor', empty);
            if (empty) {
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                $form.find('.is-invalid').first().focus();
                return;
            }

            $description.val($description.summernote('code'));
            $form.find('button[type="submit"]').prop('disabled', true);
        });
    })(jQuery);
</script>
