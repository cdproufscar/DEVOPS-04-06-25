document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  form.addEventListener("submit", function (e) {
    let mensagensErro = [];
    let primeiroCampoErro = null;

    const campoNome = form.querySelector("input[name='nome']");
    const campoDescricao = form.querySelector("textarea[name='descricao']");

    // Verificações principais
    if (!campoNome.value.trim()) {
      mensagensErro.push("⚠️ O campo 'Nome' é obrigatório.");
      if (!primeiroCampoErro) primeiroCampoErro = campoNome;
    }

    if (!campoDescricao.value.trim()) {
      mensagensErro.push("⚠️ O campo 'Descrição' é obrigatório.");
      if (!primeiroCampoErro) primeiroCampoErro = campoDescricao;
    }

    // Verificações nos materiais
    form.querySelectorAll("fieldset").forEach(field => {
      const qtd = field.querySelector("input[name*='[quantidade]']");
      const unid = field.querySelector("input[name*='[unidade]']");

      if (qtd && isNaN(parseFloat(qtd.value))) {
        mensagensErro.push("❌ Quantidade inválida em um dos materiais.");
        if (!primeiroCampoErro) primeiroCampoErro = qtd;
      }

      if (unid && !unid.value.trim()) {
        mensagensErro.push("❌ Unidade de medida ausente em um dos materiais.");
        if (!primeiroCampoErro) primeiroCampoErro = unid;
      }
    });

    // Se houver erros, impedir envio
    if (mensagensErro.length > 0) {
      e.preventDefault();
      alert(mensagensErro.join("\n"));
      if (primeiroCampoErro) primeiroCampoErro.focus();
      return false;
    }
  });

  // Realce elegante nos campos com foco
  document.querySelectorAll("input, textarea").forEach(el => {
    el.addEventListener("focus", () => {
      el.classList.add("input-focus");
    });
    el.addEventListener("blur", () => {
      el.classList.remove("input-focus");
    });
  });
});
