<?php require './Config.php';?>
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
                    <form method="post" class="co_contact" action="contact.php">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input type="text" name="name" class="form-control" placeholder="Your Name">
                            </div>
                            <div class="form-group col-sm-12">
                                <input type="email" name="email" class="form-control" placeholder="Your Email">
                            </div>
                        </div>
                        <textarea class="form-control" name="message" placeholder="Your Message"></textarea>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-xl btn-block btn-primary">Send Message</button>
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