@props([
    'article',
    'variant' => 'default', // default, featured, compact
    'showExcerpt' => true,
    'showAuthor' => true,
    'showReadingTime' => true,
    'showCategory' => true
])

<div class="bg-white rounded-lg shadow-lg overflow-hidden card hover:shadow-xl transition-shadow duration-300">
    {{-- Image --}}
    <a href="{{ route('blog.show', $article->slug) }}" class="block overflow-hidden aspect-video">
        <img src="{{ $article->image_url ?? asset('images/blog/placeholder.jpg') }}"
             alt="{{ $article->title }}"
             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
             onerror="this.src='/api/placeholder/800/450';this.onerror=null;"
             loading="lazy">
    </a>

    {{-- Content --}}
    <div class="p-4 sm:p-6">
        {{-- Meta Info --}}
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($showCategory)
            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                {{ $article->category ?? 'Non catégorisé' }}
            </span>
            @endif

            <span class="text-xs text-text-secondary">
                {{ $article->created_at->locale('fr')->isoFormat('LL') }}
            </span>

            @if($showReadingTime)
            <span class="ml-auto text-xs text-text-secondary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $article->reading_time ?? 5 }} min
            </span>
            @endif
        </div>

        {{-- Title --}}
        <h2 class="text-lg sm:text-xl font-bold text-text-primary mb-2 line-clamp-2">
            <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-primary transition-colors">
                {{ $article->title }}
            </a>
        </h2>

        {{-- Excerpt --}}
        @if($showExcerpt)
        <p class="text-sm sm:text-base text-text-secondary mb-4 line-clamp-3">
            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 150) }}
        </p>
        @endif

        {{-- Footer --}}
        <div class="flex flex-wrap items-center justify-between gap-2">
            @if($showAuthor)
            <span class="text-xs sm:text-sm text-text-secondary">
                Par {{ $article->author->display_name ?? $article->author->name ?? 'Anonyme' }}
            </span>
            @endif

            <a href="{{ route('blog.show', $article->slug) }}"
               class="text-primary hover:text-primary-dark text-sm font-medium flex items-center">
                Lire la suite
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</div>
