<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - GESTÃO DE PRODUTOS
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter alerta se houver
$alert = getAlert();

// Processar ações
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Obter categorias para formulário
$categories = $categoryManager->getAllCategories();
$subcategories = [];

if ($action === 'edit' && $id) {
    $product = $productManager->getProductById($id);
    if (!$product) {
        showAlert('Produto não encontrado!', 'danger');
        redirect('products.php');
    }
    
    // Obter subcategorias da categoria do produto
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $stmt->execute([$product['category_id']]);
    $subcategories = $stmt->fetchAll();
}

// Processar exclusão
if ($action === 'delete' && $id) {
    if ($productManager->deleteProduct($id)) {
        showAlert('Produto excluído com sucesso!', 'success');
    } else {
        showAlert('Erro ao excluir produto!', 'danger');
    }
    redirect('products.php');
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => (int)$_POST['category_id'],
        'subcategory_id' => !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null,
        'name' => sanitizeInput($_POST['name']),
        'slug' => generateSlug($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        'short_description' => sanitizeInput($_POST['short_description']),
        'price' => (float)$_POST['price'],
        'original_price' => !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null,
        'weight' => sanitizeInput($_POST['weight']),
        'dimensions' => sanitizeInput($_POST['dimensions']),
        'sku' => sanitizeInput($_POST['sku']),
        'stock_quantity' => (int)$_POST['stock_quantity'],
        'min_stock' => (int)$_POST['min_stock'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_on_sale' => isset($_POST['is_on_sale']) ? 1 : 0,
        'is_new' => isset($_POST['is_new']) ? 1 : 0,
        'benefits' => sanitizeInput($_POST['benefits'] ?? ''),
        'images' => $_POST['images'] ?? []
    ];
    
    if ($action === 'create') {
        if ($productManager->createProduct($data)) {
            showAlert('Produto criado com sucesso!', 'success');
        } else {
            showAlert('Erro ao criar produto!', 'danger');
        }
    } elseif ($action === 'edit' && $id) {
        if ($productManager->updateProduct($id, $data)) {
            showAlert('Produto atualizado com sucesso!', 'success');
        } else {
            showAlert('Erro ao atualizar produto!', 'danger');
        }
    }
    
    redirect('products.php');
}

// Obter produtos para listagem
$page = (int)($_GET['page'] ?? 1);
$products = $productManager->getAllProducts($page, 20);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Produtos</title>
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
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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
                    <a class="nav-link active" href="products.php">
                        <i class="bi bi-box-seam me-2"></i>Produtos
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="bi bi-tags me-2"></i>Categorias
                    </a>
                    <a class="nav-link" href="subcategories.php">
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
                    <h2><i class="bi bi-box-seam me-2"></i>Gestão de Produtos</h2>
                    <div>
                        <a href="products.php?action=create" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Novo Produto
                        </a>
                    </div>
                </div>

                <?php if ($action === 'create' || $action === 'edit'): ?>
                    <!-- Formulário de Produto -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Novo Produto' : 'Editar Produto'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="products.php?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nome do Produto *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $product['name'] ?? ''; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="short_description" class="form-label">Descrição Curta</label>
                                            <textarea class="form-control" id="short_description" name="short_description" rows="2"><?php echo $product['short_description'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Descrição Completa</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo $product['description'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="benefits" class="form-label">Benefícios do Produto</label>
                                            <textarea class="form-control" id="benefits" name="benefits" rows="4" placeholder="• Benefício 1&#10;• Benefício 2&#10;• Benefício 3"><?php echo $product['benefits'] ?? ''; ?></textarea>
                                            <div class="form-text">Liste os principais benefícios do produto, um por linha, começando com "•"</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="product_images" class="form-label">Imagens do Produto</label>
                                            <input type="file" class="form-control" id="product_images" name="product_images[]" multiple accept="image/*">
                                            <div class="form-text">Selecione uma ou mais imagens (JPG, PNG, WebP)</div>
                                            
                                            <!-- Preview das imagens -->
                                            <div id="image-preview" class="mt-3">
                                                <?php if ($product && $product['images']): ?>
                                                    <?php 
                                                    $images = json_decode($product['images'], true);
                                                    
                                                    // Se json_decode falhou, pode ser que o JSON esteja duplamente encodado
                                                    if (!is_array($images)) {
                                                        // Tentar decodificar novamente se for uma string
                                                        if (is_string($images)) {
                                                            $images = json_decode($images, true);
                                                        }
                                                    }
                                                    
                                                    // Garantir que $images é um array
                                                    if (!is_array($images)) {
                                                        $images = [];
                                                    }
                                                    ?>
                                                    <?php foreach ($images as $image): ?>
                                                        <div class="image-preview-item d-inline-block me-2 mb-2 position-relative">
                                                            <img src="../dist/<?php echo $image; ?>" alt="Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this)" style="top: -5px; right: -5px;">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Input hidden para armazenar URLs das imagens -->
                                            <input type="hidden" id="images_json" name="images" value="<?php echo $product['images'] ?? '[]'; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Categoria *</label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Selecione uma categoria</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo ($product['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                                        <?php echo $category['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="subcategory_id" class="form-label">Subcategoria</label>
                                            <select class="form-select" id="subcategory_id" name="subcategory_id">
                                                <option value="">Selecione uma subcategoria</option>
                                                <?php foreach ($subcategories as $subcategory): ?>
                                                    <option value="<?php echo $subcategory['id']; ?>" 
                                                            <?php echo ($product['subcategory_id'] ?? '') == $subcategory['id'] ? 'selected' : ''; ?>>
                                                        <?php echo $subcategory['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Preço *</label>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   step="0.01" value="<?php echo $product['price'] ?? ''; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="original_price" class="form-label">Preço Original</label>
                                            <input type="number" class="form-control" id="original_price" name="original_price" 
                                                   step="0.01" value="<?php echo $product['original_price'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="weight" class="form-label">Peso</label>
                                            <input type="text" class="form-control" id="weight" name="weight" 
                                                   value="<?php echo $product['weight'] ?? ''; ?>" placeholder="Ex: 100g">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="dimensions" class="form-label">Dimensões</label>
                                            <input type="text" class="form-control" id="dimensions" name="dimensions" 
                                                   value="<?php echo $product['dimensions'] ?? ''; ?>" placeholder="Ex: 10x10x5cm">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input type="text" class="form-control" id="sku" name="sku" 
                                                   value="<?php echo $product['sku'] ?? ''; ?>" placeholder="Código do produto">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Estoque</label>
                                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                                   value="<?php echo $product['stock_quantity'] ?? 0; ?>" min="0">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="min_stock" class="form-label">Estoque Mínimo</label>
                                            <input type="number" class="form-control" id="min_stock" name="min_stock" 
                                                   value="<?php echo $product['min_stock'] ?? 5; ?>" min="0">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                       <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">Produto Ativo</label>
                                            </div>
                                            
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                                       <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_featured">Produto em Destaque</label>
                                            </div>
                                            
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_on_sale" name="is_on_sale" 
                                                       <?php echo ($product['is_on_sale'] ?? 0) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_on_sale">Em Promoção</label>
                                            </div>
                                            
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_new" name="is_new" 
                                                       <?php echo ($product['is_new'] ?? 0) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_new">Produto Novo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="products.php" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Criar Produto' : 'Atualizar Produto'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Lista de Produtos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Lista de Produtos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Imagem</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Preço</th>
                                            <th>Estoque</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo getFirstProductImage($product['images']); ?>" 
                                                         alt="<?php echo $product['name']; ?>" 
                                                         class="product-image" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         onerror="this.src='../dist/img/product/default.png'">
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-bold"><?php echo $product['name']; ?></div>
                                                        <small class="text-muted"><?php echo $product['sku']; ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo $product['category_name']; ?></span>
                                                    <?php if ($product['subcategory_name']): ?>
                                                        <br><small class="text-muted"><?php echo $product['subcategory_name']; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?php echo formatPrice($product['price']); ?></div>
                                                    <?php if ($product['original_price']): ?>
                                                        <small class="text-muted text-decoration-line-through">
                                                            <?php echo formatPrice($product['original_price']); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $product['stock_quantity'] <= $product['min_stock'] ? 'danger' : 'success'; ?>">
                                                        <?php echo $product['stock_quantity']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($product['is_featured']): ?>
                                                        <span class="badge bg-warning">Destaque</span>
                                                    <?php endif; ?>
                                                    <?php if ($product['is_on_sale']): ?>
                                                        <span class="badge bg-danger">Promoção</span>
                                                    <?php endif; ?>
                                                    <?php if ($product['is_new']): ?>
                                                        <span class="badge bg-info">Novo</span>
                                                    <?php endif; ?>
                                                    <?php if (!$product['is_active']): ?>
                                                        <span class="badge bg-secondary">Inativo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="bi bi-pencil me-1"></i>Editar
                                                        </a>
                                                        <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Carregar subcategorias quando categoria mudar
        document.getElementById('category_id').addEventListener('change', function() {
            const categoryId = this.value;
            const subcategorySelect = document.getElementById('subcategory_id');
            
            // Limpar opções
            subcategorySelect.innerHTML = '<option value="">Selecione uma subcategoria</option>';
            
            if (categoryId) {
                // Fazer requisição AJAX para buscar subcategorias
                fetch(`get_subcategories.php?category_id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                    });
            }
        });

        // Upload de imagens
        document.getElementById('product_images').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('image-preview');
            const imagesJson = document.getElementById('images_json');
            
            // Obter imagens existentes
            let existingImages = JSON.parse(imagesJson.value || '[]');
            
            for (let file of files) {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('type', 'product');
                
                fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Adicionar à lista de imagens
                        existingImages.push(data.url);
                        imagesJson.value = JSON.stringify(existingImages);
                        
                        // Adicionar preview
                        const previewItem = document.createElement('div');
                        previewItem.className = 'image-preview-item d-inline-block me-2 mb-2 position-relative';
                        previewItem.innerHTML = `
                            <img src="../dist/${data.url}" alt="Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this)">
                                <i class="bi bi-x"></i>
                            </button>
                        `;
                        previewContainer.appendChild(previewItem);
                    } else {
                        alert('Erro ao fazer upload: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao fazer upload da imagem');
                });
            }
            
            // Limpar input
            e.target.value = '';
        });

        // Remover imagem
        function removeImage(button) {
            const previewItem = button.parentElement;
            const img = previewItem.querySelector('img');
            const imageUrl = img.src.replace('../dist/', '');
            
            // Remover da lista de imagens
            const imagesJson = document.getElementById('images_json');
            let existingImages = JSON.parse(imagesJson.value || '[]');
            existingImages = existingImages.filter(url => url !== imageUrl);
            imagesJson.value = JSON.stringify(existingImages);
            
            // Remover preview
            previewItem.remove();
        }
    </script>
</body>
</html>
