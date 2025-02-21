document.addEventListener("DOMContentLoaded", function () {
    fetchDashboardData();
});

function fetchDashboardData() {
    fetch("fetch_dashboard_data.php")
        .then(response => response.json())
        .then(data => {
            document.getElementById("activeListings").textContent = data.activeListings;
            document.getElementById("outOfStock").textContent = data.outOfStock;
            document.getElementById("totalOrders").textContent = data.totalOrders;
            document.getElementById("totalRevenue").textContent = `$${data.totalRevenue.toFixed(2)}`;

            let orderTableBody = document.getElementById("orderTableBody");
            orderTableBody.innerHTML = ""; // Clear existing rows

            data.orders.forEach(order => {
                let row = `
                    <tr>
                        <td>${order.orderId}</td>
                        <td>${order.customerId}</td>
                        <td>${order.customer}</td>
                        <td><span class="badge bg-${order.statusColor}">${order.status}</span></td>
                        <td>$${order.total.toFixed(2)}</td>
                        <td>${order.date}</td>
                    </tr>
                `;
                orderTableBody.innerHTML += row;
            });
        })
        .catch(error => console.error("Error fetching dashboard data:", error));
}
