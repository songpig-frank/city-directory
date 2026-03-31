<style>
    .dev-toolbar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--bg-surface);
        border: 1px solid var(--border-base);
        padding: 6px 12px;
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        gap: var(--space-4);
        box-shadow: var(--shadow-xl);
        z-index: 100000;
        backdrop-filter: blur(8px);
    }
    .dev-toolbar-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-right: 1px solid var(--border-base);
        padding-right: var(--space-3);
    }
    .dev-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 500;
    }
    .dev-btn:hover { color: var(--primary); background: var(--primary-50); }
    .dev-btn.active { color: var(--primary); background: var(--primary-100); }

    /* Simulation Styles */
    body.is-simulating {
        background: #0f172a !important;
        height: 100vh;
        overflow: hidden;
    }
    #sim-container {
        margin: 0 auto;
        background: white;
        box-shadow: 0 0 50px rgba(0,0,0,0.5);
        transition: all 0.3s ease;
        height: 100%;
        overflow-y: auto;
        transform-origin: top center;
    }
    .device-mobile #sim-container { width: 375px; height: 667px; margin-top: 20px; border: 8px solid #334155; border-radius: 20px; }
    .device-tablet #sim-container { width: 768px; height: 1024px; margin-top: 20px; border: 8px solid #334155; border-radius: 10px; }
</style>

<div class="dev-toolbar" id="dev-toolbar">
    <div class="dev-toolbar-label">Dev Mode</div>
    
    <button class="dev-btn" onclick="setSimMode('mobile')" id="btn-mobile">
        <i data-lucide="smartphone" style="width:16px;"></i> Mobile
    </button>
    <button class="dev-btn" onclick="setSimMode('tablet')" id="btn-tablet">
        <i data-lucide="tablet" style="width:16px;"></i> Tablet
    </button>
    <button class="dev-btn active" onclick="setSimMode('desktop')" id="btn-desktop">
        <i data-lucide="monitor" style="width:16px;"></i> Full
    </button>

    <div style="width:1px; height:20px; background:var(--border-base); margin:0 4px;"></div>

    <a href="/admin/crawler" class="dev-btn" title="Open Business Crawler">
        <i data-lucide="search-code" style="width:16px;"></i> Crawler
    </a>
</div>

<script>
function setSimMode(mode) {
    const body = document.body;
    const toolbar = document.getElementById('dev-toolbar');
    
    // Remove existing sim structure if any
    if (mode === 'desktop') {
        if (body.classList.contains('is-simulating')) {
            const container = document.getElementById('sim-container');
            const content = Array.from(container.childNodes);
            content.forEach(node => body.appendChild(node));
            container.remove();
            body.classList.remove('is-simulating', 'device-mobile', 'device-tablet');
        }
    } else {
        if (!body.classList.contains('is-simulating')) {
            const container = document.createElement('div');
            container.id = 'sim-container';
            const nodes = Array.from(body.childNodes);
            nodes.forEach(node => {
                if (node !== toolbar) container.appendChild(node);
            });
            body.appendChild(container);
            body.classList.add('is-simulating');
        }
        body.classList.remove('device-mobile', 'device-tablet');
        body.classList.add('device-' + mode);
    }

    // Update buttons
    document.querySelectorAll('.dev-btn').forEach(btn => {
        if (btn.id === 'btn-' + mode) btn.classList.add('active');
        else btn.classList.remove('active');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.self !== window.top) {
        document.getElementById('dev-toolbar').style.display = 'none';
    }
});
</script>
