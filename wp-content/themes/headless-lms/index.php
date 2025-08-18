<?php
/**
 * Minimal headless theme index.php
 * Redirects all frontend requests to the Next.js app
 */

// Redirect to Next.js frontend
$frontend_url = 'http://localhost:3000';
wp_redirect($frontend_url);
exit;
?>
