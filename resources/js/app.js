import './bootstrap';
import Alpine from 'alpinejs';

// Rendre Alpine global
window.Alpine = Alpine;

// Fonction pour charger les composants nécessaires avant de démarrer Alpine
async function initializeApp() {
    const promises = [];

    // Charger create-trip-form uniquement si nécessaire
    const needsTripForm = document.querySelector('[x-data*="createTripForm"]');
    if (needsTripForm) {
        console.log('📦 Chargement de create-trip-form...');
        promises.push(import('./create-trip-form'));
    }

    // Charger vendor-registration uniquement si nécessaire
    const needsVendorRegistration = document.querySelector('[x-data*="vendorRegistration"]');
    if (needsVendorRegistration) {
        console.log('📦 Chargement de vendor-registration...');
        promises.push(import('./vendor-registration'));
    }

    // Attendre que tous les modules soient chargés
    if (promises.length > 0) {
        await Promise.all(promises);
        console.log('✅ Tous les modules sont chargés');
    }

    // Démarrer Alpine
    console.log('🚀 Démarrage d\'Alpine...');
    Alpine.start();
    console.log('✅ Alpine démarré');
}

// Démarrer l'application
initializeApp();