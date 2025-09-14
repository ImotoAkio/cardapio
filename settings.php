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

// Buscar configurações do usuário
$stmt = $db->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não existir configurações, criar padrão
if (!$settings) {
    $stmt = $db->prepare("INSERT INTO user_settings (user_id, dark_mode, notifications, language, created_at) VALUES (?, 0, 1, 'pt-BR', NOW())");
    $stmt->execute([$user_id]);
    
    $settings = [
        'dark_mode' => 0,
        'notifications' => 1,
        'language' => 'pt-BR'
    ];
}

// Buscar contagem do carrinho
$stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - Configurações">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Title -->
    <title>Configurações - Tempero e Café</title>
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
    
    <!-- Header Area -->
    <div class="header-area" id="headerArea">
        <div class="container h-100 d-flex align-items-center justify-content-between d-flex rtl-flex-d-row-r">
            <!-- Logo Wrapper -->
            <div class="logo-wrapper"><a href="home.php"><img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café"></a></div>
            <div class="navbar-logo-container d-flex align-items-center">
                <!-- Cart Icon -->
                <div class="cart-icon-wrap"><a href="cart.php"><i class="ti ti-basket-bolt"></i><span class="cart-count"><?php echo $cartCount; ?></span></a></div>
                <!-- User Profile Icon -->
                <div class="user-profile-icon ms-2"><a href="profile.php"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;"></a></div>
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
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div class="container">
            <div class="settings-wrapper py-3">
                
                <!-- Título da Página -->
                <div class="page-title-area">
                    <h2 class="page-title">Configurações</h2>
                    <p class="page-subtitle">Personalize sua experiência</p>
                </div>
                
                <!-- Single Setting Card - Modo Escuro -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-moon"></i>
                                <span>Modo Escuro</span>
                            </div>
                            <div class="data-content">
                                <div class="toggle-button-cover">
                                    <div class="button r">
                                        <input class="checkbox" id="darkSwitch" type="checkbox" <?php echo $settings['dark_mode'] ? 'checked' : ''; ?>>
                                        <div class="knobs"></div>
                                        <div class="layer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Notificações -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-bell-ringing"></i>
                                <span>Notificações</span>
                            </div>
                            <div class="data-content">
                                <div class="toggle-button-cover">
                                    <div class="button r">
                                        <input class="checkbox" id="notificationsSwitch" type="checkbox" <?php echo $settings['notifications'] ? 'checked' : ''; ?>>
                                        <div class="knobs"></div>
                                        <div class="layer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Idioma -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-language"></i>
                                <span>Idioma</span>
                            </div>
                            <div class="data-content">
                                <a href="#" onclick="showLanguageModal()">
                                    Português (BR)
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Alterar Senha -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-key"></i>
                                <span>Alterar Senha</span>
                            </div>
                            <div class="data-content">
                                <a href="#" onclick="showPasswordModal()">
                                    Alterar
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Suporte -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-question-mark"></i>
                                <span>Suporte</span>
                            </div>
                            <div class="data-content">
                                <a href="#" onclick="showSupportModal()">
                                    Obter Ajuda
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Política de Privacidade -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-shield-lock"></i>
                                <span>Política de Privacidade</span>
                            </div>
                            <div class="data-content">
                                <a href="#" onclick="showPrivacyModal()">
                                    Visualizar
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Single Setting Card - Sobre o App -->
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="single-settings d-flex align-items-center justify-content-between">
                            <div class="title">
                                <i class="ti ti-info-circle"></i>
                                <span>Sobre o App</span>
                            </div>
                            <div class="data-content">
                                <a href="#" onclick="showAboutModal()">
                                    Versão 1.0.0
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Modal de Alteração de Senha -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Alterar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="changePassword()">Alterar Senha</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Suporte -->
    <div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Suporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="ti ti-headset" style="font-size: 48px; color: #d3a74e;"></i>
                    </div>
                    <h6>Como podemos ajudar?</h6>
                    <p>Entre em contato conosco através dos canais abaixo:</p>
                    <ul class="list-unstyled">
                        <li><i class="ti ti-phone me-2"></i> WhatsApp: (11) 99999-9999</li>
                        <li><i class="ti ti-mail me-2"></i> Email: suporte@temperoecafe.com</li>
                        <li><i class="ti ti-clock me-2"></i> Horário: Seg-Sex, 8h às 18h</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Política de Privacidade -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Política de Privacidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Informações que Coletamos</h6>
                    <p>Coletamos informações que você nos fornece diretamente, como nome, email, telefone e endereço.</p>
                    
                    <h6>2. Como Usamos suas Informações</h6>
                    <p>Utilizamos suas informações para processar pedidos, melhorar nossos serviços e comunicar com você.</p>
                    
                    <h6>3. Compartilhamento de Informações</h6>
                    <p>Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros.</p>
                    
                    <h6>4. Segurança</h6>
                    <p>Implementamos medidas de segurança para proteger suas informações pessoais.</p>
                    
                    <h6>5. Seus Direitos</h6>
                    <p>Você tem o direito de acessar, corrigir ou excluir suas informações pessoais.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Sobre o App -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aboutModalLabel">Sobre o App</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café" class="mb-3" style="width: 80px;">
                    <h5>Tempero e Café</h5>
                    <p class="text-muted">Versão 1.0.0</p>
                    <p>App para delivery de produtos naturais e orgânicos.</p>
                    <hr>
                    <small class="text-muted">
                        © 2024 Tempero e Café. Todos os direitos reservados.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
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
                <li><a href="settings.php" class="active"><i class="ti ti-settings"></i>Configurações</a></li>
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
        // Função para mostrar modal de senha
        function showPasswordModal() {
            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            modal.show();
        }
        
        // Função para mostrar modal de suporte
        function showSupportModal() {
            const modal = new bootstrap.Modal(document.getElementById('supportModal'));
            modal.show();
        }
        
        // Função para mostrar modal de privacidade
        function showPrivacyModal() {
            const modal = new bootstrap.Modal(document.getElementById('privacyModal'));
            modal.show();
        }
        
        // Função para mostrar modal sobre o app
        function showAboutModal() {
            const modal = new bootstrap.Modal(document.getElementById('aboutModal'));
            modal.show();
        }
        
        // Função para alterar senha
        function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('As senhas não coincidem!');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('A nova senha deve ter pelo menos 6 caracteres!');
                return;
            }
            
            // Aqui você implementaria a lógica para alterar a senha
            fetch('api/change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    currentPassword: currentPassword,
                    newPassword: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Senha alterada com sucesso!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                    modal.hide();
                    document.getElementById('passwordForm').reset();
                } else {
                    alert('Erro ao alterar senha: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao alterar senha. Tente novamente.');
            });
        }
        
        // Salvar configurações quando toggle é alterado
        document.getElementById('darkSwitch').addEventListener('change', function() {
            saveSettings('dark_mode', this.checked ? 1 : 0);
        });
        
        document.getElementById('notificationsSwitch').addEventListener('change', function() {
            saveSettings('notifications', this.checked ? 1 : 0);
        });
        
        function saveSettings(setting, value) {
            fetch('api/save_settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    setting: setting,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Erro ao salvar configuração:', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        }
    </script>
</body>
</html>
