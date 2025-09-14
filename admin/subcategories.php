<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - GESTÃO DE SUBCATEGORIAS
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter alerta se houver
$alert = getAlert();

// Processar ações
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'edit' && $id) {
    $subcategory = $subcategoryManager->getSubcategoryById($id);
    if (!$subcategory) {
        showAlert('Subcategoria não encontrada!', 'danger');
        redirect('subcategories.php');
    }
}

if ($action === 'delete' && $id) {
    if ($subcategoryManager->deleteSubcategory($id)) {
        showAlert('Subcategoria excluída com sucesso!', 'success');
    } else {
        showAlert('Erro ao excluir subcategoria!', 'danger');
    }
    redirect('subcategories.php');
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => (int)$_POST['category_id'],
        'name' => sanitizeInput($_POST['name']),
        'slug' => generateSlug($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        'image' => sanitizeInput($_POST['image']),
        'sort_order' => (int)$_POST['sort_order'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    if ($action === 'create') {
        if ($subcategoryManager->createSubcategory($data)) {
            showAlert('Subcategoria criada com sucesso!', 'success');
        } else {
            showAlert('Erro ao criar subcategoria!', 'danger');
        }
    } elseif ($action === 'edit' && $id) {
        if ($subcategoryManager->updateSubcategory($id, $data)) {
            showAlert('Subcategoria atualizada com sucesso!', 'success');
        } else {
            showAlert('Erro ao atualizar subcategoria!', 'danger');
        }
    }
    
    redirect('subcategories.php');
}

// Obter categorias para formulário
$categories = $categoryManager->getAllCategories();

// Obter subcategorias para listagem
$subcategories = $subcategoryManager->getAllSubcategories();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Subcategorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d3a74e;
            --dark-color: #0d2b2b;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--dark-color), #1a4a4a);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #666;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: linear-gradient(45deg, var(--primary-color), #b8943f);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #b8943f;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-shop me-2"></i>
                Tempero e Café Admin
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo $auth->getUser()['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <nav class="nav flex-column pt-3">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="bi bi-box-seam me-2"></i>Produtos
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="bi bi-tags me-2"></i>Categorias
                    </a>
                    <a class="nav-link active" href="subcategories.php">
                        <i class="bi bi-tag me-2"></i>Subcategorias
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="bi bi-cart-check me-2"></i>Pedidos
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people me-2"></i>Usuários
                    </a>
                    <a class="nav-link" href="coupons.php">
                        <i class="bi bi-ticket-perforated me-2"></i>Cupons
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="bi bi-gear me-2"></i>Configurações
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Alertas -->
                <?php if ($alert): ?>
                    <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alert['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Título e Botões -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-tag me-2"></i>Gestão de Subcategorias</h2>
                    <div>
                        <a href="subcategories.php?action=create" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Nova Subcategoria
                        </a>
                    </div>
                </div>

                <?php if ($action === 'create' || $action === 'edit'): ?>
                    <!-- Formulário de Subcategoria -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Nova Subcategoria' : 'Editar Subcategoria'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="subcategories.php?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Categoria *</label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Selecione uma categoria</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo ($subcategory['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                                        <?php echo $category['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nome da Subcategoria *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $subcategory['name'] ?? ''; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Descrição</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $subcategory['description'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Imagem</label>
                                            <input type="file" class="form-control" id="subcategory_image" name="subcategory_image" accept="image/*">
                                            <div class="form-text">Selecione uma imagem (JPG, PNG, WebP)</div>
                                            
                                            <!-- Preview da imagem -->
                                            <div id="image-preview" class="mt-3">
                                                <?php if ($subcategory && $subcategory['image']): ?>
                                                    <div class="image-preview-item d-inline-block me-2 mb-2 position-relative">
                                                        <img src="../dist/<?php echo $subcategory['image']; ?>" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this)">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Input hidden para armazenar URL da imagem -->
                                            <input type="hidden" id="image_url" name="image" value="<?php echo $subcategory['image'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Ordem de Exibição</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                                   value="<?php echo $subcategory['sort_order'] ?? 0; ?>" min="0">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                       <?php echo ($subcategory['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">Subcategoria Ativa</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="subcategories.php" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Criar Subcategoria' : 'Atualizar Subcategoria'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Lista de Subcategorias -->
                    <div class="row">
                        <?php foreach ($subcategories as $subcategory): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="subcategory-image me-3" style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                <?php if ($subcategory['image']): ?>
                                                    <img src="../dist/<?php echo $subcategory['image']; ?>" alt="<?php echo $subcategory['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="bi bi-tag text-muted" style="font-size: 24px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0"><?php echo $subcategory['name']; ?></h5>
                                                <small class="text-muted"><?php echo $subcategory['category_name']; ?></small>
                                            </div>
                                        </div>
                                        
                                        <p class="card-text text-muted"><?php echo $subcategory['description']; ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?php echo $subcategory['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $subcategory['is_active'] ? 'Ativa' : 'Inativa'; ?>
                                            </span>
                                            
                                            <div class="btn-group" role="group">
                                                <a href="subcategories.php?action=edit&id=<?php echo $subcategory['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="subcategories.php?action=delete&id=<?php echo $subcategory['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Tem certeza que deseja excluir esta subcategoria?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upload de imagem para subcategoria
        document.getElementById('subcategory_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('type', 'subcategory');
            
            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar input hidden
                    document.getElementById('image_url').value = data.url;
                    
                    // Adicionar preview
                    const previewContainer = document.getElementById('image-preview');
                    previewContainer.innerHTML = `
                        <div class="image-preview-item d-inline-block me-2 mb-2 position-relative">
                            <img src="../dist/${data.url}" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                } else {
                    alert('Erro ao fazer upload: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao fazer upload da imagem');
            });
            
            // Limpar input
            e.target.value = '';
        });

        // Remover imagem
        function removeImage(button) {
            const previewItem = button.parentElement;
            const img = previewItem.querySelector('img');
            const imageUrl = img.src.replace('../dist/', '');
            
            // Limpar input hidden
            document.getElementById('image_url').value = '';
            
            // Remover preview
            previewItem.remove();
        }
    </script>
</body>
</html>
