<div class="error-container">
    <div class="error-icon" style="color: var(--error); margin-bottom: var(--space-4);">
        <i data-lucide="shield-alert" style="width: 80px; height: 80px;"></i>
    </div>
    <h1 style="font-size: var(--text-4xl); font-weight: 800; color: var(--gray-900); margin-bottom: var(--space-2);">403</h1>
    <p style="font-size: var(--text-xl); color: var(--gray-500); margin-bottom: var(--space-8);">Oops! You don't have permission to access this area.</p>
    
    <div style="display: flex; gap: var(--space-4); justify-content: center;">
        <a href="/" class="btn btn-primary" style="display: flex; align-items: center; gap: var(--space-2);">
            <i data-lucide="home" style="width: 18px; height: 18px;"></i>
            Back to Home
        </a>
        <a href="/contact" class="btn btn-ghost" style="display: flex; align-items: center; gap: var(--space-2);">
            <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
            Contact Support
        </a>
    </div>
</div>

<style>
.error-container {
    padding: var(--space-12) var(--space-4);
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
