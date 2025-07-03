<script>
    function autocomplete(inputId, suggestionId, latInputId, lngInputId) {
        const input = document.getElementById(inputId);
        const suggestions = document.getElementById(suggestionId);
        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);

        if (!input || !suggestions) return;

        input.addEventListener('input', () => {
            const value = input.value;

            if (value.length < 3) {
                suggestions.innerHTML = '';
                suggestions.classList.add('hidden');
                return;
            }

            fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(value)}&limit=5`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    data.features.forEach(place => {
                        const li = document.createElement('li');
                        li.textContent = place.properties.label;
                        li.classList.add('px-3', 'py-2', 'cursor-pointer', 'hover:bg-gray-100');

                        li.onclick = () => {
                            input.value = place.properties.label;
                            suggestions.innerHTML = '';
                            suggestions.classList.add('hidden');

                            // ➡️ Récupère longitude et latitude
                            const coords = place.geometry.coordinates;
                            if (latInput && lngInput) {
                                lngInput.value = coords[0];
                                latInput.value = coords[1];
                            }
                        };
                        suggestions.appendChild(li);
                    });
                    suggestions.classList.remove('hidden');
                });
        });

        document.addEventListener('click', function (e) {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        autocomplete('from_city', 'from_city_suggestions', 'from_lat', 'from_lng');
        autocomplete('to_city', 'to_city_suggestions', 'to_lat', 'to_lng');
    });
</script>
