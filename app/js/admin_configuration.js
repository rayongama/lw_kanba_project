document.addEventListener("DOMContentLoaded", () => {
    const h = document.querySelector("input[type=hidden]");
    if (h && h.value === "true") {
        const snackbar = document.querySelector(".snackbar");
        snackbar.classList.add("snackbar-visible");
        setTimeout(function () {
            snackbar.classList.remove("snackbar-visible");
        }, 5000);
    }

    const main = document.querySelector("main");

    const loader = document.querySelector(".loader");

    const submit = document.querySelector("button");

    const create = document.querySelector("button.btn-warning");

    /*
        EV : Création de la base de donnée...
     */
    create.addEventListener("click", () => {
        main.classList.add("cloud");
        if (confirm("Attention toute la base de donnée va être écrasée")) {
            loader.classList.remove("none");
            fetch('/admin/create.php').then((r) => {
                main.classList.remove("cloud");
                loader.classList.add("none");
                if (r.status === 404) {
                    alert("Erreur: impossible de contacter le serveur de la base de donnée.");
                } else if (r.status === 403) {
                    alert("Erreur: impossible de créer la nouvelle base de donnée sur le serveur.");
                } else {
                    alert('Création et remplissage de la base terminée');
                }
            });
        } else {
            main.classList.remove("cloud");
        }
    });

    document.querySelectorAll("input").forEach(i => {
        i.addEventListener("input", () => {
            create.disabled = true;
        })
    })
});