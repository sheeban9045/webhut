<?php
require_once __DIR__ . '/forum_helper.php';
header('Content-Type: application/json');

function forum_json(array $data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

function forum_json_error($message, $status_code = 400, array $extra = array()) {
    forum_json(array_merge(array('status' => 'error', 'message' => $message), $extra), $status_code);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    forum_json_error('Invalid request.', 405);
}

$action = forum_post_string('action');

try {
    //public: topic title suggestions for the search box
    if ($action === 'suggest') {
        $search = forum_post_string('q');
        if ($search === '' || mb_strlen($search) > 100) {
            forum_json(array('status' => 'success', 'items' => array()));
        }
        $topics = forum_get_topics(" AND t.title LIKE ?", 's', array(forum_like_term($search)), 8);
        $items = array();
        foreach ($topics as $topic) {
            $items[] = array('id' => (int) $topic['id'], 'title' => $topic['title'], 'category' => $topic['category_title'], 'url' => forum_topic_url($topic['id']));
        }
        forum_json(array('status' => 'success', 'items' => $items));
    }

    //everything below requires a logged in user and a valid token
    $user = forum_current_user();
    if (!$user) {
        forum_json_error('Please log in to continue.', 401, array('login_required' => true, 'login_url' => FORUM_LOGIN_URL));
    }
    if (!forum_check_csrf(forum_post_string('csrf_token'))) {
        forum_json_error('Your session has expired. Please refresh the page and try again.', 403);
    }
    $user_id = (int) $user['id'];

    switch ($action) {
        case 'like':
        case 'unlike':
            $topic = forum_get_topic((int) ($_POST['topic_id'] ?? 0));
            if (!$topic) {
                forum_json_error('This topic is no longer available.', 404);
            }
            if ($action === 'like') {
                //the unique key (topic_id, created_by) keeps one like per user, a removed like is restored
                forum_db_query(
                    "INSERT INTO crm_forum_topic_likes (topic_id, created_by, created_at, deleted) VALUES (?, ?, ?, 0)
                     ON DUPLICATE KEY UPDATE created_at = IF(deleted = 1, VALUES(created_at), created_at), deleted = 0",
                    'iis',
                    array((int) $topic['id'], $user_id, forum_utc_now())
                );
            } else {
                forum_db_query("UPDATE crm_forum_topic_likes SET deleted = 1 WHERE topic_id = ? AND created_by = ?", 'ii', array((int) $topic['id'], $user_id));
            }
            $like_info = forum_get_like_info($topic['id'], $user_id);
            forum_json(array('status' => 'success', 'liked' => $like_info['liked'], 'total_likes' => $like_info['total_likes']));

        case 'add_reply':
            $topic = forum_get_topic((int) ($_POST['topic_id'] ?? 0));
            if (!$topic) {
                forum_json_error('This topic is no longer available.', 404);
            }
            $description = forum_post_string('description');
            if ($description === '') {
                forum_json_error('Please write a reply before posting.');
            }
            if (mb_strlen($description) > FORUM_REPLY_MAX_LENGTH) {
                forum_json_error('Your reply is too long (maximum ' . FORUM_REPLY_MAX_LENGTH . ' characters).');
            }

            $now = forum_utc_now();
            $stmt = forum_db_query(
                "INSERT INTO crm_forum_replies (topic_id, description, created_by, created_at, deleted) VALUES (?, ?, ?, ?, 0)",
                'isis',
                array((int) $topic['id'], $description, $user_id, $now)
            );
            $reply_id = (int) $stmt->insert_id;
            forum_db_query("UPDATE crm_forum_topics SET last_activity_at = ? WHERE id = ?", 'si', array($now, (int) $topic['id']));

            $reply = forum_get_reply($reply_id);
            forum_json(array(
                'status' => 'success',
                'message' => 'Your reply has been posted.',
                'reply_id' => $reply_id,
                'reply_html' => forum_render_reply($reply, $user_id, $topic['created_by']),
                'total_replies' => forum_count_topic_replies($topic['id']),
            ));

        case 'update_reply':
        case 'delete_reply':
            $reply = forum_get_reply((int) ($_POST['reply_id'] ?? 0));
            if (!$reply) {
                forum_json_error('This reply is no longer available.', 404);
            }
            //users can manage only their own replies, admin moderation is done in the backend
            if ((int) $reply['created_by'] !== $user_id) {
                forum_json_error('You can only manage your own replies.', 403);
            }

            if ($action === 'delete_reply') {
                forum_db_query("UPDATE crm_forum_replies SET deleted = 1 WHERE id = ? AND created_by = ?", 'ii', array((int) $reply['id'], $user_id));
                forum_json(array('status' => 'success', 'message' => 'Your reply has been deleted.', 'total_replies' => forum_count_topic_replies($reply['topic_id'])));
            }

            $description = forum_post_string('description');
            if ($description === '') {
                forum_json_error('The reply cannot be empty.');
            }
            if (mb_strlen($description) > FORUM_REPLY_MAX_LENGTH) {
                forum_json_error('Your reply is too long (maximum ' . FORUM_REPLY_MAX_LENGTH . ' characters).');
            }
            forum_db_query("UPDATE crm_forum_replies SET description = ? WHERE id = ? AND created_by = ? AND deleted = 0", 'sii', array($description, (int) $reply['id'], $user_id));

            $reply['description'] = $description;
            forum_json(array('status' => 'success', 'message' => 'Your reply has been updated.', 'reply_html' => forum_render_reply($reply, $user_id, $reply['topic_created_by'])));

        default:
            forum_json_error('Unknown action.');
    }
} catch (mysqli_sql_exception $e) {
    error_log('Forum action error: ' . $e->getMessage());
    forum_json_error('Something went wrong. Please try again.', 500);
}
