<?php
require_once 'config.php';
require_once 'includes/api.php';

// Get featured products for the sidebar
$featuredProducts = getFeaturedProducts(12);

// Page meta data
$pageTitle = 'Room Visualizer - See Items in Your Space';
$pageDescription = 'Upload a photo of your room and visualize how furniture and decor items will look in your actual space.';

include 'includes/header.php';
?>

<!-- Room Visualizer Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary/10 to-secondary/10 py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-5xl font-display font-bold mb-4">Room Visualizer</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                See how furniture and decor will look in your actual space before you buy. 
                Upload a photo and start designing!
            </p>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar with Products -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-soft p-4 sticky top-24">
                    <h3 class="font-bold mb-4">Available Items</h3>
                    
                    <!-- Search -->
                    <div class="mb-4">
                        <input type="text" 
                               placeholder="Search items..." 
                               class="w-full px-3 py-2 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-4">
                        <select class="w-full px-3 py-2 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All Categories</option>
                            <option value="furniture">Furniture</option>
                            <option value="lighting">Lighting</option>
                            <option value="decor">Decor</option>
                            <option value="rugs">Rugs</option>
                        </select>
                    </div>
                    
                    <!-- Product Grid -->
                    <div class="grid grid-cols-2 gap-3 max-h-[600px] overflow-y-auto">
                        <?php foreach ($featuredProducts as $product): ?>
                        <div class="visualizer-item cursor-move bg-gray-50 rounded-lg p-2 hover:shadow-md transition" 
                             data-product-id="<?php echo $product['id']; ?>"
                             data-product-image="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : ''; ?>"
                             data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                             data-product-price="<?php echo $product['priceDetails']['lowest'] ?? 0; ?>">
                            <div class="aspect-square mb-2 bg-white rounded overflow-hidden">
                                <img src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/200x200/f3f4f6/1f2937?text=No+Image'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-full object-contain p-1">
                            </div>
                            <p class="text-xs font-medium line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></p>
                            <p class="text-xs text-primary font-bold mt-1">
                                <?php echo formatPrice($product['priceDetails']['lowest'] ?? 0); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Main Canvas Area -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-soft overflow-hidden">
                    <!-- Toolbar -->
                    <div class="bg-gray-50 border-b border-gray-200 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="uploadRoomImage()" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition flex items-center">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                        <path d="M4 16L8.586 11.414C8.96106 11.0389 9.46967 10.8284 10 10.8284C10.5303 10.8284 11.0389 11.0389 11.414 11.414L16 16M14 14L15.586 12.414C15.9611 12.0389 16.4697 11.8284 17 11.8284C17.5303 11.8284 18.0389 12.0389 18.414 12.414L20 14M14 8H14.01M6 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V6C20 5.46957 19.7893 4.96086 19.4142 4.58579C19.0391 4.21071 18.5304 4 18 4H6C5.46957 4 4.96086 4.21071 4.58579 4.58579C4.21071 4.96086 4 5.46957 4 6V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Upload Room Photo
                                </button>
                                <input type="file" id="roomImageInput" accept="image/*" class="hidden" onchange="handleRoomImageUpload(event)">
                                
                                <button type="button" onclick="clearCanvas()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                    Clear
                                </button>
                                
                                <button type="button" onclick="saveDesign()" class="px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-lg transition">
                                    Save Design
                                </button>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="zoomIn()" class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition" title="Zoom In">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 21L16.65 16.65M11 6V16M6 11H16M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="zoomOut()" class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition" title="Zoom Out">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 21L16.65 16.65M6 11H16M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="toggleGrid()" class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition" title="Toggle Grid">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V9M3 3H9M3 3L9 9M15 3H21M21 3V9M21 3L15 9M21 15V21M21 21H15M21 21L15 15M9 21H3M3 21V15M3 21L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Canvas Area -->
                    <div id="visualizerCanvas" class="relative bg-gray-100 h-[600px] overflow-hidden">
                        <!-- Default State -->
                        <div id="emptyState" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-4">
                                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-xl font-medium mb-2">Upload a room photo to get started</h3>
                            <p class="text-sm mb-6">Or try one of our sample rooms below</p>
                            
                            <!-- Sample Rooms -->
                            <div class="flex space-x-4">
                                <button onclick="loadSampleRoom('living')" class="group">
                                    <div class="w-32 h-24 rounded-lg overflow-hidden border-2 border-transparent group-hover:border-primary transition">
                                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" 
                                             alt="Living Room" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-xs mt-1">Living Room</p>
                                </button>
                                
                                <button onclick="loadSampleRoom('bedroom')" class="group">
                                    <div class="w-32 h-24 rounded-lg overflow-hidden border-2 border-transparent group-hover:border-primary transition">
                                        <img src="https://images.unsplash.com/photo-1586105251261-72a756497a11?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" 
                                             alt="Bedroom" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-xs mt-1">Bedroom</p>
                                </button>
                                
                                <button onclick="loadSampleRoom('office')" class="group">
                                    <div class="w-32 h-24 rounded-lg overflow-hidden border-2 border-transparent group-hover:border-primary transition">
                                        <img src="https://images.unsplash.com/photo-1583845112203-29329902332e?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" 
                                             alt="Office" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-xs mt-1">Office</p>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Room Image Container -->
                        <div id="roomContainer" class="hidden w-full h-full relative">
                            <img id="roomImage" src="" alt="Room" class="w-full h-full object-contain">
                            
                            <!-- Grid Overlay -->
                            <div id="gridOverlay" class="absolute inset-0 pointer-events-none hidden">
                                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                                            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#grid)" />
                                </svg>
                            </div>
                            
                            <!-- Dropped Items Container -->
                            <div id="droppedItems" class="absolute inset-0"></div>
                        </div>
                    </div>
                    
                    <!-- Info Panel -->
                    <div class="bg-gray-50 border-t border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span>Drag items from the sidebar to place them in your room</span>
                            </div>
                            <div id="itemCount" class="text-sm font-medium">
                                0 items placed
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tips Section -->
                <div class="mt-8 bg-white rounded-xl shadow-soft p-6">
                    <h3 class="font-bold mb-4">How to Use Room Visualizer</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="font-bold">1</span>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">Upload Your Room</h4>
                                <p class="text-sm text-gray-600">
                                    Take a photo of your room or use one of our sample rooms to get started.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="font-bold">2</span>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">Drag & Drop Items</h4>
                                <p class="text-sm text-gray-600">
                                    Browse products in the sidebar and drag them onto your room photo.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="font-bold">3</span>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">Adjust & Save</h4>
                                <p class="text-sm text-gray-600">
                                    Resize and position items, then save your design or share it with others.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Interact.js for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<script>
let roomScale = 1;
let itemCounter = 0;
let placedItems = [];

// Initialize draggable items
document.addEventListener('DOMContentLoaded', function() {
    // Make sidebar items draggable
    interact('.visualizer-item')
        .draggable({
            inertia: true,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                })
            ],
            autoScroll: true,
            listeners: {
                start: dragStartListener,
                move: dragMoveListener,
                end: dragEndListener
            }
        });
});

function dragStartListener(event) {
    const target = event.target;
    
    // Create a clone for dragging
    const clone = target.cloneNode(true);
    clone.classList.add('dragging-clone');
    clone.style.position = 'fixed';
    clone.style.zIndex = '1000';
    clone.style.width = target.offsetWidth + 'px';
    clone.style.pointerEvents = 'none';
    clone.style.opacity = '0.8';
    
    document.body.appendChild(clone);
    event.interaction.clone = clone;
}

function dragMoveListener(event) {
    const clone = event.interaction.clone;
    if (clone) {
        clone.style.left = event.clientX - clone.offsetWidth / 2 + 'px';
        clone.style.top = event.clientY - clone.offsetHeight / 2 + 'px';
    }
}

function dragEndListener(event) {
    const clone = event.interaction.clone;
    if (clone) {
        clone.remove();
    }
    
    // Check if dropped on canvas
    const canvas = document.getElementById('droppedItems');
    const canvasRect = canvas.getBoundingClientRect();
    
    if (event.clientX >= canvasRect.left && event.clientX <= canvasRect.right &&
        event.clientY >= canvasRect.top && event.clientY <= canvasRect.bottom) {
        
        // Add item to canvas
        const productData = {
            id: event.target.dataset.productId,
            image: event.target.dataset.productImage,
            name: event.target.dataset.productName,
            price: event.target.dataset.productPrice
        };
        
        addItemToCanvas(productData, event.clientX - canvasRect.left, event.clientY - canvasRect.top);
    }
}

function addItemToCanvas(productData, x, y) {
    itemCounter++;
    const itemId = 'item_' + itemCounter;
    
    const itemElement = document.createElement('div');
    itemElement.id = itemId;
    itemElement.className = 'placed-item absolute';
    itemElement.style.left = (x - 50) + 'px';
    itemElement.style.top = (y - 50) + 'px';
    itemElement.style.width = '100px';
    itemElement.style.height = '100px';
    itemElement.innerHTML = `
        <div class="relative w-full h-full group">
            <img src="${productData.image}" alt="${productData.name}" class="w-full h-full object-contain">
            <div class="absolute inset-0 border-2 border-primary opacity-0 group-hover:opacity-100 transition"></div>
            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition">
                <button onclick="removeItem('${itemId}')" class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('droppedItems').appendChild(itemElement);
    
    // Make the placed item draggable and resizable
    interact('#' + itemId)
        .draggable({
            listeners: {
                move: function(event) {
                    const target = event.target;
                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                    
                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                }
            }
        })
        .resizable({
            edges: { left: true, right: true, bottom: true, top: true },
            listeners: {
                move: function(event) {
                    const target = event.target;
                    let x = parseFloat(target.getAttribute('data-x')) || 0;
                    let y = parseFloat(target.getAttribute('data-y')) || 0;
                    
                    target.style.width = event.rect.width + 'px';
                    target.style.height = event.rect.height + 'px';
                    
                    x += event.deltaRect.left;
                    y += event.deltaRect.top;
                    
                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                }
            },
            modifiers: [
                interact.modifiers.restrictSize({
                    min: { width: 50, height: 50 }
                })
            ]
        });
    
    // Add to placed items array
    placedItems.push({
        id: itemId,
        productData: productData
    });
    
    updateItemCount();
}

function removeItem(itemId) {
    const element = document.getElementById(itemId);
    if (element) {
        element.remove();
        placedItems = placedItems.filter(item => item.id !== itemId);
        updateItemCount();
    }
}

function updateItemCount() {
    document.getElementById('itemCount').textContent = placedItems.length + ' items placed';
}

function uploadRoomImage() {
    document.getElementById('roomImageInput').click();
}

function handleRoomImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            displayRoomImage(e.target.result);
        };
        reader.readAsDataURL(file);
    }
}

function displayRoomImage(imageSrc) {
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('roomContainer').classList.remove('hidden');
    document.getElementById('roomImage').src = imageSrc;
}

function loadSampleRoom(roomType) {
    const sampleRooms = {
        living: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80',
        bedroom: 'https://images.unsplash.com/photo-1586105251261-72a756497a11?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80',
        office: 'https://images.unsplash.com/photo-1583845112203-29329902332e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80'
    };
    
    displayRoomImage(sampleRooms[roomType]);
}

function clearCanvas() {
    document.getElementById('droppedItems').innerHTML = '';
    placedItems = [];
    updateItemCount();
}

function zoomIn() {
    roomScale = Math.min(roomScale + 0.1, 2);
    applyZoom();
}

function zoomOut() {
    roomScale = Math.max(roomScale - 0.1, 0.5);
    applyZoom();
}

function applyZoom() {
    const container = document.getElementById('roomContainer');
    container.style.transform = `scale(${roomScale})`;
}

function toggleGrid() {
    const grid = document.getElementById('gridOverlay');
    grid.classList.toggle('hidden');
}

function saveDesign() {
    // In a real app, this would save to database or generate a shareable link
    const designData = {
        roomImage: document.getElementById('roomImage').src,
        items: placedItems.map(item => {
            const element = document.getElementById(item.id);
            return {
                productData: item.productData,
                position: {
                    x: element.style.left,
                    y: element.style.top,
                    width: element.style.width,
                    height: element.style.height,
                    transform: element.style.transform
                }
            };
        })
    };
    
    console.log('Design saved:', designData);
    alert('Design saved! In a real app, this would create a shareable link or save to your account.');
}
</script>

<?php include 'includes/footer.php'; ?>