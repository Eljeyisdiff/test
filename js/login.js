window.onload = function () {
    // Set the default selected form
    document.getElementById("accountselect").value = "student";
    changeForm(); // Call the function to display the correct form

    // Pre-fill the Student form if the cookie exists
    if (getCookie("student_email")) {
        document.querySelector("input[name='studentemail']").value = getCookie("student_email");
    }

    // Pre-fill the Employee form if the cookie exists
    if (getCookie("employee_email")) {
        document.querySelector("input[name='email']").value = getCookie("employee_email");
    }

    // Pre-fill the Guest form if the cookie exists
    if (getCookie("guest_fullname")) {
        document.querySelector("input[name='fullname']").value = getCookie("guest_fullname");
    }
    if (getCookie("guest_email")) {
        document.querySelector("input[name='guestemail']").value = getCookie("guest_email");
    }
};


// Function to set a cookie
function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));  // exdays is the number of days until expiration
    const expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function setStudentCookie() {
    if (document.getElementById("remember-student").checked) {
        const studentEmail = document.querySelector("input[name='studentemail']").value;
        setCookie("student_email", studentEmail, 30);  // Cookie expires in 30 days
    }
}

function setEmployeeCookie() {
    if (document.getElementById("remember-employee").checked) {
        const employeeEmail = document.querySelector("input[name='email']").value;
        setCookie("employee_email", employeeEmail, 30);  // Cookie expires in 30 days
    }
}

function setGuestCookie() {
    if (document.getElementById("remember-guest").checked) {
        const guestName = document.querySelector("input[name='fullname']").value;
        const guestEmail = document.querySelector("input[name='guestemail']").value;
        setCookie("guest_fullname", guestName, 30);
        setCookie("guest_email", guestEmail, 30);
    }
}

// Function to change the form based on the selected account type
function changeForm() {
    var accountSelect = document.getElementById("accountselect");
    var studentForm = document.getElementById("student");
    var employeeForm = document.getElementById("employee");
    var guestForm = document.getElementById("guest");

    if (accountSelect.value === "student") {
        studentForm.classList.remove("inv");
        employeeForm.classList.add("inv");
        guestForm.classList.add("inv");
    } else if (accountSelect.value === "employee") {
        studentForm.classList.add("inv");
        employeeForm.classList.remove("inv");
        guestForm.classList.add("inv");
    } else if (accountSelect.value === "guest") {
        studentForm.classList.add("inv");
        employeeForm.classList.add("inv");
        guestForm.classList.remove("inv");
    }
}