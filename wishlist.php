<?php
require_once 'config.php';
require_once 'includes/api.php';
require_once 'includes/wishlist.php';

// Get wishlist items
$wishlistItems = Wishlist::getItems();
$products = [];

if (!empty($wishlistItems)) {
    $api = new MeiliSearchAPI();
    foreach ($wishlistItems as $productId) {
        $productData = $api->getProduct($productId);
        if ($productData && !empty($productData)) {
            $products[] = $productData['product'];
        }
    }
}

// Page meta data
$pageTitle = 'My Wishlist';
$pageDescription = 'View and manage your saved interior decoration items.';

include 'includes/header.php';
?>

<!-- Wishlist Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Header -->
    <div class="bg-white pt-8 pb-4 border-b border-gray-200">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">My Wishlist</h1>
            <p class="text-gray-500">
                <?php echo count($products); ?> item<?php echo count($products) !== 1 ? 's' : ''; ?> saved
            </p>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <?php if (empty($products)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-soft p-12 text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-neutral rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="far fa-heart text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold mb-4">Your wishlist is empty</h2>
            <p class="text-gray-600 mb-8">Save items you love to view them later. They'll appear here for easy access.</p>
            
            <a href="/search.php" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                Start Shopping
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <?php else: ?>
        <!-- Wishlist Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <?php 
                $imgUrl = !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/800x800/f3f4f6/1f2937?text=No+Image';
                $lowestPrice = $product['priceDetails']['lowest'] ?? 0;
                $priceStr = $lowestPrice > 0 ? formatPrice($lowestPrice) : 'N/A';
                $offerCount = count($product['offers'] ?? []);
            ?>
            <div class="product-card">
                <div class="bg-white rounded-xl overflow-hidden shadow-soft hover-card transition-all duration-300">
                    <div class="relative aspect-square overflow-hidden">
                        <a href="/product.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        </a>
                        
                        <button class="wishlist-btn absolute top-3 right-3 bg-white/90 backdrop-blur-sm w-10 h-10 rounded-full flex items-center justify-center hover:bg-white transition shadow-soft" 
                                data-product-id="<?php echo $product['id']; ?>"
                                onclick="removeFromWishlist('<?php echo $product['id']; ?>')">
                            <i class="fas fa-heart text-rose-500"></i>
                        </button>
                        
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
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-lg font-semibold text-primary"><?php echo $priceStr; ?></div>
                            <?php if ($offerCount > 0): ?>
                            <div class="text-xs py-1 px-2 bg-neutral rounded-lg">
                                <span><?php echo $offerCount; ?> store<?php echo $offerCount > 1 ? 's' : ''; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="/product.php?id=<?php echo $product['id']; ?>" 
                               class="flex-1 py-2 bg-primary hover:bg-primary/90 text-white text-center rounded-lg text-sm font-medium transition">
                                View Details
                            </a>
                            <button type="button" 
                                    onclick="addToCollection('<?php echo $product['id']; ?>')"
                                    class="px-3 py-2 bg-neutral hover:bg-gray-200 rounded-lg transition">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 11H13M13 11H7M13 11V5M13 11V17M9 3H4C3.44772 3 3 3.44772 3 4V9M15 3H20C20.5523 3 21 3.44772 21 4V9M15 21H20C20.5523 21 21 20.5523 21 20V15M9 21H4C3.44772 21 3 20.5523 3 20V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Share Wishlist -->
        <div class="mt-12 bg-white rounded-xl shadow-soft p-6 text-center">
            <h3 class="text-lg font-bold mb-2">Share Your Wishlist</h3>
            <p class="text-gray-600 mb-4">Let friends and family know what you're dreaming of</p>
            <button type="button" onclick="shareWishlist()" class="inline-flex items-center px-4 py-2 bg-neutral hover:bg-gray-200 rounded-lg transition">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                    <path d="M8.68387 13.3419C8.88616 12.9381 9 12.4824 9 12C9 11.5176 8.88616 11.0619 8.68387 10.6581M8.68387 13.3419C8.19134 14.3251 7.17449 15 6 15C4.34315 15 3 13.6569 3 12C3 10.3431 4.34315 9 6 9C7.17449 9 8.19134 9.67492 8.68387 10.6581M8.68387 13.3419L15.3161 16.6581M8.68387 10.6581L15.3161 7.34193M15.3161 7.34193C15.8087 8.32508 16.8255 9 18 9C19.6569 9 21 7.65685 21 6C21 4.34315 19.6569 3 18 3C16.3431 3 15 4.34315 15 6C15 6.48237 15.1138 6.93815 15.3161 7.34193ZM15.3161 16.6581C15.1138 17.0619 15 17.5176 15 18C15 19.6569 16.3431 21 18 21C19.6569 21 21 19.6569 21 18C21 16.3431 19.6569 15 18 15C16.8255 15 15.8087 15.6749 15.3161 16.6581Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Share Wishlist
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function removeFromWishlist(productId) {
    if (confirm('Remove this item from your wishlist?')) {
        fetch('/api/wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                productId: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function addToCollection(productId) {
    // In a real app, this would open a modal to select collection
    alert('This would open a modal to add the item to a collection');
}

function shareWishlist() {
    // In a real app, this would generate a shareable link
    const shareUrl = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: 'My Interior Mosaic Wishlist',
            text: 'Check out my wishlist on Interior Mosaic!',
            url: shareUrl
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(shareUrl).then(() => {
            alert('Wishlist link copied to clipboard!');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>