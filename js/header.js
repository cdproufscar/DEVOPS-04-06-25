document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logoutBtn");
  
    if (!logoutBtn) {
      console.warn("Botão de logout não encontrado.");
      return;
    }
  
    // Evita múltiplos bindings
    if (!logoutBtn.dataset.bound) {
      logoutBtn.dataset.bound = "true"; // flag para evitar rebind
  
      logoutBtn.addEventListener("click", function () {
        Swal.fire({
          title: "Tem certeza?",
          text: "Você será deslogado da sua conta.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Sim, sair!",
          cancelButtonText: "Cancelar"
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "logout.php";
          }
        });
      });
    }
  });
  