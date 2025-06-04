function confirmarExclusaoComponente(id) {
    Swal.fire({
        title: "Excluir Componente?",
        text: "Você tem certeza que deseja excluir este componente temporário?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "visualizar_componente_temp.php?excluir_id=" + id;
        }
    });
}
