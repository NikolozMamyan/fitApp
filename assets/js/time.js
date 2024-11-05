// Ajoutez ce JavaScript
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('hours').textContent = hours;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
        
        const options = { weekday: 'long', day: 'numeric', month: 'long' };
        document.getElementById('date').textContent = now.toLocaleDateString('fr-FR', options);
    }

    // Update clock every second
    updateClock();
    setInterval(updateClock, 1000);
});