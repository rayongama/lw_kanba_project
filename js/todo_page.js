let kanbaNav;

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

    // Contient tout les todos.
    const todosData = [];

    /*
        Définitions des fonctions utiles.
     */

    const loadAllTodos = () => {
        todosData.length = 0;
    };

    const sideNavClose = () =>  {
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
        if (force || history.state !==  slug) {
            history.pushState(slug, slug, `/${slug}`);
            sideNavClose();
            const kanbaId = slug.split('-')[0];
            if (kanbaId !== "-1") {
                Kanba.load(kanbaId).then((kanba) => {
                    const lists = main.querySelectorAll(".todo-list");
                    let oldLength = lists.length;
                    for (let list of lists) {
                        const t =({target}) => {oldLength--; target.remove(); target.removeEventListener("transitionend", t);};
                        list.addEventListener("transitionend", t);
                        list.classList.add("todo-list-not-visible");
                    }
                    headerTitle.innerText = kanba.title;
                    inputMove.innerHTML = "";
                    kanba.lists.forEach(l => {
                        const option = document.createElement("option");
                        option.innerText = l.title;
                        inputMove.appendChild(option);
                        const div = document.createElement("div");
                        div.classList.add("todo-list", "no-transition", "todo-list-not-visible");
                        div.innerHTML = `
                        <h6>${l.title}</h6>
                        <ul>
                            ${l.todos.map(t =>
                            `<li>
                                <div class="todo" data-list-id="${l.id}" data-id="${t.id}">
                                    <span class="todo-title">${t.title}</span>
                                    <div class="todo-time">
                                    <i class="material-icons">access_time</i>
                                    <span>${t.date}</span>
                                    <span>à</span>
                                    <span>${t.time}</span>
                                </div>
                            </li>`).join('')}
                            <li>
                                <div class="todo" data-new="y" data-list-id="${l.id}">
                                    <span class="todo-title">Nouvelle tâche</span>
                                    <div class="todo-time">
                                    <i class="material-icons">access_time</i>
                                    <span>??/??/??</span>
                                    <span>à</span>
                                    <span>??:??</span>
                                </div>
                            </li>
                        </ul>
                    `;
                        main.appendChild(div);
                        div.offsetTop;
                        div.classList.remove("no-transition");
                        //div.classList.remove("todo-list-not-visible");
                    });

                    const appears = setInterval(() => {
                        if (oldLength === 0) {
                            const lists = document.querySelectorAll(".todo-list-not-visible");
                            for (let list of lists) {
                                list.classList.remove("todo-list-not-visible");
                            }
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

    const btns = todoEdit.querySelectorAll("button");
    const btnSend = btns[0];
    const btnRemove = btns[2];

    const inputs = todoEdit.querySelectorAll("input");
    const inputTitle = inputs[0];
    const inputDate = inputs[1];
    const inputTime = inputs[2];
    const inputMove = todoEdit.querySelector("select");

    /*
        EV : Envoie de la modification d'une tâche
     */
    btnSend.addEventListener("click", () => {
        if (!todoEdit.classList.contains("not-visible")) {
            const data = new FormData();
            data.append('q', 'todo-edit');
            if (inputTitle.value !== "" && inputTitle.value !== inputTitle.defaultValue) {
                data.append('title', inputTitle.value);
            }
            if (inputDate.value !== "" && inputDate.value !== inputDate.defaultValue) {
                data.append('date', inputDate.value);
            }
            if (inputTime.value !== "" && inputTime.value !== inputTime.defaultValue) {
                data.append('time', inputTime.value);
            }
            console.log(inputMove.selectedIndex, inputMove.getAttribute("data-defaultvalue"))
            if (inputMove.selectedIndex !== -1
                && `${inputMove.selectedIndex + 1}` !== inputMove.getAttribute("data-defaultvalue")) {
                data.append('move', inputMove.selectedIndex + 1);
            }
            if (todoEdit.getAttribute("data-new") === "y") {
                data.append("new", "true");
            }
            data.append("list_id", todoEdit.getAttribute("data-list-id"));
            data.append("id", todoEdit.getAttribute("data-id"));
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "/api/");
            xhr.addEventListener("readystatechange", () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    todoEditClose();
                    kanbaNav(history.state, true);
                    snackbar.classList.add("snackbar-visible");
                    setTimeout(function () {
                        snackbar.classList.remove("snackbar-visible");
                    }, 5000);
                }
            });
            xhr.send(data);
        }


    });

    const addTodosListener = () => {
        const todos = document.querySelectorAll(".todo");
        for (let todo of todos) {

            const spans = todo.querySelectorAll("span");
            const title = spans[0];
            const date = spans[1];
            const time = spans[3];
            /*
                EV : Apparition de la fenetre d'édition pour chaque tâche
             */
            todo.addEventListener("click", ({target}) => {
                const dC = (e) => {
                    e.preventDefault();
                };

                const rect = todo.getBoundingClientRect();
                if (todoEdit.classList.contains("not-visible")) {
                    if (todo.getAttribute("data-new") === "y") {
                        todoEdit.setAttribute("data-new", "y");
                    } else {
                        todoEdit.setAttribute("data-new", "n");
                    }
                    todoEdit.setAttribute("data-id", todo.getAttribute("data-id"));
                    todoEdit.setAttribute("data-list-id", todo.getAttribute("data-list-id"));
                    inputTitle.defaultValue = title.innerText;
                    inputDate.defaultValue = date.innerText.split('/').reverse().join('-');
                    inputTime.defaultValue = time.innerText;
                    inputMove.setAttribute("data-defaultvalue", todo.getAttribute("data-list-id"));
                    inputMove.selectedIndex = todo.getAttribute("data-list-id") - 1;

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
                }
            });
        }
    };
    addTodosListener();
});