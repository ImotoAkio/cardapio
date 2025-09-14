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

// Buscar dados do produto
$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: shop.php');
    exit();
}

$stmt = $db->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: shop.php');
    exit();
}

// Buscar produtos relacionados (mesma categoria)
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
    ORDER BY p.created_at DESC
    LIMIT 4
");
$stmt->execute([$product['category_id'], $productId]);
$relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - <?php echo htmlspecialchars($product['name']); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Title -->
    <title><?php echo htmlspecialchars($product['name']); ?> - Tempero e Café</title>
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
    <style>
        .benefits-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #28a745;
        }
        
        .benefits-section h6 {
            color: #28a745;
            font-weight: 600;
        }
        
        .benefits-list li {
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .benefits-list li i {
            font-size: 16px;
        }
        
        .default-benefits {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #28a745;
        }
        
        .default-benefits h6 {
            color: #28a745;
            font-weight: 600;
        }
        
        .technical-specs {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .technical-specs h6 {
            color: #6c757d;
            font-weight: 600;
        }
    </style>
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
            <div class="back-button me-2"><a href="shop.php"><i class="ti ti-arrow-left"></i></a></div>
            <!-- Page Title-->
            <div class="page-heading">
                <h6 class="mb-0">Detalhes do Produto</h6>
            </div>
            <!-- Navbar Toggler-->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
    
    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
        <!-- Close button-->
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <!-- Offcanvas body-->
        <div class="offcanvas-body">
            <!-- Sidenav Profile-->
            <div class="sidenav-profile">
                <div class="user-profile"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;"></div>
                <div class="user-info">
                    <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
                <li><a href="notifications.php"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="shop.php"><i class="ti ti-building-store"></i>Loja</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div class="product-slide-wrapper">
            <!-- Product Slides-->
            <div class="product-slides owl-carousel">
                <?php 
                $images = getProductImages($product['images']);
                foreach ($images as $image): 
                ?>
                <!-- Single Hero Slide-->
                <div class="single-product-slide" style="background-image: url('<?php echo htmlspecialchars($image); ?>')"></div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="product-description pb-3">
            <!-- Product Title & Meta Data-->
            <div class="product-title-meta-data bg-white mb-3 py-3">
                <div class="container d-flex justify-content-between rtl-flex-d-row-r">
                    <div class="p-title-price">
                        <h5 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="sale-price mb-0 lh-1">
                            <?php if ($product['is_on_sale'] && !empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                            <?php echo formatPrice($product['price']); ?><span><?php echo formatPrice($product['original_price']); ?></span>
                            <?php else: ?>
                            <?php echo formatPrice($product['price']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <!-- Ratings-->
                <div class="product-ratings">
                    <div class="container d-flex align-items-center justify-content-between rtl-flex-d-row-r">
                        <div class="ratings">
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
                            <span class="ps-1"><?php echo $product['total_reviews'] ?? 0; ?> avaliações</span>
                        </div>
                        <div class="total-result-of-ratings">
                            <span><?php echo number_format($rating, 1); ?></span>
                            <span>
                                <?php 
                                if ($rating >= 4.5) echo 'Excelente';
                                elseif ($rating >= 3.5) echo 'Muito Bom';
                                elseif ($rating >= 2.5) echo 'Bom';
                                elseif ($rating >= 1.5) echo 'Regular';
                                else echo 'Ruim';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add To Cart-->
            <div class="cart-form-wrapper bg-white mb-3 py-3">
                <div class="container">
                    <form class="cart-form" action="#" method="post" onsubmit="addToCart(event)">
                        <div class="order-plus-minus d-flex align-items-center">
                            <div class="quantity-button-handler" onclick="decreaseQuantity()">-</div>
                            <input class="form-control cart-quantity-input" type="number" step="1" name="quantity" id="quantity" value="1" min="1" max="99">
                            <div class="quantity-button-handler" onclick="increaseQuantity()">+</div>
                        </div>
                        <button class="btn btn-primary ms-3" type="submit">Adicionar ao Carrinho</button>
                    </form>
                </div>
            </div>
            
            <!-- Product Specification-->
            <div class="p-specification bg-white mb-3 py-3">
                <div class="container">
                    <h6>Especificações</h6>
                    <?php if (!empty($product['description'])): ?>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <?php else: ?>
                    <p>Produto de alta qualidade com ingredientes naturais e orgânicos.</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['short_description'])): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($product['short_description']); ?></p>
                    <?php endif; ?>
                    
                    <!-- Benefícios do Produto -->
                    <?php if (!empty($product['benefits'])): ?>
                    <div class="benefits-section mb-3">
                        <h6 class="text-success mb-2"><i class="ti ti-heart me-1"></i> Benefícios</h6>
                        <?php 
                        // Converter benefícios em array se for JSON ou string separada por vírgulas
                        $benefits = $product['benefits'];
                        if (strpos($benefits, '[') === 0) {
                            // É JSON
                            $benefitsArray = json_decode($benefits, true);
                            if (is_array($benefitsArray)) {
                                $benefits = $benefitsArray;
                            } else {
                                $benefits = explode(',', $benefits);
                            }
                        } else {
                            // É string separada por vírgulas, quebras de linha ou bullet points
                            $benefits = preg_split('/[,\n\r]+/', $benefits);
                        }
                        
                        // Limpar espaços em branco e bullet points
                        $benefits = array_map(function($benefit) {
                            return trim($benefit, " \t\n\r\0\x0B•");
                        }, $benefits);
                        $benefits = array_filter($benefits); // Remove itens vazios
                        ?>
                        <ul class="benefits-list ps-3">
                            <?php foreach ($benefits as $benefit): ?>
                            <li class="mb-1"><i class="ti ti-check text-success me-2"></i><?php echo htmlspecialchars($benefit); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Benefícios Padrão (se não houver benefícios específicos) -->
                    <?php if (empty($product['benefits'])): ?>
                    <div class="default-benefits mb-3">
                        <h6 class="text-success mb-2"><i class="ti ti-heart me-1"></i> Benefícios</h6>
                        <ul class="ps-3">
                            <li class="mb-1"><i class="ti ti-check text-success me-2"></i> Produto Natural</li>
                            <li class="mb-1"><i class="ti ti-check text-success me-2"></i> Qualidade Garantida</li>
                            <li class="mb-1"><i class="ti ti-check text-success me-2"></i> Entrega Rápida</li>
                            <li class="mb-1"><i class="ti ti-check text-success me-2"></i> Suporte 24/7</li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Especificações Técnicas -->
                    <div class="technical-specs">
                        <h6 class="mb-2"><i class="ti ti-settings me-1"></i> Especificações Técnicas</h6>
                        <ul class="mb-3 ps-3">
                            <li><i class="ti ti-check me-1"></i> Produto Natural</li>
                            <li><i class="ti ti-check me-1"></i> Qualidade Garantida</li>
                            <li><i class="ti ti-check me-1"></i> Entrega Rápida</li>
                            <li><i class="ti ti-check me-1"></i> Suporte 24/7</li>
                        </ul>
                    </div>
                    
                    <?php if (!empty($product['weight'])): ?>
                    <p><strong>Peso:</strong> <?php echo htmlspecialchars($product['weight']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['dimensions'])): ?>
                    <p><strong>Dimensões:</strong> <?php echo htmlspecialchars($product['dimensions']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['sku'])): ?>
                    <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Related Products-->
            <?php if (!empty($relatedProducts)): ?>
            <div class="related-products-wrapper bg-white mb-3 py-3">
                <div class="container">
                    <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                        <h6>Produtos Relacionados</h6>
                        <a class="btn btn-sm btn-light" href="shop.php">Ver todos<i class="ms-1 ti ti-arrow-right"></i></a>
                    </div>
                    <div class="row g-2">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <?php $relatedImages = getProductImages($relatedProduct['images']); ?>
                        <div class="col-6 col-md-3">
                            <div class="card product-card bg-gray shadow-none">
                                <div class="card-body">
                                    <!-- Badge-->
                                    <?php if ($relatedProduct['is_on_sale']): ?>
                                    <span class="badge rounded-pill badge-warning">Promoção</span>
                                    <?php elseif ($relatedProduct['is_featured']): ?>
                                    <span class="badge rounded-pill badge-success">Destaque</span>
                                    <?php endif; ?>
                                    
                                    <!-- Wishlist Button-->
                                    
                                    <!-- Thumbnail -->
                                    <a class="product-thumbnail d-block" href="product.php?id=<?php echo $relatedProduct['id']; ?>">
                                        <img class="mb-2" src="<?php echo $relatedImages[0]; ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                                    </a>
                                    
                                    <!-- Product Title -->
                                    <a class="product-title" href="product.php?id=<?php echo $relatedProduct['id']; ?>">
                                        <?php echo htmlspecialchars($relatedProduct['name']); ?>
                                    </a>
                                    
                                    <!-- Product Price -->
                                    <p class="sale-price">
                                        <?php if ($relatedProduct['is_on_sale'] && !empty($relatedProduct['original_price']) && $relatedProduct['original_price'] > $relatedProduct['price']): ?>
                                        <?php echo formatPrice($relatedProduct['price']); ?><span><?php echo formatPrice($relatedProduct['original_price']); ?></span>
                                        <?php else: ?>
                                        <?php echo formatPrice($relatedProduct['price']); ?>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <!-- Rating -->
                                    <div class="product-rating">
                                        <?php 
                                        $relatedRating = $relatedProduct['rating'] ?? 0;
                                        $relatedFullStars = floor($relatedRating);
                                        
                                        for ($i = 1; $i <= 5; $i++): 
                                            if ($i <= $relatedFullStars): ?>
                                                <i class="ti ti-star-filled"></i>
                                            <?php else: ?>
                                                <i class="ti ti-star"></i>
                                            <?php endif;
                                        endfor; ?>
                                    </div>
                                    
                                    <!-- Add to Cart -->
                                    <button class="btn btn-primary btn-sm" onclick="addToCartFromRelated(<?php echo $relatedProduct['id']; ?>)">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
        
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }
        
        function addToCart(event) {
            event.preventDefault();
            
            const quantity = document.getElementById('quantity').value;
            const productId = <?php echo $product['id']; ?>;
            
            // Criar FormData para enviar via POST
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', parseInt(quantity));
            
            fetch('cart_api.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar feedback visual
                    const button = event.target;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="ti ti-check me-1"></i>Adicionado!';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-primary');
                    }, 2000);
                    
                    // Atualizar contador do carrinho se existir
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount && data.item_count !== undefined) {
                        cartCount.textContent = data.item_count;
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
        
        function addToCartFromRelated(productId) {
            // Criar FormData para enviar via POST
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            
            fetch('cart_api.php?action=add', {
                method: 'POST',
                body: formData
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
                    
                    // Atualizar contador do carrinho se existir
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount && data.item_count !== undefined) {
                        cartCount.textContent = data.item_count;
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
        
        
        // Inicializar carrossel de imagens
        $(document).ready(function() {
            $('.product-slides').owlCarousel({
                items: 1,
                loop: true,
                autoplay: true,
                autoplayTimeout: 5000,
                dots: true,
                nav: true,
                navText: ['<i class="ti ti-chevron-left"></i>', '<i class="ti ti-chevron-right"></i>']
            });
        });
        
    </script>
</body>
</html>