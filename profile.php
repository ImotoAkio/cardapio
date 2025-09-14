<?php
require_once 'includes/database.php';

// Verificar se usuário está logado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

// Obter dados do usuário
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Se usuário não encontrado, limpar sessão e redirecionar
    session_destroy();
    header('Location: login.php');
    exit;
}

// Obter contagem de pedidos do usuário
$stmt = $db->prepare("SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?");
$stmt->execute([$userId]);
$orderCount = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];

// Obter contagem de itens no carrinho
$cartManager = new CartManager($db);
$sessionId = session_id();
$cartCount = $cartManager->getCartItemCount($sessionId);
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
    <!-- The above tags *must* come first in the head, any other head content must come *after* these tags -->
    <!-- Title -->
    <title>Tempero e Café - Meu Perfil</title>
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
    
    <?php include 'includes/header.php'; ?>
    
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Profile Wrapper-->
            <div class="profile-wrapper-area py-3">
                 <!-- User Information-->
                 <div class="card user-info-card">
                     <div class="card-body p-4 d-flex align-items-center">
                         <div class="user-profile me-3">
                             <img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" 
                                  style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; cursor: pointer; border: 3px solid #fff;"
                                  data-bs-toggle="modal" data-bs-target="#avatarModal">
                         </div>
                         <div class="user-info">
                             <p class="mb-0 text-white"><?php echo htmlspecialchars($user['email']); ?></p>
                             <h5 class="mb-0 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                             <small class="text-white-50">Clique no avatar para alterar</small>
                         </div>
                     </div>
                 </div>
                
                <!-- User Meta Data-->
                <div class="card user-data-card">
                    <div class="card-body">
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-at"></i><span>Email</span></div>
                            <div class="data-content"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-user"></i><span>Nome Completo</span></div>
                            <div class="data-content"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-phone"></i><span>WhatsApp</span></div>
                            <div class="data-content"><?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-mail"></i><span>Email</span></div>
                            <div class="data-content"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-location-pin"></i><span>Endereço:</span></div>
                            <div class="data-content"><?php echo htmlspecialchars($user['address']); ?></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-star-filled"></i><span>Meus Pedidos</span></div>
                            <div class="data-content"><a class="btn btn-primary btn-sm" href="my-orders.php">Ver Pedidos (<?php echo $orderCount; ?>)</a></div>
                        </div>
                        <div class="single-profile-data d-flex align-items-center justify-content-between">
                            <div class="title d-flex align-items-center"><i class="ti ti-calendar"></i><span>Membro desde</span></div>
                            <div class="data-content"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        
                        <!-- Edit Profile-->
                        <div class="edit-profile-btn mt-3"><a class="btn btn-primary btn-lg w-100" href="edit-profile.php"><i class="ti ti-pencil me-2"></i>Editar Perfil</a></div>
                        
                        <!-- Logout Button-->
                        <div class="logout-btn mt-2"><a class="btn btn-outline-danger btn-lg w-100" href="logout.php"><i class="ti ti-logout me-2"></i>Sair da Conta</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Avatar Selection Modal -->
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarModalLabel">Escolher Avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="col-6 mb-3">
                                <div class="avatar-option text-center">
                                    <img src="dist/img/bg-img/user/<?php echo $i; ?>.png" 
                                         alt="Avatar <?php echo $i; ?>" 
                                         class="img-fluid rounded-circle mb-2 avatar-preview" 
                                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 3px solid transparent;"
                                         data-avatar="<?php echo $i; ?>.png"
                                         onclick="selectAvatar('<?php echo $i; ?>.png')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="avatar" id="avatar<?php echo $i; ?>" 
                                               value="<?php echo $i; ?>.png" <?php echo ($user['avatar'] ?? '1.png') == $i . '.png' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="avatar<?php echo $i; ?>">
                                            Avatar <?php echo $i; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveAvatar()">Salvar Avatar</button>
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
                <li><a href="profile.php" class="active"><i class="ti ti-user"></i>Perfil</a></li>
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
    <script src="js/cart.js"></script>
    
    <script>
        // Atualizar contador do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            if (window.cartManager) {
                window.cartManager.updateCartCount();
            }
        });

        // Função para selecionar avatar
        function selectAvatar(avatarName) {
            // Remover seleção anterior
            document.querySelectorAll('.avatar-preview').forEach(img => {
                img.style.border = '3px solid transparent';
            });
            
            // Selecionar novo avatar
            const selectedImg = document.querySelector(`[data-avatar="${avatarName}"]`);
            selectedImg.style.border = '3px solid #007bff';
            
            // Marcar radio button
            const radio = document.querySelector(`input[value="${avatarName}"]`);
            radio.checked = true;
        }

        // Função para salvar avatar
        function saveAvatar() {
            const selectedAvatar = document.querySelector('input[name="avatar"]:checked');
            if (!selectedAvatar) {
                alert('Por favor, selecione um avatar!');
                return;
            }

            // Fazer requisição AJAX para salvar avatar
            fetch('save_avatar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'avatar=' + encodeURIComponent(selectedAvatar.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar avatar na página principal
                    const mainAvatar = document.querySelector('.user-profile img');
                    mainAvatar.src = 'dist/img/bg-img/user/' + selectedAvatar.value;
                    
                    // Atualizar avatar no header
                    const headerAvatar = document.querySelector('.user-profile-icon img');
                    if (headerAvatar) {
                        headerAvatar.src = 'dist/img/bg-img/user/' + selectedAvatar.value;
                    }
                    
                    // Atualizar avatar no menu lateral
                    const sidenavAvatar = document.querySelector('.sidenav-profile .user-profile img');
                    if (sidenavAvatar) {
                        sidenavAvatar.src = 'dist/img/bg-img/user/' + selectedAvatar.value;
                    }
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
                    modal.hide();
                    
                    // Mostrar mensagem de sucesso
                    showAlert('Avatar atualizado com sucesso!', 'success');
                } else {
                    showAlert('Erro ao atualizar avatar: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao atualizar avatar. Tente novamente.', 'danger');
            });
        }

        // Função para mostrar alertas
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Remover alerta após 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
