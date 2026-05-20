let currentResults = [];
let currentTableName = '';
let resultViewMode = 'table';

//functia principala
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
        <div class="card-header">
            <div class="card-title" style="margin-bottom: 0;">${data.length} rezultate</div>
            ${renderViewControls()}
        </div>
        ${resultContent}
    </div>
`;
}

//returneaza numele coloanelor, fara id
function getResultHeaders(data) {
    return Object.keys(data[0]).filter(header => header.toLowerCase() !== 'id');
}

//in functie de ce mod de vizualizare alegem, apleam functia coresp
function renderResultView(data, headers) {
    if (resultViewMode === 'bar') {
        return renderBarChartView(data, headers);
    }
    if (resultViewMode === 'line') {
        return renderLineChartView(data, headers);
    }
    return renderTableView(data, headers);
}


//inseram butoanele pt vizualizare+export
function renderViewControls() {
    const views = [
        ['table', 'Tabel'],
        ['bar', 'Bar Chart'],
        ['line', 'Line Chart']
    ];

    //butoanele de vizualizare
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

    //butoanele de export svf,png si webP doar daca suntem pe un chart
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


//afisare ca tabel
function renderTableView(data, headers) {
    const headersHtml = headers
        .map(header => `
        <th class="table-header">
            ${formatHeader(header)}
        </th>`)
        .join('');

    const rowsHtml = data.map((row, index) => {
        const cells = headers
            .map(header => `
            <td class="table-cell">
                ${row[header]}
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

//afisare ca bar chart
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
                height: 500, 
                toolbar: { show: false },
                fontFamily: 'inherit' 
            },
            dataLabels: {
                enabled: false //Dezactivează afișarea numerelor pe bare
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    barHeight: '70%' 
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
                        fontSize: '14px',    
                        fontWeight: 600,     
                        colors: ['#374151']  
                    },
                    maxWidth: 300,           
                    formatter: function (value) {
                        //taiem textul daca e prea lung
                        if (typeof value === 'string' && value.length > 40) {
                            return value.substring(0, 40) + '...';
                        }
                        return value;
                    }
                }
            },
            grid: {
                padding: {
                    left: 20 
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
            tooltip: {
                theme: 'light',
                x: {
                    show: true,
                    formatter: function (val) {
                        if (typeof val !== 'string') return val;

                        //punem textul pe mai multe randuri daca depaseste 35 de carac
                        return wrap(val, 35);
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


//pregatim datele pt grafic
function prepareChartData(data, headers, tableName) {
    //gasim coloana dupa care sortam (value sau benef)
    let metricHeader = 'value';
    if (tableName == 'campaigns_projects') metricHeader = 'beneficiaries_count';


    //in label uri la grafic punem in general toate coloanele diferite de year si value, dar la campaigns_projects punem doar numele, pt ca e f lung
    let labelHeaders;
    if (tableName === 'campaigns_projects') {
        labelHeaders = ['name'];
    } else {
        labelHeaders = headers.filter(h => h !== 'year' && h !== metricHeader);
    }

    //concatenam cu punct valorile coloanelor din labelHeaders si facem un dictionar
    const summary = {};
    data.forEach(item => {
        const compositeLabel = labelHeaders
            .map(h => translateOption(h, item[h]))
            .join(' · ');

        const value = Number(item[metricHeader]) || 0;
        summary[compositeLabel] = (summary[compositeLabel] || 0) + value;
    });


    //sortam obiectul crescator si punem doar primele 10 valori
    const sortedLabels = Object.keys(summary)
        .sort((a, b) => summary[b] - summary[a])
        .slice(0, 10);

    return {
        labels: sortedLabels,
        values: sortedLabels.map(l => summary[l]),
        metricName: formatHeader(metricHeader)
    };
}


//afisare ca line chart
function renderLineChartView(data, headers) {
    //luam anii distincti si ii punem intr o lista
    const years = [...new Set(data.map(item => item.year))].sort();

    let metricHeader = 'value';
    if (currentTableName === 'campaigns_projects') metricHeader = 'beneficiaries_count';

    //calculam valoarea totala pe fiecare an
    const valuesPerYear = years.map(year => {
        return data
            .filter(item => item.year === year)
            .reduce((sum, item) => sum + Number(item[metricHeader]), 0);
    });

    const chartId = 'apexLineChart'
    const tableNameRo = document.getElementById('s-table').selectedOptions[0].text;

    
    requestAnimationFrame(() => {
        const options = {
            series: [{
                name: formatHeader(metricHeader),
                data: valuesPerYear
            }],
            chart: {
                type: 'line',
                height: 450, 
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'inherit'
            },
            stroke: {
                curve: 'smooth',
                width: 4 
            },
            colors: ['#4f46e5'],
            markers: {
                size: 6, 
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
                    formatter: (val) => Math.floor(val) 
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

function wrap(text, maxChars) {
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

let currentChartInstance = null; 


window.downloadChart = function (format) {
    if (!currentChartInstance) return;

    //luam numele tradus din select ul de tabel
    const fileName = document.getElementById('s-table').selectedOptions[0].text;

    if (format === 'svg') {
        currentChartInstance.exports.exportToSVG();
    } else if (format === 'png') {
        currentChartInstance.exports.exportToPng();
    } else if (format === 'webp') {
        currentChartInstance.dataURI().then(({ imgURI }) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
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




