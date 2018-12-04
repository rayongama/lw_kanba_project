document.addEventListener("DOMContentLoaded", () => {

    const header = document.querySelector("header");
    const main = document.querySelector("main");

    for (let i of document.querySelectorAll("input[type=text], input[type=password], input[type=date], input[type=time]")) {

        i.addEventListener("input", ({target}) => {
            target.parentElement.classList.remove("error");
           if (target.value === "") {
               target.parentElement.classList.remove("not-empty");
           } else {
               target.parentElement.classList.add("not-empty");
           }
        });

        i.addEventListener("focus", ({target}) => {
            target.parentElement.classList.remove("error");
            target.parentElement.classList.add("focus");
        });
        i.addEventListener("focusout", ({target}) => {
            target.parentElement.classList.remove("focus");
        })

    }

    for (let i of document.querySelectorAll(".input")) {
        i.addEventListener("click", () => {
            if (i.classList.contains("no-control")) {
                i.classList.add("focus");
            } else {
                i.children[0].dispatchEvent(new Event("focus"));
                i.children[0].dispatchEvent(new Event("click"));
                i.children[0].focus();
            }
        })
    }

    const hamburger = document.querySelector(".hamburger");
    const sideNav = document.querySelector(".side-nav");
    if (hamburger && sideNav) {
        hamburger.addEventListener("click", (e) => {
            if (!hamburger.classList.contains("no-control")) {
                e.stopPropagation();
                sideNav.classList.add("side-nav-active");
                if (header)
                    header.classList.add("cloud");
                if (main)
                    main.classList.add("cloud");

                const stop = (e) => {
                    e.stopPropagation();
                };

                sideNav.addEventListener("click", stop);

                function doc() {
                    sideNav.classList.remove("side-nav-active");
                    if (header)
                        header.classList.remove("cloud");
                    if (main)
                        main.classList.remove("cloud");
                    sideNav.removeEventListener("click", stop);
                    document.removeEventListener("click", doc)
                }

                document.addEventListener("click", doc);
            }
        });
    }

});