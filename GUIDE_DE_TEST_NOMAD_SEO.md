# Guide de Test - Nomad SEO

## 📦 Récupération des changements

```bash
cd /home/user/Nomadie
git pull origin claude/multiple-updates-011CUvSyMnHU8XzsYvU9xxwo
php artisan migrate
```

---

## ✅ Liste des fonctionnalités à tester

### 1. Migration Base de Données

**Vérifier que les colonnes ont été ajoutées** :
```bash
php artisan tinker
```

Puis dans Tinker :
```php
$article = App\Models\Article::first();
$article->subtitle; // Doit retourner null ou une valeur
$article->focus_keyphrase; // Doit retourner null ou une valeur
exit
```

---

### 2. Interface de Création d'Article

**URL** : `/writer/articles/create`

#### Test 1 : Titre et sous-titre centrés
- [ ] Le champ "Ajouter un titre" est centré
- [ ] Le champ "Ajouter un sous-titre" est centré
- [ ] Le texte que vous tapez reste centré

#### Test 2 : Boutons Undo/Redo
- [ ] Vous voyez 2 boutons au centre du header (← et →)
- [ ] Le bouton gauche (Undo) annule la dernière modification
- [ ] Le bouton droit (Redo) refait l'action annulée
- [ ] Tooltip "Annuler (Ctrl+Z)" et "Refaire (Ctrl+Y)"

#### Test 3 : Logo X (Twitter)
- [ ] Dans l'aperçu, le bouton de partage Twitter affiche le logo X
- [ ] Tooltip affiche "Partager sur X (Twitter)"

---

### 3. Mot-clé Principal (Focus Keyphrase)

**Ouvrir** : Sidebar des paramètres (bouton ⚙️)

#### Test 1 : Champ visible
- [ ] Dans la section "Référencement (SEO)", vous voyez "Mot-clé principal"
- [ ] Placeholder : "ex: voyage à Bali"

#### Test 2 : Analyse en temps réel
1. Tapez un titre : "Guide complet voyage à Bali"
2. Dans mot-clé principal, tapez : "voyage à Bali"
3. Vérifiez dans "Analyse Nomad SEO" :
   - [ ] "Mot-clé dans le titre" affiche ✓ (vert)
   - [ ] La densité s'affiche en %

#### Test 3 : Vérification meta description
1. Dans Meta Description, tapez : "Découvrez notre guide voyage à Bali"
2. Vérifiez :
   - [ ] "Mot-clé dans meta" affiche ✓ (vert)

#### Test 4 : Vérification sous-titres
1. Dans l'éditeur, ajoutez un titre H2 : "Préparer son voyage à Bali"
2. Vérifiez :
   - [ ] "Mot-clé dans sous-titres" affiche ✓ (vert)

---

### 4. Mots de Transition

**Dans l'éditeur**, écrivez un paragraphe avec des mots de transition :

```
Bali est une destination incroyable. Cependant, il faut bien se préparer.
En effet, la saison des pluies peut surprendre. Donc, vérifiez la météo avant de partir.
```

**Vérifiez dans "Analyse Nomad SEO"** :
- [ ] "Mots de transition" affiche un pourcentage (ex: 25%)
- [ ] La couleur change selon le score (vert si >= 20%)

---

### 5. Liens Internes et Externes

**Dans l'éditeur**, ajoutez des liens :

1. Lien interne : `<a href="/blog/autre-article">Article connexe</a>`
2. Lien externe : `<a href="https://www.google.com">Google</a>`

**Vérifiez dans "Analyse Nomad SEO"** :
- [ ] "Liens internes" affiche 1 (en vert si >= 1)
- [ ] "Liens externes" affiche 1 (en vert si >= 1)

---

### 6. Score SEO Global

**Barre fixe en bas de l'écran** :
- [ ] Affiche "Score Nomad SEO: XX"
- [ ] Couleur verte si >= 78
- [ ] Couleur orange si >= 50 et < 78
- [ ] Couleur rouge si < 50
- [ ] Affiche nombre de mots
- [ ] Affiche temps de lecture estimé

**Suggestions d'amélioration** :
- [ ] Si score < 78, affiche les points à améliorer
- [ ] Bouton "Voir les détails" ouvre la sidebar

---

### 7. Publication et Sauvegarde

#### Test 1 : Bouton Sauvegarder
- [ ] Cliquer sur "Sauvegarder" enregistre comme brouillon
- [ ] Status reste "draft"

#### Test 2 : Bouton Publier (dropdown)
- [ ] Cliquer sur "Publier" affiche un menu déroulant
- [ ] Option "Publier maintenant"
- [ ] Option "Planifier pour plus tard"

#### Test 3 : Planification
- [ ] Cliquer sur "Planifier" ouvre un modal
- [ ] Choisir date et heure
- [ ] Valider enregistre avec `scheduled_at`

---

### 8. Aperçu de l'article

**Cliquer sur le bouton 👁️ (Aperçu)** :

- [ ] Modal s'ouvre avec l'aperçu
- [ ] Sélecteur Desktop/Tablette/Mobile fonctionne
- [ ] Structure identique à `blog/show.blade.php` :
  - [ ] Breadcrumb : Blog > Catégorie > Article
  - [ ] Image en aspect-video
  - [ ] Catégorie + date + temps de lecture
  - [ ] Titre en text-3xl
  - [ ] Sous-titre/extrait en italic
  - [ ] Infos auteur avec avatar
  - [ ] Boutons partage social (X, Facebook, LinkedIn, WhatsApp)
  - [ ] Tags avec # et bg-bg-alt

---

### 9. Backend - Données sauvegardées

**Après avoir créé un article**, vérifiez en base de données :

```bash
php artisan tinker
```

```php
$article = App\Models\Article::latest()->first();

// Vérifier les champs
$article->subtitle; // Votre sous-titre
$article->focus_keyphrase; // Votre mot-clé

// Vérifier l'analyse SEO
$analysis = $article->latestSeoAnalysis;
$analysis->keyword_data; // Doit contenir focus_keyphrase, in_title, in_meta, density...
$analysis->internal_links_count; // Nombre de liens internes
$analysis->external_links_count; // Nombre de liens externes

// Afficher les données du mot-clé
print_r($analysis->keyword_data);
```

**Résultat attendu** :
```php
Array
(
    [focus_keyphrase] => voyage à Bali
    [in_title] => 1
    [in_meta] => 1
    [in_headings] => 1
    [density] => 2.35
    [occurrences] => 5
    [transitions_count] => 8
    [transitions_percentage] => 25.5
)
```

---

### 10. Test Multi-Rôles

#### Rédacteur (writer)
- [ ] Peut créer des articles
- [ ] Voit toutes les analyses SEO
- [ ] Peut définir un mot-clé principal

#### Client (client_contributor)
- [ ] Peut créer des articles
- [ ] Voit toutes les analyses SEO
- [ ] Peut définir un mot-clé principal

#### Vendeur Rédacteur (vendor_writer)
- [ ] Peut créer des articles
- [ ] Voit toutes les analyses SEO
- [ ] Peut définir un mot-clé principal

---

## 🐛 Problèmes connus à vérifier

1. **EditorJS ne se charge pas** : Vérifiez la console du navigateur
2. **Champs vides après sauvegarde** : Vérifiez que les hidden inputs sont bien présents
3. **Score SEO à 0** : Vérifiez que SeoAnalyzer est bien appelé dans ArticleController

---

## 📊 Données de test suggérées

**Titre** : "Guide complet pour organiser votre voyage à Bali en 2024"

**Sous-titre** : "Découvrez tous nos conseils pratiques pour un séjour inoubliable"

**Mot-clé principal** : "voyage à Bali"

**Meta description** : "Organisez votre voyage à Bali avec notre guide complet : budget, itinéraire, conseils pratiques et bons plans pour un séjour réussi."

**Contenu** (avec mots de transition et liens) :
```
Bali est une destination de rêve pour de nombreux voyageurs. Cependant, organiser son voyage demande une bonne préparation. Dans ce guide, nous vous donnons tous nos conseils.

## Quand partir à Bali ?

La meilleure période pour visiter Bali est d'avril à octobre. En effet, c'est la saison sèche. Toutefois, même pendant la saison des pluies, il est possible de profiter de l'île.

## Budget pour un voyage à Bali

Pour un voyage confortable, prévoyez environ 50€ par jour. Ainsi, vous pourrez profiter pleinement de votre séjour. De plus, Bali offre un excellent rapport qualité-prix.

Pour plus d'informations, consultez notre <a href="/blog/budget-bali">guide budget détaillé</a>.

Vous pouvez également consulter le <a href="https://www.indonesia.travel">site officiel du tourisme</a> pour plus de détails.
```

---

## ✅ Checklist Finale

- [ ] Migration exécutée sans erreur
- [ ] Champs subtitle et focus_keyphrase visibles en base
- [ ] Interface Gutenberg chargée correctement
- [ ] Boutons Undo/Redo fonctionnels
- [ ] Mot-clé principal analysé en temps réel
- [ ] Mots de transition détectés
- [ ] Liens internes/externes comptés
- [ ] Score SEO calculé correctement
- [ ] Données sauvegardées en base de données
- [ ] Aperçu identique au template blog
- [ ] Backend synchronisé avec frontend

---

## 🆘 En cas de problème

1. Vérifiez les logs Laravel : `tail -f storage/logs/laravel.log`
2. Vérifiez la console du navigateur (F12)
3. Vérifiez que la migration a bien tourné : `php artisan migrate:status`
4. Testez avec `php artisan tinker` pour les données en base

---

**Date** : 2025-11-09
**Version** : Nomad SEO v2.0 - Synchronisation Backend/Frontend
