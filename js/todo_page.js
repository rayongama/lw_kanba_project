let kanbaNav;

document.addEventListener("TodoEdit", (e) => {
    console.log(e);
});

document.addEventListener("DOMContentLoaded", () => {

    /*
        Définitions des constantes.
     */

    const main = document.querySelector("main");
    const header = document.querySelector("header");
    const headerTitle = header.querySelector("span");
    const hamburger = document.querySelector(".hamburger");
    const sideNav = document.querySelector(".side-nav");
    const snackbar = document.querySelector(".snackbar");

    const todoEdit = document.querySelector(".todo-edit");

    const container = document.querySelector(".container");


    const navSections = document.querySelectorAll(".side-nav-section");
    const kanbasPrivate = navSections[0];
    const kanbasPublic = navSections[1];

    console.log(kanbasPublic);


    // Contient tout les todos.
    const todosData = [];

    /*
        Définitions des fonctions utiles.
     */

    const saveKanbaTitle = (id, newTitle) => {
        fetch(`/api/?q=kanba-edit&id=${id}&title=${encodeURI(addSlashes(newTitle))}`).then((r) => {
            popSnackbar();
            r.json().then(slug => {
                const e = Array     .from(document  .querySelectorAll(".side-nav-link"))
                                    .find(t => t.getAttribute('onclick').includes(`('${id}-`));
                e.setAttribute('onclick', `kanbaNav('${slug}');`);
                e.innerText = newTitle;
                history.pushState(slug, slug, `/${slug}`);
            });
        });
    };

    const saveKanbaPrivacy = (id, privacy) => {
        console.log(privacy);
        fetch(`/api/?q=kanba-edit&id=${id}&private=${!privacy}`).then(() => {
            popSnackbar();
            const e = Array     .from(document  .querySelectorAll(".side-nav-link"))
                                .find(t => t.getAttribute('onclick').includes(`('${id}-`));
            const n = e.parentElement.cloneNode(true);
            e.parentElement.remove();
            if (!privacy) {
                kanbasPrivate.appendChild(n);
            } else {
                kanbasPublic.appendChild(n);
            }
        });
    };

    const addSlashes = (string) => {
        return string.replace(/\\/g, '\\\\').replace(/\u0008/g, '\\b').replace(/\t/g, '\\t').replace(/\n/g, '\\n').replace(/\f/g, '\\f').replace(/\r/g, '\\r').replace(/'/g, '\\\'').replace(/"/g, '\\"');
    };

    const popSnackbar = () => {
        snackbar.classList.add("snackbar-visible");
        setTimeout(function () {
            snackbar.classList.remove("snackbar-visible");
        }, 5000);
    };

    const loadAllTodos = () => {
        todosData.length = 0;
    };

    const sideNavClose = () => {
        sideNav.classList.remove("side-nav-active");
        header.classList.remove("cloud");
        main.classList.remove("cloud");
    };

    const todoEditClose = () => {
        todoEdit.classList.add("not-visible");
        main.classList.remove("cloud");
        header.classList.remove("cloud");
        hamburger.classList.remove('no-control');
    };

    kanbaNav = (slug, force = false) => {
        if (force || history.state !== slug) {
            if (container) {
                container.remove();
            }
            document.body.focus();
            history.pushState(slug, slug, `/${slug}`);
            sideNavClose();
            const kanbaId = slug.split('-')[0];
            if (kanbaId !== "-1") {
                Kanba.load(kanbaId).then((kanba) => {
                    document.body.dataset.kanba = kanbaId;
                    main.querySelectorAll(".todo").forEach(t => t.parentElement.replaceChild(t.cloneNode(true), t));
                    const lists = main.querySelectorAll(".todo-list");
                    let oldLength = lists.length;
                    main.querySelectorAll(".todo-list").forEach(list => {
                        const t = ({target}) => {
                            oldLength--;
                            target.remove();
                            target.removeEventListener("transitionend", t);
                        };
                        list.addEventListener("transitionend", t);
                        list.classList.add("todo-list-not-visible");
                    });
                    headerTitle.innerText = kanba.title;
                    headerTitle.dataset.title = kanba.title;
                    headerTitle.classList.add("header-title");
                    headerTitle.data = kanba.title;
                    headerTitle.range = window.getSelection().getRangeAt(0).cloneRange();
                    inputMove.innerHTML = "";
                    const newTodos = [];
                    kanba.lists.forEach(l => {
                        const option = document.createElement("option");
                        option.innerText = l.title;
                        option.value = l.id;
                        inputMove.appendChild(option);
                        const div = document.createElement("div");
                        div.classList.add(
                            "todo-list",
                            "no-transition",
                            "todo-list-not-visible",
                        );
                        if (l.title === 'Terminées') {
                            div.classList.add("todo-list-end");
                        }
                        div.innerHTML = `
                        <h6>${l.title}</h6>
                        <ul>
                            ${l.todos.map(t =>
                            `<li>
                                <div class="todo" title="${t.description}" data-list-id="${l.id}" data-id="${t.id}">
                                    <span class="todo-title">${t.title}</span>
                                    <div class="todo-time">
                                    <i class="material-icons">access_time</i>
                                    <span>${t.date.split('-').reverse().join('/')}</span>
                                    <span>à</span>
                                    <span>${t.time}</span>
                                </div>
                            </li>`).join('')}
                            <li>
                                <div class="todo" data-is-new="y" data-list-id="${l.id}">
                                    <span class="todo-title">Nouvelle tâche</span>
                                    <div class="todo-time">
                                    <i class="material-icons">access_time</i>
                                    <span>04/09/1998</span>
                                    <span>à</span>
                                    <span>13:37</span>
                                </div>
                            </li>
                        </ul>
                    `;
                        main.appendChild(div);
                        div.offsetTop;
                        div.classList.remove("no-transition");
                        //div.classList.remove("todo-list-not-visible");
                    });

                    kanbaPrivate.dataset.checked = `${kanba.isPrivate}`;
                    kanbaPrivate.checked = kanba.isPrivate;

                    const appears = setInterval(() => {
                        if (oldLength === 0) {
                            document.querySelectorAll(".todo-list-not-visible")
                                    .forEach(list => list.classList.remove("todo-list-not-visible"));
                            addTodosListener();
                            clearInterval(appears);
                        }
                    }, 100);
                });
            }
        }
    };

    if (window.location.pathname.length !== 1) {
        kanbaNav(decodeURIComponent(window.location.pathname.substring(1)));
    }


    /*
        EV : Fermeture de la fenêtre d'édition d'une tâche
     */
    const close = todoEdit.querySelector("i");
    close.addEventListener("click", todoEditClose);

    const kanbaEdit = document.querySelector(".kanba-edit");
    const kanbaPrivate = kanbaEdit.querySelector("input[type=checkbox");

    /*
        EV : On clique sur la checkbox pour la portée du Kanba
     */
    kanbaPrivate.addEventListener("change", () => {
        if (kanbaPrivate.dataset.checked === `${kanbaPrivate.checked}`) {
            main.classList.remove("cloud", "kanba-editing");
        } else {
            main.classList.add("cloud", "kanba-editing");
        }
    });
    main.addEventListener("click", () => {
        if (main.classList.contains("kanba-editing")) {
            saveKanbaPrivacy(document.body.dataset.kanba, kanbaPrivate.checked);
            main.classList.remove("kanba-editing", "cloud");
            kanbaPrivate.dataset.checked = `${kanbaPrivate.checked}`;
            popSnackbar();
        }
    });
    const btns = todoEdit.querySelectorAll("button");
    const btnSend = btns[0];
    const btnRemove = btns[2];

    const inputs = todoEdit.querySelectorAll("input");
    const inputTitle = inputs[0];
    const inputDate = inputs[1];
    const inputTime = inputs[2];
    const inputMove = todoEdit.querySelector("select");
    const inputDescription = todoEdit.querySelector("textarea");

    /*
        EV : Edition du titre du kanba
     */
    headerTitle.addEventListener('input', () => {
        const o = window.getSelection().getRangeAt(0).startOffset - 1;
        if (headerTitle.innerText.includes('\n')) {
            headerTitle.innerText = headerTitle.data;
            window.getSelection().removeAllRanges();
            const r = document.createRange();
            r.setStart(headerTitle, o);
            r.collapse(true);
            window.getSelection().addRange(r);
            headerTitle.blur();
        } else {
            headerTitle.data = headerTitle.innerText;
        }
    });
    headerTitle.addEventListener("focus", () => {
        main.classList.add("cloud");
    });
    // Sauvegarde du nouveau titre
    headerTitle.addEventListener("focusout", () => {
        main.classList.remove("cloud");
        if (headerTitle.innerText.length !== 0 && headerTitle.dataset.title !== headerTitle.innerText) {
            saveKanbaTitle(document.body.dataset.kanba, headerTitle.innerText);
            headerTitle.dataset.title = headerTitle.innerText;
        } else {
            headerTitle.innerText = headerTitle.dataset.title;
        }
    });
    /*
        EV : Envoie de la modification d'une tâche
     */
    btnSend.addEventListener("click", () => {
        if (!todoEdit.classList.contains("not-visible")) {
            const data = new FormData();
            const isNew = todoEdit.dataset.isNew === 'y';
            data.append('q', 'todo-edit');
            if (isNew | inputTitle.value !== inputTitle.defaultValue) {
                data.append('title', addSlashes(inputTitle.value));
            }
            if (isNew | inputDate.value !== inputDate.defaultValue) {
                data.append('date', addSlashes(inputDate.value));
            }
            if (isNew | inputTime.value !== inputTime.defaultValue) {
                data.append('time', addSlashes(inputTime.value));
            }
            if (isNew | inputMove.value !== inputMove.dataset.defaultValue) {
                data.append('move', addSlashes(inputMove.value));
            }
            if (isNew | inputDescription.value !== inputDescription.defaultValue) {
                data.append('description', addSlashes(inputDescription.value));
            }
            if (isNew) {
                data.append("new", "true");
            }
            data.append("list_id", todoEdit.dataset.listId);
            data.append("id", todoEdit.dataset.id);
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "/api/");
            xhr.addEventListener("readystatechange", () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    todoEditClose();
                    kanbaNav(history.state, true);
                    popSnackbar();
                }
            });
            xhr.send(data);
        }
    });

    /*
        EV : Suppression de la tâche
     */
    btnRemove.addEventListener("click", () => {
        fetch(`/api/?q=todo-remove&id=${todoEdit.dataset.id}`).then(() => {
            todoEditClose();
            kanbaNav(history.state, true);
            popSnackbar();
        });
    });

    const addTodosListener = () => {
        const todos = document.querySelectorAll(".todo");
        document.querySelectorAll(".todo").forEach(todo => {

            /*
                EV : Apparition de la fenetre d'édition pour chaque tâche
             */
            todo.addEventListener("click", ({target}) => {
                let t = target;
                while (!t.classList.contains("todo")) {
                    t = t.parentElement;
                }

                const spans = t.querySelectorAll("span");
                const title = spans[0];
                const date = spans[1];
                const time = spans[3];

                const dC = (e) => {
                    e.preventDefault();
                };

                const rect = t.getBoundingClientRect();
                if (todoEdit.classList.contains("not-visible")) {
                    todoEdit.dataset.listId = t.dataset.listId;
                    todoEdit.dataset.id = t.dataset.id;
                    todoEdit.dataset.isNew = t.dataset.isNew;
                    inputTitle.defaultValue = title.innerText;
                    inputDate.defaultValue = date.innerText.split('/').reverse().join('-');
                    inputTime.defaultValue = time.innerText;
                    inputMove.dataset.defaultValue = t.dataset.listId;
                    inputDescription.defaultValue = t.title;

                    todoEdit.querySelector("form").reset();

                    todoEdit.classList.add("no-transition");

                    const style = document.createElement("style");
                    style.type = "text/css";
                    style.appendChild(document.createTextNode(
                        `.not-visible { \n
                            top: ${rect.top + (rect.height / 2)}px;\n
                            left: ${rect.left + (rect.width / 2)}px;\n
                        }\n`
                    ));
                    document.body.appendChild(style);
                    // just redraw
                    todoEdit.offsetTop;

                    todoEdit.classList.remove("no-transition");
                    todoEdit.classList.remove("not-visible");

                    main.classList.add("cloud");
                    header.classList.add("cloud");
                    hamburger.classList.add("no-control");

                    // Il faut attendre l'affichage avant de changer la valeur, sinon pas de modification...
                    inputMove.value = `${t.dataset.listId}`;
                }
            });
        });
    };
    addTodosListener();
});