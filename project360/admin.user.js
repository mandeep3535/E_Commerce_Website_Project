document.addEventListener("DOMContentLoaded", function () {
    let userTableBody = document.getElementById("userTableBody");
    let editMode = false;
  
    // Summy user user data
    let users = [
      { userId: "1001", name: "AB",     email: "ab@example.com",    role: "User",  status: "Active" },
      { userId: "1002", name: "CD",   email: "cd@example.com",    role: "Admin", status: "Active" },
      { userId: "1003", name: "DE",email: "de@example.com", role: "User",  status: "Blocked" }
    ];
  
    // Generate status badges
    function getStatusBadge(status) {
      return `<span class="badge ${status === "Active" ? "bg-success" : "bg-danger"}">
                ${status}
              </span>`;
    }
  
    // Display users in table
    function displayUsers() {
      userTableBody.innerHTML = "";
      users.forEach((user, idx) => {
        let newRow = document.createElement("tr");
  

        newRow.innerHTML = `
          <td>${user.userId}</td>
          <td>${user.name}</td>
          <td>
            <input
              id="email-input-${idx}"
              name="email-input-${idx}"
              type="email"
              value="${user.email}"
              class="form-control form-control-sm email-input"
              required
              disabled
            />
          </td>
          <td>${user.role}</td>
          <td>${getStatusBadge(user.status)}</td>
          <td>
            <button
              class="btn btn-warning btn-sm"
              onclick="resetPassword('${user.email}')"
            >
              <i class="bi bi-key"></i>
              Reset Password
            </button>
            <button
              class="btn ${user.status === "Active" ? "btn-danger" : "btn-success"} btn-sm"
              onclick="toggleUserStatus('${user.userId}')"
            >
              <i class="bi ${user.status === "Active" ? "bi-person-x" : "bi-person-check"}"></i>
              ${user.status === "Active" ? "Block" : "Unblock"}
            </button>
          </td>
        `;
        userTableBody.appendChild(newRow);
      });
    }
  
    // Making email fields editable
    window.enableEditing = function() {
      editMode = true;
      document.querySelectorAll(".email-input").forEach(input => {
        input.disabled = false;
      });
      document.getElementById("editButton").classList.add("d-none");
      document.getElementById("saveButton").classList.remove("d-none");
    };
  
    // Save edited emails (but validate them first)
    window.saveChanges = function() {
      // Gather all email inputs
      let emailInputs = document.querySelectorAll(".email-input");
      for (let i = 0; i < emailInputs.length; i++) {
        // If invalid or blank, alert and stop save
        if (!emailInputs[i].checkValidity()) {
          alert("Please enter a valid email for user " + users[i].name);
          return;
        }
      }
  
      // If all are valid, update users array
      emailInputs.forEach((input, index) => {
        users[index].email = input.value;
      });
  
      alert("User emails have been updated.");
      disableEditing();
    };
  
    // disable editing mode
    function disableEditing() {
      editMode = false;
      document.querySelectorAll(".email-input").forEach(input => {
        input.disabled = true;
      });
      document.getElementById("editButton").classList.remove("d-none");
      document.getElementById("saveButton").classList.add("d-none");
    }
  
    window.resetPassword = function(email) {
      alert(`Password reset link has been sent to ${email}.`);
    };
  
    // Toggle user status (Active/Blocked)
    window.toggleUserStatus = function(userId) {
      users = users.map(user => {
        if (user.userId === userId) {
          user.status = user.status === "Active" ? "Blocked" : "Active";
        }
        return user;
      });
      displayUsers();
    };
  
    displayUsers();
  });
  