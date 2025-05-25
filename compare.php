<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get product IDs from URL
$productIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

// Validate IDs
$productIds = array_filter($productIds, function($id) {
    return !empty($id);
});

// Limit to 4 products max
$productIds = array_slice($productIds, 0, 4);

if (empty($productIds)) {
    header('Location: /');
    exit;
}

// Fetch products
$api = new MeiliSearchAPI();
$products = [];
$allAttributes = [];

foreach ($productIds as $id) {
    $productData = $api->getProduct($id);
    if ($productData && !empty($productData)) {
        $products[] = $productData['product'];
        
        // Collect all attributes
        $attributes = $productData['product']['processedAttributes']['edges'] ?? [];
        foreach ($attributes as $attr) {
            $label = $attr['node']['label'];
            if (!isset($allAttributes[$label])) {
                $allAttributes[$label] = [];
            }
        }
    }
}

if (empty($products)) {
    header('Location: /');
    exit;
}

// Page meta data
$pageTitle = 'Compare Products';
$pageDescription = 'Compare features and prices of multiple interior decoration items side by side.';

include 'includes/header.php';
?>

<!-- Compare Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Header -->
    <div class="bg-white pt-8 pb-4 border-b border-gray-200 sticky top-[72px] z-30 shadow-soft">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl md:text-3xl font-display font-bold">Compare Products</h1>
                <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-neutral hover:bg-gray-200 rounded-lg transition flex items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back
                </button>
            </div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Comparison Table -->
        <div class="bg-white rounded-xl shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="sticky left-0 bg-white z-10 p-4 text-left font-medium">Features</th>
                            <?php foreach ($products as $product): ?>
                            <th class="p-4 min-w-[250px] align-top">
                                <div class="space-y-3">
                                    <div class="aspect-square rounded-lg overflow-hidden bg-neutral max-w-[200px] mx-auto">
                                        <img src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/400x400/f3f4f6/1f2937?text=No+Image'; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <h3 class="font-medium text-sm line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="removeFromComparison('<?php echo $product['id']; ?>')">
                                        Remove
                                    </button>
                                </div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Price Row -->
                        <tr class="border-b border-gray-200 bg-primary/5">
                            <td class="sticky left-0 bg-primary/5 z-10 p-4 font-medium">Price</td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center">
                                <?php if (isset($product['priceDetails']['lowest'])): ?>
                                <div class="text-2xl font-bold text-primary">
                                    <?php echo formatPrice($product['priceDetails']['lowest']); ?>
                                </div>
                                <?php if (isset($product['priceDetails']['percentageDrop']) && $product['priceDetails']['percentageDrop'] > 0): ?>
                                <div class="text-sm text-accent mt-1">
                                    <?php echo $product['priceDetails']['percentageDrop']; ?>% OFF
                                </div>
                                <?php endif; ?>
                                <?php else: ?>
                                <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Store Count Row -->
                        <tr class="border-b border-gray-200">
                            <td class="sticky left-0 bg-white z-10 p-4 font-medium">Available At</td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center">
                                <?php 
                                $offerCount = count($product['offers'] ?? []);
                                echo $offerCount . ' store' . ($offerCount !== 1 ? 's' : '');
                                ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Colors Row -->
                        <tr class="border-b border-gray-200">
                            <td class="sticky left-0 bg-white z-10 p-4 font-medium">Colors</td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4">
                                <?php if (!empty($product['colorsProcessed'])): ?>
                                <div class="flex flex-wrap gap-2 justify-center">
                                    <?php foreach ($product['colorsProcessed'] as $color): ?>
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full border border-gray-300" 
                                             style="background-color: <?php echo getColorHex($color); ?>;"
                                             title="<?php echo ucfirst($color); ?>"></div>
                                        <span class="text-xs mt-1"><?php echo ucfirst($color); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Categories Row -->
                        <tr class="border-b border-gray-200">
                            <td class="sticky left-0 bg-white z-10 p-4 font-medium">Categories</td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center">
                                <?php if (!empty($product['categories'])): ?>
                                <div class="text-sm">
                                    <?php echo implode(', ', array_slice($product['categories'], 0, 3)); ?>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Dynamic Attribute Rows -->
                        <?php foreach ($allAttributes as $attrLabel => $values): ?>
                        <tr class="border-b border-gray-200">
                            <td class="sticky left-0 bg-white z-10 p-4 font-medium"><?php echo htmlspecialchars($attrLabel); ?></td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center">
                                <?php
                                $found = false;
                                $attributes = $product['processedAttributes']['edges'] ?? [];
                                foreach ($attributes as $attr) {
                                    if ($attr['node']['label'] === $attrLabel) {
                                        $values = $attr['node']['values'];
                                        $valueStr = '';
                                        foreach ($values as $value) {
                                            if (isset($value['value'])) {
                                                $valueStr .= is_array($value['value']) ? implode(', ', $value['value']) : $value['value'];
                                                if (isset($value['unit']['symbol'])) {
                                                    $valueStr .= $value['unit']['symbol'];
                                                }
                                                $valueStr .= ', ';
                                            }
                                        }
                                        echo rtrim($valueStr, ', ');
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) {
                                    echo '<span class="text-gray-400">-</span>';
                                }
                                ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Action Row -->
                        <tr>
                            <td class="sticky left-0 bg-white z-10 p-4 font-medium">Action</td>
                            <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center">
                                <a href="/product.php?id=<?php echo $product['id']; ?>" 
                                   class="inline-block px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                    View Details
                                </a>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Add More Products -->
        <?php if (count($products) < 4): ?>
        <div class="mt-8 text-center">
            <p class="text-gray-600 mb-4">You can compare up to 4 products</p>
            <a href="/search.php" class="inline-flex items-center px-6 py-3 bg-neutral hover:bg-gray-200 rounded-xl transition">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Add More Products
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function removeFromComparison(productId) {
    const currentIds = '<?php echo implode(',', $productIds); ?>'.split(',');
    const newIds = currentIds.filter(id => id !== productId);
    
    if (newIds.length > 0) {
        window.location.href = '/compare.php?ids=' + newIds.join(',');
    } else {
        window.location.href = '/';
    }
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

<?php
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