
document.addEventListener('DOMContentLoaded', function() { {
    const menuButton = document.querySelector('.menu-button');
    const menuContainer = document.querySelector('.menu-container');

    menuButton.addEventListener('click', function() {
        menuContainer.classList.toggle('active');
        menuButton.classList.toggle('active');
    });
}
console.log('i am here')
});

