class Todo {

    /**
     * Construit une nouvelle tâche
     * @param {Number|null} id
     * @param {Number|null} listId
     * @param {String|null} title
     * @param {String|null} description
     * @param {String|null} date
     * @param {String|null} time
     */
    constructor({
                    id = null,
                    listId = null,
                    title = null,
                    description = null,
                    date = null,
                    time = null,
                }) {

        this._id = id;
        this._listId = listId;
        this._title = title;
        this._description = description;
        this._date = date;
        this._time = time;
    }


    get title() {
        return this._title;
    }

    get description() {
        return this._description;
    }

    get date() {
        // TODO: Formater la date
        return this._date;
    }

    get time() {
        // TODO: Formater le temps
        return this._time;
    }

    get HTML() {
        return `
        <div class="todo">
          <span class="todo-title">${this.title}</span>
          <div class="todo-time">
            <i class="material-icons">access_time</i>
            <span>${this.date}</span>
            <span>à</span>
            <span>${this.time}</span>
          </div>
        </div>
        `
    }

    attachListener(element) {
        element.addEventListener("click", this.event());
    }

    removeListener(element) {

    }

    event() {
        console.log(this);
    }
}
