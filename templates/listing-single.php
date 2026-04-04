<!-- Single Listing Template -->
<div class="listing-hero">
    <?php if (!empty($images)): ?>
    <img src="<?= $images[0]['image_path'] ?>" alt="<?= clean($listing['name']) ?>">
    <?php else: ?>
    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-dark),var(--primary));display:flex;align-items:center;justify-content:center;">
        <span style="font-size:5rem;"><i data-lucide="<?= $listing['category_icon'] ?? 'map-pin' ?>" style="width:64px;height:64px;color:rgba(255,255,255,0.3);"></i></span>
    </div>
    <?php endif; ?>
    <div class="listing-hero-overlay">
        <div class="container">
            <div class="flex items-center gap-2" style="margin-bottom: var(--space-2); flex-wrap:wrap;">
                <?php foreach ($all_categories as $cat): ?>
                <a href="<?= category_url($cat) ?>" class="badge badge-category">
                    <i data-lucide="<?= $cat['icon'] ?? 'tag' ?>" style="width:14px;height:14px;"></i> <?= clean($cat['name']) ?>
                </a>
                <?php endforeach; ?>
                <?php if ($listing['is_featured']): ?>
                <span class="badge badge-featured"><i data-lucide="star" style="width:14px;height:14px;margin-right:2px;"></i> <?= __('featured') ?></span>
                <?php endif; ?>
                <?php $open = is_open_now($listing['hours']); if ($open !== null): ?>
                <span class="badge <?= $open ? 'badge-open' : 'badge-closed' ?>">
                    <?= $open ? __('open_now') : __('closed') ?>
                </span>
                <?php endif; ?>
            </div>
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);font-weight:700;"><?= clean($listing['name']) ?></h1>
            <?php if ($listing['address']): ?>
            <p style="color:rgba(255,255,255,0.8);margin-top:var(--space-2);display:flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:16px;height:16px;"></i> <?= clean($listing['address']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="listing-detail">
    <div class="container">
        <?php if ($listing['status'] === 'expired'): ?>
        <div class="card" style="border-left:5px solid var(--error); background:var(--error-50); margin-bottom:var(--space-8); padding:var(--space-6); display:flex; align-items:center; gap:var(--space-4);">
            <div style="background:var(--error); color:white; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i data-lucide="archive" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <h3 style="color:var(--error-700); margin:0; font-size:var(--text-lg); font-family:var(--font-heading);">Legacy / Closed Listing</h3>
                <p style="color:var(--error-600); margin:0; font-size:var(--text-sm);">This business has been archived or is no longer in operation. We preserve this page for community record and SEO continuity.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="listing-detail-grid">
            <!-- Main Content -->
            <div>
                <!-- Description -->
                <?php if ($listing['description']): ?>
                <div style="margin-bottom:var(--space-8);">
                    <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">About</h2>
                    <div style="line-height:1.8;color:var(--gray-600);">
                        <?= nl2br(clean($listing['description'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Real Estate Details -->
                <?php if ($listing['property_type']): ?>
                <div class="sidebar-card" style="margin-bottom:var(--space-8);">
                    <h3 style="display:flex;align-items:center;gap:6px;"><i data-lucide="home" style="width:18px;height:18px;"></i> Property Details</h3>
                    <table style="width:100%;font-size:var(--text-sm);">
                        <tr><td style="padding:var(--space-2) 0;color:var(--gray-500);">Type</td><td style="font-weight:600;"><?= clean(ucfirst(str_replace('_',' ',$listing['property_type']))) ?></td></tr>
                        <?php if ($listing['property_sqm']): ?>
                        <tr><td style="padding:var(--space-2) 0;color:var(--gray-500);">Size</td><td style="font-weight:600;"><?= number_format($listing['property_sqm']) ?> sqm</td></tr>
                        <?php endif; ?>
                        <?php if ($listing['property_price']): ?>
                        <tr><td style="padding:var(--space-2) 0;color:var(--gray-500);">Price</td><td style="font-weight:700;color:var(--primary);font-size:var(--text-lg);"><?= config('currency') ?><?= number_format($listing['property_price']) ?></td></tr>
                        <?php endif; ?>
                        <?php if ($listing['property_terms']): ?>
                        <tr><td style="padding:var(--space-2) 0;color:var(--gray-500);">Terms</td><td><?= clean($listing['property_terms']) ?></td></tr>
                        <?php endif; ?>
                        <?php if ($listing['broker_license']): ?>
                        <tr><td style="padding:var(--space-2) 0;color:var(--gray-500);">PRC License #</td><td><?= clean($listing['broker_license']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                    <p style="font-size:var(--text-xs);color:var(--gray-400);margin-top:var(--space-3);border-top:1px solid var(--gray-100);padding-top:var(--space-3);">
                        <?= clean(config('disclaimers')['real_estate'] ?? '') ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Map -->
                <?php if ($listing['type'] !== 'creator' && $listing['lat'] && $listing['lng']): ?>
                <div style="margin-bottom:var(--space-8);">
                    <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-4);"><?= __('view_on_map') ?></h2>
                    <div id="listing-map" class="map-container"></div>
                    <div class="flex gap-4 mt-4">
                        <button class="btn btn-primary" onclick="openDirections(<?= $listing['lat'] ?>,<?= $listing['lng'] ?>,'<?= addslashes($listing['name']) ?>')">
                            <i data-lucide="compass" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"></i> <?= __('get_directions') ?>
                        </button>
                        <button class="btn btn-ghost" onclick="shareListing('<?= addslashes($listing['name']) ?>','<?= listing_url($listing) ?>')">
                            <i data-lucide="share-2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"></i> <?= __('share') ?>
                        </button>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        initDisplayMap('listing-map', <?= $listing['lat'] ?>, <?= $listing['lng'] ?>, '<?= addslashes($listing['name']) ?>');
                    });
                </script>
                <?php endif; ?>

                <!-- Image Gallery -->
                <?php if (count($images) > 1): ?>
                <div style="margin-bottom:var(--space-8);">
                    <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Photos</h2>
                    <div class="grid grid-3">
                        <?php foreach ($images as $img): ?>
                        <div style="border-radius:var(--radius-lg);overflow:hidden;aspect-ratio:4/3;">
                            <img src="<?= $img['image_path'] ?>" alt="<?= clean($img['alt_text'] ?? $listing['name']) ?>"
                                 style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div id="reviews" style="margin-bottom:var(--space-8); border-top: 1px solid var(--gray-200); padding-top: var(--space-8);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-6);">
                        <div>
                            <h2 style="font-family:var(--font-heading); margin-bottom:var(--space-1);">Community Reviews</h2>
                            <?php if (!empty($reviews)): ?>
                                <div style="display:flex; align-items:center; gap:var(--space-2);">
                                    <?= render_stars($avg_rating) ?>
                                    <span style="font-weight:700;"><?= number_format($avg_rating, 1) ?></span>
                                    <span style="color:var(--gray-400); font-size:var(--text-sm);">(<?= count($reviews) ?> reviews)</span>
                                </div>
                            <?php else: ?>
                                <p style="color:var(--gray-400); font-size:var(--text-sm);">No reviews yet. Be the first to share your experience!</p>
                            <?php endif; ?>
                        </div>
                        <a href="#review-form" class="btn btn-ghost btn-sm">Write a Review</a>
                    </div>

                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <?= render_review_card($review) ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Submit Review Form -->
                    <div id="review-form" style="margin-top:var(--space-10); padding:var(--space-6); background: var(--bg-surface); border: 1px solid var(--border-base); border-radius: var(--radius-xl);">
                        <h3 style="font-family:var(--font-heading); margin-bottom:var(--space-4);">Submit your Review</h3>
                        <form action="/actions/review-submit" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                            
                            <div class="form-group" style="margin-bottom:var(--space-4);">
                                <label style="display:block; font-weight:600; margin-bottom:var(--space-2);">Rating</label>
                                <div class="star-picker">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                    <label style="cursor:pointer; font-size:24px;">
                                        <input type="radio" name="rating" value="<?= $i ?>" style="display:none;" <?= $i===5?'checked':'' ?>>
                                        <i data-lucide="star" class="star-input" data-rating="<?= $i ?>"></i>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:var(--space-4);">
                                <div class="form-group">
                                    <label for="review_name" style="display:block; font-weight:600; margin-bottom:var(--space-2);">Your Name</label>
                                    <input type="text" id="review_name" name="user_name" class="form-input" required 
                                           value="<?= clean(auth_user()['name'] ?? '') ?>" placeholder="e.g. Juan Dela Cruz">
                                </div>
                                <div class="form-group">
                                    <label for="review_comment" style="display:block; font-weight:600; margin-bottom:var(--space-2);">Comment (Optional)</label>
                                    <textarea id="review_comment" name="comment" class="form-input" rows="1" placeholder="What did you like about this place?"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width:100%;">Post Review</button>
                        </form>
                    </div>
                </div>

                <style>
                    .star-picker .star-input { color: var(--gray-300); transition: color 0.1s; }
                    .star-picker label:hover .star-input,
                    .star-picker label:hover ~ label .star-input,
                    .star-picker input:checked ~ i,
                    .star-picker input:checked + i { color: var(--warning); fill: var(--warning); }
                    
                    /* Simple Star Rating Styles */
                    .star-rating { color: var(--warning); display: flex; gap: 2px; }
                    .star-rating i { width: 16px; height: 16px; fill: currentColor; }
                    .star-rating .star-empty { fill: none; color: var(--gray-300); }
                    
                    .review-card { padding: var(--space-4) 0; border-bottom: 1px solid var(--gray-100); }
                    .review-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-2); }
                    .review-user strong { display: block; font-size: var(--text-base); }
                    .review-date { font-size: var(--text-xs); color: var(--gray-400); }
                    .review-body p { font-size: var(--text-sm); color: var(--gray-600); line-height: 1.6; }
                </style>
                <script>
                    // Star picker interactivity
                    document.querySelectorAll('.star-picker label').forEach(label => {
                        label.addEventListener('click', function() {
                            const rating = this.querySelector('input').value;
                            document.querySelectorAll('.star-picker i').forEach(icon => {
                                const iconRating = icon.getAttribute('data-rating');
                                if (iconRating <= rating) {
                                    icon.style.fill = 'var(--warning)';
                                    icon.style.color = 'var(--warning)';
                                } else {
                                    icon.style.fill = 'none';
                                    icon.style.color = 'var(--gray-300)';
                                }
                            });
                        });
                    });
                </script>
            </div>

            <!-- Sidebar -->
            <div class="listing-sidebar">
                <!-- Contact Card -->
                <div class="sidebar-card">
                    <h3><?= __('contact_info') ?></h3>
                    <?php if ($listing['phone']): ?>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $listing['phone']) ?>" class="btn btn-primary" style="width:100%;margin-bottom:var(--space-3);">
                        <i data-lucide="phone" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> <?= __('call_now') ?>: <?= clean($listing['phone']) ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($listing['email']): ?>
                    <a href="mailto:<?= clean($listing['email']) ?>" class="btn btn-ghost" style="width:100%;margin-bottom:var(--space-3);">
                        <i data-lucide="mail" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> <?= clean($listing['email']) ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($listing['website']): ?>
                    <a href="<?= clean($listing['website']) ?>" target="_blank" rel="noopener" class="btn btn-ghost" style="width:100%;margin-bottom:var(--space-3);">
                        <i data-lucide="globe" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> <?= __('visit_website') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($listing['facebook'])): ?>
                    <a href="<?= clean($listing['facebook']) ?>" target="_blank" rel="noopener" class="btn btn-ghost" style="width:100%;margin-bottom:var(--space-3);">
                        <i data-lucide="facebook" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> Facebook
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($listing['youtube'])): ?>
                    <a href="<?= clean($listing['youtube']) ?>" target="_blank" rel="noopener" class="btn btn-ghost" style="width:100%;margin-bottom:var(--space-3);color:#FF0000;">
                        <i data-lucide="youtube" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> YouTube
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($listing['tiktok'])): ?>
                    <a href="<?= clean($listing['tiktok']) ?>" target="_blank" rel="noopener" class="btn btn-ghost" style="width:100%;margin-bottom:var(--space-3);color:#000;">
                        <svg style="width:18px;height:18px;margin-right:6px;vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-music"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg> TikTok
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($listing['instagram'])): ?>
                    <a href="<?= clean($listing['instagram']) ?>" target="_blank" rel="noopener" class="btn btn-ghost" style="width:100%;">
                        <i data-lucide="instagram" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;"></i> Instagram
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Hours Card -->
                <?php $hours = format_hours($listing['hours']); if ($listing['type'] !== 'creator' && !empty($hours)): ?>
                <div class="sidebar-card">
                    <h3><?= __('hours_of_operation') ?></h3>
                    <?php foreach ($hours as $h): ?>
                    <div style="display:flex;justify-content:space-between;padding:var(--space-2) 0;border-bottom:1px solid var(--gray-50);font-size:var(--text-sm);">
                        <span style="color:var(--gray-600);"><?= $h['day'] ?></span>
                        <span style="font-weight:500;<?= $h['hours'] === 'Closed' ? 'color:var(--gray-400)' : '' ?>"><?= $h['hours'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Shop Online Card -->
                <?php if ($listing['shopee_link'] || $listing['lazada_link'] || $listing['amazon_link'] || $listing['food_ordering_link']): ?>
                <div class="sidebar-card">
                    <h3><?= __('shop_online') ?></h3>
                    <div class="external-links">
                        <?php if ($listing['shopee_link']): ?>
                        <a href="<?= clean($listing['shopee_link']) ?>" target="_blank" rel="noopener nofollow" class="external-link" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="shopping-cart" style="width:16px;height:16px;"></i> Shopee</a>
                        <?php endif; ?>
                        <?php if ($listing['lazada_link']): ?>
                        <a href="<?= clean($listing['lazada_link']) ?>" target="_blank" rel="noopener nofollow" class="external-link" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="shopping-bag" style="width:16px;height:16px;"></i> Lazada</a>
                        <?php endif; ?>
                        <?php if ($listing['amazon_link']): ?>
                        <a href="<?= clean($listing['amazon_link']) ?>" target="_blank" rel="noopener nofollow" class="external-link" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="package" style="width:16px;height:16px;"></i> Amazon</a>
                        <?php endif; ?>
                        <?php if ($listing['food_ordering_link']): ?>
                        <a href="<?= clean($listing['food_ordering_link']) ?>" target="_blank" rel="noopener nofollow" class="external-link" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="utensils" style="width:16px;height:16px;"></i> <?= __('order_food') ?></a>
                        <?php endif; ?>
                    </div>
                    <p style="font-size:var(--text-xs);color:var(--gray-400);margin-top:var(--space-3);">
                        <?= clean(config('disclaimers')['affiliate'] ?? '') ?>
                    </p>
                </div>
                <?php endif; ?>
                <!-- Help Improve Card -->
                <div class="sidebar-card" style="background:var(--primary-50); border: 1px dashed var(--primary-200);">
                    <h3 style="color:var(--primary-700); font-size:var(--text-base); margin-bottom:var(--space-2);">Help improve this listing</h3>
                    <p style="font-size:var(--text-xs); color:var(--primary-600); margin-bottom:var(--space-3);">Add a storefront photo from your phone so customers can find you easily!</p>
                    <a href="/add-photo/<?= $listing['slug'] ?>" class="btn btn-primary btn-sm" style="width:100%;">
                        <i data-lucide="camera" style="width:14px;height:14px;margin-right:6px;vertical-align:middle;"></i> Add Photo
                    </a>
                </div>

                <!-- Claim Business Card -->
                <?php if (empty($listing['owner_id'])): ?>
                <div class="sidebar-card" style="margin-top:var(--space-4); border:1px solid var(--border-base); background:var(--bg-surface);">
                    <h3 style="font-size:var(--text-base); margin-bottom:var(--space-2);"><?= __('is_this_your_business') ?></h3>
                    <p style="font-size:var(--text-xs); color:var(--gray-500); margin-bottom:var(--space-3);"><?= __('claim_subtitle') ?></p>
                    <a href="/claim/<?= $listing['slug'] ?>" class="btn btn-ghost btn-sm" style="width:100%; border:1px solid var(--border-base);">
                        <i data-lucide="check-circle" style="width:14px;height:14px;margin-right:6px;vertical-align:middle;"></i> <?= __('claim_this_business') ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Listings -->
<?php if (!empty($related)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Similar Places</h2>
        </div>
        <div class="grid grid-3">
            <?php foreach ($related as $item): ?>
            <a href="<?= listing_url($item) ?>" class="card listing-card">
                <div class="card-img">
                    <img src="<?= get_listing_image($item) ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= clean($item['name']) ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
