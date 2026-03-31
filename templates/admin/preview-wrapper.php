<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body { margin: 0; padding: 0; background: #0f172a; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        .preview-header { height: 50px; background: #1e293b; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; border-bottom: 1px solid #334155; color: white; }
        .preview-controls { display: flex; gap: 10px; }
        .preview-btn { background: #334155; border: none; color: #94a3b8; padding: 6px 12px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; transition: all 0.2s; }
        .preview-btn:hover { background: #475569; color: white; }
        .preview-btn.active { background: var(--primary, #0d9488); color: white; }
        
        .preview-frame-container { flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px; overflow: auto; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 20px 20px; }
        .preview-frame { background: white; border-radius: 8px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); border: 8px solid #334155; transition: width 0.3s ease; height: 100%; max-height: 90vh; }
        
        .device-mobile { width: 375px; height: 667px !important; }
        .device-tablet { width: 768px; height: 1024px !important; }
        .device-desktop { width: 100%; height: 100% !important; border: none; border-radius: 0; }
        
        .url-display { font-family: monospace; font-size: 12px; color: #94a3b8; background: #0f172a; padding: 4px 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <header class="preview-header">
        <div style="display:flex; align-items:center; gap:15px;">
            <a href="/admin" style="color:white; text-decoration:none; display:flex; align-items:center; gap:5px;"><i data-lucide="chevron-left" style="width:16px;"></i> Admin</a>
            <div class="url-display"><?= clean($preview_url) ?></div>
        </div>

        <div class="preview-controls">
            <button class="preview-btn <?= $device === 'mobile' ? 'active' : '' ?>" onclick="setDevice('mobile')">
                <i data-lucide="smartphone" style="width:16px;"></i> Mobile
            </button>
            <button class="preview-btn <?= $device === 'tablet' ? 'active' : '' ?>" onclick="setDevice('tablet')">
                <i data-lucide="tablet" style="width:16px;"></i> Tablet
            </button>
            <button class="preview-btn <?= $device === 'desktop' ? 'active' : '' ?>" onclick="setDevice('desktop')">
                <i data-lucide="monitor" style="width:16px;"></i> Desktop
            </button>
        </div>

        <div>
            <a href="<?= clean($preview_url) ?>" class="preview-btn" target="_top">Exit Preview</a>
        </div>
    </header>

    <div class="preview-frame-container">
        <iframe id="preview-iframe" class="preview-frame device-<?= $device ?>" src="<?= clean($preview_url) ?>" frameborder="0"></iframe>
    </div>

    <script>
        function setDevice(device) {
            const iframe = document.getElementById('preview-iframe');
            iframe.className = 'preview-frame device-' + device;
            
            document.querySelectorAll('.preview-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Set global size if possible
            if (window.setDeviceSize) window.setDeviceSize(device);
        }

        // Parent listener for children notifications
        window.setDeviceSize = function(device) {
             const iframe = document.getElementById('preview-iframe');
             iframe.className = 'preview-frame device-' + device;
             document.querySelectorAll('.preview-btn').forEach(btn => {
                 if (btn.textContent.toLowerCase().includes(device)) btn.classList.add('active');
                 else btn.classList.remove('active');
             });
        };

        lucide.createIcons();
    </script>
</body>
</html>
