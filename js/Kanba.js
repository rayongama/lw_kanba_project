class Kanba {

    constructor(title, lists, p) {
        this._title = title;
        this._lists = lists;
        this._private = p;
    }

    static load(kanbaId) {
        const that = this;
        return new Promise((resolve) => {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `/api/?q=kanba&id=${kanbaId}`);
            xhr.addEventListener("readystatechange", () => {
                if (xhr.readyState === 4) {
                    const raw = JSON.parse(xhr.response);
                    resolve(new Kanba(raw.title, raw.lists, raw.private));
                }
            });
            xhr.send();
        })
    }

    static loadById(id) {
        return fetch(`/api/?q=kanba&id=${id}`);
    }

    get title() {
        return this._title;
    }

    get lists() {
        return this._lists;
    }

    get isPrivate() {
        return !this._private;
    }

}