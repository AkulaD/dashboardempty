function openPopup() {
    document.getElementById("popupOverlay").style.display = "flex";
}
function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
}

function openPopup() {
    document.getElementById("popupOverlay").style.display = "block";
}
function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("#popupOverlay form");
    const loadingScreen = document.getElementById("loadingScreen");

    if (form && loadingScreen) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            loadingScreen.style.display = "flex";

            setTimeout(() => {
                form.submit();
            }, 300);
        });
    }
});