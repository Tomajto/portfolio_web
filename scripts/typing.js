const words = ["work", "school", "home"];
let wordIndex = 0;
let letterIndex = 0;
let isDeleting = false;
const dynamicWord = document.getElementById("dynamic-word");

function typeEffect() {
  const currentWord = words[wordIndex];
  if (!isDeleting) {
    // Typing
    letterIndex++;
    dynamicWord.textContent = currentWord.substring(0, letterIndex);
    if (letterIndex === currentWord.length) {
      setTimeout(() => {
        isDeleting = true;
        typeEffect();
      }, 3000);
    } else {
      setTimeout(typeEffect, 350);
    }
  } else {
    // Deleting
    letterIndex--;
    dynamicWord.textContent = currentWord.substring(0, letterIndex);
    if (letterIndex === 0) {
      isDeleting = false;
      wordIndex = (wordIndex + 1) % words.length;
      setTimeout(typeEffect, 400);
    } else {
      setTimeout(typeEffect, 60);
    }
  }
}

// Start the animation after page load
window.addEventListener("DOMContentLoaded", () => {
  setTimeout(typeEffect, 1200);
});
