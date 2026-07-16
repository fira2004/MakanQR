<?php
// Fill these in once your Fiuu Dev/Sandbox account is approved.
// Find them in the Fiuu Booster merchant portal:
//   Merchant ID   -> Merchant Profile > General Info
//   Verify Key    -> Transactions > Settings
//   Secret Key    -> Transactions > Settings
// A Dev account's Merchant ID usually ends in "_Dev".

define('FIUU_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('FIUU_VERIFY_KEY', 'YOUR_VERIFY_KEY');
define('FIUU_SECRET_KEY', 'YOUR_SECRET_KEY');

// ⚠️ CONFIRM THIS: Fiuu has used several hostnames for the Hosted Payment
// Page over the years (onlinepayment.com.my, pay.merchant.razer.com, and
// sandbox-specific subdomains). Check the welcome email / API Spec PDF you
// receive when your Dev account is approved and update this if different.
define('FIUU_PAY_URL', 'https://pay.merchant.razer.com/RMS/pay/' . FIUU_MERCHANT_ID . '/');

// ⚠️ CONFIRM THIS: this is the channel code to pre-select Touch 'n Go
// eWallet on the hosted payment page. "TNG-EWALLET" is documented for
// Fiuu's Mobile XDK/GPay channel list - the Hosted Payment Page channel
// list (Appendix C of the API Spec PDF) may use a slightly different code.
// If checkout redirects to Fiuu but doesn't land straight on TnG, this is
// the value to fix.
define('FIUU_TNG_CHANNEL', 'TNG-EWALLET');

// Base URL of your project, no trailing slash.
define('SITE_BASE_URL', 'http://localhost/makanqr_updated');
