document.getElementById('add-image').addEventListener('click', function() {
    // Récupération de l'index des futurs champs
    const index = parseInt(document.getElementById('widget-count').value, 10);
    
    // Récupération du prototype des entrées et remplacement de __name__ par l'index actuel
    const prototype = document.getElementById('annonce_images').dataset.prototype;
    const tmpl = prototype.replace(/__name__/g, index);
    //console.log(tmpl);
    
    // Création d'un nouvel élément div et ajout du contenu du prototype
    const newDiv = document.createElement('div');
    newDiv.innerHTML = tmpl;
    
    // Ajout du nouvel élément au conteneur des images
    document.getElementById('annonce_images').appendChild(newDiv);

    // Incrémentation de l'index pour le prochain élément
    document.getElementById('widget-count').value = index + 1;

    // Appelle la fonction pour initialiser les boutons de suppression
    deleteButtons();


});

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
    // Sélectionne tous les éléments div avec la classe form-group à l'intérieur de #annonce_images
    const count = document.querySelectorAll('#annonce_images div.form-group').length;
    // Met à jour la valeur de widget-count avec la longueur obtenue
    document.getElementById('widget-count').value = count;
}

// Appelle la fonction updateCounter pour initialiser le compteur
updateCounter();
// Appelle la fonction pour initialiser les boutons de suppression
deleteButtons();

