let currentResults = [];
let currentTableName = '';
let resultViewMode = 'table';

/**
 * Punctul principal de intrare pentru afișarea rezultatelor.
 */
function renderResults(data, tableName) {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;

    currentResults = Array.isArray(data) ? data : [];
    currentTableName = tableName;

    if (!Array.isArray(data) || data.length === 0) {
        resultsContainer.innerHTML = `<div class="empty-state">Nu s-au găsit rezultate.</div>`;
        return;
    }

    const headers = getResultHeaders(data);
    const resultContent = renderResultView(data, headers);

    resultsContainer.innerHTML = `
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); margin-bottom: var(--space-6); flex-wrap: wrap;">
            <div class="card-title" style="margin-bottom: 0;">${data.length} rezultate</div>
            ${renderViewControls()}
        </div>
        ${resultContent}
    </div>
`;
}

/**
 * Returnează numele coloanelor, excluzând 'id'.
 */
function getResultHeaders(data) {
    return Object.keys(data[0]).filter(header => header.toLowerCase() !== 'id');
}

/**
 * Generează butoanele de comutare între modurile de vizualizare.
 */
function renderViewControls() {
    const views = [
        ['table', 'Tabel'],
        ['bar', 'Bar Chart'],
        ['line', 'Line Chart']
    ];

    let html = `
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <div style="display: flex; gap: var(--space-2);">
                ${views.map(([value, label]) => `
                    <button
                        type="button"
                        class="btn ${resultViewMode === value ? 'btn-primary' : 'btn-ghost'} btn-sm"
                        data-result-view="${value}"
                    >${label}</button>
                `).join('')}
            </div>`;

    // AFIȘĂM EXPORTUL DOAR DACĂ NU SUNTEM PE TABEL
    if (resultViewMode === 'bar' || resultViewMode === 'line') {
        html += `
            <div style="height: 20px; width: 1px; background: var(--color-border);"></div>
            <div style="display: flex; gap: var(--space-2); align-items: center;">
                <span style="font-size: 15px; color: var(--color-text-secondary);">Export:</span>
                <button type="button" class="btn btn-ghost btn-sm" onclick="downloadChart('png')">PNG</button>
                <button type="button" class="btn btn-ghost btn-sm" onclick="downloadChart('webp')">WebP</button>
            </div>`;
    }

    html += `</div>`;
    return html;
}

/**
 * Decide ce funcție de randare să apeleze în funcție de modul selectat.
 */
function renderResultView(data, headers) {
    if (resultViewMode === 'bar') {
        return renderBarChartView(data, headers);
    }
    if (resultViewMode === 'line') {
        return renderLineChartView(data, headers);
    }
    return renderTableView(data, headers);
}

/**
 * Randarea sub formă de tabel HTML.
 */
function renderTableView(data, headers) {
    const headersHtml = headers
        .map(header => `
        <th style="padding: var(--space-3) var(--space-4); text-align: left; font-size: var(--font-size-md); color: var(--color-text-secondary); font-weight: 600; white-space: nowrap; width: ${100 / headers.length}%;">
            ${formatHeader(header)}
        </th>`)
        .join('');

    const rowsHtml = data.map((item, index) => {
        const cells = headers
            .map(header => `
            <td style="padding: var(--space-3) var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-primary); border-bottom: 1px solid var(--color-border);">
                ${item[header]}
            </td>`)
            .join('');
        return `<tr style="background: ${index % 2 === 0 ? 'white' : 'var(--color-bg-hover)'};">${cells}</tr>`;
    }).join('');

    return `
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr style="background: var(--color-bg-hover); border-bottom: 2px solid var(--color-border);">
                        ${headersHtml}
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
            </table>
        </div>
    `;
}

/**
 * Identifică coloana care conține date numerice (value, beneficiaries_count etc.).
 */
function getMetricHeader(headers) {
    if (headers.includes('value')) return 'value';
    if (headers.includes('beneficiaries_count')) return 'beneficiaries_count';

    return headers.find(header => header !== 'year' && currentResults.some(item => Number.isFinite(Number(item[header]))));
}

/**
 * Pregătește datele pentru Bar Chart (agregare Top 10).
 */
function prepareChartData(data, headers, tableName) {
    const metricHeader = getMetricHeader(headers);
    let labelHeaders;

    // Logica specifică pentru campanii (doar numele) vs restul tabelelor (compus)
    if (tableName === 'campaigns_projects') {
        labelHeaders = ['name'];
    } else {
        labelHeaders = headers.filter(h => h !== 'year' && h !== metricHeader);
    }

    const summary = {};
    data.forEach(item => {
        const compositeLabel = labelHeaders
            .map(h => translateOption(h, item[h]))
            .filter(Boolean)
            .join(' · ');

        const value = Number(item[metricHeader]) || 0;
        summary[compositeLabel] = (summary[compositeLabel] || 0) + value;
    });

    const sortedLabels = Object.keys(summary)
        .sort((a, b) => summary[b] - summary[a])
        .slice(0, 10);

    return {
        labels: sortedLabels,
        values: sortedLabels.map(l => summary[l]),
        metricName: formatHeader(metricHeader)
    };
}

/**
 * Randare Bar Chart Orizontal (Top 10).
 */
function renderBarChartView(data, headers) {
    const chartData = prepareChartData(data, headers, currentTableName);
    const canvasId = 'barCanvas-' + Math.floor(Math.random() * 1000);

    requestAnimationFrame(() => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: chartData.metricName,
                    data: chartData.values,
                    backgroundColor: '#4f46e5',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                const label = this.getLabelForValue(value);
                                return label.length > 40 ? label.substring(0, 40) + '...' : label;
                            }
                        }
                    }
                }
            }
        });
    });

    return `<div style="height: 450px; padding: 20px;"><canvas id="${canvasId}"></canvas></div>`;
}

/**
 * Randare Line Chart (Evoluție anuală).
 */
function renderLineChartView(data, headers) {
    const years = [...new Set(data.map(item => item.year))].sort();
    const metricHeader = getMetricHeader(headers);

    const valuesPerYear = years.map(year => {
        return data
            .filter(item => item.year === year)
            .reduce((sum, item) => sum + (Number(item[metricHeader]) || 0), 0);
    });

    const canvasId = 'lineCanvas-' + Math.floor(Math.random() * 1000);

    requestAnimationFrame(() => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'Evolution ' + formatHeader(metricHeader),
                    data: valuesPerYear,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });

    return `<div style="height: 400px; padding: 20px;"><canvas id="${canvasId}"></canvas></div>`;
}

window.downloadChart = function (format) {
    const canvas = document.querySelector('canvas');
    if (!canvas) return;

    // 1. Preluăm numele tradus direct din elementul SELECT existent în pagină
    const tableSelect = document.getElementById('s-table');
    let romanianName = 'grafic';
    
    if (tableSelect && tableSelect.selectedIndex > 0) {
        // Luăm textul opțiunii selectate (ex: "Capturi", "Urgențe")
        romanianName = tableSelect.options[tableSelect.selectedIndex].text;
        
        // Opțional: curățăm numele pentru fișier (fără spații, litere mici)
        romanianName = romanianName.toLowerCase().replace(/\s+/g, '_');
    }



    // 3. Logica de export (PNG / WebP)
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const ctx = tempCanvas.getContext('2d');
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
    ctx.drawImage(canvas, 0, 0);

    const link = document.createElement('a');
    link.download = `vizualizare-${romanianName}.${format}`; // Folosim numele tradus
    link.href = tempCanvas.toDataURL(`image/${format}`);
    link.click();
};