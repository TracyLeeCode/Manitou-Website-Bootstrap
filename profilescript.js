function showForm(formName) {
    // Get the form element by its ID
    var form = document.getElementById(formName + "Form");
    
    // Toggle the display style of the form
    if (form.style.display === "none") {
        form.style.display = "block"; // Show the form
    } else {
        form.style.display = "none"; // Hide the form
    }
}