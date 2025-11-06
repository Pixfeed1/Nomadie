// create-trip-form.js

document.addEventListener('alpine:init', () => {
    console.log('Alpine init - enregistrement des composants...');
    
    // Composant principal du formulaire
    Alpine.data('createTripForm', () => ({
        // Offer type (location, activite, sejour, sur-mesure)
        offerType: window.offerType || window.tripType || 'sejour',
        
        // Current step
        currentStep: 1,
        completedSteps: [],
        
        // Form data - COMPLET pour tous les types
        title: '',
        countryId: '',
        destinationId: '', // Ville pour location/activité, destinations pour séjour
        cities: [],
        loadingCities: false,
        travelTypeId: '',
        shortDescription: '',
        description: '',
        price: '',
        currency: 'EUR',
        status: 'draft',
        requirements: '',
        
        // Location specific
        capacity: 4,
        bedrooms: 1,
        bathrooms: 1,
        propertyType: '',
        minimumNights: 2,
        
        // Activité specific
        durationHours: '',
        maxParticipants: 10,
        difficultyLevel: '',
        
        // Séjour specific
        duration: '',
        maxTravelers: 10,
        physicalLevel: '',
        mealPlan: 'none',
        meetingPoint: '',
        meetingTime: '',
        
        // Sur-mesure specific
        customPricingMode: '',
        customPricingDetails: '',
        
        // Langues
        selectedLanguages: window.oldLanguages || [],
        customLanguages: [],
        
        // Services
        includedItems: [],
        
        // UI state
        showTutorial: false,
        progressPercent: 0,
        isDragging: false,
        
        // Images avec système de légendes
        uploadedImages: [],
        fileInput: null,
        showCaptionModal: false,
        currentEditingImage: null,
        currentEditingIndex: null,
        tempCaption: '',
        
        // Validation states
        validationErrors: {},
        
        // Initialize
        init() {
            console.log('Initializing createTripForm for offer type:', this.offerType);
            
            // Store reference to file input
            this.fileInput = document.getElementById('images');
            
            // Initialize at least one included item
            if (this.includedItems.length === 0) {
                this.addIncludedItem();
            }
            
            // Initialize custom languages
            this.customLanguages = window.oldCustomLanguages || [];
            
            // Listen for language updates
            this.$el.addEventListener('update-progress', () => {
                this.updateProgress();
            });
            
            // Si on a déjà un pays sélectionné (old input), charger les villes
            if (this.countryId) {
                this.loadCities();
            }
            
            // Initialize old values if they exist
            this.initializeOldValues();
            
            this.updateProgress();
        },
        
        initializeOldValues() {
            // Récupérer les anciennes valeurs depuis les inputs du formulaire si elles existent
            const oldValues = {
                title: document.querySelector('[name="title"]')?.value,
                price: document.querySelector('[name="price"]')?.value,
                capacity: document.querySelector('[name="capacity"]')?.value,
                bedrooms: document.querySelector('[name="bedrooms"]')?.value,
                bathrooms: document.querySelector('[name="bathrooms"]')?.value,
                durationHours: document.querySelector('[name="duration_hours"]')?.value,
                maxParticipants: document.querySelector('[name="max_participants"]')?.value,
                duration: document.querySelector('[name="duration"]')?.value,
                maxTravelers: document.querySelector('[name="max_travelers"]')?.value,
            };
            
            // Appliquer les valeurs si elles existent
            Object.keys(oldValues).forEach(key => {
                if (oldValues[key]) {
                    this[key] = oldValues[key];
                }
            });
        },
        
        // Charger les villes d'un pays
        async loadCities() {
            if (!this.countryId) {
                this.cities = [];
                return;
            }
            
            this.loadingCities = true;
            
            try {
                const response = await fetch(`/api/countries/${this.countryId}/cities`);
                const data = await response.json();
                
                if (data.success) {
                    this.cities = data.cities;
                } else {
                    console.error('Erreur lors du chargement des villes:', data.message);
                    this.cities = [];
                }
            } catch (error) {
                console.error('Erreur réseau:', error);
                this.cities = [];
            } finally {
                this.loadingCities = false;
            }
        },
        
        // MÉTHODE CORRIGÉE : Permettre l'enregistrement en brouillon avec moins de validation
        get canSubmit() {
            // Pour un brouillon, validation minimale
            if (this.status === 'draft') {
                // Pour un brouillon, on demande juste un titre
                return this.title && this.title.length > 0;
            }
            
            // Pour publier (status = active), on vérifie tout
            const hasLanguages = this.selectedLanguages.length > 0 || 
                               (this.customLanguages && this.customLanguages.length > 0 && 
                                this.customLanguages.some(l => l.name));
            
            const baseRequirements = this.title && this.countryId && this.travelTypeId &&
                   this.shortDescription && this.description && this.price && 
                   this.uploadedImages.length >= 5 && hasLanguages;
            
            // Conditions spécifiques selon le type
            switch(this.offerType) {
                case 'location':
                    return baseRequirements && this.destinationId && this.capacity && 
                           this.bedrooms !== '' && this.bathrooms !== '' && 
                           this.propertyType && this.minimumNights;
                
                case 'activite':
                    return baseRequirements && this.destinationId && this.durationHours && 
                           this.maxParticipants;
                
                case 'sejour':
                    return baseRequirements && this.duration && this.maxTravelers && 
                           this.physicalLevel && this.mealPlan;
                
                case 'sur-mesure':
                    return baseRequirements && this.customPricingMode && 
                           this.customPricingDetails;
                
                default:
                    return baseRequirements;
            }
        },
        
        updateProgress() {
            let filled = 0;
            let totalFields = 0;
            
            // Champs communs
            const commonFields = ['title', 'countryId', 'travelTypeId', 'shortDescription', 
                                 'description', 'price'];
            commonFields.forEach(field => {
                totalFields++;
                if (this[field]) filled++;
            });
            
            // Champs spécifiques selon le type
            switch(this.offerType) {
                case 'location':
                    const locationFields = ['destinationId', 'capacity', 'bedrooms', 
                                          'bathrooms', 'propertyType', 'minimumNights'];
                    locationFields.forEach(field => {
                        totalFields++;
                        if (this[field] !== '' && this[field] !== null) filled++;
                    });
                    break;
                
                case 'activite':
                    const activityFields = ['destinationId', 'durationHours', 'maxParticipants'];
                    activityFields.forEach(field => {
                        totalFields++;
                        if (this[field]) filled++;
                    });
                    break;
                
                case 'sejour':
                    const tripFields = ['duration', 'maxTravelers', 'physicalLevel', 'mealPlan'];
                    tripFields.forEach(field => {
                        totalFields++;
                        if (this[field]) filled++;
                    });
                    break;
                
                case 'sur-mesure':
                    const customFields = ['customPricingMode', 'customPricingDetails'];
                    customFields.forEach(field => {
                        totalFields++;
                        if (this[field]) filled++;
                    });
                    break;
            }
            
            // Photos et langues
            totalFields += 2;
            if (this.uploadedImages.length >= 5) filled++;
            if (this.selectedLanguages.length > 0 || 
                (this.customLanguages && this.customLanguages.some(l => l.name))) filled++;
            
            this.progressPercent = Math.round((filled / totalFields) * 100);
            
            // Update completed steps
            this.updateCompletedSteps();
        },
        
        updateCompletedSteps() {
            this.completedSteps = [];
            
            // Step 1: Informations
            let step1Complete = this.title && this.countryId && this.travelTypeId && 
                              this.shortDescription && this.description;
            
            if (['location', 'activite'].includes(this.offerType)) {
                step1Complete = step1Complete && this.destinationId;
            }
            
            if (step1Complete) {
                this.completedSteps.push(1);
            }
            
            // Step 2: Détails
            let step2Complete = this.price;
            const hasLanguages = this.selectedLanguages.length > 0 || 
                               (this.customLanguages && this.customLanguages.some(l => l.name));
            
            switch(this.offerType) {
                case 'location':
                    step2Complete = step2Complete && this.capacity && this.bedrooms !== '' && 
                                  this.bathrooms !== '' && this.propertyType && 
                                  this.minimumNights && hasLanguages;
                    break;
                
                case 'activite':
                    step2Complete = step2Complete && this.durationHours && 
                                  this.maxParticipants && hasLanguages;
                    break;
                
                case 'sejour':
                    step2Complete = step2Complete && this.duration && this.maxTravelers && 
                                  this.physicalLevel && this.mealPlan && hasLanguages;
                    break;
                
                case 'sur-mesure':
                    step2Complete = step2Complete && this.customPricingMode && 
                                  this.customPricingDetails && hasLanguages;
                    break;
            }
            
            if (step2Complete) {
                this.completedSteps.push(2);
            }
            
            // Step 3: Services
            if (this.includedItems.some(item => item.value)) {
                this.completedSteps.push(3);
            }
            
            // Step 4: Photos
            if (this.uploadedImages.length >= 5) {
                this.completedSteps.push(4);
            }
        },
        
        validateField(field) {
            // Validation visuelle
            switch(field) {
                case 'title':
                    this.validationErrors.title = this.title.length < 10 || this.title.length > 100;
                    break;
                case 'shortDescription':
                    this.validationErrors.shortDescription = this.shortDescription.length < 50 || 
                                                            this.shortDescription.length > 500;
                    break;
                case 'description':
                    this.validationErrors.description = this.description.length < 200 || 
                                                       this.description.length > 5000;
                    break;
                case 'price':
                    this.validationErrors.price = this.price <= 0;
                    break;
                case 'duration':
                    this.validationErrors.duration = this.duration < 1 || this.duration > 365;
                    break;
                case 'maxTravelers':
                    this.validationErrors.maxTravelers = this.maxTravelers < 1 || this.maxTravelers > 50;
                    break;
                case 'travelType':
                    this.validationErrors.travelType = !this.travelTypeId;
                    break;
            }
        },
        
        // Navigation entre les étapes
        nextStep() {
            if (this.currentStep < 4) {
                this.currentStep++;
            }
        },
        
        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        
        getStepName() {
            const steps = ['Informations', 'Détails', 'Services', 'Photos'];
            return steps[this.currentStep - 1];
        },
        
        // Gestion des services inclus
        addIncludedItem() {
            this.includedItems.push({ value: '' });
        },
        
        removeIncludedItem(index) {
            this.includedItems.splice(index, 1);
            this.updateProgress();
        },
        
        addSuggestion(text) {
            // Vérifier si la suggestion n'existe pas déjà
            if (!this.includedItems.some(item => item.value === text)) {
                // Trouver le premier item vide ou en ajouter un nouveau
                const emptyItemIndex = this.includedItems.findIndex(item => !item.value);
                if (emptyItemIndex !== -1) {
                    this.includedItems[emptyItemIndex].value = text;
                } else {
                    this.includedItems.push({ value: text });
                }
                this.updateProgress();
            }
        },
        
        // Gestion des images avec système de légendes
        handleMultipleImages(event) {
            const files = Array.from(event.target.files);
            console.log('Handling multiple images:', files.length);
            
            // Clear existing images
            this.uploadedImages = [];
            
            // Process new files
            this.processImageFiles(files);
        },
        
        handleDropMultiple(event) {
            event.preventDefault();
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files);
            console.log('Dropped files:', files.length);
            this.processImageFiles(files);
        },
        
        processImageFiles(files) {
            const remainingSlots = 20 - this.uploadedImages.length;
            const filesToProcess = files.slice(0, remainingSlots);
            
            // Filtrer seulement les types d'images acceptés
            const validFiles = filesToProcess.filter(file => {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                return validTypes.includes(file.type) && file.size <= 5 * 1024 * 1024;
            });
            
            console.log('Processing', validFiles.length, 'valid files');
            
            validFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadedImages.push({
                        file: file,
                        preview: e.target.result,
                        caption: '',
                        id: Date.now() + index
                    });
                    this.updateProgress();
                    
                    // Mettre à jour le file input après traitement
                    if (index === validFiles.length - 1) {
                        this.updateFileInput();
                    }
                };
                reader.readAsDataURL(file);
            });
        },
        
        removeUploadedImage(index) {
            this.uploadedImages.splice(index, 1);
            this.updateProgress();
            this.updateFileInput();
        },
        
        updateFileInput() {
            // Créer un nouveau FileList avec les images actuelles
            const dataTransfer = new DataTransfer();
            
            this.uploadedImages.forEach(img => {
                if (img.file) {
                    dataTransfer.items.add(img.file);
                }
            });
            
            // Mettre à jour le file input
            if (this.fileInput) {
                this.fileInput.files = dataTransfer.files;
            }
        },
        
        // Système de modal pour les légendes
        openCaptionModal(index) {
            this.currentEditingIndex = index;
            this.currentEditingImage = this.uploadedImages[index];
            this.tempCaption = this.uploadedImages[index].caption || '';
            this.showCaptionModal = true;
        },
        
        closeCaptionModal() {
            this.showCaptionModal = false;
            this.currentEditingImage = null;
            this.currentEditingIndex = null;
            this.tempCaption = '';
        },
        
        saveCaption() {
            if (this.currentEditingIndex !== null) {
                this.uploadedImages[this.currentEditingIndex].caption = this.tempCaption;
            }
            this.closeCaptionModal();
        }
    }));

    // Composant sélecteur de langues avec support des langues locales
    Alpine.data('languageSelector', () => ({
        // Mode de sélection
        languageMode: 'official', // 'official' ou 'custom'
        
        // Langues officielles
        searchText: '',
        selectedLanguages: [],
        languages: window.languages || [],
        filteredLanguages: [],
        showSuggestions: false,
        
        // Langues custom/locales
        customLanguages: [],
        maxCustomLanguages: 5,
        
        init() {
            console.log('Initializing languageSelector...');
            
            // Initialiser avec les anciennes langues si disponibles
            if (window.oldLanguages && window.oldLanguages.length > 0) {
                this.selectedLanguages = [...window.oldLanguages];
            }
            
            // Initialiser les langues custom si anciennes données
            if (window.oldCustomLanguages && window.oldCustomLanguages.length > 0) {
                this.customLanguages = [...window.oldCustomLanguages];
            } else {
                // Ajouter une ligne vide pour commencer
                this.addCustomLanguage();
            }
            
            // Synchroniser avec le parent
            this.$watch('selectedLanguages', () => {
                this.syncWithParent();
            });
            
            this.$watch('customLanguages', () => {
                this.syncWithParent();
            }, { deep: true });
        },
        
        syncWithParent() {
            // Mettre à jour le composant parent
            const parent = Alpine.$data(this.$el.closest('[x-data*="createTripForm"]'));
            if (parent) {
                parent.selectedLanguages = [...this.selectedLanguages];
                parent.customLanguages = [...this.customLanguages];
            }
            this.$dispatch('update-progress');
        },
        
        // Méthodes pour les langues officielles
        filterLanguages() {
            if (this.searchText.length < 2) {
                this.filteredLanguages = [];
                this.showSuggestions = false;
                return;
            }
            
            const search = this.searchText.toLowerCase();
            this.filteredLanguages = this.languages
                .filter(lang => 
                    !this.selectedLanguages.includes(lang.id) &&
                    (lang.name.toLowerCase().includes(search) || 
                     (lang.native_name && lang.native_name.toLowerCase().includes(search)))
                )
                .slice(0, 10);
            
            this.showSuggestions = this.filteredLanguages.length > 0;
        },
        
        addLanguage(lang) {
            if (!this.selectedLanguages.includes(lang.id)) {
                this.selectedLanguages.push(lang.id);
                this.searchText = '';
                this.filteredLanguages = [];
                this.showSuggestions = false;
                this.$dispatch('update-progress');
            }
        },
        
        addFirstSuggestion() {
            if (this.filteredLanguages.length > 0) {
                this.addLanguage(this.filteredLanguages[0]);
            }
        },
        
        quickAddLanguage(langName) {
            const lang = this.languages.find(l => l.name === langName);
            if (lang) {
                this.addLanguage(lang);
            }
        },
        
        removeLanguage(id) {
            const index = this.selectedLanguages.indexOf(id);
            if (index > -1) {
                this.selectedLanguages.splice(index, 1);
                this.$dispatch('update-progress');
            }
        },
        
        getLanguageName(id) {
            const lang = this.languages.find(l => l.id === id);
            return lang ? lang.name : '';
        },
        
        // Méthodes pour les langues custom/locales
        addCustomLanguage() {
            if (this.customLanguages.length < this.maxCustomLanguages) {
                this.customLanguages.push({
                    name: '',
                    id: 'custom_' + Date.now()
                });
            }
        },
        
        removeCustomLanguage(index) {
            this.customLanguages.splice(index, 1);
            
            // S'il n'y a plus de langues custom, en ajouter une vide
            if (this.customLanguages.length === 0) {
                this.addCustomLanguage();
            }
            this.$dispatch('update-progress');
        },
        
        clearAllLanguages() {
            this.selectedLanguages = [];
            this.customLanguages = [{ name: '', id: 'custom_' + Date.now() }];
            this.$dispatch('update-progress');
        }
    }));

    // Composant pour le tri des images avec drag & drop
    Alpine.data('imageSorter', () => ({
        draggedIndex: null,
        
        init() {
            // Référence au composant parent
            this.parent = Alpine.$data(this.$el.closest('[x-data*="createTripForm"]'));
        },
        
        dragStart(event, index) {
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', ''); // Fix Firefox
            event.target.classList.add('dragging');
        },
        
        dragEnd(event) {
            event.target.classList.remove('dragging');
            this.draggedIndex = null;
        },
        
        dragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
        },
        
        drop(event, dropIndex) {
            event.preventDefault();
            
            if (this.draggedIndex !== null && this.draggedIndex !== dropIndex && this.parent) {
                const draggedImage = this.parent.uploadedImages[this.draggedIndex];
                
                // Retirer de l'ancienne position
                this.parent.uploadedImages.splice(this.draggedIndex, 1);
                
                // Ajuster l'index de drop si nécessaire
                if (this.draggedIndex < dropIndex) {
                    dropIndex--;
                }
                
                // Insérer à la nouvelle position
                this.parent.uploadedImages.splice(dropIndex, 0, draggedImage);
                
                // Mettre à jour le file input
                this.parent.updateFileInput();
            }
            
            this.draggedIndex = null;
        },
        
        // Interaction avec la modal du parent
        openCaptionModal(index) {
            if (this.parent) {
                this.parent.openCaptionModal(index);
            }
        },
        
        removeUploadedImage(index) {
            if (this.parent) {
                this.parent.removeUploadedImage(index);
            }
        }
    }));
    
    console.log('Composants Alpine enregistrés avec succès !');
});

// Log when script is loaded
console.log('create-trip-form.js loaded successfully');