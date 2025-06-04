document.addEventListener("DOMContentLoaded", () => {
    const btnVoltar = document.getElementById("btn-voltar");
    if (btnVoltar) {
        btnVoltar.addEventListener("click", () => {
            window.history.back();
        });
    }
});

