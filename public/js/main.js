feather.replace();

const navLinks = document.querySelectorAll('.nav-item')
const menuToggle = document.getElementById('top-nav')
const bsCollapse = bootstrap.Collapse.getOrCreateInstance(menuToggle, {toggle: false})
navLinks.forEach((l) => {
    l.addEventListener('click', () => {
        if (!menuToggle.classList.contains('collapsed')) {
            bsCollapse.hide()
        }
    })
})
