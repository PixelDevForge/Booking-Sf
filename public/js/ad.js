document.addEventListener('DOMContentLoaded', function() {
    // Vérifie que l'élément avec l'ID 'add-image' existe avant d'ajouter un écouteur d'événement
    const addImageButton = document.getElementById('add-image');
    const widgetCountInput = document.getElementById('widget-count');
    const annonceImagesContainer = document.getElementById('annonce_images');

    if (addImageButton && widgetCountInput && annonceImagesContainer) {
        addImageButton.addEventListener('click', function() {
            // Récupération de l'index des futurs champs
            const index = parseInt(widgetCountInput.value, 10);

            // Récupération du prototype des entrées et remplacement de __name__ par l'index actuel
            const prototype = annonceImagesContainer.dataset.prototype;
            const tmpl = prototype.replace(/__name__/g, index);

            // Création d'un nouvel élément div et ajout du contenu du prototype
            const newDiv = document.createElement('div');
            newDiv.innerHTML = tmpl;

            // Ajout du nouvel élément au conteneur des images
            annonceImagesContainer.appendChild(newDiv);

            // Incrémentation de l'index pour le prochain élément
            widgetCountInput.value = index + 1;

            // Appelle la fonction pour initialiser les boutons de suppression
            deleteButtons();
        });
    }

    function deleteButtons() {
        // Sélectionne tous les boutons avec l'attribut data-action="delete"
        const deleteButtons = document.querySelectorAll('button[data-action="delete"]');

        // Parcourt chaque bouton et ajoute un écouteur d'événement de clic
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Récupère la valeur de l'attribut data-target
                const target = this.dataset.target;

                // Sélectionne l'élément cible et le supprime
                const targetElement = document.querySelector(target);
                if (targetElement) {
                    targetElement.remove();
                }
            });
        });
    }

    function updateCounter() {
        if (annonceImagesContainer && widgetCountInput) {
            // Sélectionne tous les éléments div avec la classe form-group à l'intérieur de #annonce_images
            const count = document.querySelectorAll('#annonce_images div.form-group').length;
            // Met à jour la valeur de widget-count avec la longueur obtenue
            widgetCountInput.value = count;
        }
    }

    // Appelle la fonction updateCounter pour initialiser le compteur
    updateCounter();
    // Appelle la fonction pour initialiser les boutons de suppression
    deleteButtons();
});
