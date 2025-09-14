// =====================================================
// 🛒 TEMPERO E CAFÉ - JAVASCRIPT DO CARRINHO
// =====================================================

class CartManager {
    constructor() {
        this.apiUrl = 'cart_api.php';
        this.init();
    }
    
    init() {
        // Atualizar contador do carrinho na inicialização
        this.updateCartCount();
        
        // Adicionar event listeners para botões de carrinho
        this.bindCartButtons();
    }
    
    bindCartButtons() {
        // Botões "Adicionar ao Carrinho"
        document.querySelectorAll('[data-cart-add]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.getAttribute('data-cart-add');
                const quantity = this.getQuantityFromButton(button);
                this.addToCart(productId, quantity);
            });
        });
        
        // Botões "Remover do Carrinho"
        document.querySelectorAll('[data-cart-remove]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.getAttribute('data-cart-remove');
                this.removeFromCart(productId);
            });
        });
        
        // Botões "Atualizar Quantidade"
        document.querySelectorAll('[data-cart-update]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.getAttribute('data-cart-update');
                const quantity = this.getQuantityFromButton(button);
                this.updateQuantity(productId, quantity);
            });
        });
    }
    
    getQuantityFromButton(button) {
        // Tentar encontrar input de quantidade próximo ao botão
        const quantityInput = button.closest('.product-card, .cart-item, .add-to-cart-wrapper')?.querySelector('input[type="number"]');
        return quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    }
    
    async addToCart(productId, quantity = 1) {
        try {
            this.showLoading(true);
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            const response = await fetch(`${this.apiUrl}?action=add`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.message);
                this.updateCartCount(result.item_count);
                // Recarregar itens do carrinho se estivermos na página do carrinho
                if (window.location.pathname.includes('cart.php')) {
                    await this.updateCartValues();
                }
            } else {
                this.showError(result.message);
            }
            
        } catch (error) {
            this.showError('Erro ao adicionar produto ao carrinho');
            console.error('Cart Error:', error);
        } finally {
            this.showLoading(false);
        }
    }
    
    async removeFromCart(productId) {
        try {
            console.log(`Removendo produto ${productId} do carrinho...`);
            this.showLoading(true);
            
            const formData = new FormData();
            formData.append('product_id', productId);
            
            const response = await fetch(`${this.apiUrl}?action=remove`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            console.log('Resultado da remoção:', result);
            
            if (result.success) {
                this.showSuccess(result.message);
                this.updateCartCount(result.item_count);
                
                // Remover elemento do DOM imediatamente
                const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
                if (itemElement) {
                    itemElement.remove();
                    console.log(`Elemento do produto ${productId} removido do DOM`);
                }
                
                // Atualizar valores do carrinho
                await this.updateCartValues();
                
                // Se não há mais itens, mostrar mensagem de carrinho vazio
                if (result.item_count === 0) {
                    const cartContainer = document.getElementById('cart-items');
                    if (cartContainer) {
                        cartContainer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="ti ti-basket" style="font-size: 3rem; color: #ccc;"></i>
                                <h5 class="mt-3">Seu carrinho está vazio</h5>
                                <p class="text-muted">Adicione alguns produtos para começar suas compras!</p>
                                <a href="home.php" class="btn btn-primary">Continuar Comprando</a>
                            </div>
                        `;
                    }
                    
                    const cartTotalElement = document.getElementById('cart-total');
                    if (cartTotalElement) {
                        cartTotalElement.innerHTML = '<h5>Total: R$ 0,00</h5>';
                    }
                }
            } else {
                this.showError(result.message);
            }
            
        } catch (error) {
            this.showError('Erro ao remover produto do carrinho');
            console.error('Cart Error:', error);
        } finally {
            this.showLoading(false);
        }
    }
    
    async clearCart() {
        try {
            console.log('Limpando carrinho...');
            this.showLoading(true);
            
            const response = await fetch(`${this.apiUrl}?action=clear`, {
                method: 'POST'
            });
            
            const result = await response.json();
            console.log('Resultado da limpeza:', result);
            
            if (result.success) {
                this.showSuccess(result.message);
                this.updateCartCount(0);
                
                // Limpar interface
                const cartContainer = document.getElementById('cart-items');
                if (cartContainer) {
                    cartContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="ti ti-basket" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3">Seu carrinho está vazio</h5>
                            <p class="text-muted">Adicione alguns produtos para começar suas compras!</p>
                            <a href="home.php" class="btn btn-primary">Continuar Comprando</a>
                        </div>
                    `;
                }
                
                const cartTotalElement = document.getElementById('cart-total');
                if (cartTotalElement) {
                    cartTotalElement.innerHTML = '<h5>Total: R$ 0,00</h5>';
                }
            } else {
                this.showError(result.message);
            }
            
        } catch (error) {
            this.showError('Erro ao limpar carrinho');
            console.error('Cart Error:', error);
        } finally {
            this.showLoading(false);
        }
    }
    
    async updateQuantity(productId, quantity) {
        try {
            console.log(`Atualizando quantidade do produto ${productId} para ${quantity}`);
            this.showLoading(true);
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            const response = await fetch(`${this.apiUrl}?action=update`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            console.log('Resultado da atualização:', result);
            
            if (result.success) {
                this.showSuccess(result.message);
                this.updateCartCount(result.cart_total.item_count);
                // Atualizar valores do carrinho
                await this.updateCartValues();
            } else {
                this.showError(result.message);
            }
            
        } catch (error) {
            this.showError('Erro ao atualizar quantidade');
            console.error('Cart Error:', error);
        } finally {
            this.showLoading(false);
        }
    }
    
    async updateCartCount() {
        try {
            const response = await fetch(`${this.apiUrl}?action=count`);
            const result = await response.json();
            
            if (result.success) {
                this.updateCartCountUI(result.item_count);
            }
        } catch (error) {
            console.error('Error updating cart count:', error);
        }
    }
    
    updateCartCountUI(count) {
        // Atualizar contador no header/navbar
        const cartCountElements = document.querySelectorAll('.cart-count, .cart-item-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline' : 'none';
        });
        
        // Atualizar badge do carrinho
        const cartBadges = document.querySelectorAll('.cart-badge');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        });
    }
    
    // Atualizar apenas os valores do carrinho sem recarregar tudo
    async updateCartValues() {
        try {
            console.log('Atualizando valores do carrinho...');
            const response = await fetch(`${this.apiUrl}?action=items`);
            const result = await response.json();
            
            console.log('Resposta da API:', result);
            
            if (result.success) {
                console.log('Dados recebidos:', result.items, result.cart_total);
                this.updateCartItemValues(result.items, result.cart_total);
            } else {
                console.error('Erro ao obter dados do carrinho:', result.message);
            }
        } catch (error) {
            console.error('Error updating cart values:', error);
        }
    }
    
    // Atualizar apenas os valores dos itens
    updateCartItemValues(items, cartTotal) {
        console.log('Atualizando valores do carrinho:', items, cartTotal);
        
        // Atualizar valores individuais dos itens
        items.forEach(item => {
            const itemElement = document.querySelector(`[data-product-id="${item.product_id}"]`);
            if (itemElement) {
                const totalElement = itemElement.querySelector('.cart-item-total h6');
                const quantityInput = itemElement.querySelector('input[type="number"]');
                
                // Converter para números para garantir cálculo correto
                const price = parseFloat(item.price);
                const quantity = parseInt(item.quantity);
                const total = price * quantity;
                
                // Atualizar total do item
                if (totalElement) {
                    totalElement.textContent = this.formatPrice(total);
                    console.log(`Total do item ${item.product_id} atualizado para: ${this.formatPrice(total)}`);
                }
                
                // Atualizar quantidade no input
                if (quantityInput) {
                    quantityInput.value = quantity;
                    console.log(`Quantidade do item ${item.product_id} atualizada para: ${quantity}`);
                }
                
                console.log(`Item ${item.product_id}: preço=${price}, quantidade=${quantity}, total=${total}`);
            } else {
                console.warn(`Elemento não encontrado para produto ${item.product_id} - pode ter sido removido`);
            }
        });
        
        // Atualizar resumo do carrinho
        let cartTotalElement = document.getElementById('cart-total');
        if (!cartTotalElement) {
            cartTotalElement = document.createElement('div');
            cartTotalElement.id = 'cart-total';
            cartTotalElement.className = 'cart-summary bg-white p-3 rounded mb-3';
            const cartContainer = document.getElementById('cart-items');
            if (cartContainer && cartContainer.parentNode) {
                cartContainer.parentNode.appendChild(cartTotalElement);
            }
        }
        
        if (cartTotalElement) {
            const totalValue = parseFloat(cartTotal.total);
            cartTotalElement.innerHTML = `
                <h6 class="mb-3">Resumo do Pedido</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>${this.formatPrice(totalValue)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <span>R$ 0,00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong>${this.formatPrice(totalValue)}</strong>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="proceedToCheckout()">
                        <i class="ti ti-credit-card"></i> Finalizar Compra
                    </button>
                    <button class="btn btn-outline-secondary" onclick="cartManager.clearCart()">
                        <i class="ti ti-trash"></i> Limpar Carrinho
                    </button>
                </div>
            `;
            console.log('Resumo do carrinho atualizado:', totalValue);
        } else {
            console.warn('Elemento cart-total não encontrado e não foi possível criar');
        }
    }
    
    async loadCartItems() {
        try {
            const response = await fetch(`${this.apiUrl}?action=items`);
            const result = await response.json();
            
            if (result.success) {
                this.renderCartItems(result.items, result.cart_total);
            }
        } catch (error) {
            console.error('Error loading cart items:', error);
        }
    }
    
    renderCartItems(items, cartTotal) {
        const cartContainer = document.getElementById('cart-items');
        let cartTotalElement = document.getElementById('cart-total');
        
        if (!cartContainer) return;
        
        if (items.length === 0) {
            cartContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="ti ti-basket" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3">Seu carrinho está vazio</h5>
                    <p class="text-muted">Adicione alguns produtos para começar suas compras!</p>
                    <a href="home.php" class="btn btn-primary">Continuar Comprando</a>
                </div>
            `;
            
            // Criar ou atualizar elemento cart-total
            if (!cartTotalElement) {
                cartTotalElement = document.createElement('div');
                cartTotalElement.id = 'cart-total';
                cartTotalElement.className = 'cart-summary bg-white p-3 rounded mb-3';
                cartContainer.parentNode.appendChild(cartTotalElement);
            }
            cartTotalElement.innerHTML = '<h5>Total: R$ 0,00</h5>';
            return;
        }
        
        let html = '';
        items.forEach(item => {
            const image = this.getProductImage(item.images);
            // Converter para números para garantir cálculo correto
            const price = parseFloat(item.price);
            const quantity = parseInt(item.quantity);
            const total = price * quantity;
            
            html += `
                <div class="cart-item d-flex align-items-center mb-3 p-3 bg-white rounded" data-product-id="${item.product_id}">
                    <div class="cart-item-image me-3">
                        <img src="${image}" alt="${item.name}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                    </div>
                    <div class="cart-item-details flex-grow-1">
                        <h6 class="mb-1">${item.name}</h6>
                        <p class="text-muted mb-1">${this.formatPrice(price)}</p>
                        <div class="quantity-controls d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity(${item.product_id})">-</button>
                            <input type="number" value="${quantity}" min="1" max="${item.stock_quantity}" class="form-control form-control-sm mx-2" style="width: 60px;" onchange="updateQuantityDirect(${item.product_id}, this.value)">
                            <button class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity(${item.product_id})">+</button>
                        </div>
                    </div>
                    <div class="cart-item-total text-end">
                        <h6 class="mb-1">${this.formatPrice(total)}</h6>
                        <button class="btn btn-sm btn-outline-danger" onclick="cartManager.removeFromCart(${item.product_id})">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        cartContainer.innerHTML = html;
        
        // Criar ou atualizar elemento cart-total
        if (!cartTotalElement) {
            cartTotalElement = document.createElement('div');
            cartTotalElement.id = 'cart-total';
            cartTotalElement.className = 'cart-summary bg-white p-3 rounded mb-3';
            cartContainer.parentNode.appendChild(cartTotalElement);
        }
        
        const totalValue = parseFloat(cartTotal.total);
        cartTotalElement.innerHTML = `
            <h6 class="mb-3">Resumo do Pedido</h6>
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <span>${this.formatPrice(totalValue)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Frete:</span>
                <span>R$ 0,00</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <strong>Total:</strong>
                <strong>${this.formatPrice(totalValue)}</strong>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" onclick="proceedToCheckout()">
                    <i class="ti ti-credit-card"></i> Finalizar Compra
                </button>
                <button class="btn btn-outline-secondary" onclick="cartManager.clearCart()">
                    <i class="ti ti-trash"></i> Limpar Carrinho
                </button>
            </div>
        `;
    }
    
    getProductImage(imagesJson) {
        try {
            const images = JSON.parse(imagesJson);
            if (Array.isArray(images) && images.length > 0) {
                return images[0].startsWith('dist/') ? images[0] : 'dist/' + images[0];
            }
        } catch (e) {
            // Se falhar, tentar decodificar novamente
            try {
                const images = JSON.parse(JSON.parse(imagesJson));
                if (Array.isArray(images) && images.length > 0) {
                    return images[0].startsWith('dist/') ? images[0] : 'dist/' + images[0];
                }
            } catch (e2) {
                // Ignorar erro
            }
        }
        return 'dist/img/product/default.png';
    }
    
    formatPrice(price) {
        return parseFloat(price).toFixed(2).replace('.', ',');
    }
    
    showLoading(show) {
        const loadingElements = document.querySelectorAll('.cart-loading');
        loadingElements.forEach(element => {
            element.style.display = show ? 'block' : 'none';
        });
    }
    
    showSuccess(message) {
        this.showAlert(message, 'success');
    }
    
    showError(message) {
        this.showAlert(message, 'danger');
    }
    
    showAlert(message, type) {
        // Criar elemento de alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Remover automaticamente após 3 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 3000);
    }
}

// Inicializar o gerenciador do carrinho quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    window.cartManager = new CartManager();
    
    // Adicionar funções globais para controle de quantidade
    window.increaseQuantity = function(productId) {
        const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
        if (itemElement) {
            const quantityInput = itemElement.querySelector('input[type="number"]');
            const currentQuantity = parseInt(quantityInput.value);
            const maxQuantity = parseInt(quantityInput.getAttribute('max'));
            
            if (currentQuantity < maxQuantity) {
                const newQuantity = currentQuantity + 1;
                quantityInput.value = newQuantity;
                window.cartManager.updateQuantity(productId, newQuantity);
            }
        }
    };
    
    window.decreaseQuantity = function(productId) {
        const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
        if (itemElement) {
            const quantityInput = itemElement.querySelector('input[type="number"]');
            const currentQuantity = parseInt(quantityInput.value);
            
            if (currentQuantity > 1) {
                const newQuantity = currentQuantity - 1;
                quantityInput.value = newQuantity;
                window.cartManager.updateQuantity(productId, newQuantity);
            }
        }
    };
    
    window.updateQuantityDirect = function(productId, quantity) {
        const newQuantity = parseInt(quantity);
        if (newQuantity >= 1) {
            window.cartManager.updateQuantity(productId, newQuantity);
        }
    };
});
