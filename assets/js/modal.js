document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('food-modal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');

    const initialChoiceSection = document.getElementById('initialChoiceSection');
    const searchSection = document.getElementById('searchSection');
    const manualAddSection = document.getElementById('manualAddSection');
    const quantitySection = document.getElementById('quantitySection');

    const chooseSearchBtn = document.getElementById('chooseSearch');
    const chooseManualBtn = document.getElementById('chooseManual');

    const backToChoiceFromSearchBtn = document.getElementById('backToChoiceFromSearch');
    const backToChoiceFromManualBtn = document.getElementById('backToChoiceFromManual');
    const backToSearchBtn = document.getElementById('backToSearch');

    const searchForm = document.getElementById('searchFoodForm');
    const addFoodForm = document.getElementById('addFoodForm');
    const manualAddForm = document.getElementById('manualAddForm');

    let selectedFoodData = null;

    const addFoodUrl = modal.dataset.addFoodUrl; // Récupère l'URL depuis l'attribut data-add-food-url

    // Gestion du modal
    function openModal() {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        resetForms();
    }

    function resetForms() {
        searchForm.reset();
        addFoodForm.reset();
        manualAddForm.reset();
        initialChoiceSection.style.display = 'block';
        searchSection.style.display = 'none';
        manualAddSection.style.display = 'none';
        quantitySection.style.display = 'none';
        document.getElementById('searchResults').innerHTML = '';
    }

    openModalBtn.addEventListener('click', openModal);
    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Choix entre recherche et ajout manuel
    chooseSearchBtn.addEventListener('click', () => {
        initialChoiceSection.style.display = 'none';
        searchSection.style.display = 'block';
    });

    chooseManualBtn.addEventListener('click', () => {
        initialChoiceSection.style.display = 'none';
        manualAddSection.style.display = 'block';
    });

    // Boutons retour
    backToChoiceFromSearchBtn.addEventListener('click', () => {
        searchSection.style.display = 'none';
        initialChoiceSection.style.display = 'block';
    });

    backToChoiceFromManualBtn.addEventListener('click', () => {
        manualAddSection.style.display = 'none';
        initialChoiceSection.style.display = 'block';
    });

    backToSearchBtn.addEventListener('click', () => {
        quantitySection.style.display = 'none';
        searchSection.style.display = 'block';
    });

    // Gestion de la recherche
    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const resultsDiv = document.getElementById('searchResults');
        const loadingIndicator = document.getElementById('loadingIndicator');
        resultsDiv.innerHTML = ''; // Efface les résultats précédents

        try {
            const response = await fetch(addFoodUrl, {
                method: 'POST',
                body: new FormData(e.target)
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la recherche');
            }

            const results = await response.json();

            // Masque l'indicateur de chargement

            if (results.error) {
                resultsDiv.innerHTML = `<div class="alert alert-danger">${results.error}</div>`;
                return;
            }

            // Affiche les résultats
            resultsDiv.innerHTML = results.map(food => `
                <div class="food-item" data-food-id="${food.id}" data-food-info='${JSON.stringify(food)}'>
                    <div class="d-flex align-items-center">
                        ${food.image ? `<img src="${food.image}" alt="${food.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">` : ''}
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${food.name}</h6>
                            <small class="text-muted d-block">${food.brand}</small>
                            <div class="mt-1">
                                <span class="badge bg-light text-dark">${food.calories} kcal/100g</span>
                                <span class="badge bg-light text-dark">P: ${food.proteins}g</span>
                                <span class="badge bg-light text-dark">G: ${food.carbs}g</span>
                                <span class="badge bg-light text-dark">L: ${food.fats}g</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            // Gestion de la sélection d'un aliment
            document.querySelectorAll('.food-item').forEach(item => {
                item.addEventListener('click', () => {
                    selectedFoodData = JSON.parse(item.dataset.foodInfo);
                    document.getElementById('food_id').value = selectedFoodData.id;

                    const selectedFoodInfo = document.querySelector('.selected-food-info');
                    selectedFoodInfo.innerHTML = `
                        <div class="d-flex align-items-center">
                            ${selectedFoodData.image ? `<img src="${selectedFoodData.image}" alt="${selectedFoodData.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">` : ''}
                            <div>
                                <h6 class="mb-1">${selectedFoodData.name}</h6>
                                <small class="text-muted">${selectedFoodData.brand}</small>
                            </div>
                        </div>
                    `;

                    searchSection.style.display = 'none';
                    quantitySection.style.display = 'block';
                });
            });

        } catch (error) {
            console.error('Erreur:', error);
            resultsDiv.innerHTML = '<div class="alert alert-danger">Erreur lors de la recherche</div>';
        }
    });

    // Ajout de l'aliment sélectionné via OpenFoodFacts
    addFoodForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const response = await fetch(addFoodUrl, {
                method: 'POST',
                body: new FormData(e.target)
            });

            const result = await response.json();
            if (result.success) {
                closeModal();
                window.location.reload();
            } else if (result.error) {
                alert(result.error);
            } else {
                throw new Error('Erreur lors de l\'ajout');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'ajout de l\'aliment.');
        }
    });

    // Ajout de l'aliment manuel
    manualAddForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('manual_add', '1'); // Indicateur pour le contrôleur

        try {
            const response = await fetch(addFoodUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                closeModal();
                window.location.reload();
            } else if (result.error) {
                alert(result.error);
            } else {
                throw new Error('Erreur lors de l\'ajout');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'ajout de l\'aliment.');
        }
    });
});
