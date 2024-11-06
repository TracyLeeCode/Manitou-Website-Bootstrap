//Make a function
document.addEventListener("DOMContentLoaded", function () 
{
    //Get the windows location to store it
    const urlParams = new URLSearchParams(window.location.search);
    //Gets the in stock to store the status of it
    const inStock = urlParams.get('inStock');

    //if it is in stock
    if (inStock === '1') {
        document.getElementById('flexCheckChecked').checked = true;
    //If it is not in stock
    } else {
        document.getElementById('flexCheckChecked').checked = false;
    }
});