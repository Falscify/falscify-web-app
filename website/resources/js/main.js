// Grab element
const selectElement = selector => {
  const element = document.querySelector(selector)
  if (element) return element;
  throw new Error(`Something went wrong. Make sure that ${selector} exists.`);
};

// Navbar style on scroll
const scrollHeader = () => {
  const headerElement = selectElement('#header');
  if (this.scrollY >= 15) {
    headerElement.classList.add('activated');
  } else {
    headerElement.classList.remove('activated');
  }
}

window.addEventListener('scroll', scrollHeader)

// Open and close the menu
const menuToggleIcon = selectElement('#menu-toggle-icon');

const toggleMenu = () => {
  const mobileMenu = selectElement('#menu');
  mobileMenu.classList.toggle('activated');
  menuToggleIcon.classList.toggle('activated');
};

menuToggleIcon.addEventListener('click', toggleMenu);

// Switch theme/add to session storage
const body = document.body;
const themeToggleBtn = selectElement('#theme-toggle-btn');
const currentTheme = sessionStorage.getItem('currentTheme');

// Check to see if there is a theme preference in local Storage, if so add the light theme to the body
if (currentTheme) {
  body.classList.add('light-theme');
}

themeToggleBtn.addEventListener('click', function () {
  // Add light theme on click
  body.classList.toggle('light-theme');

  // If the body has the class of light theme then add it to local Storage, if not remove it
  if (body.classList.contains('light-theme')) {
    sessionStorage.setItem('currentTheme', 'themeActive');
  } else {
    sessionStorage.removeItem('currentTheme');
  }
});

