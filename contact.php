<?php require './Config.php';?>
<?php
require_once './forum_helper.php';
require_once './mailer.php';

function contact_clean_header($value) {
    return trim(preg_replace('/[\r\n]+/', ' ', (string) $value));
}

function contact_send_mail($to, $subject, $body, $opts, $log_label) {
    if (!smtp_send_mail($to, $subject, $body, $opts)) {
        error_log('Contact Us: failed to send ' . $log_label . ' email to ' . $to);
        return false;
    }
    return true;
}

function contact_send_admin_notification($domain, $name, $email, $message, $message_id) {
    $to   = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '';
    $from = get_admin_email();

    if (!$to) {
        error_log('Contact Us: ADMIN_EMAIL is not defined');
        return false;
    }

    $name  = contact_clean_header($name);
    $email = contact_clean_header($email);

    $subject = 'New Contact Us enquiry from ' . $name;
    $body = "You have received a new enquiry through the WebHut Contact Us form.\r\n\r\n"
        . "Name: $name\r\n"
        . "Email: $email\r\n\r\n"
        . "Message:\r\n$message\r\n";

    return contact_send_mail($to, $subject, $body, array(
        'from_email' => $from,
        'from_name'  => 'WebHut Website',
        'reply_to'   => $email,
        'reply_name' => $name,
        'message_id' => $message_id,
    ), 'admin notification');
}

function contact_send_user_thankyou($domain, $name, $email, $message, $message_id) {
    $from  = get_admin_email();
    $name  = contact_clean_header($name);
    $email = contact_clean_header($email);

    $subject = 'Thank you for contacting WebHut';
    $body = "Hi $name,\r\n\r\n"
        . "Thank you for contacting WebHut. We have received your message and our team will get back to you soon.\r\n\r\n"
        . "Your message:\r\n$message\r\n\r\n"
        . "Regards,\r\nWebHut Team\r\n";

    return contact_send_mail($email, $subject, $body, array(
        'from_email' => $from,
        'from_name'  => 'WebHut',
        'reply_to'   => $from,
        'message_id' => $message_id,
    ), 'user thank-you');
}

$contact_errors = array();
$contact_old = array('name' => '', 'email' => '', 'message' => '');
$contact_success = isset($_GET['status']) && $_GET['status'] === 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    $contact_old['name'] = trim((string) ($_POST['name'] ?? ''));
    $contact_old['email'] = trim((string) ($_POST['email'] ?? ''));
    $contact_old['message'] = trim((string) ($_POST['message'] ?? ''));

    // Strip tags for security against XSS/HTML injection
    $contact_old['name'] = strip_tags($contact_old['name']);
    $contact_old['message'] = strip_tags($contact_old['message']);

    if ($contact_old['name'] === '') {
        $contact_errors[] = 'Please enter your name.';
    } else if (!preg_match('/^[a-zA-Z\s]+$/', $contact_old['name'])) {
        $contact_errors[] = 'Name must contain only letters and spaces.';
    } else if (mb_strlen($contact_old['name']) > 150) {
        $contact_errors[] = 'Name must be at most 150 characters long.';
    }

    if ($contact_old['email'] === '') {
        $contact_errors[] = 'Please enter your email address.';
    } else if (!filter_var($contact_old['email'], FILTER_VALIDATE_EMAIL)) {
        $contact_errors[] = 'Please enter a valid email address.';
    } else if (mb_strlen($contact_old['email']) > 255) {
        $contact_errors[] = 'Email must be at most 255 characters long.';
    }

    if ($contact_old['message'] === '') {
        $contact_errors[] = 'Please enter your message.';
    } else if (mb_strlen($contact_old['message']) < 10) {
        $contact_errors[] = 'Message must be at least 10 characters long.';
    } else if (mb_strlen($contact_old['message']) > 2000) {
        $contact_errors[] = 'Message must be at most 2000 characters long.';
    }

    $admin_email = get_admin_email();
    if (empty($admin_email)) {
        $contact_errors[] = 'Something went wrong. Please contact support.';
    }
    if (!$contact_errors) {
        $admin_id = get_user_id_by_email($admin_email);

        // Store timestamp in UTC, same as CRM
        $created_at = gmdate('Y-m-d H:i:s');

        $stmt = $conn->prepare("
            INSERT INTO crm_messages
            (
                subject,
                name,
                email,
                message,
                type,
                status,
                created_at,
                to_user_id,
                from_user_id,
                message_id,
                deleted,
                files
            )
            VALUES
            (
                'Enquiry from contact form',
                ?,
                ?,
                ?,
                'enquiry',
                'unread',
                ?,
                $admin_id,
                0,
                0,
                0,
                'a:0:{}'
            )
        ");

        if ($stmt) {

            $stmt->bind_param(
                "ssss",
                $contact_old['name'],
                $contact_old['email'],
                $contact_old['message'],
                $created_at
            );

            if ($stmt->execute()) {

                $enquiry_id = $conn->insert_id;

                $stmt->close();

                // $email_host = parse_url(
                //     $domain ?: 'https://webhut.net',
                //     PHP_URL_HOST
                // );

                // if (!$email_host) {
                //     $email_host = preg_replace(
                //         '/[^a-z0-9.\-]/i',
                //         '',
                //         $domain ?: 'webhut.net'
                //     );
                // }

                $email_host = 'webhut.net';
                $email_message_id = '<enquiry-' . $enquiry_id . '@' . $email_host . '>';
                $user_message_id  = '<enquiry-' . $enquiry_id . '-user@' . $email_host . '>';

                // Save Message-ID against this enquiry
                $update_stmt = $conn->prepare("
                    UPDATE crm_messages
                    SET email_message_id = ?
                    WHERE id = ?
                ");

                if ($update_stmt) {
                    $update_stmt->bind_param(
                        "si",
                        $email_message_id,
                        $enquiry_id
                    );

                    $update_stmt->execute();
                    $update_stmt->close();
                }

                contact_send_admin_notification(
                    $domain,
                    $contact_old['name'],
                    $contact_old['email'],
                    $contact_old['message'],
                    $email_message_id
                );

                contact_send_user_thankyou(
                    $domain,
                    $contact_old['name'],
                    $contact_old['email'],
                    $contact_old['message'],
                    $user_message_id
                );

                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Thank you! Your message has been sent successfully. We will get back to you soon.'
                    ]);
                    exit;
                }

                header('Location: contact.php?status=success');
                exit;
            }

            $stmt->close();

            $contact_errors[] = 'Sorry, we could not save your message right now. Please try again.';

        } else {
            $contact_errors[] = 'Sorry, something went wrong. Please try again.';
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $contact_errors[0] ?? 'Sorry, something went wrong. Please try again.'
        ]);
        exit;
    }
}
?>
<style>
    div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm){
        background: #2b84d1 !important;
        border: none !important;
        outline: none !important;
    }
</style>
<!--header-->
<?php require './header.php';?>

<div class="section contact-bg">
    <div class="container">
        <div class="text-left text-white">
            <h1 class=" wow fadeInUp">Contact Us</h1>
        </div>
    </div>
</div>


<div class="py-5" id="contact">
    <div class="container row mauto">
      
    
        <div class="col-md-6">
            <div class="section_title">
                <h3>Get In Touch</h3>
                <p>We always love to hear from you, let us know what you need !</p>
            </div>
            <div class="row">
                <div class="col col-md-12">
                    <?php if ($contact_success) { ?>
                        <div class="alert alert-success" role="alert">Thank you! Your message has been sent successfully. We will get back to you soon.</div>
                    <?php } ?>
                    <?php if ($contact_errors) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($contact_errors as $contact_error) { ?>
                                <div><?php echo htmlspecialchars($contact_error); ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <form method="post" id="contactForm" class="co_contact" action="contact.php">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" maxlength="150" pattern="[a-zA-Z\s]+" title="Name must contain only letters and spaces." value="<?php echo htmlspecialchars($contact_old['name']); ?>" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" maxlength="255" value="<?php echo htmlspecialchars($contact_old['email']); ?>" required>
                            </div>
                        </div>
                        <textarea class="form-control" name="message" placeholder="Your Message" minlength="10" maxlength="2000" required><?php echo htmlspecialchars($contact_old['message']); ?></textarea>
                        <br>
                        <div class="form-group">
                            <button type="submit" name="contact_submit" value="1" id="contactSubmitBtn" class="btn btn-xl btn-block btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <div class="col-md-1"></div>
          <div class="col-md-5">
            <div class="section_title">
                <h3>Contact Details</h3>
            </div>

            <div class="contact-img">
              <img src="images/contact.png" alt="Address" title="Address" class="img-responsive">
            </div>
            <ul class="contact-list pl-0 pt-5">
               <li><a href="#"><i class="pe-7s-map-marker"></i> Lorem Ipsum? dolor sit</a></li>
               <li><a href="#"><i class="pe-7s-mail"></i> abc@example.com</a></li>
               <li><a href="#"><i class="pe-7s-phone"></i> +1 123456789</a></li>
            </ul>


        </div>
    </div>
</div>

<div class="section-map">
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col-md-12">
                <div class="maap">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d190256.09899022538!2d-87.87204670263532!3d41.83364785009012!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880e2c3cd0f4cbed%3A0xafe0a6ad09c0c000!2sChicago%2C%20IL%2C%20USA!5e0!3m2!1sen!2sin!4v1639052075658!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('contactSubmitBtn');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Client-side name validation
            const nameField = contactForm.querySelector('input[name="name"]');
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(nameField.value.trim())) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Name must contain only letters and spaces.'
                });
                return;
            }

            // Client-side length validation
            const messageField = contactForm.querySelector('textarea[name="message"]');
            if (messageField.value.trim().length < 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Message must be at least 10 characters long.'
                });
                return;
            }

            if (messageField.value.trim().length > 2000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Message must be at most 2000 characters long.'
                });
                return;
            }

            const formData = new FormData(contactForm);
            // Append the button value since it's required by PHP
            formData.append('contact_submit', '1');

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            fetch('contact.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                    contactForm.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Something went wrong. Please try again.'
                    });
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'A network error occurred. Please try again.'
                });
            });
        });
    }
});
</script>

<?php require './footer.php' ?>