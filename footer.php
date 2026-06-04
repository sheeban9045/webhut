
<!--Footer-->
<div class="section bg_dark" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <img src="https://webhut.net/images/logo/logo.png" class="logo-dark" alt="Start.ly Logo" />
                    <p class="mt-3 ml-1 white_text">Lorem ipsum dolor sit amet, ut ius audiam denique  tractatos, pro cu dicat quidam neglegentur. Vel mazim aliquid.</p>
                   
                </div>
                <div class="col-sm-3">
					<h6 class="display_6">More</h6>
                    <ul class="list-unstyled footer-links ml-1">
                        <li><a href="#">Customization</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="<?php echo $baseURL ?>/terms-condition.php">Terms & Conditions</a></li>
                        <li><a href="<?php echo $baseURL ?>/privacy-policy.php">Privacy & Policy</a></li>
                    </ul>
                </div>
             
                <div class="col-sm-3">
					<h6 class="display_6">Follow Us</h6>
                    <ul class="list-unstyled footer-links ml-1">
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">Twitter</a></li>
                    </ul>
                </div>

                 <div class="col-sm-3">
                    <h6 class="display_6">Our Place</h6>
                    <ul class="list-unstyled footer-links ml-1">
                        <li><a href="javascript:void(0);"><i class="pe-7s-map-marker" style="color: #ffffff;"></i> Lorem Ipsum? dolor sit</a></li>
                        <li><a href="#"><i class="pe-7s-mail" style="color: #ffffff;"></i> abc@example.com</a></li>
                        <li><a href="#"><i class="pe-7s-phone" style="color: #ffffff;"></i> +91 9876543210</a></li>
                    </ul>
                </div>

            </div>
            <div class=" text-center white_text mt-4"> Copyright © Matt community. All rights reserved, Made with Love <span class="footer-hart"><i class="fa fa-heart"></i></span></div> 
        </div>
    </div>

	<a href="#home" class="co_top">
        <i class="pe-2x pe-7s-angle-up font-weight-bold"></i>
    </a>

    <script src="https://webhut.net/js/js-library/jquery-3.2.1.min.js"></script>
    <script src="https://webhut.net/js/js-library/bootstrap.min.js"></script>
	<script src="https://webhut.net/js/js-library/twinlight.js"></script>
    <script src="https://webhut.net/js/script.js"></script>
    <script src="https://webhut.net/js/common.js"></script>
    <script>
        function updateCartUI(data) {
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count > 0 ? data.cart_count : '';

            const countEl = document.getElementById('cartItemCount');
            if (countEl) countEl.textContent = data.cart_count + ' items';

            const listEl = document.getElementById('cartItemsList');
            if (listEl) listEl.innerHTML = data.items_html;

            const footerEl = document.getElementById('cartFooter');
            const totalEl = document.getElementById('cartTotal');

            if (data.has_items) {
                if (totalEl) totalEl.textContent = '₹' + data.total;
                if (footerEl) footerEl.style.display = 'block'; 
            } else {
                if (footerEl) footerEl.style.display = 'none';
            }
        }

        function addToCart(pluginId, pluginName, pluginPrice) {
            fetch('cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&plugin_id=${pluginId}&plugin_name=${encodeURIComponent(pluginName)}&plugin_price=${pluginPrice}`
            })
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        updateCartUI(data);
                        showCartToast(pluginName);
                    }
                } catch(e) {
                    console.error('Server error:', text);
                }
            });
        }

        function showCartToast(name) {
            let toast = document.createElement('div');
            toast.innerText = `✅ "${name}" successfully added to cart!`;
            toast.style.cssText = `
                position: fixed; bottom: 30px; right: 30px;
                background: #2d4156; color: #fff;
                padding: 12px 20px; border-radius: 8px;
                font-size: 14px; z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                animation: fadeIn 0.3s ease;
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Toggle dropdown
        document.getElementById('cartToggleBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dd = document.getElementById('cartDropdown');
            dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('cartDropdown');
            if (!dd.contains(e.target) && e.target.id !== 'cartToggleBtn') {
                dd.style.display = 'none';
            }
        });

        function removeFromCart(pluginId) {
            fetch('cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&plugin_id=${pluginId}`
            })
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        updateCartUI(data);
                    }
                } catch(e) {
                    console.error('Server error:', text);
                }
            });
        }
    </script>
  </body>



</html>
