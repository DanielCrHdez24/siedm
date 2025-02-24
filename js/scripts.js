// Función para mostrar u ocultar el menú lateral
function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("show");
}

// Menú y submenú
document.querySelectorAll('.submenu-toggle').forEach(item => {
    item.addEventListener('click', () => {
        const submenuContainer = item.parentElement;
        submenuContainer.classList.toggle('active');
    });
});
