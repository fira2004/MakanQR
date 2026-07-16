<?php
// Get your TEST keys from https://dashboard.stripe.com/test/apikeys
// (Create a free Stripe account if you don't have one — no business
// verification is needed to use test mode.)
//
// NEVER commit real/live keys to a public repo. This file should stay
// server-side only — it's never sent to the browser.

define('STRIPE_SECRET_KEY', 'sk_test_REPLACE_WITH_YOUR_OWN_TEST_SECRET_KEY');

// Base URL of your project, no trailing slash. Used to build Stripe's
// success/cancel redirect URLs. Change this if your XAMPP path differs.
define('SITE_BASE_URL', 'http://localhost/makanqr_updated');
