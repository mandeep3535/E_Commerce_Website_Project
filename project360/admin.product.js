document.addEventListener('DOMContentLoaded', function() {
    // Load products as soon as page loads
    loadProducts();
    
    // File validation
    const productImagesInput = document.getElementById('productImages');
    if (productImagesInput) {
        productImagesInput.addEventListener('change', function() {
            const fileError = document.getElementById('fileError');
            if (this.files.length > 3) {
                fileError.style.display = 'block';
                this.value = ''; // Clear the selection
            } else {
                fileError.style.display = 'none';
            }
        });
    }

    // Track existing product IDs for validation
    let existingProductIDs = [];

    // Function to update existing product IDs list
    function updateExistingProductIDs() {
        existingProductIDs = [];
        const productRows = document.querySelectorAll('#productTableBody tr');
        productRows.forEach(row => {
            const productId = row.querySelector('td:nth-child(2)')?.textContent;
            if (productId) {
                existingProductIDs.push(productId.trim());
            }
        });
        console.log("Existing product IDs:", existingProductIDs);
    }

    // Form submission
    const addProductForm = document.getElementById('addProductForm');
    
    if (addProductForm) {
        // Perform validation for duplicate product IDs
        const productIDInput = document.getElementById('productID');
        if (productIDInput) {
            productIDInput.addEventListener('change', function() {
                const inputValue = this.value.trim();
                // Check if this ID already exists and we are not in edit mode
                if (existingProductIDs.includes(inputValue) && addProductForm.dataset.mode !== 'edit') {
                    alertError("Warning: Product ID already exists. Please use a unique ID.");
                    // Add visual indication
                    this.classList.add('border-danger');
                    // Don't clear the value to allow user to modify it
                } else {
                    this.classList.remove('border-danger');
                }
            });
        }

        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEditMode = this.dataset.mode === 'edit';
            const productID = document.getElementById('productID').value.trim();
            
            //  validation for duplicate product ID
            if (!isEditMode && existingProductIDs.includes(productID)) {
                alertError("Cannot add product: Product ID already exists. Please use a unique ID.");
                document.getElementById('productID').focus();
                return; // Prevent form submission
            }
            
            // Add edit flag if in edit mode
            if (isEditMode) {
                formData.append('is_edit', '1');
                formData.append('edit_id', this.dataset.editId);
            }
            
            // Check if files are being sent
            for (let pair of formData.entries()) {
                console.log(pair[0], pair[1]);
            }
            
            fetch(isEditMode ? 'update_product.php' : 'process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Special handling for 500 errors which is due to duplicate IDs
                if (response.status === 500) {
                    // For potential duplicate product ID errors
                    if (!isEditMode) {
                        throw new Error('DUPLICATE_PRODUCT_ID');
                    } else {
                        throw new Error(`Server error (500): The operation could not be completed`);
                    }
                }
                
                // Check if response is OK before trying to parse JSON
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                
                // Check if response is empty
                if (response.headers.get('content-length') === '0') {
                    throw new Error('Server returned an empty response');
                }
                
                // Try to parse as JSON with better error handling
                return response.text().then(text => {
                    if (!text) {
                        throw new Error('Empty response from server');
                    }
                    
                    try {
                        return JSON.parse(text);
                    } catch (error) {
                        console.error('Server response:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.status === 'success') {
                    alertSuccess(data.message);
                    
                    // Reset form
                    addProductForm.reset();
                    addProductForm.dataset.mode = 'add';
                    const submitButton = addProductForm.querySelector('button[type="submit"]');
                    submitButton.textContent = 'Add Product';
                       // Clear and hide existing images container
                    const existingImagesContainer = document.getElementById('existingImagesContainer');
                    const existingImagesDiv = document.getElementById('existingImages');
                    existingImagesDiv.innerHTML = '';
                    existingImagesContainer.style.display = 'none';
        
                  // Remove hidden input for current images if it exists
                    const currentImagesInput = document.getElementById('currentImages');
                   if (currentImagesInput) {
                     currentImagesInput.remove();
                   }
                   // Make file input required again
                     document.getElementById('productImages').required = true;
        
                    loadProducts(); // Refresh the product list
                } else {
                    // Provide more specific error feedback
                    if (data.error_code === 'duplicate_id') {
                        alertError("Product ID already exists. Please use a unique ID.");
                        document.getElementById('productID').focus();
                        document.getElementById('productID').classList.add('border-danger');
                    } else {
                        alertError(data.message || "Operation failed. Please try again.");
                    }
                }
            })
            .catch(error => {
                // More specific error messages based on common issues
                let errorMessage = "An error occurred";
                
                if (error.message === 'DUPLICATE_PRODUCT_ID') {
                    errorMessage = "Product ID already exists. Please use a unique ID.";
                    document.getElementById('productID').focus();
                    document.getElementById('productID').classList.add('border-danger');
                } else if (error.message.includes('Unexpected end of JSON input')) {
                    errorMessage = "Server returned incomplete data. This may happen when trying to add a duplicate product ID.";
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = "Connection failed. Please check your internet connection and try again.";
                } else if (error.message.includes('NetworkError')) {
                    errorMessage = "Network error. The server might be down or unreachable.";
                } else {
                    errorMessage = error.message;
                }
                
                alertError(errorMessage);
                console.error("Error details:", error);
            });
        });
    }

    // Set up event  for edit and delete buttons
    const productTableBody = document.getElementById('productTableBody');
    if (productTableBody) {
        productTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-danger')) {
                // Handle delete button click
                const row = e.target.closest('tr');
                const productId = row.querySelector('td:nth-child(2)').textContent;
                if (confirm('Are you sure you want to delete this product?')) {
                    deleteProduct(productId);
                }
            } else if (e.target.classList.contains('btn-warning') || e.target.closest('.btn-warning')) {
                // Handle edit button click
                const row = e.target.closest('tr');
                const productId = row.querySelector('td:nth-child(2)').textContent;
                loadProductForEdit(productId);
            }
        });
    }

    // Initialize the existing product IDs list
    updateExistingProductIDs();
});

// Function to load products from the server
function loadProducts() {
    fetch('get_products.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    console.error('Server response:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            displayProducts(data);
            // Update the list of existing product IDs after displaying products
            updateExistingProductIDs();
        })
        .catch(error => {
            alertError("Failed to load products: " + error.message);
            console.error("Error details:", error);
        });
}

// Function to update existing product IDs list
function updateExistingProductIDs() {
    const existingProductIDs = [];
    const productRows = document.querySelectorAll('#productTableBody tr');
    productRows.forEach(row => {
        const productId = row.querySelector('td:nth-child(2)')?.textContent;
        if (productId) {
            existingProductIDs.push(productId.trim());
        }
    });
    console.log("Existing product IDs:", existingProductIDs);
    
    // Store as a global variable
    window.existingProductIDs = existingProductIDs;
    return existingProductIDs;
}

// Function to display products in the table
function displayProducts(products) {
    const tableBody = document.getElementById("productTableBody");
    
    if (!tableBody) {
        console.error("Product table body element not found");
        return;
    }
    
    tableBody.innerHTML = ""; // Clear existing data
    
    if (!products || products.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center">No products available.</td></tr>`;
        return;
    }
    
    products.forEach(product => {
        let imagePaths = product.images ? product.images.split(',') : [];
        let firstImage = imagePaths.length > 0 ? imagePaths[0] : "uploads/placeholder.jpg";
        
        let row = `
            <tr>
                <td><img src="${firstImage}" width="50" height="50" alt="Product Image" class="img-thumbnail"></td>
                <td>${product.product_id}</td>
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>${product.stock}</td>
                <td>$${parseFloat(product.price).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</button>
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Function to delete a product
function deleteProduct(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('delete_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server returned ${response.status}: ${response.statusText}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Server response:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.status === 'success') {
            alertSuccess(data.message);
            loadProducts(); // Refresh the product list
        } else {
            alertError(data.message || "Failed to delete product");
        }
    })
    .catch(error => {
        alertError("Failed to delete product: " + error.message);
        console.error("Error details:", error);
    });
}

// Function to check if a product ID exists
function isProductIDExists(productID) {
    return window.existingProductIDs && window.existingProductIDs.includes(productID);
}

// Popup message functions
function alertSuccess(message) {
    alert(" Success: " + message);
}

function alertError(message) {
    alert(" Error: " + message);
}

function loadProductForEdit(productId) {
    // Get the form reference at the beginning
    const form = document.getElementById('addProductForm');
    
    fetch(`get_single_product.php?id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    console.error('Server response:', text);
                    throw new Error('Invalid JSON in product data');
                }
            });
        })
        .then(product => {
            if (!product || !product.product_id) {
                throw new Error('Product data is incomplete or invalid');
            }
            
            // Fill the form with product data
            document.getElementById('productID').value = product.product_id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productDescription').value = product.description;
            
            // Display existing images if available
            const existingImagesContainer = document.getElementById('existingImagesContainer');
            const existingImagesDiv = document.getElementById('existingImages');
            
            // Clear previous images
            existingImagesDiv.innerHTML = '';
            
            if (product.images && product.images.length > 0) {
                // Make the container visible
                existingImagesContainer.style.display = 'block';
                
                // Split images string into array
                const images = product.images.split(',');
                
                // Display each image
                images.forEach(imagePath => {
                    if (imagePath.trim()) {
                        const imgElement = document.createElement('img');
                        imgElement.src = imagePath;
                        imgElement.className = 'img-thumbnail';
                        imgElement.style.height = '100px';
                        imgElement.alt = 'Product image';
                        existingImagesDiv.appendChild(imgElement);
                    }
                });
                
                // Store original images data in a hidden field
                const hiddenImagesInput = document.getElementById('currentImages') || document.createElement('input');
                hiddenImagesInput.type = 'hidden';
                hiddenImagesInput.id = 'currentImages';
                hiddenImagesInput.name = 'currentImages';
                hiddenImagesInput.value = product.images;
                form.appendChild(hiddenImagesInput);
                
                // Make file input optional in edit mode
                document.getElementById('productImages').required = false;
            } else {
                existingImagesContainer.style.display = 'none';
            }
            
            // Change form submission behavior
            form.dataset.mode = 'edit';
            form.dataset.editId = productId;
            
            // Change button text
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.textContent = 'Update Product';
            
            // Remove any error highlighting
            document.getElementById('productID').classList.remove('border-danger');
            
            // Scroll to the form
            form.scrollIntoView({ behavior: 'smooth' });
            
            // Alert user that edit mode is active
            alertSuccess(`Edit mode active for product ID: ${productId}`);
        })
        .catch(error => {
            alertError("Failed to load product details: " + error.message);
            console.error("Error details:", error);
        });
}