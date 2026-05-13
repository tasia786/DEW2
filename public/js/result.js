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
                <button type="button" class="btn btn-ghost btn-sm" onclick="downloadChart('svg')">SVG</button>
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


let currentChartInstance = null; // Reper pentru graficul activ

/**
 * Funcția de export actualizată pentru ApexCharts
 */
window.downloadChart = function (format) {
    if (!currentChartInstance) return;

    // Preluăm numele tradus din select-ul de tabel
    const tableSelect = document.getElementById('s-table');
    const fileName = tableSelect && tableSelect.selectedIndex > 0
        ? tableSelect.options[tableSelect.selectedIndex].text.toLowerCase().replace(/\s+/g, '_')
        : 'grafic';

    if (format === 'svg') {
        currentChartInstance.exports.exportToSVG();
    } else if (format === 'png') {
        currentChartInstance.exports.exportToPng();
    } else if (format === 'webp') {
        // ApexCharts nu are WebP nativ, folosim metoda dataURI()
        currentChartInstance.dataURI().then(({ imgURI }) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                //ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                const link = document.createElement('a');
                link.download = `vizualizare-${fileName}.webp`;
                link.href = canvas.toDataURL('image/webp');
                link.click();
            };
            img.src = imgURI;
        });
    }
};

/**
 * Randare Bar Chart Orizontal (Top 10) cu ApexCharts.
 */
function renderBarChartView(data, headers) {
    const chartData = prepareChartData(data, headers, currentTableName);
    const chartId = 'apexBarChart';

    requestAnimationFrame(() => {
        const options = {
            series: [{
                name: chartData.metricName,
                data: chartData.values
            }],
            chart: {
                type: 'bar',
                height: 500, // Am mărit puțin înălțimea pentru a lăsa textele să "respire"
                toolbar: { show: false },
                fontFamily: 'inherit' // Preia fontul paginii tale
            },
            dataLabels: {
                enabled: false // Dezactivează afișarea numerelor pe bare
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    barHeight: '70%' // Face barele puțin mai subțiri pentru a lăsa loc textului
                }
            },
            colors: ['#4f46e5'],
            xaxis: {
                categories: chartData.labels,
            },
            yaxis: {
                labels: {
                    show: true,
                    rotate: 0,
                    style: {
                        fontSize: '14px',    // Text mai mare ca în Chart.js
                        fontWeight: 600,     // Text mai clar (îngroșat)
                        colors: ['#374151']  // Culoare gri închis pentru contrast
                    },
                    maxWidth: 300,           // Permite textului să ocupe mai mult spațiu în stânga
                    formatter: function (value) {
                        // Logica de tăiere exact ca în codul tău vechi de Chart.js
                        if (typeof value === 'string' && value.length > 40) {
                            return value.substring(0, 40) + '...';
                        }
                        return value;
                    }
                }
            },
            grid: {
                padding: {
                    left: 20 // Adaugă spațiu suplimentar în marginea din stânga
                }
            },
            title: {
                text: 'Top 10 Rezultate',
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold'
                }
            },
            // În interiorul obiectului options din renderBarChartView
            // În interiorul options din renderBarChartView
            // În interiorul obiectului options din renderBarChartView
            tooltip: {
                theme: 'light',
                x: {
                    show: true,
                    formatter: function (val) {
                        if (typeof val !== 'string') return val;

                        // Aplicăm doar smartWrap pentru a pune textul pe mai multe rânduri 
                        // dacă depășește un număr de caractere (ex: 35)
                        return smartWrap(val, 35);
                    }
                },
                y: {
                    formatter: function (val) {
                        return val;
                    }
                }
            }
        };

        if (currentChartInstance) currentChartInstance.destroy();
        currentChartInstance = new ApexCharts(document.querySelector(`#${chartId}`), options);
        currentChartInstance.render();
    });

    return `<div id="${chartId}" style="padding: 10px; background: white;"></div>`;
}
/**
 * Randare Line Chart cu ApexCharts
 */
/**
 * Randare Line Chart (Evoluție anuală) cu ApexCharts.
 */
function renderLineChartView(data, headers) {
    const years = [...new Set(data.map(item => item.year))].sort();
    const metricHeader = getMetricHeader(headers);
    
    const valuesPerYear = years.map(year => {
        return data
            .filter(item => item.year === year)
            .reduce((sum, item) => sum + (Number(item[metricHeader]) || 0), 0);
    });

    const chartId = 'apexLineChart';
    
    // Preluăm numele tradus al tabelei pentru titlu
    const tableSelect = document.getElementById('s-table');
    const tableNameRo = tableSelect && tableSelect.selectedIndex > 0 
        ? tableSelect.options[tableSelect.selectedIndex].text 
        : 'Date';

    requestAnimationFrame(() => {
        const options = {
            series: [{
                name: formatHeader(metricHeader),
                data: valuesPerYear
            }],
            chart: {
                type: 'line',
                height: 450, // Mărit pentru vizibilitate
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'inherit'
            },
            stroke: {
                curve: 'smooth',
                width: 4 // Linie mai groasă și proeminentă
            },
            colors: ['#4f46e5'],
            markers: {
                size: 6, // Puncte mai mari pe linie
                hover: { size: 8 }
            },
            title: {
                text: `Evoluție Anuală - ${tableNameRo}`,
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold',
                    color: '#111827'
                }
            },
            xaxis: {
                categories: years,
                labels: {
                    style: {
                        fontSize: '14px',
                        fontWeight: 600,
                        colors: '#374151'
                    }
                },
                title: {
                    text: 'Anul',
                    style: {
                        fontSize: '14px',
                        fontWeight: 700,
                        color: '#4b5563'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '14px',
                        fontWeight: 600,
                        colors: '#374151'
                    },
                    formatter: (val) => Math.floor(val) // Evităm zecimalele dacă nu e cazul
                },
                title: {
                    text: formatHeader(metricHeader),
                    offsetX: -11,
                    style: {
                        fontSize: '14px',
                        fontWeight: 700,
                        color: '#4b5563'
                    }
                }
            },
            tooltip: {
                theme: 'light',
                x: { show: true },
                style: {
                    fontSize: '14px'
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: {
                    left: 20,
                    right: 20
                }
            }
        };

        if (currentChartInstance) currentChartInstance.destroy();
        currentChartInstance = new ApexCharts(document.querySelector(`#${chartId}`), options);
        currentChartInstance.render();
    });

    return `<div id="${chartId}" style="padding: 10px; background: white;"></div>`;
}

function smartWrap(text, maxChars = 30) {
    if (!text || text.length <= maxChars) return text;

    const words = text.split(' ');
    let lines = [];
    let currentLine = '';

    words.forEach(word => {
        if ((currentLine + word).length > maxChars) {
            lines.push(currentLine.trim());
            currentLine = word + ' ';
        } else {
            currentLine += word + ' ';
        }
    });
    lines.push(currentLine.trim());
    return lines.join('<br>');
}