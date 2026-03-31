<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);">Contact Us</h1>
            <p>Have a question or feedback? We'd love to hear from you.</p>
        </div>

        <div class="grid grid-1-2" style="gap:var(--space-12);margin-top:var(--space-8);">
            <!-- Contact Info -->
            <div>
                <div class="card p-6" style="margin-bottom:var(--space-6);">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Get in Touch</h3>
                    <div style="display:grid;gap:var(--space-4);">
                        <div style="display:flex;gap:var(--space-3);">
                            <i data-lucide="mail" style="color:var(--primary);width:20px;"></i>
                            <div>
                                <div style="font-weight:600;">Email</div>
                                <div class="text-muted"><?= clean(config('contact_email') ?: 'info@' . strtolower(str_replace(' ', '', config('city'))) . '.com') ?></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:var(--space-3);">
                            <i data-lucide="map-pin" style="color:var(--primary);width:20px;"></i>
                            <div>
                                <div style="font-weight:600;">Office</div>
                                <div class="text-muted"><?= clean(config('city')) ?>, South Cotabato</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6" style="background:var(--primary-50);border-color:var(--primary-100);">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-2);color:var(--primary);">Business Inquiries</h3>
                    <p class="text-sm">Looking to promote your business or partner with us? Reach out directly via the form.</p>
                </div>
            </div>

            <!-- Contact Form -->
            <form action="/contact" method="POST" class="card p-8">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="name" class="form-input" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" required placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select name="subject" class="form-select">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Business Listing">Business Listing Question</option>
                        <option value="Bug Report">Report a Bug / Issue</option>
                        <option value="Partnership">Partnership Proposal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-textarea" rows="5" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Send Message</button>
            </form>
        </div>
    </div>
</section>
