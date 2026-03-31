<!-- Claim Listing Template -->
<section class="section">
    <div class="container" style="max-width: 600px;">
        <div class="section-header" style="text-align: left; margin-bottom: var(--space-8);">
            <div class="badge badge-category" style="margin-bottom: var(--space-2);">
                <i data-lucide="shield-check" style="width:14px;height:14px;"></i> <?= __('is_this_your_business') ?>
            </div>
            <h1 style="font-family:var(--font-heading); font-size:var(--text-3xl); margin-bottom:var(--space-2);"><?= clean($listing['name']) ?></h1>
            <p style="color:var(--gray-500);"><?= __('claim_subtitle') ?></p>
        </div>

        <div class="card" style="padding: var(--space-8); border: 1px solid var(--gray-200); border-radius: var(--radius-xl);">
            <form action="/actions/claim-submit" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">

                <div class="form-group" style="margin-bottom: var(--space-6);">
                    <label for="full_name" style="display:block; font-weight:600; margin-bottom:var(--space-2);"><?= __('claim_name_label') ?></label>
                    <input type="text" id="full_name" name="full_name" class="form-input" value="<?= clean($user['name'] ?? '') ?>" required>
                </div>

                <div class="form-group" style="margin-bottom: var(--space-6);">
                    <label for="contact_phone" style="display:block; font-weight:600; margin-bottom:var(--space-2);"><?= __('claim_phone_label') ?></label>
                    <input type="tel" id="contact_phone" name="contact_phone" class="form-input" placeholder="09XX-XXX-XXXX" required>
                </div>

                <div class="form-group" style="margin-bottom: var(--space-6);">
                    <label for="proof_text" style="display:block; font-weight:600; margin-bottom:var(--space-2);"><?= __('claim_proof_label') ?></label>
                    <textarea id="proof_text" name="proof_text" class="form-input" style="min-height:120px;" placeholder="<?= __('claim_proof_help') ?>" required></textarea>
                </div>

                <div style="background:var(--primary-50); padding:var(--space-4); border-radius:var(--radius-lg); margin-bottom:var(--space-8);">
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                        <i data-lucide="info" style="width:20px; height:20px; color:var(--primary); flex-shrink:0;"></i>
                        <p style="font-size:var(--text-sm); color:var(--primary-700); margin:0;">
                            Your claim will be reviewed by an administrator within 24-48 hours. We may contact you for further verification.
                        </p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; min-height:54px;">
                    <i data-lucide="send" style="width:18px;height:18px;margin-right:8px;vertical-align:middle;"></i> <?= __('submit_claim') ?>
                </button>
                <a href="<?= listing_url($listing) ?>" class="btn btn-ghost" style="width:100%; margin-top:var(--space-3);"><?= __('cancel') ?></a>
            </form>
        </div>
    </div>
</section>

<style>
    .form-input { 
        width: 100%; padding: var(--space-3.5) var(--space-4); 
        border: 1px solid var(--gray-200); border-radius: var(--radius-lg); 
        font-size: var(--text-base); background: white; transition: all 0.2s;
    }
    .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-100); }
</style>
