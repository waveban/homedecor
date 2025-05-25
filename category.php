<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get category name from URL
$categoryName = isset($_GET['name']) ? urldecode($_GET['name']) : '';

// Get all colors
$allColors = getAllColors();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 24;
$offset = ($page - 1) * $itemsPerPage;

// Get all categories if no specific category is requested
$allCategories = getAllCategories();
arsort($allCategories); // Sort by count (highest first)

// Organize categories into hierarchical structure
$categoryTree = [];
foreach ($allCategories as $category => $count) {
    $parts = explode(', ', $category);
    
    if (count($parts) === 1) {
        // Top level category
        if (!isset($categoryTree[$category])) {
            $categoryTree[$category] = [
                'count' => $count,
                'children' => []
            ];
        } else {
            $categoryTree[$category]['count'] = $count;
        }
    } else {
        // Subcategory
        $parent = $parts[0];
        $child = $parts[1];
        
        if (!isset($categoryTree[$parent])) {
            $categoryTree[$parent] = [
                'count' => 0,
                'children' => []
            ];
        }
        
        $categoryTree[$parent]['children'][$child] = $count;
    }
}

// Sort top-level categories by count
uasort($categoryTree, function($a, $b) {
    return $b['count'] - $a['count'];
});

// If a category is specified, get its products
$categoryProducts = [];
$totalProducts = 0;
$totalPages = 0;
$selectedCategoryColors = [];

if (!empty($categoryName)) {
    // Prepare filters
    $filters = '';
    
    // Get color from query string if set
    $colorFilter = isset($_GET['color']) ? $_GET['color'] : '';
    
    if (!empty($colorFilter)) {
        $filters = "colorsProcessed = \"$colorFilter\"";
    }
    
    // Get min/max price if set
    $minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : 0;
    $maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 1000;
    
    if ($minPrice > 0 || $maxPrice < 1000) {
        $priceFilter = "priceDetails.lowest >= $minPrice AND priceDetails.lowest <= $maxPrice";
        $filters = !empty($filters) ? "$filters AND $priceFilter" : $priceFilter;
    }
    
    // Get sort option
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
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
    
    // Fetch products in this category
    $api = new MeiliSearchAPI();
    $result = $api->getCategory($categoryName, [
        'limit' => $itemsPerPage,
        'offset' => $offset,
        'filters' => $filters,
        'sort' => $sortOptions
    ]);
    
    $categoryProducts = $result['hits'] ?? [];
    $totalProducts = $result['estimatedTotalHits'] ?? 0;
    $totalPages = ceil($totalProducts / $itemsPerPage);
    
    // Get available colors for this category
    $selectedCategoryColors = $result['facetDistribution']['colorsProcessed'] ?? [];
    arsort($selectedCategoryColors); // Sort by count (highest first)
}

// Enable search functionality
$enableSearch = true;

// Page meta data
if (!empty($categoryName)) {
    $pageTitle = $categoryName;
    $pageDescription = "Browse our collection of $categoryName. Find the best deals and compare prices from various stores.";
} else {
    $pageTitle = "Categories";
    $pageDescription = "Explore our extensive range of interior decoration categories. Find furniture, decor, and more.";
}

include 'includes/header.php';
?>

<?php if (!empty($categoryName)): ?>
<!-- Category Products Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Category Header -->
    <div class="bg-white pt-8 pb-4 border-b border-gray-200 sticky top-[72px] z-30 shadow-soft">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">
                <?php echo htmlspecialchars($categoryName); ?>
            </h1>
            <p class="text-gray-500 mb-4">Found <?php echo number_format($totalProducts); ?> items</p>
            
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
            
            <!-- Active filters -->
            <?php if (!empty($_GET['color']) || (isset($_GET['minPrice']) && $_GET['minPrice'] > 0) || (isset($_GET['maxPrice']) && $_GET['maxPrice'] < 1000)): ?>
            <div class="mt-4">
                <div class="text-sm font-medium text-gray-500 mb-2">Active Filters:</div>
                <div class="flex flex-wrap gap-2">
                    <?php if (!empty($_GET['color'])): ?>
                    <div class="inline-flex items-center bg-primary/10 text-primary rounded-full px-3 py-1.5 text-sm">
                        <span class="inline-block w-3 h-3 rounded-full mr-1" style="background-color: <?php echo getColorHex($_GET['color']); ?>;"></span>
                        <?php echo ucfirst(htmlspecialchars($_GET['color'])); ?>
                        <a href="<?php echo buildUrlWithoutParam('color'); ?>" class="ml-2 hover:text-primary/80">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ((isset($_GET['minPrice']) && $_GET['minPrice'] > 0) || (isset($_GET['maxPrice']) && $_GET['maxPrice'] < 1000)): ?>
                    <div class="inline-flex items-center bg-primary/10 text-primary rounded-full px-3 py-1.5 text-sm">
                        Price: $<?php echo isset($_GET['minPrice']) ? $_GET['minPrice'] : '0'; ?> - $<?php echo isset($_GET['maxPrice']) ? $_GET['maxPrice'] : '1000'; ?>
                        <a href="<?php echo buildUrlWithoutParam(array('minPrice', 'maxPrice')); ?>" class="ml-2 hover:text-primary/80">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo buildUrlWithoutParam(array('color', 'minPrice', 'maxPrice')); ?>" class="text-primary text-sm hover:underline">
                        Clear all filters
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container mx-auto px-4 pt-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-64 flex-shrink-0 space-y-6">
                <!-- Price Range Filter -->
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Price Range</h3>
                    
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span id="priceValue" class="text-sm font-medium">
                                $<?php echo isset($_GET['minPrice']) ? $_GET['minPrice'] : '0'; ?> - $<?php echo isset($_GET['maxPrice']) ? $_GET['maxPrice'] : '1000'; ?>
                            </span>
                        </div>
                        
                        <div class="relative h-1 bg-gray-200 rounded-full">
                            <div class="absolute top-0 left-0 h-full bg-primary rounded-full" style="width: <?php echo ((isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 1000) - (isset($_GET['minPrice']) ? $_GET['minPrice'] : 0)) / 10; ?>%; left: <?php echo (isset($_GET['minPrice']) ? $_GET['minPrice'] : 0) / 10; ?>%;"></div>
                        </div>
                        
                        <div class="mt-8">
                            <div class="flex space-x-4 items-center">
                                <div class="flex-1">
                                    <label for="minPrice" class="block text-xs text-gray-500 mb-1">Min Price</label>
                                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                        <span class="bg-neutral px-2 flex items-center text-gray-500">$</span>
                                        <input type="number" id="minPrice" name="minPrice" min="0" max="1000" step="10" value="<?php echo isset($_GET['minPrice']) ? $_GET['minPrice'] : '0'; ?>" class="w-full py-2 px-2 focus:outline-none">
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <label for="maxPrice" class="block text-xs text-gray-500 mb-1">Max Price</label>
                                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                        <span class="bg-neutral px-2 flex items-center text-gray-500">$</span>
                                        <input type="number" id="maxPrice" name="maxPrice" min="0" max="1000" step="10" value="<?php echo isset($_GET['maxPrice']) ? $_GET['maxPrice'] : '1000'; ?>" class="w-full py-2 px-2 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                            
                            <button id="applyPriceFilter" class="mt-4 w-full py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Color Filter -->
                <?php if (!empty($selectedCategoryColors)): ?>
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Colors</h3>
                    
                    <div class="flex flex-wrap gap-2">
                        <?php $colorCount = 0; ?>
                        <?php foreach ($selectedCategoryColors as $col => $num): ?>
                            <?php if (++$colorCount <= 24): ?>
                            <label class="color-radio cursor-pointer">
                                <input type="radio" name="color" value="<?php echo $col; ?>" 
                                    <?php echo (isset($_GET['color']) && $_GET['color'] === $col) ? 'checked' : ''; ?> 
                                    onchange="window.location.href='<?php echo buildUrlWithParams(array('color' => $col, 'page' => 1)); ?>'">
                                <div class="color-swatch w-8 h-8 rounded-lg transition-transform" style="background-color: <?php echo getColorHex($col); ?>;" title="<?php echo ucfirst($col); ?>"></div>
                            </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Subcategories -->
                <?php
                // Find subcategories of current category
                $subcategories = [];
                foreach ($allCategories as $cat => $count) {
                    if (strpos($cat, "$categoryName, ") === 0) {
                        $subcategory = substr($cat, strlen("$categoryName, "));
                        $subcategories[$subcategory] = $count;
                    }
                }
                
                if (!empty($subcategories)):
                ?>
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4"><?php echo htmlspecialchars($categoryName); ?> Subcategories</h3>
                    
                    <div class="space-y-2">
                        <?php foreach ($subcategories as $subcat => $count): ?>
                        <a href="/category.php?name=<?php echo urlencode("$categoryName, $subcat"); ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-neutral transition">
                            <span><?php echo htmlspecialchars($subcat); ?></span>
                            <span class="text-xs text-gray-500"><?php echo $count; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Related Categories -->
                <?php
                // Find sibling categories (categories that share a parent with current category)
                $relatedCategories = [];
                $parentCategory = '';
                
                if (strpos($categoryName, ', ') !== false) {
                    // This is a subcategory, find its parent and siblings
                    $parts = explode(', ', $categoryName);
                    $parentCategory = $parts[0];
                    
                    foreach ($allCategories as $cat => $count) {
                        if ($cat !== $categoryName && strpos($cat, "$parentCategory, ") === 0) {
                            $siblingCategory = substr($cat, strlen("$parentCategory, "));
                            $relatedCategories[$siblingCategory] = $count;
                        }
                    }
                } else {
                    // This is a top-level category, find other top-level categories
                    foreach (array_keys($categoryTree) as $topCategory) {
                        if ($topCategory !== $categoryName) {
                            $relatedCategories[$topCategory] = $categoryTree[$topCategory]['count'];
                        }
                    }
                }
                
                // Sort related categories by count
                arsort($relatedCategories);
                
                // Take only top 5
                $relatedCategories = array_slice($relatedCategories, 0, 5, true);
                
                if (!empty($relatedCategories)):
                ?>
                <div class="bg-white rounded-xl p-5 shadow-soft">
                    <h3 class="font-medium mb-4">Related Categories</h3>
                    
                    <div class="space-y-2">
                        <?php if (!empty($parentCategory)): ?>
                        <a href="/category.php?name=<?php echo urlencode($parentCategory); ?>" class="flex items-center py-2 px-3 rounded-lg bg-primary/10 text-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to <?php echo htmlspecialchars($parentCategory); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php foreach ($relatedCategories as $relatedCat => $count): ?>
                        <a href="/category.php?name=<?php echo !empty($parentCategory) ? urlencode("$parentCategory, $relatedCat") : urlencode($relatedCat); ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-neutral transition">
                            <span><?php echo htmlspecialchars($relatedCat); ?></span>
                            <span class="text-xs text-gray-500"><?php echo $count; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Results Grid -->
            <div class="flex-1">
                <?php if (count($categoryProducts) > 0): ?>
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($categoryProducts as $product): ?>
                    <?php 
                        // Handle nested product structure
                        $productData = isset($product['product']) ? $product['product'] : $product;
                        
                        $imgUrl = !empty($productData['imageUrls']) ? $productData['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                        $lowestPrice = $productData['priceDetails']['lowest'] ?? 0;
                        $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                        $offerCount = count($productData['offers'] ?? []);
                        $productId = $productData['id'] ?? $product['id'] ?? '';
                        $isInWishlist = isInWishlist($productId);
                    ?>
                    <div class="product-card">
                        <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                            <div class="relative aspect-square overflow-hidden">
                                <a href="/product.php?id=<?php echo $productId; ?>">
                                    <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($productData['name']); ?>" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                                </a>
                                
                                <div class="absolute top-3 right-3 flex flex-col gap-2">
                                    <button class="wishlist-btn w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition shadow-soft" data-product-id="<?php echo $productId; ?>">
                                        <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500 text-sm"></i>
                                    </button>
                                    
                                    <label class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition shadow-soft cursor-pointer">
                                        <input type="checkbox" class="compare-checkbox sr-only" name="compare[]" value="<?php echo $productId; ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="compare-icon">
                                            <path d="M9 10L4 15L9 20M15 4L20 9L15 14M17 9H7" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </label>
                                </div>
                                
                                <?php if (isset($productData['priceDetails']['percentageDrop']) && $productData['priceDetails']['percentageDrop'] > 0): ?>
                                <div class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-medium">
                                    <?php echo $productData['priceDetails']['percentageDrop']; ?>% OFF
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-4">
                                <?php if (!empty($productData['categories'])): ?>
                                <div class="text-xs text-gray-500 mb-1"><?php echo $productData['categories'][0]; ?></div>
                                <?php endif; ?>
                                
                                <h3 class="font-medium text-sm mb-2 line-clamp-2 hover:text-primary transition">
                                    <a href="/product.php?id=<?php echo $productId; ?>">
                                        <?php echo htmlspecialchars($productData['name']); ?>
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
                    <h2 class="text-2xl font-bold mb-4">No products found</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">We couldn't find any products that match your criteria. Try adjusting your filters or browse other categories.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php if (!empty($_GET['color']) || (isset($_GET['minPrice']) && $_GET['minPrice'] > 0) || (isset($_GET['maxPrice']) && $_GET['maxPrice'] < 1000)): ?>
                        <a href="<?php echo buildUrlWithoutParam(array('color', 'minPrice', 'maxPrice')); ?>" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                            Clear All Filters
                        </a>
                        <?php endif; ?>
                        <a href="/category.php" class="px-6 py-3 bg-neutral hover:bg-gray-200 rounded-xl font-medium transition flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Categories
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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
        
        // Compare checkboxes functionality
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

<?php else: ?>
<!-- All Categories Page -->
<div class="min-h-screen bg-neutral pb-16">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-display font-bold mb-6">Browse Categories</h1>
        
        <!-- Featured Categories with Images -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <?php 
            // Select top 4 categories for featured display
            $featuredCategories = [
                'Furniture' => [
                    'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'icon' => 'fas fa-couch'
                ],
                'Living Room Furniture' => [
                    'image' => 'https://images.unsplash.com/photo-1616486029423-aaa4789e8c9a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'icon' => 'fas fa-tv'
                ],
                'Accent Chairs' => [
                    'image' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'icon' => 'fas fa-chair'
                ],
                'Ottomans & Poufs' => [
                    'image' => 'https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg',
                    'icon' => 'fas fa-square'
                ]
            ];
            
            foreach ($featuredCategories as $catName => $catInfo): 
                $count = $allCategories[$catName] ?? 0;
            ?>
            <a href="/category.php?name=<?php echo urlencode($catName); ?>" class="group">
                <div class="relative h-60 rounded-xl overflow-hidden shadow-soft hover-card">
                    <img src="<?php echo $catInfo['image']; ?>" alt="<?php echo htmlspecialchars($catName); ?>" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-dark/20 to-transparent"></div>
                    <div class="absolute bottom-0 p-4 w-full">
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl">
                            <div class="text-white flex items-center">
                                <i class="<?php echo $catInfo['icon']; ?> text-xl mr-3"></i>
                                <div>
                                    <span class="font-medium"><?php echo htmlspecialchars($catName); ?></span>
                                    <p class="text-xs text-white/80"><?php echo $count; ?> products</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- All Categories List -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h2 class="text-xl font-bold mb-6">All Categories</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($categoryTree as $mainCategory => $details): ?>
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-neutral/30 p-4 border-b border-gray-200">
                        <a href="/category.php?name=<?php echo urlencode($mainCategory); ?>" class="flex items-center">
                            <?php 
                            // Get icon for category
                            $icon = getCategoryIcon($mainCategory);
                            ?>
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3">
                                <i class="<?php echo $icon; ?>"></i>
                            </div>
                            <div>
                                <h3 class="font-medium"><?php echo htmlspecialchars($mainCategory); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo $details['count']; ?> products</p>
                            </div>
                        </a>
                    </div>
                    
                    <?php if (!empty($details['children'])): ?>
                    <div class="p-4 divide-y divide-gray-100">
                        <?php 
                        // Sort subcategories by count
                        arsort($details['children']);
                        
                        // Show only top 5 subcategories
                        $subcats = array_slice($details['children'], 0, 5, true);
                        
                        foreach ($subcats as $subcategory => $count): 
                        ?>
                        <a href="/category.php?name=<?php echo urlencode("$mainCategory, $subcategory"); ?>" class="py-2 flex items-center justify-between hover:text-primary">
                            <span class="text-sm"><?php echo htmlspecialchars($subcategory); ?></span>
                            <span class="text-xs text-gray-500"><?php echo $count; ?></span>
                        </a>
                        <?php endforeach; ?>
                        
                        <?php if (count($details['children']) > 5): ?>
                        <a href="/category.php?name=<?php echo urlencode($mainCategory); ?>" class="py-2 text-sm text-primary flex items-center hover:underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                <path d="M19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            View all <?php echo count($details['children']); ?> subcategories
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Browse by Color -->
        <div class="mt-12">
            <h2 class="text-xl font-bold mb-6">Browse by Color</h2>
            
            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex flex-wrap gap-4">
                    <?php 
                    // Take top 20 colors
                    $topColors = array_slice($allColors, 0, 20, true);
                    foreach ($topColors as $color => $count):
                    ?>
                    <a href="/search.php?color=<?php echo urlencode($color); ?>" class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full border-2 border-white shadow-md hover:scale-110 transition" style="background-color: <?php echo getColorHex($color); ?>;"></div>
                        <span class="text-xs mt-1"><?php echo ucfirst($color); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php
// Helper functions
function buildUrlWithParams($params = array()) {
    $query = $_GET;
    
    foreach ($params as $key => $value) {
        $query[$key] = $value;
    }
    
    return '?' . http_build_query($query);
}

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

// Helper function to get an appropriate icon for a category
function getCategoryIcon($category) {
    $iconMap = [
        'furniture' => 'fas fa-couch',
        'living room' => 'fas fa-tv',
        'bedroom' => 'fas fa-bed',
        'dining room' => 'fas fa-utensils',
        'kitchen' => 'fas fa-blender',
        'bathroom' => 'fas fa-bath',
        'outdoor' => 'fas fa-tree',
        'lighting' => 'fas fa-lightbulb',
        'decor' => 'fas fa-paint-brush',
        'rugs' => 'fas fa-rug',
        'storage' => 'fas fa-box',
        'office' => 'fas fa-briefcase',
        'kids' => 'fas fa-child',
        'pet' => 'fas fa-paw',
        'curtains' => 'fas fa-curtains',
        'pillows' => 'fas fa-blanket',
        'chairs' => 'fas fa-chair',
        'tables' => 'fas fa-table',
        'sofas' => 'fas fa-couch',
        'ottomans' => 'fas fa-square',
        'bar stools' => 'fas fa-glass-martini-alt',
        'accent chairs' => 'fas fa-chair',
    ];
    
    $category = strtolower($category);
    
    foreach ($iconMap as $key => $icon) {
        if (strpos($category, $key) !== false) {
            return $icon;
        }
    }
    
    // Default icon
    return 'fas fa-box';
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
