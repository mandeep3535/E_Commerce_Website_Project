document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addProductForm");
    const productTableBody = document.getElementById("productTableBody");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); 

        // Get form values
        const name = document.getElementById("productName").value;
        const id = document.getElementById("productID").value;
        const price = document.getElementById("productPrice").value;
        const stock = document.getElementById("productStock").value;
        const color = document.getElementById("productColor").value;
        const category = document.getElementById("productCategory").value;
        const images = document.getElementById("productImages").files;

        if (images.length > 3) {
            alert("You can only upload up to 3 images.");
            return;
        }

        // Creating table row
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${images.length > 0 ? images[0].name : "No Image"}</td>
            <td>${id}</td>
            <td>${name}</td>
            <td>${category}</td>
            <td>${stock}</td>
            <td>$${price}</td>
            <td>${color}</td>
            <td>
                <button class="btn btn-sm btn-success toggle-btn">Publish</button>
            </td>
        `;

        // Add row to table
        productTableBody.appendChild(row);

        // Add event listener to the toggle button
        row.querySelector(".toggle-btn").addEventListener("click", function () {
            if (this.textContent === "Publish") {
                this.textContent = "Unpublish";
                this.classList.remove("btn-success");
                this.classList.add("btn-danger");
                row.classList.add("table-secondary"); 
            } else {
                this.textContent = "Publish";
                this.classList.remove("btn-danger");
                this.classList.add("btn-success");
                row.classList.remove("table-secondary"); 
            }
        });

        // Clearing form after adding
        form.reset();
    });
});
