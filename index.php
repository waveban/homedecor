<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get featured and trending products
$featuredProducts = getFeaturedProducts(8);
$trendingProducts = getTrendingProducts(8);

// Get all available colors
$allColors = getAllColors();
arsort($allColors);
$topColors = array_slice($allColors, 0, 8, true);

// Enable search functionality
$enableSearch = true;

// Page meta data
$pageTitle = 'Discover Interior Pieces';
$pageDescription = 'Explore, curate, and compare interior decoration pieces to create your dream space.';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative overflow-hidden bg-white py-12 md:py-20 border-t-4 border-primary">
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-12 md:mb-0">
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-dark">Discover your <span class="text-primary">perfect</span> interior pieces</h1>
                <p class="text-gray-600 text-lg mb-8 max-w-lg">Search, compare, and curate interior decoration items with advanced tools to visualize them in your space.</p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#explore" class="flex-none px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition flex items-center justify-center shadow-soft">
                        <span>Start exploring</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path d="M13 5L20 12L13 19M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                    <a href="#" class="px-6 py-3 border-2 border-primary/30 hover:border-primary/50 text-dark rounded-xl font-medium transition flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2 text-primary">
                            <path d="M6 9C6.55228 9 7 8.55228 7 8C7 7.44772 6.55228 7 6 7C5.44772 7 5 7.44772 5 8C5 8.55228 5.44772 9 6 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M18 9C18.5523 9 19 8.55228 19 8C19 7.44772 18.5523 7 18 7C17.4477 7 17 7.44772 17 8C17 8.55228 17.4477 9 18 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M6 17C6.55228 17 7 16.5523 7 16C7 15.4477 6.55228 15 6 15C5.44772 15 5 15.4477 5 16C5 16.5523 5.44772 17 6 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M18 17C18.5523 17 19 16.5523 19 16C19 15.4477 18.5523 15 18 15C17.4477 15 17 15.4477 17 16C17 16.5523 17.4477 17 18 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7 8H17M7 16H17M12 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        Create mood board
                    </a>
                </div>
                
                <!-- Search box for homepage -->
                <div class="mt-12 relative">
                    <div class="flex overflow-hidden rounded-xl bg-white shadow-soft border-2 border-gray-100 focus-within:border-primary transition duration-300">
                        <div class="flex-1 flex items-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-4 text-gray-400">
                                <path d="M21 21L16.6569 16.6569M16.6569 16.6569C18.1046 15.2091 19 13.2091 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C13.2091 19 15.2091 18.1046 16.6569 16.6569Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <input type="text" placeholder="What are you looking for?" id="homeSearchInput" class="flex-1 py-4 px-3 focus:outline-none" onkeydown="handleHomeSearch(event)">
                        </div>
                        <button type="button" class="home-search-voice bg-primary text-white flex items-center justify-center px-6 hover:bg-primary/90 transition">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 18.5C15.3137 18.5 18 15.8137 18 12.5V11.5M12 18.5C8.68629 18.5 6 15.8137 6 12.5V11.5M12 18.5V21.5M8 22H16M12 15.5C10.3431 15.5 9 14.1569 9 12.5V4.5C9 2.84315 10.3431 1.5 12 1.5C13.6569 1.5 15 2.84315 15 4.5V12.5C15 14.1569 13.6569 15.5 12 15.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Trending searches -->
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="/search.php?q=ottoman" class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 rounded-lg text-dark text-sm transition flex items-center">
                            <span class="text-xs text-primary font-semibold mr-2">#</span>
                            Ottoman
                        </a>
                        <a href="/search.php?q=velvet" class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 rounded-lg text-dark text-sm transition flex items-center">
                            <span class="text-xs text-primary font-semibold mr-2">#</span>
                            Velvet
                        </a>
                        <a href="/search.php?q=scandinavian" class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 rounded-lg text-dark text-sm transition flex items-center">
                            <span class="text-xs text-primary font-semibold mr-2">#</span>
                            Scandinavian
                        </a>
                        <a href="/search.php?q=minimalist" class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 rounded-lg text-dark text-sm transition flex items-center">
                            <span class="text-xs text-primary font-semibold mr-2">#</span>
                            Minimalist
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="md:w-1/2 md:pl-8 flex justify-end">
                <div class="relative">
                    <!-- Main image -->
                    <div class="rounded-2xl overflow-hidden shadow-xl relative z-20 animate-float border-2 border-primary/20">
                        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Modern living room" class="w-full h-auto">
                    </div>
                    
                    <!-- Decorative elements -->
                    <div class="absolute top-1/4 -left-16 transform -translate-y-1/2 w-44 h-44 rounded-xl overflow-hidden shadow-lg z-10 animate-float border-2 border-accent/20" style="animation-delay: 0.2s;">
                        <img src="https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg" alt="Ottoman" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="absolute bottom-1/4 -right-12 w-36 h-36 rounded-xl overflow-hidden shadow-lg z-10 animate-float border-2 border-secondary/20" style="animation-delay: 0.3s;">
                        <img src="https://i5.walmartimages.com/seo/Meridian-Furniture-Claude-34-5-H-Velvet-Adjustable-Bar-Stool-in-Black_f60a3f35-2715-42b6-ab7f-991c2846fbb9.155f230bcfff1f80a5b7188d7a4cf969.jpeg" alt="Bar stool" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Animated circular gradients -->
                    <div class="absolute top-0 left-1/3 w-72 h-72 rounded-full bg-primary/20 filter blur-3xl animate-pulse-slow"></div>
                    <div class="absolute bottom-1/4 right-1/4 w-48 h-48 rounded-full bg-accent/20 filter blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
                </div>
            </div>
        </div>
        
        <!-- Features overview -->
        <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 animate-on-scroll">
            <div class="bg-white p-6 rounded-xl border-2 border-primary/20 shadow-soft flex flex-col items-center text-center hover:border-primary/50 transition-colors duration-300">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-4">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 21L16.65 16.65M11 6C13.7614 6 16 8.23858 16 11M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-dark">Smart Search</h3>
                <p class="text-gray-600 text-sm">Find exactly what you're looking for with our AI-powered search that understands your style.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl border-2 border-secondary/20 shadow-soft flex flex-col items-center text-center hover:border-secondary/50 transition-colors duration-300">
                <div class="w-12 h-12 rounded-full bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 20.2896V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V15C21 16.1046 20.1046 17 19 17H7.96125M3 20.2896L7.96125 17M16 11H16.01M12 11H12.01M8 11H8.01M16.5 7H7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-dark">Price Comparison</h3>
                <p class="text-gray-600 text-sm">Compare prices across multiple stores to find the best deals on your favorite pieces.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl border-2 border-accent/20 shadow-soft flex flex-col items-center text-center hover:border-accent/50 transition-colors duration-300">
                <div class="w-12 h-12 rounded-full bg-accent/10 text-accent flex items-center justify-center mb-4">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 3H4V8M20 3H15V8M20 16H15V21M9 16H4V21M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-dark">Room Visualizer</h3>
                <p class="text-gray-600 text-sm">See how items will look in your space with our AR-powered room visualization tool.</p>
            </div>
        </div>
    </div>
    
    <!-- Background pattern -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <!-- Subtle grid pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        
        <!-- Accent corner shape -->
        <div class="absolute -top-16 -left-16 w-64 h-64 rounded-full bg-primary/5"></div>
        <div class="absolute -bottom-16 -right-16 w-64 h-64 rounded-full bg-accent/5"></div>
        
        <!-- Diagonal accent line -->
        <div class="absolute top-0 right-0 bottom-0 w-1/4">
            <div class="absolute top-0 right-1/4 bottom-0 w-1 bg-gradient-to-b from-primary/10 via-accent/10 to-transparent"></div>
        </div>
    </div>
</section>


<!-- Browse by Color Section -->
<section id="browse-by-color" class="py-16 bg-light relative overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-lg mb-2">Trending</span>
                <h2 class="text-2xl md:text-3xl font-display font-bold">Browse by Color</h2>
            </div>
            <a href="/search.php?filter=color" class="text-primary hover:text-primary/90 flex items-center font-medium transition">
                <span>View all colors</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1">
                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-6 stagger">
            <?php foreach ($topColors as $color => $count): ?>
                <a href="/search.php?color=<?php echo urlencode($color); ?>" class="flex flex-col items-center group">
                    <div class="w-full aspect-square rounded-2xl mb-3 overflow-hidden relative hover-card">
                        <div class="absolute inset-0" style="background-color: <?php echo getColorHex($color); ?>;"></div>
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition"></div>
                    </div>
                    <span class="text-sm font-medium group-hover:text-primary transition"><?php echo ucfirst($color); ?></span>
                    <span class="text-xs text-gray-500"><?php echo $count; ?> items</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="explore" class="py-16 bg-neutral relative overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-end justify-between mb-8">
            <div class="mb-4 md:mb-0">
                <span class="inline-block px-3 py-1 bg-secondary/10 text-secondary text-sm font-medium rounded-lg mb-2">Featured</span>
                <h2 class="text-2xl md:text-3xl font-display font-bold">Curated For You</h2>
            </div>
            
            <!-- Filter Tabs -->
            <div class="flex overflow-x-auto scrollable-container pb-2 -mx-4 px-4 space-x-2 md:space-x-3">
                <button type="button" class="flex-none px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium whitespace-nowrap" data-tab="all" data-tab-group="featured">All</button>
                <button type="button" class="flex-none px-4 py-2 bg-neutral text-dark hover:bg-neutral/80 rounded-xl text-sm font-medium whitespace-nowrap" data-tab="furniture" data-tab-group="featured">Furniture</button>
                <button type="button" class="flex-none px-4 py-2 bg-neutral text-dark hover:bg-neutral/80 rounded-xl text-sm font-medium whitespace-nowrap" data-tab="living-room" data-tab-group="featured">Living Room</button>
                <button type="button" class="flex-none px-4 py-2 bg-neutral text-dark hover:bg-neutral/80 rounded-xl text-sm font-medium whitespace-nowrap" data-tab="ottomans" data-tab-group="featured">Ottomans</button>
                <button type="button" class="flex-none px-4 py-2 bg-neutral text-dark hover:bg-neutral/80 rounded-xl text-sm font-medium whitespace-nowrap" data-tab="accent-chairs" data-tab-group="featured">Accent Chairs</button>
            </div>
        </div>
        
        <!-- All Products Tab (default) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-on-scroll stagger" data-tab-content="all" data-tab-group="featured">
            <?php foreach ($featuredProducts as $index => $product): ?>
            <?php 
                $imgUrl = !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                $lowestPrice = $product['priceDetails']['lowest'] ?? 0;
                $highestPrice = $product['priceDetails']['highest'] ?? 0;
                $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                $offerCount = count($product['offers'] ?? []);
                $isInWishlist = isInWishlist($product['id']);
            ?>
            <a href="/product.php?id=<?php echo $product['id']; ?>" class="group">
                <div class="bg-white rounded-2xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                    <div class="relative aspect-square overflow-hidden">
                        <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                        <button class="wishlist-btn absolute top-4 right-4 bg-white/80 backdrop-blur-sm p-2 rounded-full hover:bg-white transition z-10" data-product-id="<?php echo $product['id']; ?>">
                            <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500"></i>
                        </button>
                        <?php if (isset($product['priceDetails']['percentageDrop']) && $product['priceDetails']['percentageDrop'] > 0): ?>
                        <div class="absolute top-4 left-4 bg-accent text-white px-2.5 py-1.5 rounded-lg text-xs font-medium">
                            <?php echo $product['priceDetails']['percentageDrop']; ?>% OFF
                        </div>
                        <?php endif; ?>
                        
                        <!-- Quick view button on hover -->
                        <div class="absolute inset-0 bg-dark/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <button type="button" class="bg-white/90 backdrop-blur-sm hover:bg-white text-dark py-2 px-4 rounded-lg text-sm font-medium transition" onclick="event.preventDefault(); showQuickView('<?php echo $product['id']; ?>')">
                                    Quick view
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <?php if (!empty($product['categories'])): ?>
                        <div class="text-xs text-gray-500 mb-1"><?php echo $product['categories'][0]; ?></div>
                        <?php endif; ?>
                        
                        <h3 class="font-medium mb-2 line-clamp-2 group-hover:text-primary transition"><?php echo htmlspecialchars($product['name']); ?></h3>
                        
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
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Other category tabs (only showing placeholders for demo) -->
        <?php
        $categoryTabs = ['furniture', 'living-room', 'ottomans', 'accent-chairs'];
        foreach ($categoryTabs as $tab):
        ?>
        <div class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-tab-content="<?php echo $tab; ?>" data-tab-group="featured">
            <div class="bg-neutral rounded-2xl p-8 flex items-center justify-center">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mx-auto"></div>
                    <p class="mt-4 text-gray-500">Loading <?php echo str_replace('-', ' ', $tab); ?>...</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute -right-40 top-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute -left-20 bottom-20 w-72 h-72 bg-secondary/5 rounded-full blur-3xl"></div>
</section>

<!-- Room Visualizer Promo -->
<section class="py-16 bg-dark text-light">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
                <span class="inline-block px-3 py-1 bg-accent/20 text-accent text-sm font-medium rounded-lg mb-4">New Feature</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold mb-4">Room Visualizer</h2>
                <p class="text-lg text-light/90 mb-6">See how furniture and decor will look in your space before you buy. Our AR tool helps you make confident interior decisions.</p>
                
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-accent/20 text-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12L11 14L15 10M3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-1">Upload a photo of your room</h3>
                            <p class="text-light/70">Use any photo of your space as the starting point</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-accent/20 text-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12L11 14L15 10M3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-1">Select any product</h3>
                            <p class="text-light/70">Choose from thousands of furniture and decor pieces</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-accent/20 text-accent flex items-center justify-center mr-4 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12L11 14L15 10M3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-1">See how it looks</h3>
                            <p class="text-light/70">Our AI realistically places the item in your space</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <a href="/room-visualizer.php" class="inline-flex items-center justify-center px-6 py-3 bg-accent hover:bg-accent/90 text-white rounded-xl font-medium transition-colors">
                        Try Room Visualizer
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path d="M13 5L20 12L13 19M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="order-1 md:order-2">
                <div class="relative">
                    <!-- Before/After slider visualization -->
                    <div class="rounded-xl overflow-hidden">
                        <div class="relative w-full aspect-video">
                            <!-- After image (with furniture) -->
                            <img src="https://images.unsplash.com/photo-1616137422495-1e9e46e2aa77?ixlib=rb-1.2.1&auto=format&fit=crop&w=2000&q=80" alt="Room with furniture" class="absolute inset-0 w-full h-full object-cover">
                            
                            <!-- Before image (empty room) overlay with clip-path -->
                            <div class="absolute inset-0 w-full h-full" style="clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);">
                                <img src="https://images.unsplash.com/photo-1615529328331-f8917597711f?ixlib=rb-1.2.1&auto=format&fit=crop&w=2000&q=80" alt="Empty room" class="w-full h-full object-cover">
                                
                                <!-- Edge highlight -->
                                <div class="absolute top-0 bottom-0 right-0 w-1 bg-white"></div>
                            </div>
                            
                            <!-- Slider handle -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center cursor-pointer z-10">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 4L4 8L8 12M16 4L20 8L16 12" stroke="#7F5AF0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            
                            <!-- Labels -->
                            <div class="absolute top-4 left-4 bg-dark/70 backdrop-blur-sm text-white py-1 px-3 rounded-lg text-sm">Before</div>
                            <div class="absolute top-4 right-4 bg-dark/70 backdrop-blur-sm text-white py-1 px-3 rounded-lg text-sm">After</div>
                        </div>
                    </div>
                    
                    <!-- Example items used -->
                    <div class="absolute -bottom-6 -right-6 bg-accent text-white p-4 rounded-xl shadow-lg">
                        <div class="text-sm mb-1">Items in this design:</div>
                        <div class="flex space-x-2">
                            <a href="#" class="block w-12 h-12 bg-white rounded-lg overflow-hidden">
                                <img src="https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg" alt="Ottoman" class="w-full h-full object-cover">
                            </a>
                            <a href="#" class="block w-12 h-12 bg-white rounded-lg overflow-hidden">
                                <img src="https://i5.walmartimages.com/seo/Meridian-Furniture-Claude-34-5-H-Velvet-Adjustable-Bar-Stool-in-Black_f60a3f35-2715-42b6-ab7f-991c2846fbb9.155f230bcfff1f80a5b7188d7a4cf969.jpeg" alt="Bar stool" class="w-full h-full object-cover">
                            </a>
                            <a href="#" class="flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg text-white">
                                <span>+3</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Now Section -->
<section class="py-16 bg-neutral">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-block px-3 py-1 bg-accent/10 text-accent text-sm font-medium rounded-lg mb-2">Popular</span>
                <h2 class="text-2xl md:text-3xl font-display font-bold">Trending Now</h2>
            </div>
            <a href="/search.php?sort=trending" class="text-primary hover:text-primary/90 flex items-center font-medium transition">
                <span>View all</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1">
                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        
        <!-- Masonry grid for trending items -->
        <div class="masonry animate-on-scroll">
            <?php foreach ($trendingProducts as $index => $product): ?>
            <?php 
                $imgUrl = !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                $lowestPrice = $product['priceDetails']['lowest'] ?? 0;
                $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                $offerCount = count($product['offers'] ?? []);
                $isInWishlist = isInWishlist($product['id']);
                
                // Determine size for variety (in a real app, this could be based on popularity or featured status)
                $isBig = $index % 5 === 0 || $index % 7 === 0;
            ?>
            <div class="masonry-item">
                <div class="masonry-content">
                    <a href="/product.php?id=<?php echo $product['id']; ?>" class="group">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                            <div class="relative <?php echo $isBig ? 'aspect-[4/5]' : 'aspect-square'; ?> overflow-hidden">
                                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                <button class="wishlist-btn absolute top-4 right-4 bg-white/80 backdrop-blur-sm p-2 rounded-full hover:bg-white transition z-10" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart text-rose-500"></i>
                                </button>
                                <?php if (isset($product['priceDetails']['percentageDrop']) && $product['priceDetails']['percentageDrop'] > 0): ?>
                                <div class="absolute top-4 left-4 bg-accent text-white px-2.5 py-1.5 rounded-lg text-xs font-medium">
                                    <?php echo $product['priceDetails']['percentageDrop']; ?>% OFF
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-4">
                                <?php if (!empty($product['categories'])): ?>
                                <div class="text-xs text-gray-500 mb-1"><?php echo $product['categories'][0]; ?></div>
                                <?php endif; ?>
                                
                                <h3 class="font-medium mb-2 line-clamp-2 group-hover:text-primary transition"><?php echo htmlspecialchars($product['name']); ?></h3>
                                
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
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-10 text-center">
            <a href="/search.php?sort=trending" class="inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                Explore All Trending Items
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                    <path d="M13 5L20 12L13 19M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Personalized Collections -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div>
            <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-lg mb-2">Curated</span>
            <h2 class="text-2xl md:text-3xl font-display font-bold mb-6">Style Collections</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-soft hover-card">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 12L5 10M5 10L12 3L19 10M5 10V20C5 20.2652 5.10536 20.5196 5.29289 20.7071C5.48043 20.8946 5.73478 21 6 21H9M19 10L21 12M19 10V20C19 20.2652 18.8946 20.5196 18.7071 20.7071C18.5196 20.8946 18.2652 21 18 21H15M9 21C9.26522 21 9.51957 20.8946 9.70711 20.7071C9.89464 20.5196 10 20.2652 10 20V16C10 15.7348 10.1054 15.4804 10.2929 15.2929C10.4804 15.1054 10.7348 15 11 15H13C13.2652 15 13.5196 15.1054 13.7071 15.2929C13.8946 15.4804 14 15.7348 14 16V20C14 20.2652 14.1054 20.5196 14.2929 20.7071C14.4804 20.8946 14.7348 21 15 21M9 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium">Scandinavian</h3>
                        <p class="text-sm text-gray-500">28 items</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1567016376408-0226e4d0c1ea?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Scandinavian style" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Scandinavian style" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <a href="/collections.php?style=scandinavian" class="block text-primary hover:text-primary/90 font-medium text-center py-2">
                    View Collection
                </a>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-soft hover-card">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 7.5V6.75C21 5.50736 19.9926 4.5 18.75 4.5H5.25C4.00736 4.5 3 5.50736 3 6.75V7.5M21 7.5V17.25C21 18.4926 19.9926 19.5 18.75 19.5H5.25C4.00736 19.5 3 18.4926 3 17.25V7.5M21 7.5H3M12 12H12.01V12.01H12V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium">Mid-Century Modern</h3>
                        <p class="text-sm text-gray-500">34 items</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Mid-Century Modern style" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1554295405-abb8fd54f153?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Mid-Century Modern style" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <a href="/collections.php?style=mid-century-modern" class="block text-primary hover:text-primary/90 font-medium text-center py-2">
                    View Collection
                </a>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-soft hover-card">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.7526 20.0036C12.2987 20.1543 11.7013 20.1543 11.2474 20.0036M12.7526 20.0036C14.3201 19.5443 15 18.4894 15 18.4894H9C9 18.4894 9.67988 19.5443 11.2474 20.0036M12.7526 20.0036C12.2739 20.1655 11.7261 20.1655 11.2474 20.0036M22 5.67921V14.2115C22 16.6471 19.7614 18 17.5 18H6.5C4.23858 18 2 16.6471 2 14.2115V5.67921C2 3.24362 4.23858 2 6.5 2H17.5C19.7614 2 22 3.24362 22 5.67921Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium">Minimalist</h3>
                        <p class="text-sm text-gray-500">19 items</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1588854337221-4cf9fa96059c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Minimalist style" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral">
                        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Minimalist style" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <a href="/collections.php?style=minimalist" class="block text-primary hover:text-primary/90 font-medium text-center py-2">
                    View Collection
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-primary rounded-2xl overflow-hidden shadow-soft relative hover-card">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-primary/70"></div>
                <div class="flex flex-col md:flex-row p-6 md:p-8 relative z-10">
                    <div class="md:w-1/2 mb-6 md:mb-0 md:pr-6">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-medium rounded-full backdrop-blur-sm mb-4">Featured Collection</span>
                        <h3 class="text-2xl font-bold text-white mb-2">Industrial Loft</h3>
                        <p class="text-primary-100 mb-6">Raw materials, weathered textures, and mechanical elements. Perfect for urban spaces.</p>
                        <a href="/collections.php?style=industrial-loft" class="inline-flex items-center text-white hover:underline font-medium">
                            Explore Collection
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <div class="md:w-1/2 flex justify-end items-center">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="aspect-square rounded-lg overflow-hidden shadow-lg">
                                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Industrial style" class="w-full h-full object-cover">
                            </div>
                            <div class="aspect-square rounded-lg overflow-hidden shadow-lg">
                                <img src="https://images.unsplash.com/photo-1619296094533-e8c4d0769656?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Industrial style" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-accent rounded-2xl overflow-hidden shadow-soft relative hover-card">
                <div class="absolute inset-0 bg-gradient-to-r from-accent/90 to-accent/70"></div>
                <div class="flex flex-col md:flex-row p-6 md:p-8 relative z-10">
                    <div class="md:w-1/2 mb-6 md:mb-0 md:pr-6">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-medium rounded-full backdrop-blur-sm mb-4">New Collection</span>
                        <h3 class="text-2xl font-bold text-white mb-2">Bohemian</h3>
                        <p class="text-accent-100 mb-6">Embrace free-spirited aesthetics with rich patterns, warm colors, and natural materials.</p>
                        <a href="/collections.php?style=bohemian" class="inline-flex items-center text-white hover:underline font-medium">
                            Explore Collection
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <div class="md:w-1/2 flex justify-end items-center">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="aspect-square rounded-lg overflow-hidden shadow-lg">
                                <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Bohemian style" class="w-full h-full object-cover">
                            </div>
                            <div class="aspect-square rounded-lg overflow-hidden shadow-lg">
                                <img src="https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Bohemian style" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-neutral relative">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <span class="inline-block px-3 py-1 bg-secondary/10 text-secondary text-sm font-medium rounded-lg mb-2">Platform Features</span>
            <h2 class="text-2xl md:text-3xl font-display font-bold">How Interior Mosaic Works</h2>
            <p class="text-gray-600 mt-4 max-w-xl mx-auto">Discover, compare, and visualize interior pieces in one seamless experience</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger">
            <div class="bg-white p-6 rounded-2xl shadow-soft">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.9536 14.9458L21 21M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Discover & Search</h3>
                <p class="text-gray-600 mb-6">Find the perfect pieces with our intelligent search that understands both product details and style preferences.</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>AI-powered image search</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Voice search capability</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Advanced filtering options</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-soft">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 15L14 18C14 19.1046 13.1046 20 12 20C10.8954 20 10 19.1046 10 18L10 15M17 7C17 8.65685 14.7614 10 12 10C9.23858 10 7 8.65685 7 7M17 7C17 5.34315 14.7614 4 12 4C9.23858 4 7 5.34315 7 7M17 7L17 13C17 14.1046 16.1046 15 15 15L9 15C7.89543 15 7 14.1046 7 13L7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Compare & Decide</h3>
                <p class="text-gray-600 mb-6">Compare prices across stores, review product details, and find the best deals for your perfect interior pieces.</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Multi-store price comparison</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Side-by-side comparison tool</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Price drop notifications</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-soft">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 16L8.586 11.414C8.96106 11.0389 9.46967 10.8284 10 10.8284C10.5303 10.8284 11.0389 11.0389 11.414 11.414L16 16M14 14L15.586 12.414C15.9611 12.0389 16.4697 11.8284 17 11.8284C17.5303 11.8284 18.0389 12.0389 18.414 12.414L20 14M14 8H14.01M6 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V6C20 5.46957 19.7893 4.96086 19.4142 4.58579C19.0391 4.21071 18.5304 4 18 4H6C5.46957 4 4.96086 4.21071 4.58579 4.58579C4.21071 4.96086 4 5.46957 4 6V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Visualize & Create</h3>
                <p class="text-gray-600 mb-6">Try before you buy with our visualization tools that let you see how pieces will look in your space.</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>AR room visualization</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Create personalized mood boards</span>
                    </li>
                    <li class="flex items-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary mr-2">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Save and share your designs</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="mt-12 text-center">
            <a href="#" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-dark hover:bg-dark/90 text-white rounded-xl font-medium transition shadow-soft">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 10L20 15L15 20M4 4V9C4 9.55228 4.44772 10 5 10H12M9 14H12M20 15H7C5.89543 15 5 14.1046 5 13V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Sign Up For Free</span>
            </a>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute -left-40 top-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute -right-20 bottom-40 w-72 h-72 bg-secondary/5 rounded-full blur-3xl"></div>
</section>

<!-- Quick View Modal Template (hidden by default) -->
<div id="quickViewModal" class="fixed inset-0 z-50 bg-dark/50 backdrop-blur-sm flex items-center justify-center invisible opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 shadow-xl" id="quickViewContent">
        <div class="sticky top-0 bg-white z-30 p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-lg">Quick View</h3>
            <button type="button" id="closeQuickView" class="p-2 text-gray-500 hover:text-dark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        
        <div id="quickViewBody" class="p-4">
            <div class="flex justify-center items-center p-12">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-primary"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Home search functionality
function handleHomeSearch(event) {
    if (event.key === 'Enter') {
        const query = document.getElementById('homeSearchInput').value.trim();
        if (query.length > 0) {
            window.location.href = `/search.php?q=${encodeURIComponent(query)}`;
        }
    }
}

// Quick view functionality
function showQuickView(productId) {
    const modal = document.getElementById('quickViewModal');
    const content = document.getElementById('quickViewContent');
    const body = document.getElementById('quickViewBody');
    
    // Show loading state
    body.innerHTML = `
        <div class="flex justify-center items-center p-12">
            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-primary"></div>
        </div>
    `;
    
    // Show modal
    modal.classList.remove('invisible', 'opacity-0');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
    document.body.classList.add('overflow-hidden');
    
    // Fetch product data with AJAX
    fetch(`/api/product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            // For demo purposes, simulate a response
            setTimeout(() => {
                // In a real app, you would populate this with actual data from the API response
                const demoData = {
                    name: "Demo Product",
                    price: "$199.99",
                    image: "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
                    description: "This is a sample description for the quick view demo."
                };
                
                body.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="aspect-square rounded-xl overflow-hidden bg-neutral">
                            <img src="${demoData.image}" alt="${demoData.name}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h2 class="text-xl font-bold mb-2">${demoData.name}</h2>
                            <div class="text-2xl text-primary font-bold mb-4">${demoData.price}</div>
                            <p class="text-gray-600 mb-6">${demoData.description}</p>
                            
                            <div class="space-y-4">
                                <a href="/product.php?id=${productId}" class="block w-full py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium text-center transition">
                                    View Full Details
                                </a>
                                <button type="button" class="block w-full py-3 border border-gray-200 hover:bg-neutral rounded-xl font-medium text-center transition wishlist-btn" data-product-id="${productId}">
                                    <i class="far fa-heart text-rose-500 mr-2"></i> Add to Wishlist
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }, 1000);
        })
        .catch(error => {
            body.innerHTML = `
                <div class="p-12 text-center">
                    <div class="text-red-500 mb-4">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                            <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p>Sorry, an error occurred while loading the product.</p>
                </div>
            `;
        });
    
    // Close quick view
    const closeBtn = document.getElementById('closeQuickView');
    closeBtn.addEventListener('click', closeQuickView);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeQuickView();
        }
    });
}

function closeQuickView() {
    const modal = document.getElementById('quickViewModal');
    const content = document.getElementById('quickViewContent');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('invisible');
        document.body.classList.remove('overflow-hidden');
    }, 300);
}

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
    
    return colorMap[colorName.toLowerCase()] || '#cccccc';
}
</script>

<?php include 'includes/footer.php'; ?>
