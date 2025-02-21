document.getElementById("addProductForm").addEventListener("submit", function(event) {
    event.preventDefault();

    // Get form values
    let productName = document.getElementById("productName").value;
    let productID = document.getElementById("productID").value;
    let productPrice = document.getElementById("productPrice").value;
    let productStock = document.getElementById("productStock").value;
    let productColor = document.getElementById("productColor").value;
    let productCategory = document.getElementById("productCategory").value;
    let productImage = document.getElementById("productImage").files[0];

    // Validate that an image is selected
    if (!productImage) {
        alert("Please select a product image.");
        return;
    }

    // Create an object URL for the image preview
    let imageURL = URL.createObjectURL(productImage);

    // Create a new row in the table
    let tableBody = document.getElementById("productTableBody");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><img src="${imageURL}" width="50" height="50" class="rounded"></td>
        <td>${productID}</td>
        <td>${productName}</td>
        <td>${productCategory}</td>
        <td>${productStock}</td>
        <td>$${productPrice}</td>
        <td>${productColor}</td>
        <td>
            <button class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deleteProduct(this)"><i class="bi bi-trash"></i> Delete</button>
        </td>
    `;
    tableBody.appendChild(newRow);

    // Reset form fields
    document.getElementById("addProductForm").reset();
});

// Function to delete a product row
function deleteProduct(button) {
    button.parentElement.parentElement.remove();
}
