import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    this.element.addEventListener("change", this.redirectToTagSearch.bind(this));
  }

  redirectToTagSearch(event) {
    const tagName = event.target.value;
    if (tagName) {
      window.location.href = '/search/tag/' + encodeURIComponent(tagName);
    }
  }
}
