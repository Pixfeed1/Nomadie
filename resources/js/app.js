import './bootstrap';
import Alpine from 'alpinejs';

// Rendre Alpine global
window.Alpine = Alpine;

// Importer create-trip-form AVANT de démarrer Alpine
import './create-trip-form';

// Importer vendor-registration AVANT de démarrer Alpine
import './vendor-registration';

// Démarrer Alpine après que tout soit chargé
Alpine.start();