// Слайдер
class SimpleSlider {
    constructor(selector) {
        this.container = document.querySelector(selector);
        if (this.container) this.init();
    }

    init() {
        // Логика слайдера
        console.log('Slider initialized');
    }
}
