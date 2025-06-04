document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modal-edicao");
  const form = document.getElementById("form-edicao");
  const checkboxes = form.querySelectorAll("input[name='secoes[]']");
  const inputFoto = form.querySelector("input[name='foto']");
  const avatarPreview = document.querySelector(".perfil-img");

  window.abrirModalEdicao = () => {
    modal.style.display = "flex";
    modal.classList.add("fade-in");
    atualizarSecoes();
  };

  window.fecharModalEdicao = () => {
    if (!modal || modal.style.display === "none") return;
    modal.classList.remove("fade-in");
    modal.classList.add("fade-out");
    setTimeout(() => {
      modal.style.display = "none";
      modal.classList.remove("fade-out");
    }, 300);
  };

  window.addEventListener("click", (e) => {
    if (e.target === modal) fecharModalEdicao();
  });

  window.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && modal.style.display === "flex") {
    fecharModalEdicao();
  }
});

  inputFoto.addEventListener("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = (e) => {
        avatarPreview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });

  const dados = {
    "PCD": {
      deficiencia: form.dataset.pcd_deficiencia || "",
      limitacoes: form.dataset.pcd_limitacoes || ""
    },
    "MAKER": { formacao: form.dataset.maker_formacao || "" },
    "FAMILIAR": {
      relacao: form.dataset.familiar_relacao || "",
      tipo: form.dataset.familiar_deficiencia || "",
      descricao: form.dataset.familiar_descricao || ""
    },
    "ESPECIALISTA DA SAÚDE": { formacao: form.dataset.especialista_formacao || "" },
    "FORNECEDOR": { atuacao: form.dataset.fornecedor_atuacao || "" }
  };

  const secoesHtml = {
    "PCD": (d = {}) => `
      <div class="secao-box" id="secao-PCD">
        <h4>Seção PCD</h4>
        <label>Tipo de Deficiência:</label>
        <select name="pcd_deficiencia">
          ${["mobilidade", "visual", "auditiva", "intelectual"].map(opt =>
            `<option value="${opt}" ${d.deficiencia === opt ? "selected" : ""}>${opt}</option>`).join("")}
        </select>
        <label>Descrição das limitações:</label>
        <textarea name="pcd_limitações">${d.limitacoes || ""}</textarea>
      </div>`,
    "MAKER": (d = {}) => `
      <div class="secao-box" id="secao-MAKER">
        <h4>Seção Maker</h4>
        <label>Formação Acadêmica:</label>
        <input type="text" name="maker_projetista_formacao" value="${d.formacao || ""}">
      </div>`,
    "FAMILIAR": (d = {}) => `
      <div class="secao-box" id="secao-FAMILIAR">
        <h4>Seção Familiar</h4>
        <label>Relação com a Pessoa:</label>
        <input type="text" name="familiar_relacao" value="${d.relacao || ""}">
        <label>Tipo de Deficiência:</label>
        <select name="familiar_deficiencia">
          ${["mobilidade", "visual", "auditiva", "intelectual", "múltipla", "outro"].map(opt =>
            `<option value="${opt}" ${d.tipo === opt ? "selected" : ""}>${opt}</option>`).join("")}
        </select>
        <label>Descrição:</label>
        <textarea name="descricao_deficiencia_familiar">${d.descricao || ""}</textarea>
      </div>`,
    "ESPECIALISTA DA SAÚDE": (d = {}) => `
      <div class="secao-box" id="secao-ESPECIALISTA">
        <h4>Seção Especialista da Saúde</h4>
        <label>Formação Acadêmica:</label>
        <input type="text" name="especialista_saude_formacao" value="${d.formacao || ""}">
      </div>`,
    "FORNECEDOR": (d = {}) => `
      <div class="secao-box" id="secao-FORNECEDOR">
        <h4>Seção Fornecedor</h4>
        <label>Áreas de Atuação:</label>
        <input type="text" name="fornecedor_atuacao" value="${d.atuacao || ""}">
      </div>`
  };

  function atualizarSecoes() {
    const tabsNav = document.getElementById("tabs-nav");
    const tabsContent = document.getElementById("tabs-content");

    tabsNav.innerHTML = "";
    tabsContent.innerHTML = "";

    const selecionadas = [...checkboxes].filter(cb => cb.checked);

    if (selecionadas.length === 0) {
      tabsContent.innerHTML = `<p class="tab-alert">Nenhuma classificação selecionada.</p>`;
      return;
    }

    selecionadas.forEach((cb, i) => {
      const secao = cb.value;
      const tabId = `tab-${secao.replace(/\s+/g, '-')}`;
      const icones = {
        "PCD": "♿", "MAKER": "🛠️", "FAMILIAR": "👨‍👩‍👧", "ESPECIALISTA DA SAÚDE": "🩺", "FORNECEDOR": "🏭"
      };

      const tabButton = document.createElement("button");
      tabButton.type = "button"; // Impede submissão do form
      tabButton.className = "tab-button";
      tabButton.innerHTML = `<span class="tab-icon">${icones[secao] || "📁"}</span><span class="tab-label">${secao}</span>`;
      tabButton.setAttribute("data-tab", tabId);

      if (i === 0) tabButton.classList.add("active");
      tabsNav.appendChild(tabButton);

      const tabPane = document.createElement("div");
      tabPane.className = "tab-pane";
      tabPane.id = tabId;
      tabPane.innerHTML = secoesHtml[secao] ? secoesHtml[secao](dados[secao] || {}) : '';
      tabPane.style.display = i === 0 ? "block" : "none";
      tabsContent.appendChild(tabPane);
    });

    document.querySelectorAll(".tab-button").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".tab-button").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".tab-pane").forEach(p => p.style.display = "none");
        btn.classList.add("active");
        const target = btn.getAttribute("data-tab");
        document.getElementById(target).style.display = "block";
      });
    });
  }

  checkboxes.forEach(cb => cb.addEventListener("change", atualizarSecoes));
  atualizarSecoes();

  // AJAX envio
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    if (inputFoto.files.length > 0) {
      formData.append("foto", inputFoto.files[0]);
    }
  
    fetch("atualiza_perfil_ajax.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
         // Oculta o modal sem esperar animação
          modal.style.display = "none";

          // Agora mostra o SweetAlert imediatamente
          Swal.fire({
            title: "Sucesso!",
            text: data.mensagem,
            icon: "success",
            allowOutsideClick: false
          }).then(() => {
            window.location.reload();
          });

        } else {
          Swal.fire("Erro", data.mensagem, "error");
        }
      })
      .catch(() => {
        Swal.fire("Erro", "Erro ao salvar o perfil. Tente novamente.", "error");
      });
  });
  
});
