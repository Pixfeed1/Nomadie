import './bootstrap';
import Alpine from 'alpinejs';

// Rendre Alpine global
window.Alpine = Alpine;

// Charger conditionnellement create-trip-form uniquement sur les pages de création de trips/offres
// Vérifier si on est sur une page qui nécessite ce composant
const needsTripForm = document.querySelector('[x-data*="createTripForm"]');
if (needsTripForm) {
    import('./create-trip-form');
}

// Charger conditionnellement vendor-registration uniquement sur les pages d'inscription vendeur
const needsVendorRegistration = document.querySelector('[x-data*="vendorRegistration"]');
if (needsVendorRegistration) {
    import('./vendor-registration');
}

// Démarrer Alpine après un court délai pour permettre aux imports conditionnels de se charger
setTimeout(() => {
    Alpine.start();
}, 100);