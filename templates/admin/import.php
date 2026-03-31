<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'import'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1>Bulk Import Listings</h1>
            <span class="text-sm text-muted">Upload a CSV to quickly add multiple profiles (like Vloggers)</span>
        </div>

        <div class="card" style="margin-bottom: var(--space-6);">
            <h3>1. Prepare Your CSV File</h3>
            <p>Your CSV file should have a header row with exact layout. Not all fields are required, but the Name is.</p>
            <p><strong>Expected Format:</strong><br>
                <code>Name, Address, Phone, Email, Description, Facebook, YouTube, TikTok, Instagram</code>
            </p>
            <div style="margin-top: var(--space-4);">
                <a href="#" onclick="alert('In a real app, this downloads a sample CSV template.'); return false;" class="btn btn-ghost btn-sm">Download Sample Template</a>
            </div>
        </div>

        <div class="card">
            <h3>2. Upload File</h3>
            <form action="/admin/import" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="form-group" style="margin-top: var(--space-4);">
                    <label>Target Category</label>
                    <select name="category_id" required class="form-control" style="max-width:300px;">
                        <option value="">-- Select a Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= clean($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="small text-muted" style="margin-top:4px;">All imported rows will be placed in this category.</div>
                </div>

                <div class="form-group" style="margin-top: var(--space-4);">
                    <label>CSV File</label>
                    <input type="file" name="csv_file" accept=".csv" required class="form-control" style="max-width:300px;">
                </div>

                <div style="margin-top: var(--space-4);">
                    <button type="submit" class="btn btn-primary">Start Import</button>
                    <a href="/admin" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
