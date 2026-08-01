function confirmDelete(e) {
    return confirm(e || "Вы уверены, что хотите удалить?")
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("Admin-panel initialized");
    var e = document.querySelector(".sidebar-toggle");
    e && e.addEventListener("click", function () {
        document.body.classList.toggle("sidebar-collapsed")
    })
});
