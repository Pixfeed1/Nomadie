# 🎨 COMPOSANTS BLADE À CRÉER - APPLICATION NOMADIE

## 📊 RÉSUMÉ

**Duplication actuelle** : 216 occurrences dans 52 fichiers (40% du HTML)
**Composants à créer** : 15 minimum (recommandé : 25+)
**Gain estimé** : Réduction de 60% du code HTML

---

## 1️⃣ COMPOSANTS DE CARTES (Cards)

### ✅ `<x-article-card>`
**Duplication** : 5+ fichiers
**Lignes dupliquées** : ~45 lignes × 5 = 225 lignes

**Fichiers concernés** :
- `blog/index.blade.php` (lignes 40-82, 91-120)
- `blog/category.blade` (même structure)
- `blog/show.blade.php` (articles connexes)
- `home.blade.php` (section blog)
- `writer/dashboard/index.blade.php`

**Props attendues** :
```php
@props([
    'article',              // Object Article
    'size' => 'default',   // 'large' | 'default' | 'small'
    'showAuthor' => true,
    'showCategory' => true,
    'showReadTime' => true,
])
```

**Structure actuelle dupliquée** :
```html
<div class="bg-white rounded-lg shadow-lg overflow-hidden card">
    <a href="{{ route('blog.show', $article->slug) }}" class="block overflow-hidden aspect-video">
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
    </a>
    <div class="p-6">
        <div class="flex items-center mb-3">
            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                {{ $article->category }}
            </span>
            <span class="ml-2 text-xs text-text-secondary">
                {{ $article->created_at->locale('fr')->isoFormat('LL') }}
            </span>
            <span class="ml-auto text-xs text-text-secondary flex items-center">
                <svg class="h-4 w-4 mr-1">...</svg>
                {{ $article->reading_time }} min
            </span>
        </div>
        <h2 class="text-xl font-bold text-text-primary mb-2">
            <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
        </h2>
        <p class="text-text-secondary mb-4">{{ $article->excerpt }}</p>
        <div class="flex items-center justify-between">
            <span class="text-sm text-text-secondary">Par {{ $article->author->name }}</span>
            <a href="{{ route('blog.show', $article->slug) }}" class="text-primary">Lire la suite →</a>
        </div>
    </div>
</div>
```

**Utilisation proposée** :
```blade
<x-article-card :article="$article" size="large" />
<x-article-card :article="$article" :show-author="false" />
```

---

### ✅ `<x-trip-card>`
**Duplication** : 3+ fichiers
**Fichiers** : `trips/index.blade.php`, `home.blade.php`, `search.blade.php`

**Props** :
```php
@props([
    'trip',
    'showVendor' => true,
    'showPrice' => true,
    'featured' => false,
])
```

---

### ✅ `<x-stat-card>`
**Duplication** : 4 fois dans le même fichier !
**Fichier** : `writer/dashboard/index.blade.php` (lignes 13-90)

**Props** :
```php
@props([
    'title',
    'value',
    'subtitle' => null,
    'icon',                // 'book' | 'chart' | 'link' | 'star' etc.
    'iconColor' => 'primary',
    'valueColor' => 'text-primary',
])
```

**Structure dupliquée** :
```html
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-text-secondary">{{ $title }}</p>
            <p class="text-2xl font-bold text-text-primary">{{ $value }}</p>
            <p class="text-xs text-text-secondary mt-1">{{ $subtitle }}</p>
        </div>
        <div class="h-12 w-12 bg-primary/10 rounded-full flex items-center justify-center">
            <svg class="h-6 w-6 text-primary"><!-- icon --></svg>
        </div>
    </div>
</div>
```

**Utilisation** :
```blade
<x-stat-card title="Articles publiés" :value="$count" icon="book" />
<x-stat-card title="Score SEO" :value="$score" icon="chart" value-color="text-green-600" />
```

---

### ✅ `<x-offer-type-card>`
**Duplication** : 4 fois dans le même fichier
**Fichier** : `home.blade.php` (lignes 91-150)

**Props** :
```php
@props([
    'type',      // 'accommodation' | 'organized_trip' | 'activity' | 'custom'
    'title',
    'description',
    'count',
    'icon',
    'url',
])
```

---

### ✅ `<x-vendor-card>`
**Duplication** : Probablement dans search, home, vendors index
**Props** :
```php
@props([
    'vendor',
    'showStats' => true,
    'showBadge' => true,
])
```

---

### ✅ `<x-booking-card>`
**Duplication** : Customer dashboard, vendor dashboard
**Props** :
```php
@props([
    'booking',
    'showActions' => true,
    'variant' => 'customer', // 'customer' | 'vendor'
])
```

---

## 2️⃣ COMPOSANTS DE FORMULAIRE (Forms)

### ✅ `<x-form.input>`
**Duplication** : ~30 fichiers
**Lignes dupliquées** : ~10 lignes × 30 = 300 lignes

**Props** :
```php
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'error' => null,
])
```

**Structure dupliquée** :
```html
<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-text-secondary mb-1">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5
               @error($name) border-red-500 @enderror"
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
    >
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if($help)
        <p class="mt-1 text-sm text-text-secondary">{{ $help }}</p>
    @endif
</div>
```

**Utilisation** :
```blade
<x-form.input name="email" label="Email" type="email" required />
<x-form.input name="company_name" label="Nom de l'entreprise" help="Raison sociale complète" />
```

---

### ✅ `<x-form.textarea>`
**Duplication** : ~15 fichiers

**Props** :
```php
@props([
    'name',
    'label' => null,
    'rows' => 4,
    'value' => '',
    'required' => false,
    'placeholder' => '',
])
```

---

### ✅ `<x-form.select>`
**Duplication** : ~20 fichiers

**Props** :
```php
@props([
    'name',
    'label' => null,
    'options' => [],      // ['value' => 'label']
    'selected' => null,
    'placeholder' => 'Sélectionnez...',
    'required' => false,
])
```

---

### ✅ `<x-form.checkbox>`
**Duplication** : ~10 fichiers

**Props** :
```php
@props([
    'name',
    'label',
    'checked' => false,
    'value' => '1',
])
```

---

### ✅ `<x-form.radio>`
**Duplication** : ~5 fichiers

**Props** :
```php
@props([
    'name',
    'label',
    'value',
    'checked' => false,
])
```

---

### ✅ `<x-form.file-upload>`
**Duplication** : ~5 fichiers (vendor registration, create trip, etc.)

**Props** :
```php
@props([
    'name',
    'label' => null,
    'accept' => null,
    'multiple' => false,
    'preview' => false,   // Afficher preview image
])
```

---

### ✅ `<x-form.group>`
**Duplication** : Wrapper pour tous les formulaires

**Props** :
```php
@props([
    'label',
    'name',
    'required' => false,
    'error' => null,
    'help' => null,
])
```

**Utilisation** :
```blade
<x-form.group label="Email" name="email" required>
    <input type="email" name="email" ... />
</x-form.group>
```

---

## 3️⃣ COMPOSANTS UI (Interface)

### ✅ `<x-button>`
**Duplication** : PARTOUT (~50 fichiers)
**Lignes dupliquées** : ~5 lignes × 50 = 250 lignes

**Props** :
```php
@props([
    'variant' => 'primary',  // 'primary' | 'secondary' | 'danger' | 'success' | 'outline'
    'size' => 'md',          // 'sm' | 'md' | 'lg'
    'type' => 'button',
    'href' => null,          // Si lien
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
])
```

**Exemples d'utilisation** :
```blade
<x-button>Enregistrer</x-button>
<x-button variant="danger">Supprimer</x-button>
<x-button href="{{ route('blog') }}" icon="arrow-right">Voir le blog</x-button>
<x-button :loading="true">Envoi en cours...</x-button>
```

---

### ✅ `<x-badge>`
**Duplication** : ~30 fichiers (statuts, catégories, etc.)

**Props** :
```php
@props([
    'variant' => 'primary',  // 'primary' | 'success' | 'warning' | 'danger' | 'info'
    'size' => 'md',
    'rounded' => 'full',     // 'full' | 'md' | 'sm'
])
```

**Utilisation** :
```blade
<x-badge>Nouveau</x-badge>
<x-badge variant="success">Actif</x-badge>
<x-badge variant="warning">En attente</x-badge>
```

---

### ✅ `<x-alert>`
**Duplication** : Messages flash dans tous les fichiers

**Props** :
```php
@props([
    'type' => 'info',    // 'success' | 'error' | 'warning' | 'info'
    'dismissible' => true,
    'icon' => true,
])
```

**Structure dupliquée** :
```html
<div class="rounded-md bg-green-50 p-4 mb-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400">...</svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ $slot }}</p>
        </div>
        <div class="ml-auto pl-3">
            <button class="...">×</button>
        </div>
    </div>
</div>
```

**Utilisation** :
```blade
<x-alert type="success">Enregistrement réussi !</x-alert>
<x-alert type="error">Une erreur est survenue.</x-alert>
```

---

### ✅ `<x-modal>`
**Duplication** : ~8 fichiers

**Props** :
```php
@props([
    'name',               // ID unique
    'title' => null,
    'size' => 'md',      // 'sm' | 'md' | 'lg' | 'xl'
    'closeButton' => true,
])
```

---

### ✅ `<x-dropdown>`
**Duplication** : Menus, actions, etc.

**Props** :
```php
@props([
    'align' => 'right',  // 'left' | 'right'
    'width' => '48',     // Largeur en rem
])
```

---

### ✅ `<x-pagination>`
**Duplication** : Toutes les listes

**Props** :
```php
@props([
    'paginator',
    'simple' => false,
])
```

---

### ✅ `<x-table>`
**Duplication** : Admin panels, dashboards

**Props** :
```php
@props([
    'headers' => [],
    'striped' => true,
    'hoverable' => true,
])
```

---

### ✅ `<x-tabs>`
**Duplication** : Settings, dashboards

**Props** :
```php
@props([
    'active' => null,
])
```

---

### ✅ `<x-breadcrumb>`
**Duplication** : ~20 fichiers

**Props** :
```php
@props([
    'items' => [],  // [['label' => 'Home', 'url' => '/'], ...]
])
```

---

## 4️⃣ COMPOSANTS SPÉCIFIQUES NOMADIE

### ✅ `<x-search-bar>`
**Duplication** : home.blade.php, search page
**Fichiers** : `home.blade.php` lignes 33-79

**Props** :
```php
@props([
    'variant' => 'full',  // 'full' | 'compact'
    'action' => null,
])
```

---

### ✅ `<x-hero-section>`
**Duplication** : Pages publiques

**Props** :
```php
@props([
    'title',
    'subtitle' => null,
    'backgroundImage' => null,
    'cta' => [],  // [['label' => 'CTA', 'url' => '/']]
])
```

---

### ✅ `<x-rating-stars>`
**Duplication** : Reviews, vendor cards, trip cards

**Props** :
```php
@props([
    'rating',          // 0-5
    'size' => 'md',
    'showValue' => true,
])
```

---

### ✅ `<x-price-display>`
**Duplication** : Trip cards, booking details

**Props** :
```php
@props([
    'amount',
    'currency' => 'EUR',
    'period' => null,  // 'jour' | 'personne' | 'nuit'
    'oldPrice' => null,
])
```

---

### ✅ `<x-destination-badge>`
**Duplication** : Trip cards, search results

**Props** :
```php
@props([
    'destination',
    'showFlag' => true,
])
```

---

## 5️⃣ COMPOSANTS D'ICÔNES

### ✅ `<x-icon>`
**Duplication** : SVG dupliqués PARTOUT

**Props** :
```php
@props([
    'name',              // 'home' | 'user' | 'calendar' | 'search' | etc.
    'size' => 'md',      // 'sm' | 'md' | 'lg'
    'color' => 'currentColor',
])
```

**Utilisation** :
```blade
<x-icon name="calendar" size="sm" />
<x-icon name="search" color="text-primary" />
```

---

## 📊 RÉCAPITULATIF DES GAINS

| Composant | Occurrences | Lignes/occurrence | Lignes économisées |
|-----------|-------------|-------------------|-------------------|
| `<x-article-card>` | 5+ fichiers | 45 lignes | **225+ lignes** |
| `<x-stat-card>` | 4× dans 1 fichier | 15 lignes | **60 lignes** |
| `<x-form.input>` | 30 fichiers | 10 lignes | **300 lignes** |
| `<x-button>` | 50+ fichiers | 5 lignes | **250+ lignes** |
| `<x-badge>` | 30 fichiers | 3 lignes | **90 lignes** |
| `<x-alert>` | 20 fichiers | 12 lignes | **240 lignes** |
| `<x-offer-type-card>` | 4× dans 1 fichier | 20 lignes | **80 lignes** |
| **+ 18 autres** | ... | ... | **~2000 lignes** |

**TOTAL ESTIMÉ** : **~3,200+ lignes économisées** (sur ~8,000 lignes HTML)

---

## 🎯 PRIORITÉS DE CRÉATION

### PHASE 1 (URGENT) - 5 composants
1. `<x-article-card>` - Utilisé partout
2. `<x-button>` - Utilisé partout
3. `<x-form.input>` - Formulaires critiques
4. `<x-alert>` - Messages utilisateur
5. `<x-badge>` - Statuts partout

### PHASE 2 (IMPORTANT) - 5 composants
6. `<x-stat-card>` - Dashboards
7. `<x-trip-card>` - Core business
8. `<x-form.select>` - Formulaires
9. `<x-form.textarea>` - Formulaires
10. `<x-modal>` - Interactions

### PHASE 3 (UTILE) - 5 composants
11. `<x-table>` - Admin
12. `<x-pagination>` - Listes
13. `<x-offer-type-card>` - Home page
14. `<x-search-bar>` - Recherche
15. `<x-rating-stars>` - Reviews

### PHASE 4 (NICE TO HAVE) - 10 composants
16-25. Tous les autres composants

---

## 📝 STRUCTURE DE FICHIERS PROPOSÉE

```
resources/views/components/
├── ui/
│   ├── button.blade.php
│   ├── badge.blade.php
│   ├── alert.blade.php
│   ├── modal.blade.php
│   ├── dropdown.blade.php
│   ├── pagination.blade.php
│   ├── table.blade.php
│   ├── tabs.blade.php
│   └── breadcrumb.blade.php
├── form/
│   ├── input.blade.php
│   ├── textarea.blade.php
│   ├── select.blade.php
│   ├── checkbox.blade.php
│   ├── radio.blade.php
│   ├── file-upload.blade.php
│   └── group.blade.php
├── cards/
│   ├── article.blade.php
│   ├── trip.blade.php
│   ├── vendor.blade.php
│   ├── booking.blade.php
│   ├── stat.blade.php
│   └── offer-type.blade.php
├── nomadie/
│   ├── search-bar.blade.php
│   ├── hero-section.blade.php
│   ├── rating-stars.blade.php
│   ├── price-display.blade.php
│   └── destination-badge.blade.php
└── icon.blade.php
```

---

## 🚀 EXEMPLE DE REFACTORING

### AVANT (45 lignes dupliquées) :
```blade
<!-- blog/index.blade.php -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden card">
    <a href="{{ route('blog.show', $article->slug) }}" class="block overflow-hidden aspect-video">
        <img src="{{ $article->image_url ?? asset('images/blog/placeholder.jpg') }}"
             alt="{{ $article->title }}"
             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
    </a>
    <div class="p-6">
        <!-- ... 35 lignes de HTML ... -->
    </div>
</div>
```

### APRÈS (1 ligne) :
```blade
<!-- blog/index.blade.php -->
<x-article-card :article="$article" size="large" />
```

---

*Document créé lors de l'audit complet de Nomadie*
*147+ problèmes identifiés - Composants : Priorité HAUTE*
