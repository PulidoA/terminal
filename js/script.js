// Script básico

console.log("Página Terminal de Transporte cargada correctamente ✅");

// Resalta el link activo del menú al hacer scroll
document.addEventListener("scroll", () => {
  const sections = document.querySelectorAll("section, header");
  const navLinks = document.querySelectorAll(".nav-link");

  let current = "";
  sections.forEach(section => {
    const sectionTop = section.offsetTop - 80;
    if (pageYOffset >= sectionTop) {
      current = section.getAttribute("id");
    }
  });

  navLinks.forEach(link => {
    link.classList.remove("active");
    if (link.getAttribute("href") === `#${current}`) {
      link.classList.add("active");
    }
  });
});