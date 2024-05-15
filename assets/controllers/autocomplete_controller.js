import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [ "input", "results" ]

    search(event) {
        event.preventDefault();

        const url = this.inputTarget.dataset.autocompleteUrl;
        const query = this.inputTarget.value;

        fetch(`${url}?q=${query}`)
            .then(response => response.json())
            .then(data => {
                // On clean le résultat précédent
                this.resultsTarget.innerHTML = '';

                // Ajout des résultats
                data.forEach(item => {
                    const element = document.createElement('a');
                    element.href = item.url;
                    element.classList.add('list-group-item', 'list-group-item-action', 'd-flex', 'align-items-center');

                    const img = document.createElement('img');
                    img.src = item.thumbnailUrl;
                    img.alt = item.title;
                    img.width = 50;
                    img.classList.add('me-2');

                    const text = document.createTextNode(item.title);

                    element.appendChild(img);
                    element.appendChild(text);
                    this.resultsTarget.appendChild(element);
                });
            });
    }
}
