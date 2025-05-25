<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get search parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 1000;
$visualSearch = isset($_GET['visual']) && $_GET['visual'] === 'true';

// Items per page
$itemsPerPage = 24;
$offset = ($page - 1) * $itemsPerPage;

// Prepare filters
$filters = [];

if ($category) {
    $filters[] = "categories = \"$category\"";
}

// Remove color filter since colorsProcessed is not filterable
// if ($color) {
//     $filters[] = "colorsProcessed = \"$color\"";
// }

// Price filters might not be available either - comment out for now
// if ($minPrice > 0 || $maxPrice < PHP_FLOAT_MAX) {
//     $filters[] = "priceDetails.lowest >= $minPrice AND priceDetails.lowest <= $maxPrice";
// }

// Join filters
$filterStr = implode(' AND ', $filters);

// Prepare sort
$sortOptions = [];

switch ($sort) {
    case 'price_asc':
        $sortOptions = ['priceDetails.lowest:asc'];
        break;
    case 'price_desc':
        $sortOptions = ['priceDetails.lowest:desc'];
        break;
    case 'newest':
        $sortOptions = ['priceDetails.lastUpdatedAt:desc'];
        break;
    case 'discount':
        $sortOptions = ['priceDetails.percentageDrop:desc'];
        break;
    case 'trending':
        // In a real app, this could be based on view counts or popularity metrics
        $sortOptions = ['priceDetails.percentageDrop:desc'];
        break;
    case 'featured':
    default:
        // Default sort (could be relevance for search, popularity for browsing)
        $sortOptions = [];
}

// Perform search
$api = new MeiliSearchAPI();
$searchOptions = [
    'limit' => $itemsPerPage,
    'offset' => $offset,
    'filters' => $filterStr,
    'facets' => ['categories', 'colorsProcessed'],
    'sort' => $sortOptions
];

$searchResult = $api->search($query, $searchOptions);
$products = $searchResult['hits'] ?? [];
$totalProducts = $searchResult['estimatedTotalHits'] ?? 0;
$totalPages = ceil($totalProducts / $itemsPerPage);

// Get categories for faceting
$categories = $searchResult['facetDistribution']['categories'] ?? [];
arsort($categories); // Sort by count (highest first)

// Get colors for faceting
$colors = $searchResult['facetDistribution']['colorsProcessed'] ?? [];
arsort($colors); // Sort by count (highest first)

// Enable search functionality
$enableSearch = true;

// Page meta data
if (!empty($query)) {
    $pageTitle = "Search results for \"$query\"";
    $pageDescription = "Browse interior decoration items matching \"$query\". Find the best deals and compare prices from various stores.";
} elseif (!empty($category)) {
    $pageTitle = "Browse $category";
    $pageDescription = "Explore our collection of $category. Compare prices and find the best deals.";
} elseif (!empty($color)) {
    $pageTitle = ucfirst($color) . " Interior Items";
    $pageDescription = "Discover interior decoration items in " . ucfirst($color) . ". Find pieces that match your color scheme.";
} else {
    $pageTitle = "Discover Interior Pieces";
    $pageDescription = "Explore our curated collection of interior decoration items. Find your perfect piece.";
}

include 'includes/header.php';
?>

<!-- Search Results Page -->
<div class="min-h-screen bg-neutral pb-16">
    <?php if ($visualSearch): ?>
    <!-- Visual Search Banner -->
    <div class="bg-primary text-white py-4">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 8V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H8M4 16V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20H8M16 4H18C18.5304 4 19.0391 4.21071 19.4142 4.58579C19.7893 4.96086 20 5.46957 20 6V8M16 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V16M9 10C9 10.7956 9.31607 11.5587 9.87868 12.1213C10.4413 12.6839 11.2044 13 12 13C12.7956 13 13.5587 12.6839 14.1213 12.1213C14.6839 11.5587 15 10.7956 15 10C15 9.20435 14.6839 8.44129 14.1213 7.87868C13.5587 7.31607 12.7956 7 12 7C11.2044 7 10.4413 7.31607 9.87868 7.87868C9.31607 8.44129 9 9.20435 9 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-medium">Visual Search Results</h2>
                    <p class="text-sm text-white/80">Showing items similar to your uploaded image</p>
                </div>
            </div>
            <button type="button" class="text-white/80 hover:text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Search Header -->
    <div class="bg-white pt-8 pb-4 border-b border-gray-200 sticky top-[72px] z-30 shadow-soft">
        <div class="container mx-auto px-4">
            <?php if (!empty($query) || $visualSearch): ?>
                <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">
                    <?php if ($visualSearch): ?>
                        Visual search results
                    <?php else: ?>
                        Results for "<?php echo htmlspecialchars($query); ?>"
                    <?php endif; ?>
                </h1>
                <p class="text-gray-500 mb-4">Found <?php echo number_format($totalProducts); ?> items</p>
            <?php elseif (!empty($category)): ?>
                <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">
                    Browsing <?php echo htmlspecialchars($category); ?>
                </h1>
                <p class="text-gray-500 mb-4">Found <?php echo number_format($totalProducts); ?> items</p>
            <?php elseif (!empty($color)): ?>
                <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">
                    <span class="inline-block w-5 h-5 rounded-full mr-2" style="background-color: <?php echo getColorHex($color); ?>;"></span>
                    <?php echo ucfirst(htmlspecialchars($color)); ?> Items
                </h1>
                <p class="text-gray-500 mb-4">Found <?php echo number_format($totalProducts); ?> items</p>
            <?php else: ?>
                <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">All Products</h1>
                <p class="text-gray-500 mb-4">Browse our curated collection of interior items</p>
            <?php endif; ?>
            
            <!-- Filter chips -->
            <div class="flex flex-wrap gap-2 mt-4 overflow-x-auto scrollable-container pb-2 -mx-4 px-4">
                <a href="<?php echo buildUrlWithoutParam(array('sort', 'page')); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo empty($sort) ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Relevance
                </a>
                <a href="<?php echo buildUrlWithParams(array('sort' => 'price_asc', 'page' => 1)); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo $sort === 'price_asc' ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Price: Low to High
                </a>
                <a href="<?php echo buildUrlWithParams(array('sort' => 'price_desc', 'page' => 1)); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo $sort === 'price_desc' ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Price: High to Low
                </a>
                <a href="<?php echo buildUrlWithParams(array('sort' => 'discount', 'page' => 1)); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo $sort === 'discount' ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Biggest Discounts
                </a>
                <a href="<?php echo buildUrlWithParams(array('sort' => 'newest', 'page' => 1)); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo $sort === 'newest' ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Newest
                </a>
                <a href="<?php echo buildUrlWithParams(array('sort' => 'trending', 'page' => 1)); ?>" class="inline-flex items-center px-4 py-2 rounded-full <?php echo $sort === 'trending' ? 'bg-primary text-white' : 'bg-neutral text-dark hover:bg-neutral/80'; ?> text-sm whitespace-nowrap transition">
                    Trending
                </a>
                
                <?php if ($totalProducts > 0): ?>
                <!-- Compare button (shows when items are selected) -->
                <div class="ml-auto hidden" id="compareContainer">
                    <button id="compareBtn" class="inline-flex items-center px-4 py-2 rounded-full bg-secondary text-white opacity-50 cursor-not-allowed text-sm whitespace-nowrap transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                            <path d="M9 10L4 15L9 20M15 4L20 9L15 14M17 9H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Compare (<span id="compareCount">0</span>)
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 pt-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-64 flex-shrink-0 space-y-6">
                <!-- Active filters summary -->
                <?php if (!empty($category) || !empty($color) || $minPrice > 0 || $maxPrice < 1000): ?>
                <div>
                    <h3 class="font-medium mb-3">Active Filters</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($category)): ?>
                        <div class="inline-flex items-center bg-primary/10 text-primary rounded-full px-3 py-1.5 text-sm">
                            <span><?php echo htmlspecialchars($category); ?></span>
                            <a href="<?php echo buildUrlWithoutParam('category'); ?>" class="ml-2 hover:text-primary/80">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($color)): ?>
                        <div class="inline-flex items-center bg-primary/10 text-primary rounded-full px-3 py-1.5 text-sm">
                            <span class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full mr-1" style="background-color: <?php echo getColorHex($color); ?>;"></span>
                                <?php echo ucfirst(htmlspecialchars($color)); ?>
                            </span>
                            <a href="<?php echo buildUrlWithoutParam('color'); ?>" class="ml-2 hover:text-primary/80">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($minPrice > 0 || $maxPrice < 1000): ?>
                        <div class="inline-flex items-center bg-primary/10 text-primary rounded-full px-3 py-1.5 text-sm">
                            <span>Price: $<?php echo $minPrice; ?> - $<?php echo $maxPrice; ?></span>
                            <a href="<?php echo buildUrlWithoutParam(array('minPrice', 'maxPrice')); ?>" class="ml-2 hover:text-primary/80">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php echo buildUrlWithoutParam(array('category', 'color', 'minPrice', 'maxPrice', 'page')); ?>" class="inline-block text-sm text-primary hover:underline mt-3">
                        Clear all filters
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Price Range Filter -->
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Price Range</h3>
                    
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span id="priceValue" class="text-sm font-medium">$<?php echo $minPrice; ?> - $<?php echo $maxPrice; ?></span>
                        </div>
                        
                        <div class="relative h-1 bg-gray-200 rounded-full">
                            <div class="absolute top-0 left-0 h-full bg-primary rounded-full" style="width: <?php echo ($maxPrice - $minPrice) / 10; ?>%; left: <?php echo $minPrice / 10; ?>%;"></div>
                        </div>
                        
                        <div class="mt-8">
                            <div class="flex space-x-4 items-center">
                                <div class="flex-1">
                                    <label for="minPrice" class="block text-xs text-gray-500 mb-1">Min Price</label>
                                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                        <span class="bg-neutral px-2 flex items-center text-gray-500">$</span>
                                        <input type="number" id="minPrice" name="minPrice" min="0" max="1000" step="10" value="<?php echo $minPrice; ?>" class="w-full py-2 px-2 focus:outline-none">
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <label for="maxPrice" class="block text-xs text-gray-500 mb-1">Max Price</label>
                                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                        <span class="bg-neutral px-2 flex items-center text-gray-500">$</span>
                                        <input type="number" id="maxPrice" name="maxPrice" min="0" max="1000" step="10" value="<?php echo $maxPrice; ?>" class="w-full py-2 px-2 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                            
                                                        <button id="applyPriceFilter" class="mt-4 w-full py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Categories</h3>
                    
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                        <?php $count = 0; ?>
                        <?php foreach ($categories as $cat => $num): ?>
                            <?php if ($count++ < 10): ?>
                            <label class="flex items-center group cursor-pointer">
                                <input type="radio" name="category" value="<?php echo $cat; ?>" 
                                    class="sr-only"
                                    <?php echo ($category === $cat) ? 'checked' : ''; ?> 
                                    onchange="window.location.href='<?php echo buildUrlWithParams(array('category' => $cat, 'page' => 1)); ?>'">
                                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center mr-3 group-hover:border-primary transition-all">
                                    <?php if ($category === $cat): ?>
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    <?php endif; ?>
                                </span>
                                <span class="flex-1 text-sm"><?php echo $cat; ?></span>
                                <span class="text-xs text-gray-500"><?php echo $num; ?></span>
                            </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if (count($categories) > 10): ?>
                        <button type="button" id="showMoreCategories" class="text-primary text-sm hover:underline">
                            Show more
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Color Filter -->
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Colors</h3>
                    
                    <div class="flex flex-wrap gap-2">
                        <?php $colorCount = 0; ?>
                        <?php foreach ($colors as $col => $num): ?>
                            <?php if (++$colorCount <= 15): ?>
                            <label class="color-radio cursor-pointer">
                                <input type="radio" name="color" value="<?php echo $col; ?>" 
                                    <?php echo ($color === $col) ? 'checked' : ''; ?> 
                                    onchange="window.location.href='<?php echo buildUrlWithParams(array('color' => $col, 'page' => 1)); ?>'">
                                <div class="color-swatch w-8 h-8 rounded-lg transition-transform" style="background-color: <?php echo getColorHex($col); ?>;" title="<?php echo ucfirst($col); ?>"></div>
                            </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if (count($colors) > 15): ?>
                        <button type="button" class="text-primary text-sm hover:underline flex items-center">
                            <span class="w-8 h-8 rounded-lg border border-primary flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Results Grid -->
            <div class="flex-1">
                <?php if (count($products) > 0): ?>
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($products as $product): ?>
                    <?php 
                        $imgUrl = !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                        $lowestPrice = $product['priceDetails']['lowest'] ?? 0;
                        $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                        $offerCount = count($product['offers'] ?? []);
                        $isInWishlist = isInWishlist($product['id']);
                    ?>
                    <div class="product-card">
                        <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                            <div class="relative aspect-square overflow-hidden">
                                <a href="/product.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                                </a>
                                
                                <div class="absolute top-3 right-3 flex flex-col gap-2">
                                    <button class="wishlist-btn w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition shadow-soft" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 text-sm"></i>
                                    </button>
                                    
                                    <label class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition shadow-soft cursor-pointer">
                                        <input type="checkbox" class="compare-checkbox sr-only" name="compare[]" value="<?php echo $product['id']; ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="compare-icon">
                                            <path d="M9 10L4 15L9 20M15 4L20 9L15 14M17 9H7" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </label>
                                </div>
                                
                                <?php if (isset($product['priceDetails']['percentageDrop']) && $product['priceDetails']['percentageDrop'] > 0): ?>
                                <div class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-medium">
                                    <?php echo $product['priceDetails']['percentageDrop']; ?>% OFF
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-4">
                                <?php if (!empty($product['categories'])): ?>
                                <div class="text-xs text-gray-500 mb-1"><?php echo $product['categories'][0]; ?></div>
                                <?php endif; ?>
                                
                                <h3 class="font-medium text-sm mb-2 line-clamp-2 hover:text-primary transition">
                                    <a href="/product.php?id=<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                
                                <div class="flex items-center justify-between">
                                    <div class="text-lg font-semibold text-primary"><?php echo $priceStr; ?></div>
                                    <?php if ($offerCount > 0): ?>
                                    <div class="text-xs py-1 px-2 bg-neutral rounded-lg">
                                        <span><?php echo $offerCount; ?> store<?php echo $offerCount > 1 ? 's' : ''; ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <div class="inline-flex rounded-xl overflow-hidden shadow-soft">
                        <?php if ($page > 1): ?>
                        <a href="<?php echo buildUrlWithParams(array('page' => $page - 1)); ?>" class="px-4 py-2 bg-white hover:bg-neutral transition text-dark font-medium flex items-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Prev
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 bg-white text-gray-400 cursor-not-allowed font-medium flex items-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Prev
                        </span>
                        <?php endif; ?>
                        
                        <div class="border-l border-r border-gray-200 px-4 py-2 bg-white font-medium">
                            <?php echo $page; ?> of <?php echo $totalPages; ?>
                        </div>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="<?php echo buildUrlWithParams(array('page' => $page + 1)); ?>" class="px-4 py-2 bg-white hover:bg-neutral transition text-dark font-medium flex items-center">
                            Next
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1">
                                <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 bg-white text-gray-400 cursor-not-allowed font-medium flex items-center">
                            Next
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1">
                                <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Fixed Compare Button (Mobile) -->
                <div id="compareFab" class="fixed bottom-24 right-4 z-30 hidden">
                    <button type="button" class="flex items-center justify-center bg-secondary text-white px-4 py-2 rounded-full shadow-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                            <path d="M9 10L4 15L9 20M15 4L20 9L15 14M17 9H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Compare (<span id="mobileCompareCount">0</span>)
                    </button>
                </div>
                
                <?php else: ?>
                <!-- No Results Found -->
                <div class="bg-white rounded-xl shadow-soft p-12 text-center">
                    <div class="w-20 h-20 bg-neutral rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10L14 14M14 10L10 14M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">No results found</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">We couldn't find any products that match your criteria. Try adjusting your filters or search terms.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php if (!empty($query) || !empty($category) || !empty($color) || $minPrice > 0 || $maxPrice < 1000): ?>
                        <a href="<?php echo buildUrlWithoutParam(array('q', 'category', 'color', 'minPrice', 'maxPrice', 'page')); ?>" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                            Clear All Filters
                        </a>
                        <?php endif; ?>
                        <a href="/" class="px-6 py-3 bg-neutral hover:bg-gray-200 rounded-xl font-medium transition flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M3 12L5 10M5 10L12 3L19 10M5 10V20C5 20.2652 5.10536 20.5196 5.29289 20.7071C5.48043 20.8946 5.73478 21 6 21H9M19 10L21 12M19 10V20C19 20.2652 18.8946 20.5196 18.7071 20.7071C18.5196 20.8946 18.2652 21 18 21H15M9 21C9.26522 21 9.51957 20.8946 9.70711 20.7071C9.89464 20.5196 10 20.2652 10 20V16C10 15.7348 10.1054 15.4804 10.2929 15.2929C10.4804 15.1054 10.7348 15 11 15H13C13.2652 15 13.5196 15.1054 13.7071 15.2929C13.8946 15.4804 14 15.7348 14 16V20C14 20.2652 14.1054 20.5196 14.2929 20.7071C14.4804 20.8946 14.7348 21 15 21M9 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Search Page Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price filter
        const applyPriceFilter = document.getElementById('applyPriceFilter');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        if (applyPriceFilter && minPriceInput && maxPriceInput) {
            applyPriceFilter.addEventListener('click', function() {
                const minPrice = minPriceInput.value;
                const maxPrice = maxPriceInput.value;
                
                window.location.href = '<?php echo buildUrlWithoutParam(array('minPrice', 'maxPrice', 'page')); ?>' + 
                    (window.location.search ? '&' : '?') + 
                    'minPrice=' + minPrice + 
                    '&maxPrice=' + maxPrice +
                    '&page=1';
            });
            
            // Update price display when inputs change
            function updatePriceDisplay() {
                document.getElementById('priceValue').textContent = '$' + minPriceInput.value + ' - $' + maxPriceInput.value;
            }
            
            minPriceInput.addEventListener('input', updatePriceDisplay);
            maxPriceInput.addEventListener('input', updatePriceDisplay);
        }
        
        // Compare checkboxes
        const compareCheckboxes = document.querySelectorAll('.compare-checkbox');
        const compareBtn = document.getElementById('compareBtn');
        const compareContainer = document.getElementById('compareContainer');
        const compareFab = document.getElementById('compareFab');
        const compareCount = document.getElementById('compareCount');
        const mobileCompareCount = document.getElementById('mobileCompareCount');
        
        if (compareCheckboxes.length > 0) {
            const maxCompare = 4; // Maximum number of items to compare
            const comparedItems = [];
            
            compareCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const productId = this.value;
                    const icon = this.nextElementSibling;
                    
                    if (this.checked) {
                        if (comparedItems.length < maxCompare) {
                            comparedItems.push(productId);
                            icon.classList.add('text-secondary');
                            icon.classList.remove('text-gray-500');
                        } else {
                            this.checked = false;
                            showToast(`You can compare maximum ${maxCompare} items`, 'error');
                        }
                    } else {
                        const index = comparedItems.indexOf(productId);
                        if (index !== -1) {
                            comparedItems.splice(index, 1);
                            icon.classList.remove('text-secondary');
                            icon.classList.add('text-gray-500');
                        }
                    }
                    
                    // Update compare count and visibility
                    if (compareCount) compareCount.textContent = comparedItems.length;
                    if (mobileCompareCount) mobileCompareCount.textContent = comparedItems.length;
                    
                    if (comparedItems.length > 0) {
                        if (compareContainer) compareContainer.classList.remove('hidden');
                        if (compareFab) compareFab.classList.remove('hidden');
                    } else {
                        if (compareContainer) compareContainer.classList.add('hidden');
                        if (compareFab) compareFab.classList.add('hidden');
                    }
                    
                    // Update compare button state
                    if (compareBtn) {
                        if (comparedItems.length > 1) {
                            compareBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        } else {
                            compareBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                });
            });
            
            // Handle compare button click
            if (compareBtn) {
                compareBtn.addEventListener('click', function(e) {
                    if (comparedItems.length > 1) {
                        window.location.href = `/compare.php?ids=${comparedItems.join(',')}`;
                    } else {
                        e.preventDefault();
                        showToast('Please select at least 2 items to compare', 'error');
                    }
                });
            }
            
            // Handle compare fab click (mobile)
            if (compareFab) {
                compareFab.addEventListener('click', function(e) {
                    if (comparedItems.length > 1) {
                        window.location.href = `/compare.php?ids=${comparedItems.join(',')}`;
                    } else {
                        e.preventDefault();
                        showToast('Please select at least 2 items to compare', 'error');
                    }
                });
            }
        }
        
        // Show more categories toggle
        const showMoreCategoriesBtn = document.getElementById('showMoreCategories');
        if (showMoreCategoriesBtn) {
            showMoreCategoriesBtn.addEventListener('click', function() {
                const labels = this.parentElement.querySelectorAll('label');
                labels.forEach(label => {
                    if (label.classList.contains('hidden')) {
                        label.classList.remove('hidden');
                    }
                });
                this.classList.add('hidden');
            });
        }
        
        // Show toast message
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed left-1/2 transform -translate-x-1/2 bottom-6 py-3 px-4 rounded-xl shadow-lg z-50 transition-all duration-300 opacity-0 flex items-center ${type === 'success' ? 'bg-dark text-white' : 'bg-red-500 text-white'}`;
            
            // Add icon based on type
            const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            
            toast.innerHTML = `
                <i class="${iconClass} mr-2"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);
            
            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                toast.style.transform += ' translateY(20px)';
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    });
</script>

<?php
// Helper function to build URL with additional parameters
function buildUrlWithParams($params = array()) {
    $query = $_GET;
    
    foreach ($params as $key => $value) {
        $query[$key] = $value;
    }
    
    return '?' . http_build_query($query);
}

// Helper function to build URL without specific parameters
function buildUrlWithoutParam($param = array()) {
    $query = $_GET;
    
    if (is_array($param)) {
        foreach ($param as $p) {
            if (isset($query[$p])) {
                unset($query[$p]);
            }
        }
    } else {
        if (isset($query[$param])) {
            unset($query[$param]);
        }
    }
    
    return empty($query) ? '?' : '?' . http_build_query($query);
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
    
    return $colorMap[strtolower($colorName)] ?? '#cccccc';
}
?>

<?php include 'includes/footer.php'; ?>

