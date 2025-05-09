document.addEventListener("DOMContentLoaded", function () {
  const modalOverlay = document.getElementById("access-notice-modal");
  const agreeButton = document.getElementById("agree-button");
  const mainContent = document.getElementById("page-content");
  const body = document.body;

  // Show the modal when the page loads
  // Using a short timeout to ensure styles are applied before transition
  setTimeout(() => {
    modalOverlay.classList.add("active");
    body.style.overflow = "hidden"; // Prevent background scrolling
  }, 50);

  // When the user clicks on the "I Agree" button, close the modal
  agreeButton.addEventListener("click", function () {
    modalOverlay.classList.remove("active");
    // Allow main content to be visible and body to scroll
    mainContent.classList.add("visible");
    body.style.overflow = "auto";
  });
});
