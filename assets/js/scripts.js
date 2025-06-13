console.log("JS loaded")


// ai helped with these debugging features as i ran into some issues
// Wait for DOM to be fully loaded
// document.addEventListener('DOMContentLoaded', function() {
//     console.log("DOM loaded, validation functions ready");
// });

// Add this to your script after DOM loads
// document.addEventListener('DOMContentLoaded', function() {
//     const form = document.querySelector('form[action="includes/register_logic.php"]');
//     if (form) {
//         form.addEventListener('submit', function(e) {
//             if (!validateRegisterForm()) {
//                 e.preventDefault(); // Stop form submission
//             }
//         });
//     }
// });



// ai helped with these debugging features as i ran into some issues
function validateRegisterForm() {
    console.log("Validation function called");

    // Check if elements exist before accessing them
    // const usernameEl = document.getElementById("username");
    // const firstNameEl = document.getElementById("first_name");
    // const lastNameEl = document.getElementById("last_name");
    // const emailEl = document.getElementById("email");
    // const passwordEl = document.getElementById("password");
    // const confirmPasswordEl = document.getElementById("confirmedPassword");

    // Debug: Check if elements exist
    // if (!usernameEl) {
    //     console.error("Username element not found!");
    //     return false;
    // }
    // if (!firstNameEl) {
    //     console.error("First name element not found!");
    //     return false;
    // }
    // if (!lastNameEl) {
    //     console.error("Last name element not found!");
    //     return false;
    // }
    // if (!emailEl) {
    //     console.error("Email element not found!");
    //     return false;
    // }
    // if (!passwordEl) {
    //     console.error("Password element not found!");
    //     return false;
    // }
    // if (!confirmPasswordEl) {
    //     console.error("Confirm password element not found!");
    //     return false;
    // }

    const username = document.getElementById("username").value.trim();
    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmedPassword").value;

    const errors = [];


    if (!username) errors.push("Username is required");

    if (!firstName) errors.push("First name is required");
    if (!lastName) errors.push("Last name is required");


    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errors.push("Email is required");
    } else if (!emailPattern.test(email)) {
        errors.push("Invalid email format");
    }



    if (!password) {
        errors.push("Password is required");
    } else if (password.length < 8) {
        errors.push("Password must be at least 8 characters");
    }

    if (password !== confirmPassword) {
        errors.push("Passwords do not match");
    }

    
    if (errors.length > 0) {
        alert(errors.join("\n"));
        return false;  
    }

    return true; 
}




// toggle password views
function togglePassword(id = "password") {
    const field = document.getElementById(id);
    if (!field) return;

    field.type = field.type === "password" ? "text" : "password";
}

function toggle_confirmed_Password(id = "confirmedPassword") {
    const field = document.getElementById(id);
    if (!field) return;

    field.type = field.type === "password" ? "text" : "password";
}

// image previeew
function previewImage() {
    const input = document.getElementById('product_image');
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
    preview.src = URL.createObjectURL(input.files[0]);
    preview.style.display = 'block';
    }
}






