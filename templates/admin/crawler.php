<div class="admin-layout">
    <?php $active_page = 'admin/crawler'; include __DIR__ . '/_sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-header">
            <div>
                <h1>AI Business Crawler</h1>
                <p class="text-sm text-muted">Enter a URL to automatically extract business details and import them into the directory.</p>
            </div>
        </div>

        <!-- Crawler Input -->
        <div class="card p-6 mb-8" style="background:var(--primary-50); border:1px dashed var(--primary-300);">
            <form action="/admin/crawler/execute" method="POST" id="crawl-form">
                <?= csrf_field() ?>
                <div class="form-group mb-4">
                    <label class="form-label" for="url">Target URL</label>
                    <div style="display:flex; gap:12px;">
                        <input type="url" name="url" id="url" class="form-input" placeholder="https://facebook.com/business-page or any website" style="flex:1;" required>
                        <button type="submit" class="btn btn-primary" id="crawl-btn">
                            <i data-lucide="zap" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"></i> Run Crawler
                        </button>
                    </div>
                </div>
                <p class="text-xs text-primary-700">Powered by AI extraction. It will attempt to find Name, Address, Category, and Contact details.</p>
            </form>
            
            <div id="crawl-loader" style="display:none; text-align:center; padding:var(--space-8);">
                <i data-lucide="loader-2" class="animate-spin" style="width:48px;height:48px;color:var(--primary);margin-bottom:12px;"></i>
                <p class="font-bold">Analyzing website content... This may take 10-20 seconds.</p>
            </div>
        </div>

        <?php if (!empty($results)): ?>
        <div class="card overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold">Extraction Results</h2>
                <form action="/admin/crawler/clear" method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-xs text-error">Clear Results</button>
                </form>
            </div>
            
            <form action="/admin/crawler/import" method="POST">
                <?= csrf_field() ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                            <th>Business Details</th>
                            <th>Category Hub</th>
                            <th width="150" class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $item): ?>
                        <tr>
                            <td><input type="checkbox" name="import_idx[]" value="<?= $index ?>" checked></td>
                            <td>
                                <div class="mb-2">
                                    <input type="text" name="data[<?= $index ?>][name]" value="<?= clean($item['name']) ?>" class="form-input form-input-sm font-bold mb-1">
                                    <textarea name="data[<?= $index ?>][description]" class="form-input form-input-sm text-xs" rows="2"><?= clean($item['description']) ?></textarea>
                                </div>
                                <div class="grid grid-2 gap-2">
                                    <input type="text" name="data[<?= $index ?>][address]" value="<?= clean($item['address']) ?>" class="form-input form-input-sm text-xs" title="Address">
                                    <input type="text" name="data[<?= $index ?>][phone]" value="<?= clean($item['phone']) ?>" class="form-input form-input-sm text-xs" title="Phone">
                                </div>
                            </td>
                            <td>
                                <select name="data[<?= $index ?>][category_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Assign Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (stripos($item['category'] ?? '', $cat['name']) !== false) ? 'selected' : '' ?>>
                                        <?= clean($cat['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-right">
                                <a href="<?= $item['website'] ?>" target="_blank" class="text-primary text-xs flex items-center justify-end gap-1">
                                    <i data-lucide="external-link" style="width:12px;height:12px;"></i> View Source
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="p-4 bg-gray-50 text-right border-t">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="download" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"></i> Import Selected Listings
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('crawl-form').onsubmit = () => {
        document.getElementById('crawl-btn').disabled = true;
        document.getElementById('crawl-loader').style.display = 'block';
    };

    function toggleAll(cb) {
        document.querySelectorAll('input[name="import_idx[]"]').forEach(i => i.checked = cb.checked);
    }
</script>

<style>
    .form-input-sm { padding: 4px 8px; font-size: 13px; }
    .form-select-sm { padding: 4px; font-size: 13px; min-height: 32px; }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
