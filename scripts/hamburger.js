const hamburger = document.getElementById("hamburger");
const mobileMenu = document.getElementById("mobileMenu");
let isOpen = false;

hamburger.addEventListener("click", () => {
  isOpen = !isOpen;
  if (isOpen) {
    mobileMenu.classList.add("open");
    // změna hamburger ikony na křížek
    hamburger.children[0].style.transform = "rotate(45deg) translateY(8px)";
    hamburger.children[1].style.opacity = "0";
    hamburger.children[2].style.transform = "rotate(-45deg) translateY(-8px)";
  } else {
    mobileMenu.classList.remove("open");
    hamburger.children[0].style.transform = "rotate(0) translateY(0)";
    hamburger.children[1].style.opacity = "1";
    hamburger.children[2].style.transform = "rotate(0) translateY(0)";
  }
});
// Close the mobile menu when clicking outside of it
document.addEventListener("click", (event) => {
  if (!mobileMenu.contains(event.target) && !hamburger.contains(event.target) && isOpen) {
    isOpen = false;
    mobileMenu.classList.remove("open");
    hamburger.children[0].style.transform = "rotate(0) translateY(0)";
    hamburger.children[1].style.opacity = "1";
    hamburger.children[2].style.transform = "rotate(0) translateY(0)";
  }
});