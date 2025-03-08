// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ DOM fully loaded!");
    
    // Get the product form
    const addProductForm = document.getElementById("addProductForm");
    
    // Handle image file validation
    const productImagesField = document.getElementById("productImages");
    const fileErrorElement = document.getElementById("fileError");
    
    // Set up image file validation
    if (productImagesField && fileErrorElement) {
        productImagesField.addEventListener("change", function() {
            if (this.files.length > 3) {
                fileErrorElement.style.display = "block";
                this.value = ""; // Clear the selection
            } else {
                fileErrorElement.style.display = "none";
            }
        });
    }
    
    // Set up form submission handler
    if (addProductForm) {
        addProductForm.addEventListener("submit", function(event) {
            console.log("📤 Form submission attempted");
            
            // Get all required form fields
            const requiredFields = {
                "productID": document.getElementById("productID"),
                "productName": document.getElementById("productName"),
                "productPrice": document.getElementById("productPrice"),
                "productStock": document.getElementById("productStock"),
                "productCategory": document.getElementById("productCategory"),
                "productDescription": document.getElementById("productDescription"),
                "productImages": document.getElementById("productImages")
            };
            
            // Check if any required field is missing from the DOM
            let missingFields = [];
            for (const [name, element] of Object.entries(requiredFields)) {
                if (!element) {
                    missingFields.push(name);
                }
            }
            
            if (missingFields.length > 0) {
                event.preventDefault();
                console.error("❌ Missing form fields: " + missingFields.join(", "));
                alert("Form error: Some required fields are missing from the page. Please contact the administrator.");
                return;
            }
            
            // Validate field values
            let emptyFields = [];
            for (const [name, element] of Object.entries(requiredFields)) {
                // Skip file input validation differently
                if (name === "productImages") {
                    if (element.files.length === 0) {
                        emptyFields.push("Product Images");
                    }
                    continue;
                }
                
                if (!element.value.trim()) {
                    emptyFields.push(name.replace("product", ""));
                }
            }
            
            if (emptyFields.length > 0) {
                event.preventDefault();
                alert("⚠️ Please fill in the following required fields: " + 
                      emptyFields.join(", ").replace(/([A-Z])/g, ' $1'));
                return;
            }
            
            // Validate product ID is numeric
            const productID = requiredFields.productID.value.trim();
            if (isNaN(parseInt(productID))) {
                event.preventDefault();
                alert("⚠️ Product ID must be a number!");
                return;
            }
            
            // Validate price is numeric and positive
            const productPrice = requiredFields.productPrice.value.trim();
            if (isNaN(parseFloat(productPrice)) || parseFloat(productPrice) <= 0) {
                event.preventDefault();
                alert("⚠️ Price must be a positive number!");
                return;
            }
            
            // Validate stock is numeric and non-negative
            const productStock = requiredFields.productStock.value.trim();
            if (isNaN(parseInt(productStock)) || parseInt(productStock) < 0) {
                event.preventDefault();
                alert("⚠️ Stock must be a non-negative number!");
                return;
            }
            
            console.log("✅ Form validation passed, submitting...");
        });
    } else {
        console.error("❌ Product form not found!");
    }
    
    // Load existing products (you'll need to implement this part)
    loadProducts();
});

// Function to load existing products
function loadProducts() {
    console.log("Attempting to load products...");
    
    // Get the table body element
    const productTableBody = document.getElementById("productTableBody");
    
    if (!productTableBody) {
        console.error("❌ Product table body not found!");
        return;
    }
    
    // Fetch products from the server
    fetch("load_products.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(products => {
            // Clear existing table rows
            productTableBody.innerHTML = "";
            
            if (products.length === 0) {
                // Show "no products" message
                const row = document.createElement("tr");
                row.innerHTML = `<td colspan="8" class="text-center">No products found</td>`;
                productTableBody.appendChild(row);
                return;
            }
            
            // Add each product to the table
            products.forEach(product => {
                const row = document.createElement("tr");
                
                // Get the first image URL or use a placeholder
                let imageUrl = "images/placeholder.jpg";
                if (product.images && product.images.length > 0) {
                    imageUrl = `uploads/products/${product.images[0]}`;
                }
                
                row.innerHTML = `
                    <td><img src="${imageUrl}" width="50" height="50" class="img-thumbnail" alt="${product.name}"></td>
                    <td>${product.product_id}</td>
                    <td>${product.name}</td>
                    <td>${product.category || '-'}</td>
                    <td>${product.stock}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-product" data-id="${product.product_id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-product" data-id="${product.product_id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                
                productTableBody.appendChild(row);
            });
            
            // Set up event listeners for edit and delete buttons
            setupProductActions();
        })
        .catch(error => {
            console.error("Error loading products:", error);
            productTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error loading products</td></tr>`;
        });
}

// Function to set up edit and delete buttons
function setupProductActions() {
    // Set up delete buttons
    const deleteButtons = document.querySelectorAll(".delete-product");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const productId = this.getAttribute("data-id");
            if (confirm("Are you sure you want to delete this product?")) {
                deleteProduct(productId);
            }
        });
    });
    
    // Set up edit buttons (you'll need to implement this)
    const editButtons = document.querySelectorAll(".edit-product");
    editButtons.forEach(button => {
        button.addEventListener("click", function() {
            const productId = this.getAttribute("data-id");
            // Redirect to edit page or open modal
            alert("Edit functionality not implemented yet");
        });
    });
}

// Function to delete a product
function deleteProduct(productId) {
    console.log(`Attempting to delete product ID: ${productId}`);
    
    // Create form data
    const formData = new FormData();
    formData.append("product_id", productId);
    
    // Send delete request
    fetch("delete_product.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Reload products
            loadProducts();
        } else {
            alert(`Error: ${data.message}`);
        }
    })
    .catch(error => {
        console.error("Error deleting product:", error);
        alert("An error occurred while deleting the product");
    });
}