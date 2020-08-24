document.addEventListener('DOMContentLoaded', function()
{

    const paginationStandardElements = document.querySelectorAll('.pagination-standard');
    for(let paginationElement of paginationStandardElements)
    {
        paginationElement.setAttribute('style', "display : none");
    }

    // sélectionner les liens et les ranger dans un tableau
    const postList = document.querySelector('.post-list');

    // on connecte un écouteur de clic
    postList.addEventListener('click', function(event)
    {
        // Si on a cliqué sur un lien de type "Like" ...
        const likeButton = getLikeButton(event.target);
        if (likeButton !== null) {
            // on empêche le navigateur de suivre le lien
            // et donc de recharger la page
            event.preventDefault();

            // On récupère l'URL du bouton
            const url = likeButton.getAttribute('href');
            if (likeButton.classList.contains('loading'))
            {
                return; // si on est d"ja en train de charger ce Post, on ne relance pas de requete
            }

            const post = likeButton.closest("article.post");
            likePostOnServer(url, post);
        }
    });
});


function likePostOnServer(url, postElement) {
    // On récupère l'ID du Post sur lequel on a cliqué
    const id = postElement.dataset.post_id;
    console.log(`Envoi du like au serveur ... (post #${id})`);

    // On change l'état de chargement de ce Post,
    // (affichage du spinner, blocage des clics successifs)
    postElement.querySelector('.like').classList.add('loading');
    const linkIcon = postElement.querySelector('.like').innerHTML;
    postElement.querySelector('.like').innerHTML = `
        <div class="spinner-grow spinner-grow-sm text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;

    // On envoie la requête au serveur, (code asynchrone => on utilise les promesses javascript)
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest', // requis pour que Symfony comprenne que c'est une requête AJAX
        }
    })
        .then(function(response) {
            if (response.ok) {
                return response.text();
            } else {
                console.warn('erreur du serveur', response.status);
            }
        })
        .then(function(htmlFragment) {

            console.log("HTML FRAG", htmlFragment)
            // Si on obtient un fragment html du serveur on l'affiche
            if (typeof htmlFragment === 'string' && !!htmlFragment) {
                postElement.querySelector('.card-footer').innerHTML = htmlFragment;
            } else {
                // Sinon on remet le vieux contenu html
                postElement.querySelector('.like').innerHTML = linkIcon;
            }

            // Quoi qu'il arrive, on enlève l'état de chargement à la fin
            // pour pouvoir refaire de nouvelles requêtes
            postElement.querySelector('.like').classList.remove('loading');
        });
}

function getLikeButton(element) {
    if (element.classList.contains('like')) {
        return element;
    }

    if (
        element.closest('a') &&
        element.closest('a').classList.contains('like')
    ) {
       return element.closest('a');
    }

    return null;
}