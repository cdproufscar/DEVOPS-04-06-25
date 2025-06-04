document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll("input[name='secoes[]']");
    const sections = document.querySelectorAll(".section");

    // Oculta todas as seções e desativa campos inicialmente
    sections.forEach(section => {
        section.style.display = "none";
        section.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.hasAttribute('required')) {
                el.dataset.originalRequired = "true";
                el.removeAttribute('required');
            }
            el.setAttribute('disabled', 'disabled');
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const sectionId = `seção-${this.id}`;
            const section = document.getElementById(sectionId);
            if (section) {
                if (this.checked) {
                    section.style.display = 'block';
                    section.querySelectorAll('input, select, textarea').forEach(el => {
                        el.removeAttribute('disabled');
                        if (el.dataset.originalRequired === "true") {
                            el.setAttribute('required', 'required');
                        }
                    });
                } else {
                    section.style.display = 'none';
                    section.querySelectorAll('input, select, textarea').forEach(el => {
                        if (el.hasAttribute('required')) {
                            el.dataset.originalRequired = "true";
                            el.removeAttribute('required');
                        }
                        el.setAttribute('disabled', 'disabled');
                    });
                }
            }
        });
    });

    // Validação do formulário antes do envio
    document.getElementById("formCadastro").addEventListener("submit", function (event) {
        const email = document.getElementById("email").value;
        const confirmarEmail = document.getElementById("confirmar-email").value.trim();
        const senha = document.getElementById("senha").value;
        const confirmarSenha = document.getElementById("confirmar-senha").value.trim();
        const checkboxesMarcados = document.querySelectorAll('input[name="secoes[]"]:checked').length;

        if (email !== confirmarEmail) {
            event.preventDefault();
            Swal.fire("Erro", "Os e-mails não coincidem.", "error");
        } else if (senha !== confirmarSenha) {
            event.preventDefault();
            Swal.fire("Erro", "As senhas não coincidem.", "error");
        } else if (checkboxesMarcados === 0) {
            event.preventDefault();
            Swal.fire("Erro", "Selecione pelo menos uma classificação.", "error");
        }
    });
});
