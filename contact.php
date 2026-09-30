<?php require './Config.php';?>
<?php
require_once './forum_helper.php';

function contact_send_admin_notification($domain, $name, $email, $message) {
    $to = ADMIN_EMAIL;
    $subject = 'New Contact Us enquiry from ' . $name;
    $body = "You have received a new enquiry through the WebHut Contact Us form.\r\n\r\n"
        . "Name: $name\r\n"
        . "Email: $email\r\n\r\n"
        . "Message:\r\n$message\r\n";

    $from_domain = preg_replace('/[^a-z0-9.\-]/i', '', $domain ?: 'webhut.net');
    $headers = "From: WebHut Website <no-reply@$from_domain>\r\n"
        . "Reply-To: $name <$email>\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!@mail($to, $subject, $body, $headers)) {
        error_log('Contact Us: failed to send admin notification email for ' . $email);
    }
}

$contact_errors = array();
$contact_old = array('name' => '', 'email' => '', 'message' => '');
$contact_success = isset($_GET['status']) && $_GET['status'] === 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $contact_old['name'] = trim((string) ($_POST['name'] ?? ''));
    $contact_old['email'] = trim((string) ($_POST['email'] ?? ''));
    $contact_old['message'] = trim((string) ($_POST['message'] ?? ''));

    if ($contact_old['name'] === '') {
        $contact_errors[] = 'Please enter your name.';
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
    }

    if (!$contact_errors) {
        $admin_id = forum_get_user_id_by_email(ADMIN_EMAIL);

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
                $stmt->close();

                contact_send_admin_notification(
                    $domain,
                    $contact_old['name'],
                    $contact_old['email'],
                    $contact_old['message']
                );

                // Redirect so refreshing the confirmation page never re-submits the form
                header('Location: contact.php?status=success');
                exit;
            }

            $stmt->close();

            $contact_errors[] = 'Sorry, we could not save your message right now. Please try again.';

        } else {
            $contact_errors[] = 'Sorry, something went wrong. Please try again.';
        }
    }
}
?>
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
                    <form method="post" class="co_contact" action="contact.php">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" maxlength="150" value="<?php echo htmlspecialchars($contact_old['name']); ?>" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" maxlength="255" value="<?php echo htmlspecialchars($contact_old['email']); ?>" required>
                            </div>
                        </div>
                        <textarea class="form-control" name="message" placeholder="Your Message" required><?php echo htmlspecialchars($contact_old['message']); ?></textarea>
                        <br>
                        <div class="form-group">
                            <button type="submit" name="contact_submit" value="1" class="btn btn-xl btn-block btn-primary">Send Message</button>
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
              <li><a href="#"><i class="pe-7s-phone"></i> +1 123456789</a>
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
</div>

<?php require './footer.php' ?>