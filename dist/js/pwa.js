// Service Worker Register 
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('service-worker.js')
      .then(registration => {
        //console.log('Service Worker is registered', registration);
      })
      .catch(err => {
        console.error('Registration failed:', err);
      });
  });
}

// PWA Installation
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
   e.preventDefault();
   deferredPrompt = e;
   console.log('PWA: beforeinstallprompt event fired');
});

// Aguardar o DOM estar carregado
document.addEventListener('DOMContentLoaded', function() {
    console.log('PWA: DOM loaded, setting up install button');
    setupInstallButton();
});

// Também executar quando a página carregar completamente
window.addEventListener('load', function() {
    console.log('PWA: Window loaded, setting up install button');
    setupInstallButton();
});

function setupInstallButton() {
    const installButton = document.getElementById('installSuha');
    
    if (installButton) {
        console.log('PWA: Install button found');
        
        function updateInstallButton() {
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                installButton.textContent = 'Instalado';
                installButton.disabled = true;
            } else {
                installButton.textContent = 'Instalar Agora';
                installButton.disabled = false;
            }
        }

        installButton.addEventListener('click', async () => {
            console.log('PWA: Install button clicked');
            
            if (installButton.textContent === 'Instalado') {
                return;
            }

            if (deferredPrompt) {
                console.log('PWA: Showing install prompt');
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    console.log('PWA: User accepted installation');
                    installButton.textContent = 'Instalado';
                    installButton.disabled = true;
                } else {
                    console.log('PWA: User declined installation');
                    installButton.textContent = 'Instalar Agora';
                }
                deferredPrompt = null;
            } else {
                console.log('PWA: No deferred prompt available');
                // Fallback para iOS Safari
                if (window.navigator.standalone === false) {
                    alert('Para instalar este app no iOS:\n1. Toque no botão Compartilhar\n2. Selecione "Adicionar à Tela Inicial"');
                }
            }
        });

        updateInstallButton();
        window.matchMedia('(display-mode: standalone)').addEventListener('change', updateInstallButton);
    } else {
        console.log('PWA: Install button not found');
    }
}