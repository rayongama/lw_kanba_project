class Kanba {

    constructor(title, lists) {
        this._title = title;
        this._lists = lists;
    }

    static load(kanbaId) {
        const that = this;
        return new Promise((resolve) => {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `/api/?q=kanba&id=${kanbaId}`);
            xhr.addEventListener("readystatechange", () => {
                if (xhr.readyState === 4) {
                    const raw = JSON.parse(xhr.response);
                    resolve(new Kanba(raw.title, raw.lists));
                }
            });
            xhr.send();
        })
    }

    get title() {
      return this._title;
    }

    get lists() {
        return this._lists;
    }

}