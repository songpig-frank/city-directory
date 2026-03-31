<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);">About <?= clean(config('site_name')) ?></h1>
            <p class="text-xl">Your digital gateway to the heart of South Cotabato.</p>
        </div>

        <div class="content" style="line-height:1.8;color:var(--gray-700);">
            <p><strong><?= clean(config('site_name')) ?></strong> is a community-driven directory and tourism platform dedicated to showcasing the vibrant businesses, breathtaking destinations, and talented creators of <?= clean(config('city')) ?>.</p>
            
            <h2 style="font-family:var(--font-heading);margin-top:var(--space-12);">Our Mission</h2>
            <p>We aim to empower local entrepreneurs by giving them a professional digital presence while making it easier for residents and tourists to discover everything our municipality has to offer.</p>
            
            <div class="card" style="margin-top:var(--space-12);background:var(--primary-50);border:1px solid var(--primary-100);padding:var(--space-8);">
                <h3 style="color:var(--primary);">A Platform for Everyone</h3>
                <ul style="margin-top:var(--space-4);display:grid;gap:var(--space-3);">
                    <li><strong>For Residents:</strong> Find daily essentials, contact emergency services, and stay updated on local news.</li>
                    <li><strong>For Businesses:</strong> Connect with new customers and build your local brand.</li>
                    <li><strong>For Tourists:</strong> Discover waterfalls, resorts, and hidden gems in <?= clean(config('city')) ?>.</li>
                </ul>
            </div>
            
            <p style="margin-top:var(--space-12);text-align:center;">
                <a href="/directory" class="btn btn-primary btn-lg">Explore the Directory</a>
                <a href="/submit" class="btn btn-ghost btn-lg">Add Your Business</a>
            </p>
        </div>
    </div>
</section>
