// take posts list
const postsList = document.querySelector('ul.post-list');

//take a last child (post) of list
const lastPostOfList = postsList.lastElementChild;

const spinnerbox = document.createElement('div')

spinnerbox.innerHTML =`
                    <div id="pagination-spinner" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `;
postsList.insertAdjacentElement("afterend", spinnerbox);

const spinner = document.querySelector('#pagination-spinner');

const errorBox = document.createElement("div");
errorBox.innerHTML = `<div class='alert alert-danger text-center error-message'>
    Une erreur est survenue 
    <button class="btn btn-danger retry-pagination">retry</button>
</div>`;

errorBox.querySelector('.retry-pagination').addEventListener('click', function ()
{
    loadNextPage(postsList);
});

let loading = false;

//Add API for check if a element is fully visible
const observer = new IntersectionObserver(function(entries) {
    if(entries[0].isIntersecting === true && !isLoadingPagination()) {
        console.log('last post is fully visible in screen');
        loadNextPage(postsList);
    }
}, { threshold: [1] });

//use API for last post
observer.observe(spinner);

//get GET parameter of current url
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

//get GET 'page' parameter value
let nextPage = Number(urlParams.get('page')) + 1;

//get pathname url
const baseUrl = window.location.pathname; //  ~/posts/

function loadNextPage(elementsList) {
    loading = true;

    console.log(`load next page ${nextPage}`);

    const url = `${baseUrl}?page=${nextPage}`;

    // On envoie la requête au serveur, (code asynchrone => on utilise les promesses javascript)
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest', // requis pour que Symfony comprenne que c'est une requête AJAX
        }
    })
    .then(function(response) {
        console.log(response);
        if(response.ok)
        {
            return response.text();
        }
    })
    .then(function(postsHtml)
    {
        if(typeof postsHtml === 'string' && !!postsHtml)
        {
            elementsList.insertAdjacentHTML('beforeend', postsHtml);
            nextPage ++;
        }
        else
        {
            postsList.insertAdjacentElement("afterend", spinnerbox).remove();
           elementsList.insertAdjacentElement('beforeend', errorBox);
        }
    })
    .finally(function()
    {
        loading = false;
    })
    errorBox.remove();
    postsList.insertAdjacentElement("afterend", spinnerbox);
}

function isLoadingPagination()
{
    return loading;
}
