document.addEventListener('DOMContentLoaded', function() {
    // Logique de la navigation
    const links = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            links.forEach(l => l.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));

            link.classList.add('active');

            const sectionId = link.getAttribute('data-section') + '-section';
            document.getElementById(sectionId).classList.add('active');
        });
    });

    // Logique du Timer
    const minutesDisplay = document.getElementById('minutes');
    const secondsDisplay = document.getElementById('seconds');
    const millisecondsDisplay = document.getElementById('milliseconds'); // Nouvel élément pour les millisecondes
    const startButton = document.getElementById('startTimer');
    const stopButton = document.getElementById('stopTimer');
    const resetButton = document.getElementById('resetTimer');

    let interval = null;
    let totalMilliseconds = 0;

    function startTimer() {
        if (!interval) {
            interval = setInterval(updateTimer, 10); // Mise à jour toutes les 10 millisecondes
            startButton.classList.add('timer-button-active');
            document.querySelector('.timer-circle').style.animationPlayState = 'running';
        }
    }

    function stopTimer() {
        if (interval) {
            clearInterval(interval);
            interval = null;
            startButton.classList.remove('timer-button-active');
            document.querySelector('.timer-circle').style.animationPlayState = 'paused';
        }
    }

    function resetTimer() {
        stopTimer();
        totalMilliseconds = 0;
        updateTimerDisplay();
        document.querySelector('.timer-circle').style.animationPlayState = 'paused';
        document.querySelector('.timer-circle').style.setProperty('--progress', '0deg');
        document.querySelector('.timer-pointer').style.transform = 'rotate(0deg)';
    }

    function updateTimer() {
        totalMilliseconds += 10; // Incrémente de 10 ms à chaque appel
        updateTimerDisplay();
        
        // Mise à jour de la progression du cercle
        const progress = ((totalMilliseconds / 1000) % 60) / 60 * 360;
        document.querySelector('.timer-circle').style.setProperty('--progress', `${progress}deg`);
        
        // Rotation du pointeur
        document.querySelector('.timer-pointer').style.transform = `rotate(${progress}deg)`;
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(totalMilliseconds / 60000);
        const seconds = Math.floor((totalMilliseconds % 60000) / 1000);
        const milliseconds = Math.floor((totalMilliseconds % 1000) / 10)

        minutesDisplay.textContent = minutes.toString().padStart(2, '0');
        secondsDisplay.textContent = seconds.toString().padStart(2, '0');
        millisecondsDisplay.textContent = milliseconds.toString().padStart(2, '0'); // Affiche les millisecondes sur 3 chiffres
    }

    startButton.addEventListener('click', startTimer);
    stopButton.addEventListener('click', stopTimer);
    resetButton.addEventListener('click', resetTimer);

});