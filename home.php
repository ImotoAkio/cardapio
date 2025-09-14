<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - PÁGINA HOME DINÂMICA
// =====================================================

require_once 'includes/database.php';
session_start();
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
    <!-- The above tags *must* come first in the head, any other head content must *come after* these tags -->
    <!-- Title -->
    <title>Tempero e Café - Produtos Naturais e Orgânicos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" href="dist/img/core-img/logo_cafe.png">
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
  </head>
  <body>
    <!-- Preloader-->
    <div class="preloader" id="preloader">
      <div class="spinner-grow text-secondary" role="status">
        <div class="sr-only"></div>
      </div>
    </div>
    <!-- Header Area -->
    <?php include 'includes/header.php'; ?>
    <div class="page-content-wrapper">
      <!-- Search Form-->
      <div class="container">
        <div class="search-form pt-3 rtl-flex-d-row-r">
          <form action="search.php" method="GET" id="searchForm">
            <input class="form-control" type="search" name="q" id="searchInput" placeholder="Buscar no Tempero e Café" autocomplete="off">
            <button type="submit"><i class="ti ti-search"></i></button>
            <!-- Search Suggestions -->
            <div class="search-suggestions" id="searchSuggestions" style="display: none;">
              <div class="suggestions-list" id="suggestionsList"></div>
            </div>
          </form>
          <!-- Alternative Search Options -->
          <div class="alternative-search-options">
            <div class="dropdown"><a class="btn btn-primary dropdown-toggle" id="altSearchOption" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-adjustments-horizontal"></i></a>
              <!-- Dropdown Menu -->
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="altSearchOption">
                <li><a class="dropdown-item" href="#" onclick="startVoiceSearch()"><i class="ti ti-microphone"></i>Voz</a></li>
                <li><a class="dropdown-item" href="#" onclick="startImageSearch()"><i class="ti ti-layout-collage"></i>Imagem</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Hero Wrapper -->
      <div class="hero-wrapper">
        <div class="container">
          <div class="pt-3">
            <!-- Hero Slides-->
            <div class="hero-slides owl-carousel">
              <!-- Single Hero Slide-->
              <div class="single-hero-slide" style="background-image: url('dist/img/bg-img/1.jpg')">
                <div class="slide-content h-100 d-flex align-items-center">
                  <div class="slide-text">
                    <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Tempero e Café</h4>
                    <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">Produtos Naturais e Orgânicos</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="800ms" data-duration="1000ms">Comprar Agora</a>
                  </div>
                </div>
              </div>
              <!-- Single Hero Slide-->
              <div class="single-hero-slide" style="background-image: url('dist/img/bg-img/2.jpg')">
                <div class="slide-content h-100 d-flex align-items-center">
                  <div class="slide-text">
                    <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Chás Premium</h4>
                    <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">Apenas R$ 15,90</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="500ms" data-duration="1000ms">Comprar Agora</a>
                  </div>
                </div>
              </div>
              <!-- Single Hero Slide-->
              <div class="single-hero-slide" style="background-image: url('dist/img/bg-img/3.jpg')">
                <div class="slide-content h-100 d-flex align-items-center">
                  <div class="slide-text">
                    <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Suplementos Naturais</h4>
                    <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">Garantia de qualidade</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="800ms" data-duration="1000ms">Comprar Agora</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Product Catagories -->
      <div class="product-catagories-wrapper py-3">
        <div class="container">
          <div class="row g-2 rtl-flex-d-row-r">
            <?php foreach ($categories as $index => $category): ?>
            <!-- Catagory Card -->
            <div class="col-3">
              <div class="card catagory-card <?php echo $index == 7 ? 'active' : ''; ?>">
                <div class="card-body px-2">
                  <a href="category.php?id=<?php echo $category['id']; ?>">
                    <img src="<?php echo getCategoryImage($category); ?>" alt="<?php echo $category['name']; ?>">
                    <span><?php echo $category['name']; ?></span>
                  </a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <!-- Flash Sale Slide -->
      <?php if (!empty($onSaleProducts)): ?>
      <div class="flash-sale-wrapper">
        <div class="container">
          <div class="section-heading d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <h6 class="d-flex align-items-center rtl-flex-d-row-r"><i class="ti ti-bolt-lightning me-1 text-danger lni-flashing-effect"></i>Oferta Relâmpago</h6>
            <!-- Please use event time this format: YYYY/MM/DD hh:mm:ss -->
            <ul class="sales-end-timer ps-0 d-flex align-items-center rtl-flex-d-row-r" data-countdown="2025/12/31 14:21:59">
              <li><span class="days">0</span>d</li>
              <li><span class="hours">0</span>h</li>
              <li><span class="minutes">0</span>m</li>
              <li><span class="seconds">0</span>s</li>
            </ul>
          </div>
          <!-- Flash Sale Slide-->
          <div class="flash-sale-slide owl-carousel">
            <?php foreach ($onSaleProducts as $product): ?>
            <?php $images = getProductImages($product['images']); ?>
            <!-- Flash Sale Card -->
            <div class="card flash-sale-card">
              <div class="card-body">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                  <img src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>">
                  <span class="product-title"><?php echo $product['name']; ?></span>
                  <div class="product-price">
                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                      <div class="price-container">
                        <div class="current-price-wrapper">
                          <span class="current-price text-success fw-bold"><?php echo formatPrice($product['price']); ?></span>
                          <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1" style="font-size: 0.7rem;">
                            <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                          </span>
                        </div>
                        <div class="original-price-wrapper">
                          <span class="original-price text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                            <?php echo formatPrice($product['original_price']); ?>
                          </span>
                        </div>
                      </div>
                    <?php else: ?>
                      <div class="price-container">
                        <span class="current-price text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                  <span class="progress-title"><?php echo rand(30, 99); ?>% Vendido</span>
                  <!-- Progress Bar-->
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo rand(30, 99); ?>%" aria-valuenow="<?php echo rand(30, 99); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <!-- Dark Mode -->
      <div class="container">
        <div class="dark-mode-wrapper mt-3 bg-img p-4 p-lg-5">
          <p class="text-white">Você pode alterar sua exibição para um fundo escuro usando o modo escuro.</p>
          <div class="form-check form-switch mb-0">
            <label class="form-check-label text-white h6 mb-0" for="darkSwitch">Alternar para Modo Escuro</label>
            <input class="form-check-input" id="darkSwitch" type="checkbox" role="switch">
          </div>
        </div>
      </div>
      <!-- Top Products -->
      <div class="top-products-area py-3">
        <div class="container">
          <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Produtos Mais Vendidos</h6><a class="btn btn-sm btn-light" href="shop.php">Ver todos<i class="ms-1 ti ti-arrow-right"></i></a>
          </div>
          <div class="row g-2">
            <?php foreach ($topProducts as $product): ?>
            <?php $images = getProductImages($product['images']); ?>
            <!-- Product Card -->
            <div class="col-6 col-md-4">
              <div class="card product-card">
                <div class="card-body">
                  <!-- Badge-->
                  <?php if ($product['is_on_sale']): ?>
                  <span class="badge rounded-pill badge-warning">Promoção</span>
                  <?php elseif ($product['is_new']): ?>
                  <span class="badge rounded-pill badge-success">Novo</span>
                  <?php endif; ?>
                  <!-- Thumbnail -->
                  <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                    <img class="mb-2" src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>">
                    <?php if ($product['is_on_sale']): ?>
                    <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                    <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/31 23:59:59">
                      <li><span class="days">0</span>d</li>
                      <li><span class="hours">0</span>h</li>
                      <li><span class="minutes">0</span>m</li>
                      <li><span class="seconds">0</span>s</li>
                    </ul>
                    <?php endif; ?>
                  </a>
                  <!-- Product Title -->
                  <a class="product-title" href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                  <!-- Product Price -->
                  <div class="product-price">
                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                      <div class="price-container">
                        <div class="current-price-wrapper">
                          <span class="current-price text-success fw-bold"><?php echo formatPrice($product['price']); ?></span>
                          <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1" style="font-size: 0.7rem;">
                            <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                          </span>
                        </div>
                        <div class="original-price-wrapper">
                          <span class="original-price text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                            <?php echo formatPrice($product['original_price']); ?>
                          </span>
                        </div>
                      </div>
                    <?php else: ?>
                      <div class="price-container">
                        <span class="current-price text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                  <!-- Rating -->
                  <div class="product-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="ti ti-star-filled"></i>
                    <?php endfor; ?>
                  </div>
                  <!-- Add to Cart -->
                  <button class="btn btn-primary btn-sm" data-cart-add="<?php echo $product['id']; ?>">
                    <i class="ti ti-plus"></i>
                  </button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <!-- CTA Area -->
      <div class="container">
        <div class="cta-text dir-rtl p-4 p-lg-5">
          <div class="row">
            <div class="col-9">
              <h5 class="text-white">20% de desconto em produtos orgânicos.</h5><a class="btn btn-primary" href="#">Aproveite esta oferta</a>
            </div>
          </div><img src="<?php echo getBasePath(); ?>dist/img/bg-img/make-up.png" alt="">
        </div>
      </div>
      <!-- Weekly Best Sellers-->
      <div class="weekly-best-seller-area py-3">
        <div class="container">
          <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Mais Vendidos da Semana</h6><a class="btn btn-sm btn-light" href="shop.php">Ver todos<i class="ms-1 ti ti-arrow-right"></i></a>
          </div>
          <div class="row g-2">
            <?php foreach ($weeklyProducts as $product): ?>
            <?php $images = getProductImages($product['images']); ?>
            <!-- Weekly Product Card -->
            <div class="col-12">
              <div class="card horizontal-product-card">
                <div class="d-flex align-items-center">
                  <div class="product-thumbnail-side">
                    <!-- Thumbnail -->
                    <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                      <img src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                  </div>
                  <div class="product-description">
                    <!-- Product Title -->
                    <a class="product-title d-block" href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                    <!-- Price -->
                    <div class="product-price">
                      <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                        <div class="price-container">
                          <div class="current-price-wrapper">
                            <span class="current-price text-success fw-bold">
                              <i class="ti ti-currency-dollar"></i><?php echo formatPrice($product['price']); ?>
                            </span>
                            <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1" style="font-size: 0.7rem;">
                              <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                            </span>
                          </div>
                          <div class="original-price-wrapper">
                            <span class="original-price text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                              <?php echo formatPrice($product['original_price']); ?>
                            </span>
                          </div>
                        </div>
                      <?php else: ?>
                        <div class="price-container">
                          <span class="current-price text-primary fw-bold">
                            <i class="ti ti-currency-dollar"></i><?php echo formatPrice($product['price']); ?>
                          </span>
                        </div>
                      <?php endif; ?>
                    </div>
                    <!-- Rating -->
                    <div class="product-rating">
                      <i class="ti ti-star-filled"></i><?php echo number_format($product['avg_rating'] ?: 4.5, 2); ?> 
                      <span class="ms-1">(<?php echo $product['review_count'] ?: rand(10, 100); ?> avaliações)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <!-- Discount Coupon Card-->
      <?php if ($mainCoupon): ?>
      <div class="container">
        <div class="discount-coupon-card p-4 p-lg-5 dir-rtl">
          <div class="d-flex align-items-center">
            <div class="discountIcon"><img class="w-100" src="<?php echo getBasePath(); ?>dist/img/core-img/discount.png" alt=""></div>
            <div class="text-content">
              <h5 class="text-white mb-2"><?php echo $mainCoupon['name']; ?>!</h5>
              <p class="text-white mb-0"><?php echo $mainCoupon['description']; ?><span class="px-1 fw-bold"><?php echo $mainCoupon['code']; ?></span>na página de checkout.</p>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <!-- Featured Products Wrapper-->
      <div class="featured-products-wrapper py-3">
        <div class="container">
          <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Produtos em Destaque</h6><a class="btn btn-sm btn-light" href="shop.php">Ver todos<i class="ms-1 ti ti-arrow-right"></i></a>
          </div>
          <div class="row g-2">
            <?php foreach ($featuredProducts as $product): ?>
            <?php $images = getProductImages($product['images']); ?>
            <!-- Featured Product Card-->
            <div class="col-4">
              <div class="card featured-product-card">
                <div class="card-body">
                  <!-- Badge-->
                  <span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                  <div class="product-thumbnail-side">
                    <!-- Thumbnail -->
                    <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                      <img src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                  </div>
                  <div class="product-description">
                    <!-- Product Title -->
                    <a class="product-title d-block" href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                    <!-- Price -->
                    <div class="product-price">
                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                      <div class="price-container">
                        <div class="current-price-wrapper">
                          <span class="current-price text-success fw-bold"><?php echo formatPrice($product['price']); ?></span>
                          <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1" style="font-size: 0.7rem;">
                            <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                          </span>
                        </div>
                        <div class="original-price-wrapper">
                          <span class="original-price text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                            <?php echo formatPrice($product['original_price']); ?>
                          </span>
                        </div>
                      </div>
                    <?php else: ?>
                      <div class="price-container">
                        <span class="current-price text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="pb-3">
        <div class="container">
          <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
            <h6>Coleções</h6><a class="btn btn-sm btn-light" href="#">Ver todas<i class="ms-1 ti ti-arrow-right"></i></a>
          </div>
          <!-- Collection Slide-->
          <div class="collection-slide owl-carousel">
            <?php foreach ($categories as $category): ?>
            <!-- Collection Card-->
            <div class="card collection-card">
              <a href="category.php?id=<?php echo $category['id']; ?>">
                <img src="<?php echo getCategoryImage($category); ?>" alt="<?php echo $category['name']; ?>">
              </a>
              <div class="collection-title">
                <span><?php echo $category['name']; ?></span>
                <span class="badge bg-danger"><?php echo $category['product_count']; ?></span>
              </div>
            </div>
            <?php endforeach; ?>
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
          <li><a href="home.php" class="active"><i class="ti ti-home"></i>Início</a></li>
          <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
          <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
          <li><a href="profile.php"><i class="ti ti-user"></i>Perfil</a></li>
        </ul>
      </div>
    </div>
    <!-- All JavaScript Files-->
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
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
    <script src="js/cart.js"></script>
    
    <script>
        // Search suggestions functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchSuggestions = document.getElementById('searchSuggestions');
        const suggestionsList = document.getElementById('suggestionsList');
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetchSearchSuggestions(query);
            }, 300);
        });
        
        function fetchSearchSuggestions(query) {
            fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.suggestions.length > 0) {
                        showSuggestions(data.suggestions);
                    } else {
                        hideSuggestions();
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar sugestões:', error);
                    hideSuggestions();
                });
        }
        
        function showSuggestions(suggestions) {
            suggestionsList.innerHTML = '';
            
            suggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.innerHTML = `
                    <i class="ti ti-search me-2"></i>
                    <span>${suggestion.text}</span>
                `;
                item.addEventListener('click', () => {
                    searchInput.value = suggestion.text;
                    hideSuggestions();
                    document.getElementById('searchForm').submit();
                });
                suggestionsList.appendChild(item);
            });
            
            searchSuggestions.style.display = 'block';
        }
        
        function hideSuggestions() {
            searchSuggestions.style.display = 'none';
        }
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-form')) {
                hideSuggestions();
            }
        });
        
        // Voice search functionality
        function startVoiceSearch() {
            if ('webkitSpeechRecognition' in window) {
                const recognition = new webkitSpeechRecognition();
                recognition.lang = 'pt-BR';
                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    searchInput.value = transcript;
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
        
    </script>
    
    <style>
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            align-items: center;
        }
        
        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .search-form {
            position: relative;
        }
    </style>
    
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/waypoints.min.js"></script>
    <script src="dist/js/jquery.easing.min.js"></script>
    <script src="dist/js/jquery.counterup.min.js"></script>
    <script src="dist/js/jquery.countdown.min.js"></script>
    <script src="dist/js/owl.carousel.min.js"></script>
    <script src="dist/js/jquery.magnific-popup.min.js"></script>
    <script src="dist/js/jquery.nice-select.min.js"></script>
    <script src="dist/js/active.js"></script>
    <script src="js/cart.js"></script>
  </body>
</html>
