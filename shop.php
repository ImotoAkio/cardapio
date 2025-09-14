<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Incluir arquivo de configuração do banco de dados
require_once 'includes/database.php';

// Buscar dados do usuário
$user_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Buscar contagem do carrinho
$stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Buscar todos os produtos ativos
$stmt = $db->query("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias
$stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);



$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - Produtos Naturais e Orgânicos">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Title -->
    <title>Loja - Tempero e Café</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="dist/img/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="dist/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="dist/img/icons/favicon-96x96.png">
    <link rel="shortcut icon" href="dist/img/icons/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="dist/img/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="dist/img/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="dist/img/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="dist/img/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="dist/img/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="dist/img/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="dist/img/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="dist/img/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="dist/img/icons/apple-icon-180x180.png">
    <link rel="apple-touch-icon" href="dist/img/icons/apple-icon.png">
    <meta name="msapplication-TileColor" content="#d3a74e">
    <meta name="msapplication-TileImage" content="dist/img/icons/ms-icon-144x144.png">
    <meta name="msapplication-config" content="browserconfig.xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    
    <!-- Apple Touch Icon -->
    
    
    
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/tabler-icons.min.css">
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="dist/css/magnific-popup.css">
    <link rel="stylesheet" href="dist/css/nice-select.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="dist/style.css">
    <link rel="stylesheet" href="dist/css/avatar-styles.css">
    <!-- Web App Manifest -->
    <link rel="manifest" href="dist/manifest.json">
</head>
<body>
    <!-- Preloader-->
    <div class="preloader" id="preloader">
        <div class="spinner-grow text-secondary" role="status">
            <div class="sr-only"></div>
        </div>
    </div>
    
    <!-- Header Area-->
    <div class="header-area" id="headerArea">
        <div class="container h-100 d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <!-- Back Button-->
            <div class="back-button me-2"><a href="home.php"><i class="ti ti-arrow-left"></i></a></div>
            <!-- Page Title-->
            <div class="page-heading">
                <h6 class="mb-0">Loja</h6>
            </div>
            <!-- Filter Option-->
            <div class="filter-option ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaFilterOffcanvas" aria-controls="suhaFilterOffcanvas"><i class="ti ti-adjustments-horizontal"></i></div>
        </div>
    </div>
    
    <!-- Offcanvas Filter -->
    <div class="offcanvas offcanvas-start suha-filter-offcanvas-wrap" tabindex="-1" id="suhaFilterOffcanvas" aria-labelledby="suhaFilterOffcanvasLabel">
        <!-- Close button-->
        <button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <!-- Offcanvas body-->
        <div class="offcanvas-body py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Category Filter -->
                        <div class="widget catagory mb-4">
                            <h6 class="widget-title mb-2">Categorias</h6>
                            <div class="widget-desc">
                                <?php foreach ($categories as $category): ?>
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox" id="cat_<?php echo $category['id']; ?>" type="checkbox" value="<?php echo $category['id']; ?>">
                                    <label class="form-check-label" for="cat_<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Price Range -->
                        <div class="widget price-range mb-4">
                            <h6 class="widget-title mb-2">Faixa de Preço</h6>
                            <div class="widget-desc">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input class="form-control" id="minPrice" type="number" placeholder="0" value="0" min="0">
                                            <label for="minPrice">Mínimo</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input class="form-control" id="maxPrice" type="number" placeholder="1000" value="1000" min="0">
                                            <label for="maxPrice">Máximo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Apply Filter -->
                        <div class="apply-filter-btn">
                            <button class="btn btn-lg btn-success w-100" onclick="applyFilters()">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div class="py-3">
            <div class="container">
                <div class="row g-1 align-items-center rtl-flex-d-row-r">
                    <div class="col-8">
                        <!-- Product Categories Slide -->
                        <div class="product-catagories owl-carousel catagory-slides">
                            <?php foreach ($categories as $category): ?>
                            <a class="shadow-sm" href="#" onclick="filterByCategory(<?php echo $category['id']; ?>)">
                                <img src="<?php echo getCategoryImage($category); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-4">
                        <!-- Select Product Category -->
                        <div class="select-product-catagory">
                            <select class="right small border-0" id="selectProductCatagory" name="selectProductCatagory" aria-label="Default select example" onchange="sortProducts()">
                                <option selected>Ordenar por</option>
                                <option value="newest">Mais recentes</option>
                                <option value="price_low">Menor preço</option>
                                <option value="price_high">Maior preço</option>
                                <option value="name">Nome A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3"></div>
                
                <!-- Products Grid -->
                <div class="row g-2 rtl-flex-d-row-r" id="productsContainer">
                    <?php foreach ($products as $product): ?>
                    <?php $images = getProductImages($product['images']); ?>
                    <div class="col-6 col-md-4 product-item" 
                         data-category="<?php echo $product['category_id']; ?>" 
                         data-price="<?php echo $product['price']; ?>" 
                         data-name="<?php echo strtolower($product['name']); ?>">
                        <div class="card product-card">
                            <div class="card-body">
                                <!-- Badge -->
                                <?php if ($product['is_on_sale']): ?>
                                <span class="badge rounded-pill badge-warning">Promoção</span>
                                <?php elseif ($product['is_featured']): ?>
                                <span class="badge rounded-pill badge-success">Destaque</span>
                                <?php elseif ($product['is_new']): ?>
                                <span class="badge rounded-pill badge-info">Novo</span>
                                <?php endif; ?>
                                
                                
                                <!-- Thumbnail -->
                                <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                                    <img class="mb-2" src="<?php echo $images[0]; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                
                                <!-- Product Title -->
                                <a class="product-title" href="product.php?id=<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                                
                                <!-- Product Price -->
                                <p class="sale-price">
                                    <?php if ($product['is_on_sale'] && !empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <?php echo formatPrice($product['price']); ?>
                                    <span><?php echo formatPrice($product['original_price']); ?></span>
                                    <?php else: ?>
                                    <?php echo formatPrice($product['price']); ?>
                                    <?php endif; ?>
                                </p>
                                
                                <!-- Rating -->
                                <div class="product-rating">
                                    <?php 
                                    $rating = $product['rating'] ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    
                                    for ($i = 1; $i <= 5; $i++): 
                                        if ($i <= $fullStars): ?>
                                            <i class="ti ti-star-filled"></i>
                                        <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                            <i class="ti ti-star-half-filled"></i>
                                        <?php else: ?>
                                            <i class="ti ti-star"></i>
                                        <?php endif;
                                    endfor; ?>
                                </div>
                                
                                <!-- Add to Cart -->
                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Products Message -->
                <div id="noProductsMessage" class="text-center py-5" style="display: none;">
                    <i class="ti ti-package" style="font-size: 48px; color: #6c757d;"></i>
                    <h5 class="mt-3">Nenhum produto encontrado</h5>
                    <p class="text-muted">Tente ajustar os filtros para encontrar o que procura.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Internet Connection Status-->
    <div class="internet-connection-status" id="internetStatus"></div>
    
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="suha-footer-nav">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0 d-flex rtl-flex-d-row-r">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="profile.php"><i class="ti ti-user"></i>Perfil</a></li>
            </ul>
        </div>
    </div>
    
    <!-- All JavaScript Files-->
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/waypoints.min.js"></script>
    <script src="dist/js/jquery.easing.min.js"></script>
    <script src="dist/js/owl.carousel.min.js"></script>
    <script src="dist/js/jquery.magnific-popup.min.js"></script>
    <script src="dist/js/jquery.counterup.min.js"></script>
    <script src="dist/js/jquery.countdown.min.js"></script>
    <script src="dist/js/jquery.passwordstrength.js"></script>
    <script src="dist/js/jquery.nice-select.min.js"></script>
    <script src="dist/js/theme-switching.js"></script>
    <script src="dist/js/no-internet.js"></script>
    <script src="dist/js/active.js"></script>
    <script src="dist/js/pwa.js"></script>
    
    <script>
        // Filtrar por categoria
        function filterByCategory(categoryId) {
            const products = document.querySelectorAll('.product-item');
            let visibleCount = 0;
            
            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                
                if (categoryId == 0 || productCategory == categoryId) {
                    product.style.display = 'block';
                    visibleCount++;
                } else {
                    product.style.display = 'none';
                }
            });
            
            updateNoProductsMessage(visibleCount);
        }
        
        // Aplicar filtros
        function applyFilters() {
            const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
            
            const products = document.querySelectorAll('.product-item');
            let visibleCount = 0;
            
            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                const productPrice = parseFloat(product.getAttribute('data-price'));
                
                const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(productCategory);
                const priceMatch = productPrice >= minPrice && productPrice <= maxPrice;
                
                if (categoryMatch && priceMatch) {
                    product.style.display = 'block';
                    visibleCount++;
                } else {
                    product.style.display = 'none';
                }
            });
            
            updateNoProductsMessage(visibleCount);
            
            // Fechar offcanvas
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('suhaFilterOffcanvas'));
            if (offcanvas) {
                offcanvas.hide();
            }
        }
        
        // Ordenar produtos
        function sortProducts() {
            const sortValue = document.getElementById('selectProductCatagory').value;
            const container = document.getElementById('productsContainer');
            const products = Array.from(container.querySelectorAll('.product-item'));
            
            products.sort((a, b) => {
                switch (sortValue) {
                    case 'price_low':
                        return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                    case 'price_high':
                        return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                    case 'name':
                        return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                    case 'newest':
                    default:
                        return 0; // Manter ordem original
                }
            });
            
            // Reordenar elementos no DOM
            products.forEach(product => container.appendChild(product));
        }
        
        // Atualizar mensagem de "nenhum produto"
        function updateNoProductsMessage(visibleCount) {
            const noProductsMessage = document.getElementById('noProductsMessage');
            if (visibleCount === 0) {
                noProductsMessage.style.display = 'block';
            } else {
                noProductsMessage.style.display = 'none';
            }
        }
        
        // Adicionar ao carrinho
        function addToCart(productId) {
            fetch('api/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    productId: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar feedback visual
                    const button = event.target;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="ti ti-check"></i>';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-primary');
                    }, 2000);
                    
                    // Atualizar contador do carrinho
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = parseInt(cartCount.textContent) + 1;
                    }
                } else {
                    alert('Erro ao adicionar produto ao carrinho: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao adicionar produto ao carrinho. Tente novamente.');
            });
        }
        
        
        // Inicializar carrossel de categorias
        $(document).ready(function() {
            $('.catagory-slides').owlCarousel({
                items: 3,
                margin: 10,
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                responsive: {
                    0: { items: 2 },
                    768: { items: 3 },
                    1024: { items: 4 }
                }
            });
        });
        
    </script>
</body>
</html>