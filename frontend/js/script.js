document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    // return application/json instead of x-www-form-urlencoded
    let response = await fetch("../backend/Authentication/login.php", { //changed to /backend/Authentication/login.php
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        email: email,
        password: password
    })
});


    let data = await response.json();

    if(data.status === "success"){
        if(data.role === "owner"){
            window.location.href = "home_owner.html";
        } else {
            window.location.href = "home_user.html";
        }
    } else {
        alert(data.message);
    }
});
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('signupUsername').value;
    const email = document.getElementById('signupEmail').value;
    const password = document.getElementById('signupPassword').value;
    const role = document.getElementById('signupRole').value;

    fetch("../backend/Authentication/signup.php", { //changed to /backend/Authentication/signup.php
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `username=${username}&email=${email}&password=${password}&role=${role}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "success") {
            alert("Account created successfully!");
            document.getElementById("login-tab").click();
        } else {
            alert("Error: " + data.message);
        }
    });
});
