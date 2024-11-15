document.addEventListener('DOMContentLoaded', async function() {
    const timeRangeButtons = document.querySelectorAll('.time-range-selector .btn');
    let currentPeriod = 'week';

    // Fonction pour récupérer les données du backend
    async function fetchNutritionData(period) {
        try {
            const response = await fetch(`/nutrition-data?period=${period}`);
            if (!response.ok) {
                throw new Error('Erreur lors de la récupération des données');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur:', error);
            return null;
        }
    }

    // Fonction pour mettre à jour les totaux nutritionnels
    function updateNutritionTotals(totals) {
        const totalsDiv = document.getElementById('nutritionTotals');
        totalsDiv.innerHTML = `
            <div class="stat-item">
                <div class="h3 mb-0 text-primary">${totals.calories.toFixed(0)} kcal</div>
                <small class="text-muted">Calories consommées</small>
            </div>
            <div class="stat-item">
                <div class="h3 mb-0 text-success">${totals.proteins.toFixed(1)} g</div>
                <small class="text-muted">Protéines</small>
            </div>
            <div class="stat-item">
                <div class="h3 mb-0 text-warning">${totals.carbs.toFixed(1)} g</div>
                <small class="text-muted">Glucides</small>
            </div>
            <div class="stat-item">
                <div class="h3 mb-0 text-danger">${totals.fats.toFixed(1)} g</div>
                <small class="text-muted">Lipides</small>
            </div>
        `;
    }
    function createMobileSelect() {
        const select = document.createElement('select');
        select.className = 'form-select time-range-mobile d-md-none';
        
        const options = [
            { value: 'week', text: 'Semaine' },
            { value: 'month', text: 'Mois' },
            { value: 'year', text: 'Année' }
        ];

        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.text;
            if (option.value === currentPeriod) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });

        // Insérer le select avant les boutons
        const buttonContainer = document.querySelector('.time-range-selector');
        buttonContainer.insertBefore(select, buttonContainer.firstChild);

        // Gestionnaire d'événement pour le select
        select.addEventListener('change', async function() {
            currentPeriod = this.value;
            // Mettre à jour le bouton actif correspondant
            timeRangeButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.range === currentPeriod);
            });
            await loadNutritionData(currentPeriod);
        });
    }
    createMobileSelect()

    // Fonction pour mettre à jour le graphique de tendance
    function updateTrendGraph(trendData, period) {
        const ctx = document.getElementById('nutritionTrend').getContext('2d');
        const labels = trendData.map(item => item.date);
        const data = trendData.map(item => item.calories);

        // Détruire le graphique précédent s'il existe
        if (window.nutritionChart) {
            window.nutritionChart.destroy();
        }

        window.nutritionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: 'var(--fresh-peach)',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        grid: {
                            display: true
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }
                }
            }
        });
    }

    // Fonction pour mettre à jour le calendrier hebdomadaire
    function updateWeeklyCalendar(trendData) {
        const weeklyCalendar = document.getElementById('weeklyCalendar');
        const ringsContainer = weeklyCalendar.querySelector('.rings-container');

        // Afficher le calendrier hebdomadaire uniquement pour la vue "Semaine"
        if (currentPeriod === 'week') {
            weeklyCalendar.style.display = 'block';
        } else {
            weeklyCalendar.style.display = 'none';
            return;
        }

        // Nettoyer le contenu précédent
        ringsContainer.innerHTML = '';

        // Les jours de la semaine
        const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

        // Générer les anneaux pour chaque jour
        trendData.forEach((item, index) => {
            const percentage = Math.min((item.calories / 2000) * 100, 100); // Supposons un objectif de 2000 kcal
            const dashOffset = 94.2 - (94.2 * (percentage / 100));

            const dayRing = document.createElement('div');
            dayRing.classList.add('day-ring', 'text-center');
            dayRing.innerHTML = `
                <svg class="ring" width="40" height="40" viewBox="0 0 40 40">
                    <!-- Cercle de fond -->
                    <circle cx="20" cy="20" r="15" fill="none" stroke="#f0f0f0" stroke-width="3"/>
                    <!-- Cercle de progression -->
                    <circle cx="20" cy="20" r="15" fill="none" 
                            stroke="var(--fresh-peach)" 
                            stroke-width="3"
                            stroke-dasharray="94.2"
                            stroke-dashoffset="${dashOffset}"
                            transform="rotate(-90 20 20)"/>
                </svg>
                <span class="day-label mt-2">${days[index]}</span>
            `;
            ringsContainer.appendChild(dayRing);
        });
    }

    // Fonction principale pour charger les données
    async function loadNutritionData(period) {
        const data = await fetchNutritionData(period);
        if (data) {
            updateNutritionTotals(data.totals);
            updateTrendGraph(data.trendData, period);
            updateWeeklyCalendar(data.trendData);
        }
    }

    // Charger les données pour la période initiale
    await loadNutritionData(currentPeriod);

    // Gestion des boutons de période
    timeRangeButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            timeRangeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = this.dataset.range;
            const mobileSelect = document.querySelector('.time-range-mobile');
            if (mobileSelect) {
                mobileSelect.value = currentPeriod;
            }
            await loadNutritionData(currentPeriod);
        });
    });
});
