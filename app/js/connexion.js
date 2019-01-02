document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");
    const container = document.querySelector(".container");
    const subtitle1 = document.querySelector(".subtitle1");

    document.querySelector("button").addEventListener("click", () => {
        const username = document.getElementsByName("username")[0];
        const password_raw = document.getElementsByName("password")[0];

        if (username.value === "") {
            username.parentElement.classList.add("error");
        }
        if (password_raw.value === "") {
            password_raw.parentElement.classList.add("error");
        }

        const password = sjcl.codec.hex.fromBits(sjcl.hash.sha256.hash(password_raw.value));
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `/api/?q=user&username=${username.value}&password=${password}`);
        xhr.addEventListener("readystatechange", () => {
            if (xhr.readyState === 4) {
                if (xhr.status !== 200) {
                    container.classList.add("error");
                } else {
                    console.log(xhr.response);
                    const r = JSON.parse(xhr.response);
                    if (r === true) {
                        password_raw.value = password;
                        form.submit();
                    } else {
                        subtitle1.innerText = "Identifiants invalides"
                    }
                }
            }
        });
        xhr.send();

    });
});