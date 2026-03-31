<section class="section" style="min-height: 50vh; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="text-align: center;">
        <div style="font-size: 5rem; margin-bottom: var(--space-4);">🚧</div>
        <h1 style="font-family: var(--font-heading); font-size: 3rem; margin-bottom: var(--space-4);">Coming Soon</h1>
        <p class="text-muted" style="font-size: var(--text-lg); max-width: 500px; margin: 0 auto var(--space-8);">
            <?= htmlspecialchars($message ?? 'We are working hard to bring this feature to life. Please check back soon!') ?>
        </p>
        <div class="flex" style="justify-content: center; gap: var(--space-4);">
            <a href="javascript:history.back()" class="btn btn-ghost">Go Back</a>
            <a href="/" class="btn btn-primary">Return to Homepage</a>
        </div>
    </div>
</section>
