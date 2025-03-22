/*1) REGISTER PLUGINS & SET GLOBALS ON DOM LOADED*/
document.addEventListener('DOMContentLoaded', function() {
    // Register the data labels plugin globally (required in Chart.js 2.x)
    Chart.plugins.register(ChartDataLabels);

    // Make copies of your original data so we can reset after filtering
    originalMonthlySales = Array.isArray(monthlySales) ? [...monthlySales] : [];
    originalRevenueByCustomer = Array.isArray(revenueByCustomer) ? [...revenueByCustomer] : [];
    originalCategoryData = Array.isArray(categoryData) ? [...categoryData] : [];

    // Initialize charts
    initializeCharts();

    // Initialize all filter controls
    initializeFilters();
});

/**
 * 2) GLOBAL VARIABLES (chart instances + data copies)
 */
let monthlySalesChartInstance = null;
let customerRevenueChartInstance = null;
let categoryChartInstance = null;

// These originals are used for resetting filters:
let originalMonthlySales = [];
let originalRevenueByCustomer = [];
let originalCategoryData = [];

/*3) INITIALIZE ALL CHARTS*/
function initializeCharts() {
    initializeMonthlySalesChart();
    initializeCustomerRevenueChart();
    initializeCategoryChart();
}

/*4) INITIALIZE ALL FILTERS*/
function initializeFilters() {
    addMonthlyChartFilters();
    addCustomerRevenueFilters();
    addCategoryFilters();
}

/** 5) MONTHLY SALES FILTERS*/
function addMonthlyChartFilters() {
    const chartCard = document.querySelector('#monthlySalesChart').closest('.card');
    const cardHeader = chartCard.querySelector('.card-header');
    
    const filterDiv = document.createElement('div');
    filterDiv.className = 'mt-2 row g-2 align-items-center';
    filterDiv.innerHTML = `
        <label for="monthlyDateRange" class="me-2">Date Range:</label>
        <select id="monthlyDateRange" class="form-select form-select-sm me-2" style="width: auto;">
            <option value="all">All Time</option>
            <option value="week">This Week</option>
            <option value="month">Monthly</option>
            <option value="quarter">Quarterly</option>
            <option value="year">Yearly</option>
        </select>

        <button id="resetMonthlySalesFilter" class="btn btn-sm btn-outline-secondary">Reset</button>
    `;
    cardHeader.appendChild(filterDiv);
    
    document.getElementById('monthlyDateRange').addEventListener('change', function() {
        filterMonthlySalesChart(this.value);
    });
    
    document.getElementById('resetMonthlySalesFilter').addEventListener('click', function() {
        document.getElementById('monthlyDateRange').value = 'all';
        filterMonthlySalesChart('all');
    });
}

function filterMonthlySalesChart(rangeValue) {
        // Make a copy of the original data
        let filteredData = [...originalMonthlySales];
    
        // We'll compare each data point's date to `startDate`
        let now = new Date();
        let startDate = null;
    
        switch (rangeValue) {
            case 'week':
                // Last 7 days
                startDate = new Date(now);
                startDate.setDate(startDate.getDate() - 7);
                break;
            case 'month':
                // Last 1 month
                startDate = new Date(now);
                startDate.setMonth(startDate.getMonth() - 1);
                break;
            case 'quarter':
                // Last 3 months
                startDate = new Date(now);
                startDate.setMonth(startDate.getMonth() - 3);
                break;
            case 'year':
                // Last 12 months
                startDate = new Date(now);
                startDate.setFullYear(startDate.getFullYear() - 1);
                break;
            case 'all':
            default:
                // Show entire dataset
                startDate = null;
                break;
        }
    
        // If we have a startDate, filter out older entries
        if (startDate) {
            filteredData = filteredData.filter(item => {
                // Parse item.month (format "YYYY-MM") into a JS Date
                const [year, month] = item.month.split('-');
                // We'll assume day=1 since it's monthly data
                const itemDate = new Date(year, month - 1, 1);
                
                // Keep only data points within the date range
                return itemDate >= startDate;
            });
        }
    
        // Update global monthlySales & reinitialize chart
        monthlySales = filteredData;
        initializeMonthlySalesChart();
    }
/* 6) CUSTOMER REVENUE FILTERS */
function addCustomerRevenueFilters() {
    const chartCard = document.querySelector('#customerRevenueChart').closest('.card');
    const cardHeader = chartCard.querySelector('.card-header');
    
    const filterDiv = document.createElement('div');
    filterDiv.className = 'mt-2 row g-2 align-items-center';
    filterDiv.innerHTML = `
        <label for="customerCount" class="me-2">Top:</label>
        <select id="customerCount" class="form-select form-select-sm me-2" style="width: auto;">
            <option value="5">5 Customers</option>
            <option value="10" selected>10 Customers</option>
            <option value="15">15 Customers</option>
            <option value="all">All Customers</option>
        </select>
        <input type="number" id="minRevenue" class="form-control form-control-sm me-2" style="width: auto;" placeholder="Min $">
        <button id="resetCustomerFilter" class="btn btn-sm btn-outline-secondary">Reset</button>
    `;
    
    cardHeader.appendChild(filterDiv);
    
    document.getElementById('customerCount').addEventListener('change', applyCustomerRevenueFilter);
    document.getElementById('minRevenue').addEventListener('input', applyCustomerRevenueFilter);
    document.getElementById('resetCustomerFilter').addEventListener('click', function() {
        document.getElementById('customerCount').value = '10';
        document.getElementById('minRevenue').value = '';
        applyCustomerRevenueFilter();
    });
}

function applyCustomerRevenueFilter() {
    const customerCount = document.getElementById('customerCount').value;
    const minRevenue = document.getElementById('minRevenue').value;
    
    let filteredData = [...originalRevenueByCustomer];
    
    // Filter by minimum revenue
    if (minRevenue && !isNaN(minRevenue) && minRevenue > 0) {
        filteredData = filteredData.filter(item => parseFloat(item.total_spent) >= parseFloat(minRevenue));
    }
    
    // Slice top X customers
    if (customerCount !== 'all') {
        filteredData = filteredData.slice(0, parseInt(customerCount));
    }
    
    // Update global data, re-init chart
    revenueByCustomer = filteredData;
    initializeCustomerRevenueChart();
}

/*7) CATEGORY FILTERS*/
function addCategoryFilters() {
    const chartCard = document.querySelector('#categoryChart').closest('.card');
    const cardHeader = chartCard.querySelector('.card-header');
    
    const filterDiv = document.createElement('div');
    filterDiv.className = 'mt-2 row g-2 align-items-center';
    
    // Generate a unique list of categories
    const categories = [...new Set(originalCategoryData.map(item => item.category))];
    let categoryOptions = '<option value="all">All Categories</option>';
    categories.forEach(category => {
        categoryOptions += `<option value="${category}">${category}</option>`;
    });
    
    filterDiv.innerHTML = `
        <label for="categoryFilter" class="me-2">Category:</label>
        <select id="categoryFilter" class="form-select form-select-sm me-2" style="width: auto;">
            ${categoryOptions}
        </select>
        <div class="me-3">
            <input type="number" id="minProducts" class="form-control form-control-sm" style="width: auto;" placeholder="Min Products">
        </div>
        <button id="resetCategoryFilter" class="btn btn-sm btn-outline-secondary">Reset</button>
    `;
    cardHeader.appendChild(filterDiv);
    
    document.getElementById('categoryFilter').addEventListener('change', applyCategoryFilter);
    document.getElementById('minProducts').addEventListener('input', applyCategoryFilter);
    document.getElementById('resetCategoryFilter').addEventListener('click', function() {
        document.getElementById('categoryFilter').value = 'all';
        document.getElementById('minProducts').value = '';
        applyCategoryFilter();
    });
}

function applyCategoryFilter() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const minProducts = document.getElementById('minProducts').value;
    
    let filteredData = [...originalCategoryData];
    
    // Filter by category
    if (categoryFilter !== 'all') {
        filteredData = filteredData.filter(item => item.category === categoryFilter);
    }
    
    // Filter by minimum product count
    if (minProducts && !isNaN(minProducts) && minProducts > 0) {
        filteredData = filteredData.filter(item => parseInt(item.product_count) >= parseInt(minProducts));
    }
    
    // Update global data, re-init chart
    categoryData = filteredData;
    initializeCategoryChart();
}

/* 8) INITIALIZE MONTHLY SALES CHART (LINE)*/
function initializeMonthlySalesChart() {
    const canvas = document.getElementById('monthlySalesChart');
    const ctx = canvas.getContext('2d');
    
    if (monthlySalesChartInstance) {
        monthlySalesChartInstance.destroy();
    }
    
    if (!monthlySales || monthlySales.length === 0) {
        console.error("Monthly sales data is missing or empty");
        return;
    }
    
    const labels = monthlySales.map(item => {
        const [year, month] = item.month.split('-');
        return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    
    const data = monthlySales.map(item => parseFloat(item.sales));
    
    monthlySalesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Sales',
                data: data,
                borderWidth: 2,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                fill: true,
                lineTension: 0.4 // Chart.js 2.x
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const val = tooltipItem.yLabel || 0;
                        return '$' + parseFloat(val).toLocaleString();
                    }
                }
            }
        }
    });
}

/* 9) INITIALIZE CUSTOMER REVENUE CHART (BAR)*/
function initializeCustomerRevenueChart() {
    const canvas = document.getElementById('customerRevenueChart');
    const ctx = canvas.getContext('2d');
    
    if (customerRevenueChartInstance) {
        customerRevenueChartInstance.destroy();
    }
    
    if (!revenueByCustomer || revenueByCustomer.length === 0) {
        console.error("Customer revenue data is missing or empty");
        return;
    }
    
    const labels = revenueByCustomer.map(item => item.customer_name);
    const data = revenueByCustomer.map(item => parseFloat(item.total_spent));
    
    customerRevenueChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: data,
                backgroundColor: 'rgba(255, 159, 64, 0.7)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const val = tooltipItem.yLabel || 0;
                        return '$' + parseFloat(val).toLocaleString();
                    }
                }
            }
        }
    });
}

/*10) INITIALIZE CATEGORY CHART (PIE)*/
function initializeCategoryChart() {
    const canvas = document.getElementById('categoryChart');
    const ctx = canvas.getContext('2d');
    
    if (categoryChartInstance) {
        categoryChartInstance.destroy();
    }
    
    if (!categoryData || categoryData.length === 0) {
        console.error("Category data is missing or empty");
        return;
    }
    
    const labels = categoryData.map(item => item.category);
    const data = categoryData.map(item => parseInt(item.product_count));
    const total = data.reduce((sum, val) => sum + val, 0);

    const colors = generateColors(data.length);
    
    categoryChartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.background,
                borderColor: colors.border,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right'
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, chartData) {
                        const i = tooltipItem.index;
                        const val = chartData.datasets[0].data[i];
                        const lbl = chartData.labels[i];
                        const percentage = Math.round((val / total) * 100);
                        return `${lbl}: ${val} (${percentage}%)`;
                    }
                }
            },
            plugins: {
                datalabels: {
                    formatter: function(value, ctx) {
                        const percentage = Math.round((value / total) * 100);
                        return percentage + '%';
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            }
        }
    });
}

/* 11) COLOR HELPER */
function generateColors(count) {
    const backgroundColors = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(40, 159, 64, 0.7)',
        'rgba(210, 99, 132, 0.7)'
    ];
    const borderColors = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(199, 199, 199, 1)',
        'rgba(83, 102, 255, 1)',
        'rgba(40, 159, 64, 1)',
        'rgba(210, 99, 132, 1)'
    ];
    
    if (count > backgroundColors.length) {
        for (let i = backgroundColors.length; i < count; i++) {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            backgroundColors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
            borderColors.push(`rgba(${r}, ${g}, ${b}, 1)`);
        }
    }
    
    return {
        background: backgroundColors.slice(0, count),
        border: borderColors.slice(0, count)
    };
}
