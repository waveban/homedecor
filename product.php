<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;

// Redirect if no product ID is provided
if (!$productId) {
    header('Location: /');
    exit;
}

// Get product data
$api = new MeiliSearchAPI();
$productData = $api->getProduct($productId);

// Redirect if product not found
if (!$productData || empty($productData)) {
    header('Location: /');
    exit;
}

// Add to recently viewed
addToRecentlyViewed($productId);

// Organize data for use in template
$product = $productData['product'];
$categories = $productData['categories'] ?? [];
$otherVariants = $productData['othervariants'] ?? [];

// Get related products if other variants exist
$relatedProducts = [];
if (!empty($otherVariants)) {
    // Fetch first 4 variants at most
    $variantsToFetch = array_slice($otherVariants, 0, 4);
    foreach ($variantsToFetch as $variantId) {
        $variantData = $api->getProduct($variantId);
        if ($variantData && !empty($variantData)) {
            $relatedProducts[] = $variantData['product'];
        }
    }
}

// Helper function for color hex codes
function getColorHex($colorName) {
    $colorMap = [
        'black' => '#000000',
        'white' => '#ffffff',
        'red' => '#ff0000',
        'green' => '#00ff00',
        'blue' => '#0000ff',
        'yellow' => '#ffff00',
        'purple' => '#800080',
        'pink' => '#ffc0cb',
        'orange' => '#ffa500',
        'brown' => '#a52a2a',
        'gray' => '#808080',
        'silver' => '#c0c0c0',
        'gold' => '#ffd700',
        'beige' => '#f5f5dc',
        'ivory' => '#fffff0',
        'cream' => '#fffdd0',
        'tan' => '#d2b48c',
        'navy' => '#000080',
        'teal' => '#008080',
        'mint' => '#98fb98',
        'olive' => '#808000',
        'maroon' => '#800000',
        'aqua' => '#00ffff',
        'turquoise' => '#40e0d0',
        'chrome' => '#dcdcdc',
        'off-white' => '#f8f8ff',
    ];
    
    return isset($colorMap[strtolower($colorName)]) ? $colorMap[strtolower($colorName)] : '#cccccc';
}


// Organize offers by price (lowest first)
$offers = $product['offers'] ?? [];
usort($offers, function($a, $b) {
    return $a['price'] <=> $b['price'];
});

// Get active offers only
$activeOffers = array_filter($offers, function($offer) {
    return $offer['isAvailable'] ?? false;
});

// Sort product attributes for display
$attributes = $product['processedAttributes']['edges'] ?? [];

// Get similar products in the same category
$similarProducts = [];
if (!empty($categories)) {
    $mainCategory = $categories[0];
    $result = $api->getCategory($mainCategory, ['limit' => 8]);
    $similarProducts = $result['hits'] ?? [];
    
    // Remove the current product from similar products
    $similarProducts = array_filter($similarProducts, function($item) use ($productId) {
        return $item['id'] !== $productId;
    });
    
    // Limit to 4 products
    $similarProducts = array_slice($similarProducts, 0, 4);
}

// Get recently viewed products
$recentlyViewedProducts = getRecentlyViewedProducts();
// Remove current product from recently viewed
$recentlyViewedProducts = array_filter($recentlyViewedProducts, function($item) use ($productId) {
    return $item['id'] !== $productId;
});
// Limit to 4 products
$recentlyViewedProducts = array_slice($recentlyViewedProducts, 0, 4);

// Check if product is in wishlist
$isInWishlist = isInWishlist($productId);

// Enable search functionality
$enableSearch = true;

// Page meta data
$pageTitle = $product['name'];
$pageDescription = substr(strip_tags($product['description']), 0, 160);
$ogImage = !empty($product['imageUrls']) ? $product['imageUrls'][0] : '';

include 'includes/header.php';
?>

<!-- Product Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Quick links - breadcrumb & actions -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Breadcrumbs -->
                <nav class="text-sm flex items-center overflow-x-auto whitespace-nowrap scrollable-container">
                    <a href="/" class="text-gray-500 hover:text-primary transition">Home</a>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-2 text-gray-400 flex-shrink-0">
                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    
                    <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $category): ?>
                    <a href="/category.php?name=<?php echo urlencode($category); ?>" class="text-gray-500 hover:text-primary transition">
                        <?php echo $category; ?>
                    </a>
                    
                    <?php if ($index < count($categories) - 1): ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-2 text-gray-400 flex-shrink-0">
                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
                
                <!-- Actions -->
                <div class="flex items-center">
                    <button type="button" id="shareBtn" class="p-2 rounded-full hover:bg-neutral transition" title="Share">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.68387 13.3419C8.88616 12.9381 9 12.4824 9 12C9 11.5176 8.88616 11.0619 8.68387 10.6581M8.68387 13.3419C8.19134 14.3251 7.17449 15 6 15C4.34315 15 3 13.6569 3 12C3 10.3431 4.34315 9 6 9C7.17449 9 8.19134 9.67492 8.68387 10.6581M8.68387 13.3419L15.3161 16.6581M8.68387 10.6581L15.3161 7.34193M15.3161 7.34193C15.8087 8.32508 16.8255 9 18 9C19.6569 9 21 7.65685 21 6C21 4.34315 19.6569 3 18 3C16.3431 3 15 4.34315 15 6C15 6.48237 15.1138 6.93815 15.3161 7.34193ZM15.3161 16.6581C15.1138 17.0619 15 17.5176 15 18C15 19.6569 16.3431 21 18 21C19.6569 21 21 19.6569 21 18C21 16.3431 19.6569 15 18 15C16.8255 15 15.8087 15.6749 15.3161 16.6581Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <button class="wishlist-btn ml-2 p-2 rounded-full hover:bg-neutral transition" title="Add to Wishlist" data-product-id="<?php echo $product['id']; ?>">
                        <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="order-2 lg:order-1">
                <!-- Main Image -->
                <div class="bg-white rounded-2xl overflow-hidden mb-4 aspect-square shadow-soft">
                    <img 
                        id="productMainImage" 
                        src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image'; ?>" 
                        alt="<?php echo htmlspecialchars($product['name']); ?>" 
                        class="w-full h-full object-contain transition-opacity duration-200">
                </div>
                
                <!-- Thumbnails & Special Actions -->
                <div class="grid grid-cols-6 gap-3">
                    <div class="col-span-5">
                        <div class="flex overflow-x-auto scrollable-container gap-3 pb-2">
                            <?php foreach (array_slice($product['imageUrls'], 0, 5) as $index => $img): ?>
                            <button 
                                class="product-thumbnail flex-shrink-0 w-16 h-16 md:w-20 md:h-20 bg-white rounded-lg overflow-hidden border-2 <?php echo ($index === 0) ? 'ring-2 ring-primary' : 'border-transparent'; ?> hover:border-primary/50 transition" 
                                data-img-src="<?php echo $img; ?>">
                                <img 
                                    src="<?php echo $img; ?>" 
                                    alt="<?php echo htmlspecialchars($product['name']) . ' thumbnail ' . ($index + 1); ?>" 
                                    class="w-full h-full object-cover">
                            </button>
                            <?php endforeach; ?>
                            
                            <?php if (count($product['imageUrls']) > 5): ?>
                            <button class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 bg-white rounded-lg overflow-hidden border-2 border-transparent hover:border-primary/50 transition flex items-center justify-center" id="viewAllImagesBtn">
                                <span class="text-primary text-sm font-medium">+<?php echo count($product['imageUrls']) - 5; ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-span-1">
                        <div class="grid grid-rows-2 gap-3 h-full">
                            <button type="button" id="view3DBtn" class="bg-white hover:bg-neutral rounded-lg border border-gray-200 flex items-center justify-center transition">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 17L12 21M12 17L5 14M12 17L19 14M12 3L19 6L12 9L5 6L12 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" id="visualizeBtn" class="bg-white hover:bg-neutral rounded-lg border border-gray-200 flex items-center justify-center transition">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 9L21 3M21 3L15 3M21 3L13 11M10 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19H17C18.1046 19 19 18.1046 19 17V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="order-1 lg:order-2">
                <h1 class="text-2xl md:text-3xl font-display font-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <!-- Colors (if available) -->
                <?php if (!empty($product['colorsProcessed'])): ?>
                <div class="mb-6">
                    <div class="text-sm text-gray-500 mb-2">Colors:</div>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($product['colorsProcessed'] as $color): ?>
                        <div class="flex flex-col items-center">
                            <div 
                                class="w-8 h-8 rounded-full border-2 border-white shadow-md cursor-pointer" 
                                style="background-color: <?php echo getColorHex($color); ?>;" 
                                title="<?php echo ucfirst($color); ?>">
                            </div>
                            <span class="text-xs mt-1"><?php echo ucfirst($color); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Price and Savings -->
                <div class="bg-neutral/50 rounded-xl p-4 mb-6">
                    <?php if (isset($product['priceDetails']['lowest']) && $product['priceDetails']['lowest'] > 0): ?>
                    <div class="flex items-baseline">
                        <span class="text-3xl font-bold text-primary"><?php echo formatPrice($product['priceDetails']['lowest']); ?></span>
                        
                        <?php if (isset($product['priceDetails']['percentageDrop']) && $product['priceDetails']['percentageDrop'] > 0): ?>
                        <?php 
                        $originalPrice = $product['priceDetails']['lowest'] * (100 / (100 - $product['priceDetails']['percentageDrop']));
                        ?>
                        <span class="ml-3 text-lg text-gray-400 line-through">
                            <?php echo formatPrice($originalPrice); ?>
                        </span>
                        <span class="ml-2 text-accent font-medium">
                            Save <?php echo $product['priceDetails']['percentageDrop']; ?>%
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center mt-2 text-sm text-gray-500">
<span><?php echo count($offers); ?> offers from 
<?php 
    // Extract store IDs or names instead of the whole store objects
    $storeIds = [];
    foreach ($offers as $offer) {
        if (isset($offer['store']['name'])) {
            $storeIds[] = $offer['store']['name'];
        } elseif (isset($offer['store']['id'])) {
            $storeIds[] = $offer['store']['id'];
        }
    }
    echo count(array_unique($storeIds)); 
?> stores</span>

                        
                        <!-- Last updated -->
                        <?php if (isset($product['priceDetails']['lastUpdatedAt'])): ?>
                        <span class="flex items-center ml-3">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                <path d="M12 8V12L14 14M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Updated <?php echo date('M j, Y', strtotime($product['priceDetails']['lastUpdatedAt'])); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-3xl font-bold text-gray-400">Price not available</div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <!-- View Best Offer Button -->
                    <?php if (!empty($activeOffers)): ?>
                    <a href="<?php echo $activeOffers[0]['url']; ?>" target="_blank" rel="noopener" class="flex-1 bg-primary hover:bg-primary/90 text-white py-3 px-6 rounded-xl font-medium flex items-center justify-center transition shadow-soft">
                        <span>View Best Offer</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php else: ?>
                    <button type="button" class="flex-1 bg-gray-300 text-gray-600 py-3 px-6 rounded-xl font-medium flex items-center justify-center cursor-not-allowed">
                        <span>Not Available</span>
                    </button>
                    <?php endif; ?>
                    
                    <!-- Compare Offers Button -->
                    <button type="button" id="compareOffersBtn" class="py-3 px-6 rounded-xl font-medium flex items-center justify-center bg-neutral hover:bg-neutral/80 transition">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                            <path d="M9 10L4 15L9 20M15 4L20 9L15 14M17 9H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Compare Offers</span>
                    </button>
                    
                    <!-- Add to Wishlist Button (Mobile Only) -->
                    <button class="wishlist-btn lg:hidden py-3 px-6 rounded-xl font-medium flex items-center justify-center bg-neutral hover:bg-neutral/80 transition" data-product-id="<?php echo $product['id']; ?>">
                        <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 mr-2"></i>
                        <span><?php echo $isInWishlist ? 'Saved' : 'Save'; ?></span>
                    </button>
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <h2 class="text-lg font-bold mb-3">Description</h2>
                    <div class="text-gray-600 space-y-3">
                        <?php 
                        // Format description with proper paragraphs
                        $paragraphs = explode("\n", $product['description']);
                        foreach($paragraphs as $para) {
                            if (trim($para)) {
                                echo '<p>' . $para . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
<!-- Product Specifications -->
<?php if (!empty($attributes)): ?>
<div class="mb-6">
    <h2 class="text-lg font-bold mb-3">Specifications</h2>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-soft">
        <div class="divide-y divide-gray-200">
            <?php foreach ($attributes as $index => $attribute): ?>
            <?php 
                $node = $attribute['node'];
                $label = $node['label'];
                $values = $node['values'];
                
                if (empty($values)) continue;
                
                $valueStr = '';
                foreach ($values as $value) {
                    // Handle cases where value is an array or contains nested arrays
                    if (!isset($value['value'])) {
                        continue; // Skip if value key doesn't exist
                    }
                    
                    $currentValue = $value['value'];
                    
                    if (is_array($currentValue)) {
                        // Convert array to string
                        $flattenedValue = '';
                        array_walk_recursive($currentValue, function($item) use (&$flattenedValue) {
                            $flattenedValue .= $item . ', ';
                        });
                        $valueStr .= rtrim($flattenedValue, ', ');
                    } else {
                        $valueStr .= $currentValue;
                    }
                    
                    if (isset($value['unit']) && !empty($value['unit']['symbol'])) {
                        $valueStr .= $value['unit']['symbol'];
                    }
                    $valueStr .= ', ';
                }
                $valueStr = rtrim($valueStr, ', ');
            ?>
            <div class="flex py-3 px-4 <?php echo $index % 2 === 0 ? 'bg-neutral/30' : ''; ?>">
                <div class="w-1/3 text-gray-500 pr-4"><?php echo $label; ?></div>
                <div class="w-2/3 font-medium"><?php echo $valueStr; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>


            </div>
        </div>
        
        <!-- Offers Comparison Tab -->
        <div id="offersTab" class="mt-12">
            <h2 class="text-xl font-display font-bold mb-6">Compare All Offers</h2>
            
            <?php if (!empty($offers)): ?>
            <div class="bg-white rounded-xl shadow-soft overflow-hidden">
                <div class="divide-y divide-gray-200">
                    <?php foreach ($offers as $index => $offer): ?>
                    <?php 
                        $store = $offer['store'] ?? [];
                        $storeName = $store['name'] ?? 'Unknown Store';
                        $storeLogo = $store['logoUrl'] ?? null;
                        $isAvailable = $offer['isAvailable'] ?? false;
                        $offerName = $offer['name'] ?? $product['name'];
                        
                        $bestOfferClass = ($index === 0) ? 'bg-primary/5' : '';
                    ?>
                    <div class="flex flex-col md:flex-row p-4 md:items-center hover:bg-neutral/30 transition <?php echo $bestOfferClass; ?>">
                        <!-- Store Logo & Info -->
                        <div class="md:w-48 flex items-center mb-4 md:mb-0">
                            <div class="w-12 h-12 rounded-lg bg-neutral flex items-center justify-center flex-shrink-0">
                                <?php if ($storeLogo): ?>
                                <img src="<?php echo $storeLogo; ?>" alt="<?php echo htmlspecialchars($storeName); ?>" class="max-w-full max-h-full p-2">
                                <?php else: ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 9V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V9M3 9V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V9M3 9H21M12 12.5C12 11.6716 12.6716 11 13.5 11H17.5C18.3284 11 19 11.6716 19 12.5V12.5C19 13.3284 18.3284 14 17.5 14H13.5C12.6716 14 12 13.3284 12 12.5V12.5Z" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium"><?php echo htmlspecialchars($storeName); ?></div>
                                <div class="text-xs text-gray-500"><?php echo $store['hostname'] ?? ''; ?></div>
                            </div>
                            
                            <?php if ($index === 0): ?>
                            <div class="ml-3 bg-primary/10 text-primary text-xs px-2 py-1 rounded-full">
                                Best Price
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price & Availability -->
                        <div class="flex-1 flex items-center justify-between">
                            <div>
                                <div class="text-xl font-bold text-primary"><?php echo formatPrice($offer['price']); ?></div>
                                <div class="text-sm text-<?php echo $isAvailable ? 'green-600' : 'gray-500'; ?>">
                                    <?php echo $isAvailable ? 'In Stock' : 'Check availability'; ?>
                                </div>
                            </div>
                            
                            <div class="ml-auto">
                                <a href="<?php echo $offer['url']; ?>" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 rounded-lg <?php echo ($index === 0) ? 'bg-primary text-white' : 'bg-neutral text-gray-700 hover:bg-neutral/80'; ?> transition">
                                    View Offer
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1">
                                        <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-neutral rounded-xl p-8 text-center">
                <div class="w-16 h-16 bg-neutral/80 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 9V12M12 15H12.01M5.07183 19H18.9282C20.4678 19 21.4301 17.3333 20.6603 16L13.7321 4C12.9623 2.66667 11.0378 2.66667 10.268 4L3.33978 16C2.56998 17.3333 3.53223 19 5.07183 19Z" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">No offers available</h3>
                <p class="text-gray-500">This product is currently not available for purchase.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Related Products (Variants) -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-16">
            <h2 class="text-xl font-display font-bold mb-6">Other Variants</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                <?php 
                    $imgUrl = !empty($relatedProduct['imageUrls']) ? $relatedProduct['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                    $lowestPrice = $relatedProduct['priceDetails']['lowest'] ?? 0;
                    $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                    $isRelatedInWishlist = isInWishlist($relatedProduct['id']);
                ?>
                <a href="/product.php?id=<?php echo $relatedProduct['id']; ?>" class="group">
                    <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute top-3 right-3 bg-white/90 backdrop-blur-sm w-8 h-8 rounded-full flex items-center justify-center hover:bg-white transition shadow-soft z-10" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                <i class="<?php echo $isRelatedInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 text-sm"></i>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-sm mb-2 line-clamp-2 group-hover:text-primary transition"><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
                            <div class="text-lg font-semibold text-primary"><?php echo $priceStr; ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Similar Products -->
        <?php if (!empty($similarProducts)): ?>
        <div class="mt-16">
            <h2 class="text-xl font-display font-bold mb-6">Similar Products</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($similarProducts as $similarProduct): ?>
                <?php 
                    $imgUrl = !empty($similarProduct['imageUrls']) ? $similarProduct['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                    $lowestPrice = $similarProduct['priceDetails']['lowest'] ?? 0;
                    $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                    $isSimilarInWishlist = isInWishlist($similarProduct['id']);
                ?>
                <a href="/product.php?id=<?php echo $similarProduct['id']; ?>" class="group">
                    <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($similarProduct['name']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute top-3 right-3 bg-white/90 backdrop-blur-sm w-8 h-8 rounded-full flex items-center justify-center hover:bg-white transition shadow-soft z-10" data-product-id="<?php echo $similarProduct['id']; ?>">
                                <i class="<?php echo $isSimilarInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 text-sm"></i>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-sm mb-2 line-clamp-2 group-hover:text-primary transition"><?php echo htmlspecialchars($similarProduct['name']); ?></h3>
                            <div class="text-lg font-semibold text-primary"><?php echo $priceStr; ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Recently Viewed Products -->
        <?php if (!empty($recentlyViewedProducts)): ?>
        <div class="mt-16">
            <h2 class="text-xl font-display font-bold mb-6">Recently Viewed</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($recentlyViewedProducts as $recentProduct): ?>
                <?php 
                    $imgUrl = !empty($recentProduct['imageUrls']) ? $recentProduct['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                    $lowestPrice = $recentProduct['priceDetails']['lowest'] ?? 0;
                    $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                    $isRecentInWishlist = isInWishlist($recentProduct['id']);
                ?>
                <a href="/product.php?id=<?php echo $recentProduct['id']; ?>" class="group">
                    <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($recentProduct['name']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute top-3 right-3 bg-white/90 backdrop-blur-sm w-8 h-8 rounded-full flex items-center justify-center hover:bg-white transition shadow-soft z-10" data-product-id="<?php echo $recentProduct['id']; ?>">
                                <i class="<?php echo $isRecentInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 text-sm"></i>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-sm mb-2 line-clamp-2 group-hover:text-primary transition"><?php echo htmlspecialchars($recentProduct['name']); ?></h3>
                            <div class="text-lg font-semibold text-primary"><?php echo $priceStr; ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Full Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 z-50 bg-black/90 invisible opacity-0 transition-opacity duration-300 flex items-center justify-center">
    <button type="button" id="closeGalleryBtn" class="absolute top-6 right-6 text-white text-2xl hover:text-gray-300 z-50">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    
    <div class="w-full max-w-5xl">
        <!-- Main image -->
        <div class="mb-4 h-[70vh] flex items-center justify-center">
            <img id="galleryImage" src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : ''; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-w-full max-h-full object-contain">
        </div>
        
        <!-- Thumbnails -->
        <div class="flex flex-wrap justify-center gap-2 px-4">
            <?php foreach ($product['imageUrls'] as $index => $img): ?>
            <button 
                class="gallery-thumbnail w-16 h-16 rounded-md overflow-hidden border-2 <?php echo ($index === 0) ? 'border-primary' : 'border-transparent'; ?> hover:border-primary/50 transition" 
                data-img-src="<?php echo $img; ?>">
                <img 
                    src="<?php echo $img; ?>" 
                    alt="<?php echo htmlspecialchars($product['name']) . ' thumbnail ' . ($index + 1); ?>" 
                    class="w-full h-full object-cover">
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 3D View Modal -->
<div id="view3DModal" class="fixed inset-0 z-50 bg-black/80 invisible opacity-0 transition-opacity duration-300">
    <div class="absolute top-0 left-0 right-0 flex items-center justify-between p-4 text-white">
        <h3 class="text-lg font-bold">3D View</h3>
        <button type="button" id="closeView3D" class="text-white hover:text-gray-300">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <div class="h-full flex items-center justify-center">
        <!-- 3D view would be implemented with a 3D model viewer library like model-viewer -->
        <div class="text-white text-center">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 animate-spin">
                <path d="M12 3V6M12 18V21M5.63604 5.63604L7.75736 7.75736M16.2426 16.2426L18.364 18.364M3 12H6M18 12H21M5.63604 18.364L7.75736 16.2426M16.2426 7.75736L18.364 5.63604" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-xl font-medium">Loading 3D model...</p>
            <p class="text-sm text-gray-400 mt-2">This feature is coming soon!</p>
        </div>
    </div>
</div>

<!-- Visualize in Room Modal -->
<div id="visualizeModal" class="fixed inset-0 z-50 bg-black/80 invisible opacity-0 transition-opacity duration-300">
    <div class="absolute top-0 left-0 right-0 flex items-center justify-between p-4 text-white">
        <h3 class="text-lg font-bold">Room Visualizer</h3>
        <button type="button" id="closeVisualize" class="text-white hover:text-gray-300">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <div class="h-full flex flex-col items-center justify-center p-4">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 max-w-md w-full text-white">
            <h4 class="text-xl font-bold mb-4">See this item in your space</h4>
            <p class="mb-6">Upload a photo of your room to visualize how this item would look in your space.</p>
            
            <div class="border-2 border-dashed border-white/30 rounded-lg p-8 text-center mb-6">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4">
                    <path d="M4 16L8.586 11.414C8.96106 11.0389 9.46967 10.8284 10 10.8284C10.5303 10.8284 11.0389 11.0389 11.414 11.414L16 16M14 14L15.586 12.414C15.9611 12.0389 16.4697 11.8284 17 11.8284C17.5303 11.8284 18.0389 12.0389 18.414 12.414L20 14M14 8H14.01M6 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V6C20 5.46957 19.7893 4.96086 19.4142 4.58579C19.0391 4.21071 18.5304 4 18 4H6C5.46957 4 4.96086 4.21071 4.58579 4.58579C4.21071 4.96086 4 5.46957 4 6V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="mb-4">Drag and drop a photo here or click to browse</p>
                <label class="inline-flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg cursor-pointer transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                        <path d="M3 16.5L7 12.5M12.5 7L8.5 11M21 7.5L16.5 12L13 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Upload Photo</span>
                    <input type="file" class="hidden" accept="image/*">
                </label>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-white/70 mb-2">Or try one of our sample rooms:</p>
                <div class="flex justify-center gap-3">
                    <button class="w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition">
                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80" alt="Living room" class="w-full h-full object-cover">
                    </button>
                    <button class="w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition">
                        <img src="https://images.unsplash.com/photo-1586105251261-72a756497a11?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80" alt="Bedroom" class="w-full h-full object-cover">
                    </button>
                    <button class="w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition">
                        <img src="https://images.unsplash.com/photo-1583845112822-290af0fe1740?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80" alt="Office" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>
        </div>
        
        <p class="text-sm text-white/50 mt-4">This feature is coming soon!</p>
    </div>
</div>

<!-- Share Modal -->
<div id="shareModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center invisible opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl w-full max-w-md p-6 transform scale-95 transition-transform duration-300" id="shareContent">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Share this product</h3>
            <button type="button" id="closeShareBtn" class="text-gray-500 hover:text-dark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-4 gap-4 mb-6">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 rounded-lg bg-neutral hover:bg-blue-100 transition">
                <i class="fab fa-facebook-f text-blue-600 text-xl mb-2"></i>
                <span class="text-sm">Facebook</span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($product['name']); ?>&url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 rounded-lg bg-neutral hover:bg-blue-100 transition">
                <i class="fab fa-twitter text-blue-400 text-xl mb-2"></i>
                <span class="text-sm">Twitter</span>
            </a>
            
            <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&media=<?php echo urlencode(!empty($product['imageUrls']) ? $product['imageUrls'][0] : ''); ?>&description=<?php echo urlencode($product['name']); ?>" target="_blank" rel="noopener" class="flex flex-col items-center justify-center p-4 rounded-lg bg-neutral hover:bg-red-100 transition">
                <i class="fab fa-pinterest-p text-red-600 text-xl mb-2"></i>
                <span class="text-sm">Pinterest</span>
            </a>
            
            <a href="mailto:?subject=<?php echo urlencode('Check out this ' . $product['name']); ?>&body=<?php echo urlencode('I found this product and thought you might like it: ' . SITE_URL . $_SERVER['REQUEST_URI']); ?>" class="flex flex-col items-center justify-center p-4 rounded-lg bg-neutral hover:bg-gray-200 transition">
                <i class="fas fa-envelope text-gray-600 text-xl mb-2"></i>
                <span class="text-sm">Email</span>
            </a>
        </div>
        
        <div class="relative">
            <input type="text" id="shareUrl" value="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-24 focus:outline-none focus:ring-2 focus:ring-primary" readonly>
            <button type="button" id="copyLinkBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary text-white px-4 py-1 rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                Copy
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Product image gallery
    const productMainImage = document.getElementById('productMainImage');
    const productThumbnails = document.querySelectorAll('.product-thumbnail');
    
    if (productMainImage && productThumbnails.length > 0) {
        productThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image
                const newImgSrc = this.getAttribute('data-img-src');
                
                // Create a temporary image to preload
                const tempImg = new Image();
                tempImg.onload = function() {
                    // Once loaded, update the main image with fade effect
                    productMainImage.style.opacity = '0';
                    
                    setTimeout(() => {
                        productMainImage.src = newImgSrc;
                        productMainImage.style.opacity = '1';
                    }, 200);
                };
                tempImg.src = newImgSrc;
                
                // Update active thumbnail
                productThumbnails.forEach(t => t.classList.remove('ring-2', 'ring-primary'));
                this.classList.add('ring-2', 'ring-primary');
            });
        });
    }
    
    // Full Gallery modal
    const viewAllImagesBtn = document.getElementById('viewAllImagesBtn');
    const galleryModal = document.getElementById('galleryModal');
    const closeGalleryBtn = document.getElementById('closeGalleryBtn');
    const galleryImage = document.getElementById('galleryImage');
    const galleryThumbnails = document.querySelectorAll('.gallery-thumbnail');
    
    if (viewAllImagesBtn && galleryModal && closeGalleryBtn) {
        viewAllImagesBtn.addEventListener('click', function() {
            galleryModal.classList.remove('invisible', 'opacity-0');
            document.body.classList.add('overflow-hidden');
        });
        
        closeGalleryBtn.addEventListener('click', closeGallery);
        
        galleryModal.addEventListener('click', function(e) {
            if (e.target === galleryModal) {
                closeGallery();
            }
        });
        
        // Gallery thumbnails
        galleryThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imgSrc = this.getAttribute('data-img-src');
                galleryImage.src = imgSrc;
                
                galleryThumbnails.forEach(t => t.classList.remove('border-primary'));
                this.classList.add('border-primary');
            });
        });
    }
    
    function closeGallery() {
        galleryModal.classList.add('opacity-0');
        setTimeout(() => {
            galleryModal.classList.add('invisible');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }
    
    // 3D View modal
    const view3DBtn = document.getElementById('view3DBtn');
    const view3DModal = document.getElementById('view3DModal');
    const closeView3D = document.getElementById('closeView3D');
    
    if (view3DBtn && view3DModal && closeView3D) {
        view3DBtn.addEventListener('click', function() {
            view3DModal.classList.remove('invisible', 'opacity-0');
            document.body.classList.add('overflow-hidden');
        });
        
        closeView3D.addEventListener('click', close3DView);
        
        view3DModal.addEventListener('click', function(e) {
            if (e.target === view3DModal) {
                close3DView();
            }
        });
    }
    
    function close3DView() {
        view3DModal.classList.add('opacity-0');
        setTimeout(() => {
            view3DModal.classList.add('invisible');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }
    
    // Visualize in Room modal
    const visualizeBtn = document.getElementById('visualizeBtn');
    const visualizeModal = document.getElementById('visualizeModal');
    const closeVisualize = document.getElementById('closeVisualize');
    
    if (visualizeBtn && visualizeModal && closeVisualize) {
        visualizeBtn.addEventListener('click', function() {
            visualizeModal.classList.remove('invisible', 'opacity-0');
            document.body.classList.add('overflow-hidden');
        });
        
        closeVisualize.addEventListener('click', closeVisualizeModal);
        
        visualizeModal.addEventListener('click', function(e) {
            if (e.target === visualizeModal) {
                closeVisualizeModal();
            }
        });
    }
    
    function closeVisualizeModal() {
        visualizeModal.classList.add('opacity-0');
        setTimeout(() => {
            visualizeModal.classList.add('invisible');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }
    
    // Share functionality
    const shareBtn = document.getElementById('shareBtn');
    const shareModal = document.getElementById('shareModal');
    const closeShareBtn = document.getElementById('closeShareBtn');
    const shareContent = document.getElementById('shareContent');
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const shareUrl = document.getElementById('shareUrl');
    
    if (shareBtn && shareModal && closeShareBtn) {
        shareBtn.addEventListener('click', function() {
            shareModal.classList.remove('invisible', 'opacity-0');
            shareContent.classList.remove('scale-95');
            shareContent.classList.add('scale-100');
            document.body.classList.add('overflow-hidden');
            
            // Select the URL text
            setTimeout(() => {
                shareUrl.select();
            }, 300);
        });
        
        closeShareBtn.addEventListener('click', closeShare);
        
        shareModal.addEventListener('click', function(e) {
            if (e.target === shareModal) {
                closeShare();
            }
        });
        
        // Copy link functionality
        if (copyLinkBtn && shareUrl) {
            copyLinkBtn.addEventListener('click', function() {
                shareUrl.select();
                document.execCommand('copy');
                
                // Update button text temporarily
                const originalText = copyLinkBtn.textContent;
                copyLinkBtn.textContent = 'Copied!';
                copyLinkBtn.classList.add('bg-secondary');
                copyLinkBtn.classList.remove('bg-primary');
                
                setTimeout(() => {
                    copyLinkBtn.textContent = originalText;
                    copyLinkBtn.classList.remove('bg-secondary');
                    copyLinkBtn.classList.add('bg-primary');
                }, 2000);
            });
        }
    }
    
    function closeShare() {
        shareModal.classList.add('opacity-0');
        shareContent.classList.remove('scale-100');
        shareContent.classList.add('scale-95');
        setTimeout(() => {
            shareModal.classList.add('invisible');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }
    
    // Scroll to offers section when clicking Compare Offers button
    const compareOffersBtn = document.getElementById('compareOffersBtn');
    const offersTab = document.getElementById('offersTab');
    
    if (compareOffersBtn && offersTab) {
        compareOffersBtn.addEventListener('click', function() {
            offersTab.scrollIntoView({ behavior: 'smooth' });
        });
    }
});

// Helper function for color hex codes
function getColorHex(colorName) {
    const colorMap = {
        'black': '#000000',
        'white': '#ffffff',
        'red': '#ff0000',
        'green': '#00ff00',
        'blue': '#0000ff',
        'yellow': '#ffff00',
        'purple': '#800080',
        'pink': '#ffc0cb',
        'orange': '#ffa500',
        'brown': '#a52a2a',
        'gray': '#808080',
        'silver': '#c0c0c0',
        'gold': '#ffd700',
        'beige': '#f5f5dc',
        'ivory': '#fffff0',
        'cream': '#fffdd0',
        'tan': '#d2b48c',
        'navy': '#000080',
        'teal': '#008080',
        'mint': '#98fb98',
        'olive': '#808000',
        'maroon': '#800000',
        'aqua': '#00ffff',
        'turquoise': '#40e0d0',
        'chrome': '#dcdcdc',
        'off-white': '#f8f8ff',
    };
    
    return colorMap[colorName.toLowerCase()] ?? '#cccccc';
}
</script>

<?php include 'includes/footer.php'; ?>
