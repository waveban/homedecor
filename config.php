<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'username');
define('DB_PASS', 'password');
define('DB_NAME', 'interior_mosaic');

// MeiliSearch configuration
define('MEILISEARCH_HOST', 'https://ms.xxxxxxxxx.in');
define('MEILISEARCH_KEY', 'xxxxxxxxxxxxxxxx');
define('MEILISEARCH_INDEX', 'home');

// Site configuration
define('SITE_NAME', 'Interior Mosaic');
define('SITE_DESCRIPTION', 'Discover, curate, and compare interior pieces for your space');
define('SITE_URL', 'https://interiormosaic.com');

// Social media links
define('FACEBOOK_URL', 'https://facebook.com/interiormosaic');
define('INSTAGRAM_URL', 'https://instagram.com/interiormosaic');
define('PINTEREST_URL', 'https://pinterest.com/interiormosaic');
define('TWITTER_URL', 'https://twitter.com/interiormosaic');


if("yes" == "no"){
// Color theme - using a more unique, modern palette
define('PRIMARY_COLOR', '#7F5AF0');     // Purple - primary brand color
define('SECONDARY_COLOR', '#2CB67D');   // Teal - accent for actions
define('ACCENT_COLOR', '#FF8906');      // Orange - for highlights and accents
define('NEUTRAL_COLOR', '#F2F4F7');     // Light gray - for backgrounds
define('DARK_COLOR', '#232946');        // Dark blue - for text and dark UI elements
define('LIGHT_COLOR', '#FFFFFE');       // White - for text on dark backgrounds
}



if("yes" == "no"){

// Color theme - Orangish-Pink Theme
define('PRIMARY_COLOR', '#FF5470');     // Pink - primary brand color
define('SECONDARY_COLOR', '#FF9E7D');   // Peach - accent for actions
define('ACCENT_COLOR', '#FFBD59');      // Orange - for highlights and accents
define('NEUTRAL_COLOR', '#FFF8F0');     // Light cream - for backgrounds
define('DARK_COLOR', '#2D3748');        // Dark blue-gray - for text and dark UI elements
define('LIGHT_COLOR', '#FFFFFF');       // White - for text on dark backgrounds

}



if("yes" == "yes"){
// Color theme - Beige/Earth Tones Theme
define('PRIMARY_COLOR', '#BF9270');     // Warm brown - primary brand color
define('SECONDARY_COLOR', '#8C7361');   // Dark beige - accent for actions
define('ACCENT_COLOR', '#E4B08E');      // Light tan - for highlights and accents
define('NEUTRAL_COLOR', '#F6F1EB');     // Off-white - for backgrounds
define('DARK_COLOR', '#3D3027');        // Deep brown - for text and dark UI elements
define('LIGHT_COLOR', '#FFFFFF');       // White - for text on dark backgrounds
}



?>
