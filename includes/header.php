<?php
// Verificar se usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
$cartCount = 0;

if ($isLoggedIn) {
    $db = Database::getInstance()->getConnection();
    $userId = $_SESSION['user_id'];
    
    // Obter dados do usuário
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obter contagem de itens no carrinho
    $cartManager = new CartManager($db);
    $sessionId = session_id();
    $cartCount = $cartManager->getCartItemCount($sessionId);
}
?>

<!-- Header Area -->
<div class="header-area" id="headerArea">
    <div class="container h-100 d-flex align-items-center justify-content-between d-flex rtl-flex-d-row-r">
        <!-- Logo Wrapper -->
        <div class="logo-wrapper"><a href="home.php"><img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café"></a></div>
        <div class="navbar-logo-container d-flex align-items-center">
            <!-- Cart Icon -->
            <div class="cart-icon-wrap"><a href="cart.php"><i class="ti ti-basket-bolt"></i><span class="cart-count"><?php echo $cartCount; ?></span></a></div>
            
            <?php if ($isLoggedIn && $user): ?>
                <!-- User Profile Icon (Logado) -->
                <div class="user-profile-icon ms-2"><a href="profile.php"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;"></a></div>
            <?php else: ?>
                <!-- Login Icon (Não logado) -->
                <div class="user-profile-icon ms-2"><a href="login.php"><i class="ti ti-login" style="font-size: 24px; color: #fff;"></i></a></div>
            <?php endif; ?>
            
            <!-- Navbar Toggler -->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas Menu -->
<div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
    <!-- Close button-->
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    <!-- Offcanvas body-->
    <div class="offcanvas-body">
        <?php if ($isLoggedIn && $user): ?>
            <!-- Sidenav Profile (Logado) -->
            <div class="sidenav-profile">
                <div class="user-profile"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;"></div>
                <div class="user-info">
                    <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav (Logado) -->
            <ul class="sidenav-nav ps-0">
                <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
            </ul>
        <?php else: ?>
            <!-- Sidenav Nav (Não logado) -->
            <ul class="sidenav-nav ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="login.php"><i class="ti ti-login"></i>Entrar</a></li>
                <li><a href="cadastro.php"><i class="ti ti-user-plus"></i>Cadastrar</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div>
