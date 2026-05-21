<?php
use backend\assets\AdminAsset;
use yii\helpers\Url;

AdminAsset::register($this);

$baseUrl = Yii::$app->HomeUrl;
$searchQuery = Yii::$app->request->get('search', '');
?>

<div class="search-results-page">
    <div class="search-header-premium">
        <div class="container">
            <h1 class="search-title">Search Results</h1>
            <p class="search-subtitle">Found matches for "<span class="highlight"><?= htmlspecialchars($searchQuery) ?></span>" across all modules</p>
        </div>
    </div>

    <div class="container mt-4">
        <div id="table-container" class="modern-search-container">
            <div class="loading-state">
                <div class="premium-spinner"></div>
                <p>Gathering results from all modules...</p>
            </div>
        </div>
    </div>
</div>

<style>
.search-results-page {
    background: #f8fafc;
    min-height: 100vh;
    padding-bottom: 50px;
}

.search-header-premium {
    background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
    padding: 60px 0;
    color: white;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.2);
}

.search-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
}

.search-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.highlight {
    font-weight: 700;
    text-decoration: underline;
}

.modern-search-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.module-section {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f5f9;
}

.module-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.module-badge {
    background: #e0e7ff;
    color: #4338ca;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 600;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.result-card-premium {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
    display: block;
    text-decoration: none !important;
    color: inherit !important;
}

.result-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
    border-color: #6366f1;
}

.result-main-info {
    margin-bottom: 15px;
}

.result-primary-text {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
    display: block;
}

.result-secondary-text {
    font-size: 0.85rem;
    color: #64748b;
    display: block;
}

.result-meta-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.meta-tag {
    font-size: 0.75rem;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    color: #475569;
}

.loading-state {
    text-align: center;
    padding: 100px 0;
}

.premium-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e0e7ff;
    border-top: 4px solid #6366f1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSearchResults();
});

function loadSearchResults() {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search') || '';
    const selectedmodule = urlParams.get('selectedmodule') || 'all';
    const tabid = urlParams.get('tabid') || 'all';

    const ajaxUrl = '<?= Url::to(['searchinallmodule']) ?>' +
        '?search=' + encodeURIComponent(search) +
        '&selectedmodule=' + encodeURIComponent(selectedmodule) +
        '&tabid=' + encodeURIComponent(tabid);

    $.ajax({
        url: ajaxUrl,
        type: "GET",
        dataType: "json",
        success: function(data) {
            renderModernResults(data);
        },
        error: function() {
            document.getElementById('table-container').innerHTML = '<div class="alert alert-danger">Error loading results. Please try again.</div>';
        }
    });
}

function renderModernResults(data) {
    const container = document.getElementById('table-container');
    container.innerHTML = '';

    const results = data.search === 'single' ? [data.result] : data.result;

    if (!results || results.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No results found matching your search.</div>';
        return;
    }

    results.forEach(module => {
        if (module.RecordList.length > 0) {
            const section = document.createElement('div');
            section.className = 'module-section';
            
            let cardsHtml = '';
            module.RecordList.forEach(record => {
                const keys = Object.keys(module.Column);
                const primaryText = record[keys[0]] || 'Untitled';
                const secondaryText = record[keys[1]] || '';
                
                let tagsHtml = '';
                for(let i=2; i<Math.min(5, keys.length); i++) {
                    if(record[keys[i]]) {
                        tagsHtml += `<span class="meta-tag">${module.Column[keys[i]]}: ${record[keys[i]]}</span>`;
                    }
                }

                cardsHtml += `
                    <a href="<?= Url::to(['detail']) ?>?Record=${record.RecordId}" class="result-card-premium">
                        <div class="result-main-info">
                            <span class="result-primary-text">${primaryText}</span>
                            <span class="result-secondary-text">${secondaryText}</span>
                        </div>
                        <div class="result-meta-tags">
                            ${tagsHtml}
                        </div>
                    </a>
                `;
            });

            section.innerHTML = `
                <div class="module-header">
                    <div class="module-name">${module.modulename}</div>
                    <div class="module-badge">${module.totalitemcount.totrecords} Results</div>
                </div>
                <div class="results-grid">
                    ${cardsHtml}
                </div>
            `;
            container.appendChild(section);
        }
    });

    if (container.innerHTML === '') {
        container.innerHTML = '<div class="alert alert-info">No results found matching your search.</div>';
    }
}
</script>