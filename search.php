<?php
// =====================================================
// 🔍 TEMPERO E CAFÉ - PÁGINA DE BUSCA
// =====================================================

require_once 'includes/database.php';

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

// Buscar dados do usuário se estiver logado
$user = null;
if ($isLoggedIn) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obter parâmetros de busca
$query = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Inicializar variáveis
$products = [];
$categories = [];
$totalResults = 0;
$totalPages = 0;
$searchMessage = '';

// Buscar categorias para filtro
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se há uma consulta de busca
if (!empty($query)) {
    try {
        // Construir query de busca
        $whereConditions = ["p.is_active = 1"];
        $params = [];
        
        // Busca por nome do produto
        $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)";
        $searchTerm = "%{$query}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        
        // Filtro por categoria
        if (!empty($category)) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $category;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Query para contar total de resultados
        $countSql = "
            SELECT COUNT(*) as total
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE {$whereClause}
        ";
        
        $stmt = $db->prepare($countSql);
        $stmt->execute($params);
        $totalResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalResults / $limit);
        
        // Query para buscar produtos
        $orderBy = match($sort) {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'rating' => 'p.rating DESC',
            'newest' => 'p.created_at DESC',
            'name' => 'p.name ASC',
            default => 'p.name ASC'
        };
        
        $sql = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE {$whereClause}
            ORDER BY {$orderBy}
            LIMIT {$limit} OFFSET {$offset}
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mensagem de resultado
        if ($totalResults > 0) {
            $searchMessage = "Encontrados {$totalResults} resultado(s) para \"{$query}\"";
        } else {
            $searchMessage = "Nenhum resultado encontrado para \"{$query}\"";
        }
        
    } catch (Exception $e) {
        $searchMessage = "Erro na busca: " . $e->getMessage();
    }
} else {
    // Se não há consulta, mostrar produtos em destaque
    $stmt = $db->query("
        SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1 AND p.is_featured = 1
        ORDER BY p.created_at DESC
        LIMIT {$limit}
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $searchMessage = "Produtos em Destaque";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Busca - Tempero e Café">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Busca - Tempero e Café</title>
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
    
    
    
    
    
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/tabler-icons.min.css">
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="dist/css/magnific-popup.css">
    <link rel="stylesheet" href="dist/css/nice-select.css">
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
                <h6 class="mb-0">Busca</h6>
            </div>
            <!-- Navbar Toggler-->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
    
    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas"></button>
        <div class="offcanvas-body">
            <!-- Sidenav Profile-->
            <div class="sidenav-profile">
                <div class="user-profile">
                    <?php if ($isLoggedIn): ?>
                        <img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;">
                    <?php else: ?>
                        <img src="dist/img/bg-img/9.jpg" alt="">
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <?php if ($isLoggedIn): ?>
                        <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                        <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                    <?php else: ?>
                        <h5 class="user-name mb-1 text-white">Visitante</h5>
                        <p class="available-balance text-white">Faça login para continuar</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
                    <li><a href="notifications.php"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
                    <li><a href="my-orders.php"><i class="ti ti-package"></i>Meus Pedidos</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="ti ti-login"></i>Fazer Login</a></li>
                    <li><a href="cadastro.php"><i class="ti ti-user-plus"></i>Criar Conta</a></li>
                <?php endif; ?>
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="shop.php"><i class="ti ti-building-store"></i>Loja</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Search Form-->
            <div class="search-form pt-3 rtl-flex-d-row-r">
                <form action="search.php" method="GET" id="searchForm">
                    <input class="form-control" type="search" name="q" placeholder="Buscar no Tempero e Café" value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit"><i class="ti ti-search"></i></button>
                </form>
                <!-- Alternative Search Options -->
                <div class="alternative-search-options">
                    <div class="dropdown">
                        <a class="btn btn-primary dropdown-toggle" id="altSearchOption" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-adjustments-horizontal"></i>
                        </a>
                        <!-- Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="altSearchOption">
                            <li><a class="dropdown-item" href="#" onclick="startVoiceSearch()"><i class="ti ti-microphone"></i>Voz</a></li>
                            <li><a class="dropdown-item" href="#" onclick="startImageSearch()"><i class="ti ti-layout-collage"></i>Imagem</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-section py-3">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select" name="category" id="categoryFilter" onchange="applyFilters()">
                            <option value="">Todas as Categorias</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="sort" id="sortFilter" onchange="applyFilters()">
                            <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Nome A-Z</option>
                            <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Menor Preço</option>
                            <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Maior Preço</option>
                            <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Melhor Avaliação</option>
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mais Recentes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="ti ti-x"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Search Results -->
            <div class="search-results">
                <?php if (!empty($searchMessage)): ?>
                    <div class="alert alert-info">
                        <h6 class="mb-0"><?php echo htmlspecialchars($searchMessage); ?></h6>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($products)): ?>
                    <div class="row g-2">
                        <?php foreach ($products as $product): ?>
                            <?php $images = getProductImages($product['images']); ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card product-card">
                                    <div class="card-body">
                                        <!-- Badge-->
                                        <?php if ($product['is_on_sale']): ?>
                                            <span class="badge rounded-pill badge-warning">Promoção</span>
                                        <?php elseif ($product['is_featured']): ?>
                                            <span class="badge rounded-pill badge-success">Destaque</span>
                                        <?php elseif ($product['is_new']): ?>
                                            <span class="badge rounded-pill badge-info">Novo</span>
                                        <?php endif; ?>
                                        
                                        <!-- Wishlist Button-->
                                        
                                        <!-- Thumbnail -->
                                        <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                                            <img class="mb-2" src="<?php echo $images[0]; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        </a>
                                        
                                        <!-- Product Title -->
                                        <a class="product-title" href="product.php?id=<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                        
                                        <!-- Category -->
                                        <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($product['category_name']); ?></small>
                                        
                                        <!-- Product Price -->
                                        <p class="sale-price">
                                            <?php if ($product['is_on_sale'] && !empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                                <?php echo formatPrice($product['price']); ?><span><?php echo formatPrice($product['original_price']); ?></span>
                                            <?php else: ?>
                                                <?php echo formatPrice($product['price']); ?>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <!-- Rating -->
                                        <div class="product-rating">
                                            <?php 
                                            $rating = $product['rating'] ?? 0;
                                            $fullStars = floor($rating);
                                            
                                            for ($i = 1; $i <= 5; $i++): 
                                                if ($i <= $fullStars): ?>
                                                    <i class="ti ti-star-filled"></i>
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
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Navegação de páginas" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildSearchUrl($query, $category, $sort, $page - 1); ?>">
                                            <i class="ti ti-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildSearchUrl($query, $category, $sort, $i); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildSearchUrl($query, $category, $sort, $page + 1); ?>">
                                            <i class="ti ti-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="ti ti-search" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Nenhum produto encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros ou fazer uma nova busca.</p>
                        <a href="shop.php" class="btn btn-primary">Ver Todos os Produtos</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
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
    
    <!-- All JavaScript Files -->
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
        function applyFilters() {
            const form = document.getElementById('searchForm');
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const query = form.querySelector('input[name="q"]').value;
            
            const url = new URL('search.php', window.location.origin);
            if (query) url.searchParams.set('q', query);
            if (category) url.searchParams.set('category', category);
            if (sort) url.searchParams.set('sort', sort);
            
            window.location.href = url.toString();
        }
        
        function clearFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('sortFilter').value = 'name';
            document.getElementById('searchForm').querySelector('input[name="q"]').value = '';
            window.location.href = 'search.php';
        }
        
        function addToCart(productId) {
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
                } else {
                    alert('Erro ao adicionar produto ao carrinho: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao adicionar produto ao carrinho. Tente novamente.');
            });
        }
        
        
        function startVoiceSearch() {
            if ('webkitSpeechRecognition' in window) {
                const recognition = new webkitSpeechRecognition();
                recognition.lang = 'pt-BR';
                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    document.getElementById('searchForm').querySelector('input[name="q"]').value = transcript;
                    document.getElementById('searchForm').submit();
                };
                recognition.start();
            } else {
                alert('Busca por voz não suportada neste navegador.');
            }
        }
        
        function startImageSearch() {
            alert('Busca por imagem será implementada em breve!');
        }
        
        // Auto-submit form on Enter
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
    </script>
</body>
</html>

<?php
// Função para construir URL de busca
function buildSearchUrl($query, $category, $sort, $page) {
    $params = [];
    if ($query) $params['q'] = $query;
    if ($category) $params['category'] = $category;
    if ($sort) $params['sort'] = $sort;
    if ($page > 1) $params['page'] = $page;
    
    return 'search.php' . (empty($params) ? '' : '?' . http_build_query($params));
}
?>
