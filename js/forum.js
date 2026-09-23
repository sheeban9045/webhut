/* Webhut Community Forum - requires jQuery (footer.php) and window.forumConfig (forum_helper.php) */
(function ($) {
    'use strict';

    var config = window.forumConfig || {};

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : String(text)).html();
    }

    function toast(message, type) {
        var $toast = $('<div class="forum-toast" role="status"></div>').text(message).css({
            position: 'fixed', bottom: '30px', right: '30px', zIndex: 10000,
            background: type === 'error' ? '#d9546a' : '#2d4156', color: '#fff',
            padding: '12px 20px', borderRadius: '8px', fontSize: '14px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.2)', maxWidth: '90%', display: 'none'
        });
        $('body').append($toast);
        $toast.fadeIn(200);
        setTimeout(function () { $toast.fadeOut(300, function () { $toast.remove(); }); }, 3000);
    }

    function showError(message) {
        if (window.Swal) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: message, confirmButtonColor: '#2b84d1' });
        } else {
            alert(message);
        }
    }

    function confirmAction(options, onConfirm) {
        if (!window.Swal) {
            if (window.confirm(options.text)) { onConfirm(); }
            return;
        }
        Swal.fire({
            icon: 'warning',
            title: options.title,
            text: options.text,
            showCancelButton: true,
            confirmButtonText: options.confirmText,
            confirmButtonColor: '#d9546a',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed || result.value) { onConfirm(); }
        });
    }

    function loginPrompt(text) {
        text = text || 'Please log in to join the discussion.';
        if (!window.Swal) {
            if (window.confirm(text + ' Go to the login page?')) { window.location.href = config.loginUrl; }
            return;
        }
        Swal.fire({
            icon: 'info',
            title: 'Log in required',
            text: text,
            showCancelButton: true,
            confirmButtonText: 'Log in',
            cancelButtonText: 'Not now',
            confirmButtonColor: '#2b84d1'
        }).then(function (result) {
            if (result.isConfirmed || result.value) { window.location.href = config.loginUrl; }
        });
    }

    //POST to forum_action.php, errors are handled centrally
    function request(action, data) {
        var payload = $.extend({ action: action, csrf_token: config.csrfToken }, data || {});
        return $.ajax({ url: config.endpoint, type: 'POST', dataType: 'json', data: payload })
            .then(function (response) {
                if (!response || response.status !== 'success') {
                    return $.Deferred().reject(response || {}).promise();
                }
                return response;
            }, function (xhr) {
                return $.Deferred().reject(xhr.responseJSON || { message: 'Something went wrong. Please try again.' }).promise();
            });
    }

    function handleFailure(response) {
        if (response && response.login_required) {
            loginPrompt(response.message);
        } else {
            showError((response && response.message) || 'Something went wrong. Please try again.');
        }
    }

    function updateReplyCount(total) {
        $('.js-forum-reply-count').text(total);
        $('.js-forum-no-replies').toggle(total === 0);
    }

    /* ---------- Search suggestions ---------- */
    $('.js-forum-search-input').each(function () {
        var $input = $(this);
        var $box = $input.closest('form').find('.js-forum-suggestions');
        var timer = null;
        var lastTerm = '';
        var xhr = null;

        function close() { $box.removeClass('is-open').empty(); }

        function render(items, term) {
            if (!items.length) {
                $box.html('<div class="forum-suggestion-empty">No topics found for "' + escapeHtml(term) + '". Press Enter to search all content.</div>');
            } else {
                $box.html($.map(items, function (item) {
                    return '<a class="forum-suggestion" role="option" href="' + escapeHtml(item.url) + '">' + escapeHtml(item.title)
                        + '<small>in ' + escapeHtml(item.category) + '</small></a>';
                }).join(''));
            }
            $box.addClass('is-open');
        }

        $input.on('input', function () {
            var term = $.trim($input.val());
            clearTimeout(timer);
            if (term.length < 2) { lastTerm = ''; close(); return; }
            timer = setTimeout(function () {
                if (term === lastTerm) { return; }
                lastTerm = term;
                if (xhr) { xhr.abort(); }
                xhr = $.ajax({ url: config.endpoint, type: 'POST', dataType: 'json', data: { action: 'suggest', q: term } })
                    .done(function (response) {
                        if (response && response.status === 'success' && $.trim($input.val()) === term) {
                            render(response.items || [], term);
                        }
                    });
            }, 250);
        });

        $input.on('keydown', function (e) {
            var $items = $box.find('.forum-suggestion');
            if (!$box.hasClass('is-open') || !$items.length) {
                if (e.key === 'Escape') { close(); }
                return;
            }
            var index = $items.index($items.filter('.is-active'));
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                index = e.key === 'ArrowDown' ? (index + 1) % $items.length : (index <= 0 ? $items.length - 1 : index - 1);
                $items.removeClass('is-active').eq(index).addClass('is-active');
            } else if (e.key === 'Enter' && index >= 0) {
                e.preventDefault();
                window.location.href = $items.eq(index).attr('href');
            } else if (e.key === 'Escape') {
                close();
            }
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest($input.closest('form')).length) { close(); }
        });
    });

    /* ---------- Like / Unlike ---------- */
    $(document).on('click', '.js-forum-like', function () {
        var $btn = $(this);
        if (!config.loggedIn) {
            loginPrompt('Please log in to like this topic.');
            return;
        }
        var liked = $btn.attr('data-liked') === '1';
        $btn.addClass('is-busy');
        request(liked ? 'unlike' : 'like', { topic_id: $btn.data('topic-id') })
            .done(function (response) {
                $btn.attr({ 'data-liked': response.liked ? '1' : '0', 'aria-pressed': response.liked ? 'true' : 'false' })
                    .toggleClass('is-liked', response.liked);
                $btn.find('i').attr('class', 'fa ' + (response.liked ? 'fa-heart' : 'fa-heart-o'));
                $btn.find('.forum-like-label').text(response.liked ? 'Liked' : 'Like');
                $btn.find('.forum-like-count').text(response.total_likes);
                $('.js-forum-like-total').text(response.total_likes);
                if (response.liked) {
                    $btn.removeClass('is-pop');
                    void $btn[0].offsetWidth;
                    $btn.addClass('is-pop');
                }
            })
            .fail(handleFailure)
            .always(function () { $btn.removeClass('is-busy'); });
    });

    /* ---------- Post a reply ---------- */
    var $replyForm = $('#forum-reply-form');
    var $replyText = $replyForm.find('textarea[name="description"]');
    var $charCount = $replyForm.find('.js-forum-char-count');

    function refreshCharCount() {
        var length = $replyText.val().length;
        $charCount.text(length + ' / ' + config.replyMaxLength).css('color', length > config.replyMaxLength ? '#d9546a' : '');
    }
    if ($replyText.length) {
        $replyText.on('input', refreshCharCount);
        refreshCharCount();
    }

    $replyForm.on('submit', function (e) {
        e.preventDefault();
        var text = $.trim($replyText.val());
        if (!text) {
            $replyText.addClass('is-invalid').focus();
            return;
        }
        if (text.length > config.replyMaxLength) {
            showError('Your reply is too long (maximum ' + config.replyMaxLength + ' characters).');
            return;
        }
        $replyText.removeClass('is-invalid');
        var $submit = $replyForm.find('button[type="submit"]').prop('disabled', true);

        request('add_reply', { topic_id: $replyForm.data('topic-id'), description: text })
            .done(function (response) {
                var $reply = $(response.reply_html).hide();
                $('#forum-reply-list').append($reply);
                $reply.fadeIn(300);
                updateReplyCount(response.total_replies);
                $replyText.val('');
                refreshCharCount();
                toast(response.message);
            })
            .fail(handleFailure)
            .always(function () { $submit.prop('disabled', false); });
    });

    /* ---------- Edit own reply ---------- */
    $(document).on('click', '.js-forum-edit-reply', function () {
        var $reply = $(this).closest('.forum-reply');
        if ($reply.find('.forum-reply-edit').length) { return; }
        var $body = $reply.find('.forum-reply-body');
        var raw = $reply.find('.forum-reply-raw').val();
        var $editor = $(
            '<form class="forum-reply-edit">'
            + '<textarea class="form-control forum-textarea" name="description" aria-label="Edit reply"></textarea>'
            + '<div class="text-right"><button type="button" class="btn btn-light js-forum-cancel-edit">Cancel</button> '
            + '<button type="submit" class="btn btn-primary">Save</button></div></form>'
        );
        $editor.find('textarea').val(raw);
        $body.hide().after($editor);
        $reply.find('.forum-reply-actions').hide();
        $editor.find('textarea').focus();
    });

    $(document).on('click', '.js-forum-cancel-edit', function () {
        var $reply = $(this).closest('.forum-reply');
        $reply.find('.forum-reply-edit').remove();
        $reply.find('.forum-reply-body, .forum-reply-actions').show();
    });

    $(document).on('submit', '.forum-reply-edit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $reply = $form.closest('.forum-reply');
        var $textarea = $form.find('textarea');
        var text = $.trim($textarea.val());
        if (!text) {
            $textarea.addClass('is-invalid').focus();
            return;
        }
        var $buttons = $form.find('button').prop('disabled', true);
        request('update_reply', { reply_id: $reply.data('reply-id'), description: text })
            .done(function (response) {
                $reply.replaceWith(response.reply_html);
                toast(response.message);
            })
            .fail(handleFailure)
            .always(function () { $buttons.prop('disabled', false); });
    });

    /* ---------- Delete own reply ---------- */
    $(document).on('click', '.js-forum-delete-reply', function () {
        var $reply = $(this).closest('.forum-reply');
        confirmAction({
            title: 'Delete this reply?',
            text: 'Your reply will be removed from the discussion.',
            confirmText: 'Yes, delete it'
        }, function () {
            request('delete_reply', { reply_id: $reply.data('reply-id') })
                .done(function (response) {
                    $reply.fadeOut(300, function () {
                        $reply.remove();
                        updateReplyCount(response.total_replies);
                    });
                    toast(response.message);
                })
                .fail(handleFailure);
        });
    });

    /* ---------- Guests: login prompt buttons ---------- */
    $(document).on('click', '.js-forum-login-required', function (e) {
        e.preventDefault();
        loginPrompt($(this).data('message'));
    });

    /* ---------- Highlight a linked reply (#reply-ID) ---------- */
    function highlightReply() {
        var match = /^#reply-(\d+)$/.exec(window.location.hash);
        if (!match) { return; }
        var $reply = $('#reply-' + match[1]);
        if (!$reply.length) { return; }
        $('.forum-reply.is-highlighted').removeClass('is-highlighted');
        $reply.addClass('is-highlighted');
        $reply[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { $reply.removeClass('is-highlighted'); }, 3500);
    }
    highlightReply();
    $(window).on('hashchange', highlightReply);

})(jQuery);
