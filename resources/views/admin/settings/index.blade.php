@extends('layouts.admin')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres du site')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-success/10 border-l-4 border-success text-success p-4 rounded">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-error/10 border-l-4 border-error text-error p-4 rounded">
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-border">
            <nav class="flex -mb-px" x-data="{ activeTab: 'general' }">
                <button @click="activeTab = 'general'"
                        :class="activeTab === 'general' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-border'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                    Général
                </button>
                <button @click="activeTab = 'seo'"
                        :class="activeTab === 'seo' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-border'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                    SEO
                </button>
                <button @click="activeTab = 'email'"
                        :class="activeTab === 'email' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-border'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                    Email
                </button>
            </nav>
        </div>

        <div x-data="{ activeTab: 'general' }">
            <!-- General Settings -->
            <div x-show="activeTab === 'general'" class="p-6">
                <h2 class="text-xl font-semibold text-text-primary mb-6">Paramètres généraux</h2>

                <form action="{{ route('admin.settings.update.general') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="site_name" class="block text-sm font-medium text-text-primary mb-2">Nom du site</label>
                        <input type="text" name="site_name" id="site_name"
                               value="{{ $settings['site_name'] ?? 'Nomadie' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Le nom de votre site web</p>
                    </div>

                    <div>
                        <label for="site_tagline" class="block text-sm font-medium text-text-primary mb-2">Slogan</label>
                        <input type="text" name="site_tagline" id="site_tagline"
                               value="{{ $settings['site_tagline'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Quelques mots pour décrire votre site</p>
                    </div>

                    <div>
                        <label for="site_description" class="block text-sm font-medium text-text-primary mb-2">Description</label>
                        <textarea name="site_description" id="site_description" rows="4"
                                  class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $settings['site_description'] ?? '' }}</textarea>
                        <p class="mt-1 text-xs text-text-secondary">Description générale de votre site</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-text-primary mb-2">Email de contact</label>
                            <input type="email" name="contact_email" id="contact_email"
                                   value="{{ $settings['contact_email'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-text-primary mb-2">Téléphone</label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   value="{{ $settings['contact_phone'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label for="contact_address" class="block text-sm font-medium text-text-primary mb-2">Adresse</label>
                        <textarea name="contact_address" id="contact_address" rows="3"
                                  class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>

                    <div class="flex items-center pt-4">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                               {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                        <label for="maintenance_mode" class="ml-2 text-sm text-text-primary">
                            Activer le mode maintenance
                        </label>
                    </div>

                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                            Enregistrer les paramètres généraux
                        </button>
                    </div>
                </form>
            </div>

            <!-- SEO Settings -->
            <div x-show="activeTab === 'seo'" class="p-6">
                <h2 class="text-xl font-semibold text-text-primary mb-6">Paramètres SEO</h2>

                <form action="{{ route('admin.settings.update.seo') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-text-primary mb-2">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title"
                               value="{{ $settings['meta_title'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Titre affiché dans les résultats de recherche (60 caractères max)</p>
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-text-primary mb-2">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                                  class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <p class="mt-1 text-xs text-text-secondary">Description affichée dans les résultats de recherche (160 caractères max)</p>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-text-primary mb-2">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords"
                               value="{{ $settings['meta_keywords'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Mots-clés séparés par des virgules</p>
                    </div>

                    <div>
                        <label for="google_analytics_id" class="block text-sm font-medium text-text-primary mb-2">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" id="google_analytics_id"
                               value="{{ $settings['google_analytics_id'] ?? '' }}"
                               placeholder="G-XXXXXXXXXX"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label for="google_search_console" class="block text-sm font-medium text-text-primary mb-2">Google Search Console</label>
                        <input type="text" name="google_search_console" id="google_search_console"
                               value="{{ $settings['google_search_console'] ?? '' }}"
                               placeholder="Code de vérification"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label for="facebook_pixel_id" class="block text-sm font-medium text-text-primary mb-2">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" id="facebook_pixel_id"
                               value="{{ $settings['facebook_pixel_id'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                            Enregistrer les paramètres SEO
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div x-show="activeTab === 'email'" class="p-6">
                <h2 class="text-xl font-semibold text-text-primary mb-6">Paramètres Email</h2>

                <form action="{{ route('admin.settings.update.email') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Ces paramètres sont également configurables dans le fichier .env. Les valeurs saisies ici remplaceront celles du fichier .env.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-text-primary mb-2">Email administrateur</label>
                        <input type="email" name="admin_email" id="admin_email"
                               value="{{ $settings['admin_email'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Email qui recevra les notifications importantes</p>
                    </div>

                    <div>
                        <label for="notification_email" class="block text-sm font-medium text-text-primary mb-2">Email de notification</label>
                        <input type="email" name="notification_email" id="notification_email"
                               value="{{ $settings['notification_email'] ?? '' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="mt-1 text-xs text-text-secondary">Email expéditeur pour les notifications automatiques</p>
                    </div>

                    <div>
                        <label for="from_name" class="block text-sm font-medium text-text-primary mb-2">Nom de l'expéditeur</label>
                        <input type="text" name="from_name" id="from_name"
                               value="{{ $settings['from_name'] ?? 'Nomadie' }}"
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="pt-4 border-t border-border">
                        <h3 class="text-lg font-medium text-text-primary mb-4">Configuration SMTP (optionnel)</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-text-primary mb-2">Hôte SMTP</label>
                                <input type="text" name="smtp_host" id="smtp_host"
                                       value="{{ $settings['smtp_host'] ?? '' }}"
                                       placeholder="smtp.example.com"
                                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>

                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-text-primary mb-2">Port SMTP</label>
                                <input type="text" name="smtp_port" id="smtp_port"
                                       value="{{ $settings['smtp_port'] ?? '587' }}"
                                       placeholder="587"
                                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>

                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-text-primary mb-2">Nom d'utilisateur</label>
                                <input type="text" name="smtp_username" id="smtp_username"
                                       value="{{ $settings['smtp_username'] ?? '' }}"
                                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>

                            <div>
                                <label for="smtp_password" class="block text-sm font-medium text-text-primary mb-2">Mot de passe</label>
                                <input type="password" name="smtp_password" id="smtp_password"
                                       value="{{ $settings['smtp_password'] ?? '' }}"
                                       placeholder="Laisser vide pour ne pas changer"
                                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>

                            <div>
                                <label for="smtp_encryption" class="block text-sm font-medium text-text-primary mb-2">Chiffrement</label>
                                <select name="smtp_encryption" id="smtp_encryption"
                                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ ($settings['smtp_encryption'] ?? '') == '' ? 'selected' : '' }}>Aucun</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-border">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                            Enregistrer les paramètres Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
