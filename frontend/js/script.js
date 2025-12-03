document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    if(email === "" || password === "") {
        alert("Please fill in all fields.");
        return;
    }

    // هنا ممكن تستخدم AJAX للتواصل مع login.php
    console.log("Login data:", email, password);
});

document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('signupUsername').value;
    const email = document.getElementById('signupEmail').value;
    const password = document.getElementById('signupPassword').value;

    if(username === "" || email === "" || password === "") {
        alert("Please fill in all fields.");
        return;
    }

    // هنا ممكن تستخدم AJAX للتواصل مع register.php
    console.log("Signup data:", username, email, password);
});
