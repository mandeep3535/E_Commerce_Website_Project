document.addEventListener("DOMContentLoaded", function () {
    let orderTableBody = document.getElementById("orderTableBody");

    let orders = [
        {
            orderId: "1023",
            customer: "AB",
            product: "iPhone 15",
            quantity: 1,
            totalPrice: 999.99,
            orderDate: "Feb 20, 2025",
            deliveryAddress: "123 Main St, Kelowna",
            paymentMethod: "Credit Card",
            status: "Pending"
        },
        {
            orderId: "#ORD1024",
            customer: "CD",
            product: "MacBook Pro",
            quantity: 1,
            totalPrice: 1999.99,
            orderDate: "Feb 19, 2025",
            deliveryAddress: "456 Oak St, Kelowna",
            paymentMethod: "PayPal",
            status: "Shipped"
        },
        {
            orderId: "#ORD1025",
            customer: "DE",
            product: "Sony Headphones",
            quantity: 2,
            totalPrice: 299.99,
            orderDate: "Feb 18, 2025",
            deliveryAddress: "1725 Scranton, Kelowna",
            paymentMethod: "Cash on Delivery",
            status: "Delivered"
        }
    ];

    // Function to generate status badges
    function getStatusBadge(status) {
        let badgeClass = "";
        if (status === "Pending") badgeClass = "bg-warning";
        else if (status === "Shipped") badgeClass = "bg-primary";
        else if (status === "Delivered") badgeClass = "bg-success";
        else if (status === "Cancelled") badgeClass = "bg-danger";

        return `<span class="badge ${badgeClass}">${status}</span>`;
    }

    // Function to populate the orders table
    function displayOrders() {
        orders.forEach(order => {
            let newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${order.orderId}</td>
                <td>${order.customer}</td>
                <td>${order.product}</td>
                <td>${order.quantity}</td>
                <td>$${order.totalPrice.toFixed(2)}</td>
                <td>${order.orderDate}</td>
                <td>${order.deliveryAddress}</td>
                <td>${order.paymentMethod}</td>
                <td>${getStatusBadge(order.status)}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="updateStatus('${order.orderId}', 'Shipped')"><i class="bi bi-box"></i> Ship</button>
                    <button class="btn btn-danger btn-sm" onclick="updateStatus('${order.orderId}', 'Cancelled')"><i class="bi bi-x"></i> Cancel</button>
                </td>
            `;
            orderTableBody.appendChild(newRow);
        });
    }

    // Function to update order status
    function updateStatus(orderId, newStatus) {
        orders = orders.map(order => {
            if (order.orderId === orderId) {
                order.status = newStatus;
            }
            return order;
        });

        // To refresh the table
        orderTableBody.innerHTML = "";
        displayOrders();
    }

    //To  display orders on page load
    displayOrders();
});
