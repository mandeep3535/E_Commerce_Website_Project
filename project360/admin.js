// Global variables to store chart instances
let monthlySalesChartInstance = null;
let customerRevenueChartInstance = null;
let categoryChartInstance = null;

// DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts
    initializeCharts();
});

// Function to initialize all charts
function initializeCharts() {
    // Monthly Sales Chart
    initializeMonthlySalesChart();
    
    // Customer Revenue Chart
    initializeCustomerRevenueChart();
    
    // Product Categories Chart
    initializeCategoryChart();
}

// Function to initialize Monthly Sales Chart
function initializeMonthlySalesChart() {
    // Get canvas element
    const canvas = document.getElementById('monthlySalesChart');
    const ctx = canvas.getContext('2d');
    
    // Destroy previous chart instance if it exists
    if (monthlySalesChartInstance) {
        monthlySalesChartInstance.destroy();
    }
    
    // Check if monthlySales data exists
    if (!monthlySales || monthlySales.length === 0) {
        console.error("Monthly sales data is missing or empty");
        return;
    }
    
    // Extract labels and data from monthlySales
    const labels = monthlySales.map(item => {
        const [year, month] = item.month.split('-');
        return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    
    const data = monthlySales.map(item => parseFloat(item.sales));
    
    // Create chart
    monthlySalesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Sales',
                data: data,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return '$' + parseFloat(tooltipItem.value).toLocaleString();
                    }
                }
            }
        }
    });
}

// Function to initialize Customer Revenue Chart
function initializeCustomerRevenueChart() {
    // Get canvas element
    const canvas = document.getElementById('customerRevenueChart');
    const ctx = canvas.getContext('2d');
    
    // Destroy previous chart instance if it exists
    if (customerRevenueChartInstance) {
        customerRevenueChartInstance.destroy();
    }
    
    // Check if revenueByCustomer data exists
    if (!revenueByCustomer || revenueByCustomer.length === 0) {
        console.error("Customer revenue data is missing or empty");
        return;
    }
    
    // Extract data for chart
    const labels = revenueByCustomer.map(item => item.customer_name);
    const data = revenueByCustomer.map(item => parseFloat(item.total_spent));
    
    // Create chart
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return '$' + parseFloat(tooltipItem.value).toLocaleString();
                    }
                }
            }
        }
    });
}

function initializeCategoryChart() {
    // Get canvas element
    const canvas = document.getElementById('categoryChart');
    const ctx = canvas.getContext('2d');
    
    // Destroy previous chart instance if it exists
    if (categoryChartInstance) {
        categoryChartInstance.destroy();
    }
    
    // Check if categoryData exists
    if (!categoryData || categoryData.length === 0) {
        console.error("Category data is missing or empty");
        return;
    }
    
    // Extract data for chart
    const labels = categoryData.map(item => item.category);
    const data = categoryData.map(item => parseInt(item.product_count));
    
    // Calculate total for percentage computation
    const total = data.reduce((sum, value) => sum + value, 0);
    
    // Create color array
    const colors = generateColors(data.length);
    
    // Create chart
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
                    label: function(tooltipItem, data) {
                        const dataset = data.datasets[tooltipItem.datasetIndex];
                        const index = tooltipItem.index;
                        const value = dataset.data[index];
                        const label = data.labels[index];
                        const percentage = Math.round((value / data.datasets[0].data.reduce((a, b) => a + b, 0)) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
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
        },
        plugins: [ChartDataLabels]
    });
}

// Helper function to generate colors for charts
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
    
    // more colors than in our predefined array
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