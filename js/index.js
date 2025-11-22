// faq
function toggleAnswer(faqId) {
  var answer = document.getElementById("answer-" + faqId);
  var icon = answer.previousElementSibling.querySelector(".faq-icon");

  // Toggle the answer visibility
  if (answer.style.display === "none" || answer.style.display === "") {
    answer.style.display = "block";
    icon.textContent = "-"; // Change icon to "-" when open
  } else {
    answer.style.display = "none";
    icon.textContent = "+"; // Change icon to "+" when closed
  }
}

// Optional: Hide answers by default when the page loads
document.addEventListener("DOMContentLoaded", function () {
  var allAnswers = document.querySelectorAll(".faq-answer");
  allAnswers.forEach(function (answer) {
    answer.style.display = "none";
  });
});

// serch bar

// Search Bar Toggle
const searchBtn = document.getElementById("searchBtn");
const searchBar = document.getElementById("searchBar");
const closeSearchBtn = document.getElementById("closeSearchBtn");
const searchInput = document.getElementById("searchInput");

searchBtn.addEventListener("click", () => {
  searchBar.classList.add("active");
  searchInput.focus();
});

closeSearchBtn.addEventListener("click", () => {
  searchBar.classList.remove("active");
});

// Mobile Menu Toggle (optional for future expansion)
const navbarMenu = document.getElementById("navbarMenu");
window.addEventListener("resize", () => {
  if (window.innerWidth > 768) {
    navbarMenu.classList.remove("active");
  }
});
