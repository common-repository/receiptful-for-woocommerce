=== CM Commerce for WooCommerce ===
Contributors: cm-commerce
Tags: cm commerce, campaign monitor commerce, campaign monitor, conversio, conversio woocommerce, marketing, email, ecommerce, newsletter, product reviews, abandoned cart, receiptful woocommerce, receipt, receipts
Requires at least: 4.0.0
Tested up to: 6.3
Stable tag: 1.6.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CM Commerce, the all-in-one marketing app for your WooCommerce store, increasing sales with automated email campaigns & widgets. Simply sell more.


== Description ==

= Why choose CM Commerce for your WooCommerce store? =

1. Sell more using our supercharged, intelligent and automated email options: Receipts, Follow-Up, Abandoned Cart and Newsletters.
2. Upsell and cross-sell using our easy on-site widgets: Product Reviews and Feedback.
3. Take advantage of our all-in-one dashboard to create smarter campaigns. We can help you with email marketing **and** product reviews.

= Supercharged marketing automation earns you more money. =

More than 20,000 entrepreneurs and ecommerce businesses all over the world use CM Commerce. Our features enable you to increase customer lifetime value by sending marketing campaigns, automated emails using data-driven features and powerful segmentation. **Data-driven marketing made easy to earn you more.**

= How does it work? =

**Integrate quickly** CM Commerce provides a super simple and seamless integration process with your WooCommerce store to get your data working towards your goals.

**Supercharged Segmentation** CM Commerce empowers you to enable powerful personalization to sell more using targeted audiences. You can choose from our pre-made segments or create a segment that is specific for your audience.

**Conversion driven campaigns** CM Commerce includes all the highest converting templates and flows, ready for you when you first log in.

**Metrics that matter** Track your conversions using our Insights dashboard so it’s easy to understand how your doing. Use our Improvements dashboard to know where you could be improving.

**Responsive Customer Success** Our dedicated and passionate support team is here to help you. When you speak, we listen.

= What are the key features? =

**Receipts** Increase sales by 5% in just a couple of minutes. It’s simple to start with CM Commerce’s Receipts, simply drag and drop our upsell modules to increase your Customer Lifetime Value.

**Abandoned Cart Emails** Approximately 70% of your customers will abandon their carts. CM Commerce provides pre-made conversion driven campaigns that make it easy to recover revenue.

**Follow-Up Emails** The probability of selling to an existing customer is 60% to 70%. Use CM Commerce’s powerful segmentation to send personalized emails to sell more by cross-selling.

**Newsletters Email** is 40x more successful at acquiring new clients than social. Easily set-up and start sending personalized emails to help you convert more.

**Product Reviews** More than 60% of consumers say they research and read Product Reviews before buying. Get your best reviews in the spotlight to build trust and increase sales.

**Customer Feedback** It is 6-7 times more expensive to acquire a new customer than it is to keep a current one. Increase repeat revenue by offering integrated, automated Feedback.

= Ready to start? =

Once you sign up you automatically get a 30-day free trial of CM Commerce, which includes access to all of our tools.

**Quick & Easy Setup**
Installing CM Commerce is simple.

1. Download & Activate this plugin
2. Sign up for a free CM Commerce account
3. Paste your API key in your site
4. Tweak your receipt design and start sending supercharged receipts
5. Explore all of the other supercharged tools

**Need help?**
[Open a support ticket](https://wordpress.org/support/plugin/receiptful-for-woocommerce), We're here to make your life easier!

**Please Note:** You require a CM Commerce account ([sign up here and get 30 days free trial](http://campaignmonitor.com/products/cm-commerce/))

== Installation ==

1. Go to the **Plugins > Add New page** in your WordPress admin.
2. Search for "CM Commerce" and install the "CM Commerce for WooCommerce" plugin.
3. Click **Activate Plugin**.
4. Go to **WooCommerce > Settings > CM Commerce** and enter your Receiptful API key. (If you haven't signed up for CM Commerce yet, now's the time!)

That's it! You can now manage everything about your CM Commerce setup right from your CM Commerce dashboard.

== Frequently Asked Questions ==

= Do I need to modify any code? =

Nope - we take care of everything for you. Just install the plugin, add your API key and you’ll be good to go!

= Does CM Commerce work with my theme(s)? =

Yes, CM Commerce works with any theme - whether free, commercial or custom. You do however need WooCommerce activated for CM Commerce to work.

== Screenshots ==

1. An extensive library of advanced email flows are ready for you to take the stress out of marketing.
2. Personalise your emails with powerful segmentation and make your emails count.
3. Collect, showcase and leverage your reviews (on your site and in emails) to build brand trust.
4. Ready-to-go marketing automations.

== Changelog ==

= 1.6.7 - 21/07/2023 =

= 1.6.6 - 18/03/2020 =

* [Fix] - Fix critical error when WC()->cart is null

= 1.6.2 - 24/12/2020 =

* [Fix] - Update script URL

= 1.6.1 - 18/12/2020 =

* [Fix] - Use get_coupon_codes() if available (fall back to get_used_coupons() if not)
* [Improvement] - Update branding
* [Improvement] - Optionally override URLs with env vars

= 1.6.0 - 09/03/2020 =

* [Improvement] - sanitize user inputs

= 1.5.7 - 09/12/2019 =

* [Fix] - wp_footer hook fully applied, removed priority in function

= 1.5.5 - 12/11/2019 =

* [Fix] - Uses wp_footer hook for "After all pages content" for widget installer

= 1.5.3 - 01/10/2019 =

* [Improvement] - Rename Conversio to CM Commerce

= 1.5.2 - 20/08/2019 =

* [Fix] - Settings not saving on WC 3.7

= 1.5.1 - 05/07/2019 =

* [Fix] - Provide compatibility with older php versions
* [Fix] - Error on initial receipt sync cron job

= 1.5.0 - 04/07/2019 =

* [Add] - Settings to disable default email suppression
* [Add] - User Interface for Conversio widget implementation at locations on the site

= 1.4.5 - 22/02/2019 =

* [Add] - Additional header for API calls
* [Add] - Allow for 204 response on receipt resend queue

= 1.4.4 - 06/01/2019 =

* [Change] - Change the API request timeout to 5 seconds

= 1.4.3 - 28/12/2018 =

* [Improvement] - Add order ID to receipts for improved duplication detection.

= 1.4.2 - 02/11/2018 =

* [Improvement] - Manage product stock / backorder status better for product availability

= 1.4.1 - 15/08/2018 =

* [Add] - Better compatibility with checkout field plugins (preventing a common error on the admin area)
* [Fix] - Discount total including taxes even when settings say it shouldn't

= 1.4.0 - 18/04/2018 =

* [Add] - Op-tin setting for the checkout page to to allow customers to opt-in for marketing activities (https://help.commerce.campaignmonitor.com/s/article/woocommerce-checkout-subscription-opt-in)
* [Add] - Product stock is now synced with products
* [Fix] - Free shipping rates not showing on the receipt
* [Fix] - Processing orders are now included with a bulk sync
* [Note] - WooCommerce version 3.0 or higher is required as of this version

= 1.3.6 - 22/09/2017 =

* [Improvement] - Keep the query strings on the abandoned cart redirect (UTM params specifically)
* [Add] - Conversio is notified/updated when the cart path changes
* [Add] - WC 3.2 version check headers
* [Fix] - WC Subscription order download links not work as expected
* [Fix] - Remove new email trigger for default WC email

= 1.3.5 - 13/04/2017 =

* [Improvement] - Additional rebranding from Receiptful > Conversio
* [Add] - WC 3.0 compatibility changes

= 1.3.4 - 15/02/2017 =

* [Improvement] - Load tracking script in the footer instead of header

= 1.3.3 - 01/11/2016 =

* [Improvement] - Rename Receiptful to Conversio
* [Add] - Product Reviews widget shortcode
* [Add] - Email Newsletters widget shortcode

= 1.3.2 - 18/10/2016 =

* [Fix] - Remove cookie on thank you page. Ensures proper tracking

= 1.3.1 - 21/09/2016 =

* [Add] - Support for 'product' attribute in [rf_recommendations product='123'] shortcode
* [Improvement] - Remove the DELETE abandoned cart requests, now handled through Receiptful
* [Improvement] - Sanitize product image URL so special URLs are handled better
* [Improvement] - Include discount tax in the total discount amount
* [Fix] - Warning in WP 4.6+ when recovering abandoned cart

= 1.3.0 - 10/06/2016 =

* [Add] - Feedback widget shortcode
* [Improvement] - Keep all relevant URL parameters from the when redirecting from the abandoned cart (e.g. utm parameters are no longer removed)

= 1.2.5 - 06/05/2016 =

* [Add] - Re-sync products for data accuracy with new features.
* [Fix] - Fix error when sending a receipt including a product that doesn't exist.
* [Add] - Allow check to see if Receiptful is activated.

= 1.2.4 - 24/03/2016 =

* [Fix] - Rare receipt resend loop when the API responds with 50x

= 1.2.3 - 04/03/2016 =

* [Add] - Support for Receiptful search
* [Add] - Support for WooThemes Sensei

= 1.2.2 - 11/01/2016 = Happy new year!

* [Add] - 'Clear unused coupons' feature in WooCommerce -> System -> Tools area

= 1.2.1 - 02/12/2015 =

* [Add] - Make sure abandoned cart is removed after purchase
* [Fix] - Redirect to cart with proper parameters

= 1.2.0 - 25/11/2015 =

* [Add] - Abandoned cart functionality

= 1.1.13 - 06/10/2015 =

* [Add] - 'Synchronize orders' feature in the WooCommerce -> System -> Tools area
* [Improvement] - Use order currency instead of shop currency (supports multi-currency shops)

= 1.1.12 - 01/09/2015 =

* [Improvement] - Update products on sale price start/expiry (accuracy)
* [Add] - 'Synchronize products' feature in the WooCommerce -> System -> Tools area

= 1.1.11 - 06/08/2015 =

* [Improvement] - Coupon expiry is now always will end of day that is promoted on the receipt
* [Improvement] - Update product when scheduled sale price starts/ends
* [Improvement] - Allow some HTML in product note field
* [Fix] - No longer initiate order sync on every update

= 1.1.10 - 21/07/2015 =

* [Improvement] - Improved product thumbnails, less blurry images on edge cases.
* [Fix] - Recommendations weren't showing headers/titles (overridden) fixed now.

= 1.1.9 - 15/07/2015 =

* [Add] - Product image to the API call, allow to show the product image on the receipt.
* [Improvement] - Update products to not be recommended when going out of stock.
* [Improvement] - Allow custom shortcode attributes. read more; https://app.receiptful.com/recommendations/instructions.
* [Add] - Re-sync all orders to improve our data.

= 1.1.8 - 28/05/2015 =

* [Fix] - Javascript error when recommendations are not enabled.
* [Improvement] - Add used order coupons to the API call.
* [Deprecated] - Conversio()->print_scripts() will be automatically from now on in receiptful.init().

= 1.1.7 - 22/05/2015 =

* [Add] - Cart product IDs to recommendation init. Ensures you can use recommendations in the cart.

= 1.1.6 - 19/05/2015 =

* [Add] - Add recommendation options
* [Add] - Page tracking
* [Improvement] - Set out of stock products to hidden within Receiptful

= 1.1.5 - 27/04/2015 =

* [Fix] - WooCommerce 2.2.x compatibility notice with wc_tax_enabled()
* [Improvement] - WPML won't break checkout
* [Improvement] - Strip shortcodes from product descriptions
* [Improvement] - Pass protected, draft, hidden, private products are now synced as hidden=true

= 1.1.4 - 09/04/2015 =

* [Add] - Product pageview tracking for personalised product recommendations
* [Improvement] - Add Javascript defined checks
* [Improvement] - Cleanup unused receipt api args
* [Improvement] - Small refactor coupon creation

= 1.1.3 - 01/04/2015 =

* [Fix] - Typo in filter name 'receiptful_api_args_related_products'
* [Improvement] - Prevent shipping coupons from having discount amounts
* [Improvement] - Prevent getting related products in the initial product sync
* [Improvement] - Automatically picking up Tax/totals translation from WooCommerce
* [Improvement] - Prevent notice when API doesn't return the 'products' parameter

= 1.1.2 - 12/03/2015 =

* [Add] - Receipt sync for better recommendations
* [Add] - Order note support
* [Add] - Product note support
* [Improvement] - Changed 'Shipping' to the actual shipping title
* [Improvement] - Changed textdomain to 'receiptful' for consistency
* [Prevent] - Prevent notice in upcoming Receiptful update

= 1.1.1 - 05/03/2015 =

* [Add] - Product sync for better recommendations
* [Fix] - load translation files
* [Improvement] - Subtotals refactor
* [Improvement] - CDN for JavaScript - Improving loading time
* [Improvement] - Small queue improvements (don't add 400 response to queue)
* [Improvement] - Subscriptions email notifications

= 1.1.0 - 28/01/2015 =

* [Add] - Unit tests!
* [Add] - WooCommerce 2.3 support
* [Add] - Filters & hooks for extending/modifying
* [Add] - Receipt comparison screenshot, you should see it ;-)
* [Improvement] - Payment method to the receipt
* [Improvement] - Date parameter to the API call to keep order date/time equal
* [Improvement] - Support for multiple download URLs
* [Improvement] - Split up compatibility files in separate file
* [Improvement] - Email class refactor

= 1.0.5 - 14/01/2015 =

* [Happy New Year!]
* [Improvement] - Refactored email WC overrides
* [Fix] - Warning when descriptions < 100 char

= 1.0.4 - 18/12/2014 =

* [Add] - helper function to not copy meta data for subscription renewals
* [Add] - Send random products as related when none are found by core function
* [Improvement] - Sending discounts as negative number to API
* [Improvement] - Refactored helper functions
* [Fix] - Error when updating WooCommerce version while Receiptful is active
* [Fix] - for WC Subscriptions emails

= 1.0.3 - 12/12/2014 =

* [Add] - Support for product meta
* [Add] - Support for downloadable products (download links in Receipt)
* [Improvement] - Change the coupon tracking to JS at checkout
* [Fix] - Bug that caused coupon product restrictions
* [Fix] - Javascript error on thank you page

= 1.0.2 - 03/12/2014 =

* [Add] - Receiptful is now FREE
* [Add] - Added reporting for email conversions
* [Improvement] - Refactor the API class
* [Improvement] - Refactor related products code
* [Improvement] - Add more code commenting
* [Improvement] - Remove custom API endpoint for coupons
* [Fix] - WC Pending email sending
* [Fix] - Email being sent for digital downloads

= 1.0.1 - 19/11/2014 =

* [Add] - Plugin screen shots + banner + icon
* [Add] - Coupon usage tracking
* [Add] - Option to restrict coupon usage to customer email
* [Add] - WooCommerce 2.1.X support
* [Improvement] - Change CRON from 60 to 15 minutes
* [Improvement] - WooCommerce activated check for both network activated and single site
* [Fix] - Notice when using Free shipping upsell
* [Fix] - Incorrect coupon expiry date

= 1.0.0 - 22/10/2014 =

* Initial Release


== Upgrade Notice ==
