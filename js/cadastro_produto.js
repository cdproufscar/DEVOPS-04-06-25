document.addEventListener("DOMContentLoaded", () => {
  renderizaComponentes();
});

// Abrir nova aba para cadastro de componente
function abrirCadastroComponente() {
  window.open("cadastro_componente.php", "_blank");
}

// Modal genérico
function abrirModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add("show");
}

function fecharModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove("show");
}

// Modal específico do processo de montagem
function abrirModalMontagem() {
  atualizarOpcoesComponentesMontagem();
  abrirModal("modalMontagem");
}

// Torna a função global para o HTML reconhecer
window.abrirModalMontagem = abrirModalMontagem;

// Atualiza opções do modal com os componentes
function atualizarOpcoesComponentesMontagem() {
  const lista = document.getElementById("montagem_componentes");
  const compInput = document.getElementById("componentes_temp_php");

  if (!lista || !compInput) return;

  lista.innerHTML = "";
  const componentes = JSON.parse(compInput.value || "[]");

  componentes.forEach(comp => {
    const label = document.createElement("label");
    label.innerHTML = `<input type="checkbox" value="${comp.id}"> ${comp.nome}`;
    lista.appendChild(label);
  });
}

// Renderiza a tabela e campo hidden de componentes
function renderizaComponentes() {
  const compInput = document.getElementById("componentes_temp_php");
  const tbody = document.getElementById("componentes-lista");
  const inputHidden = document.getElementById("componentes_selecionados");

  if (!compInput || !tbody || !inputHidden) return;

  const componentes = JSON.parse(compInput.value || "[]");
  tbody.innerHTML = "";
  const ids = [];

  componentes.forEach((comp, i) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${comp.nome}</td>
      <td>${comp.descricao.slice(0, 60)}...</td>
      <td><a href="visualizar_componente_temp.php?id=${comp.id}" target="_blank">Ver</a></td>
      <td><a href="editar_componente_temp.php?id=${comp.id}">✎</a></td>
      <td><a href="excluir_componente_temp.php?id=${comp.id}" onclick="return confirm('Deseja excluir este componente?')">🗑</a></td>
    `;
    tbody.appendChild(tr);
    ids.push(comp.id);
  });

  inputHidden.value = JSON.stringify(ids);
}

// Adiciona um processo de montagem
function adicionarProcessoMontagem() {
  const descricao = document.getElementById("descricao_montagem").value.trim();
  const midias = document.getElementById("midias_montagem").files;
  const arquivos = document.getElementById("arquivos_montagem").files;
  const checkboxes = document.querySelectorAll("#montagem_componentes input:checked");

  if (!descricao || checkboxes.length === 0) {
    alert("Preencha a descrição e selecione pelo menos um componente.");
    return;
  }

  const nomes = [], ids = [];
  checkboxes.forEach(cb => {
    nomes.push(cb.parentElement.textContent.trim());
    ids.push(cb.value);
  });

  const linha = document.createElement("tr");
  linha.innerHTML = `
    <td>${contagemMontagem++}</td>
    <td>${nomes.join(", ")}</td>
    <td>${descricao}</td>
    <td>${midias.length || arquivos.length ? '📷📎' : '—'}</td>
    <td><button type="button" onclick="this.closest('tr').remove()">🗑</button></td>
  `;
  document.getElementById("listaMontagem").appendChild(linha);

  const form = document.querySelector("form");
  const id = Date.now();

  // Campos ocultos para envio no form
  form.insertAdjacentHTML("beforeend", `
    <input type="hidden" name="montagem[${id}][descricao]" value="${descricao}">
    ${ids.map(cid => `<input type="hidden" name="montagem[${id}][componentes][]" value="${cid}">`).join("")}
  `);

  // Mídias
  for (const file of midias) {
    const input = document.createElement("input");
    input.type = "file";
    input.name = `montagem_${id}[midias][]`;
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    form.appendChild(input);
  }

  // Arquivos
  for (const file of arquivos) {
    const input = document.createElement("input");
    input.type = "file";
    input.name = `montagem_${id}[arquivos][]`;
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    form.appendChild(input);
  }

  // Reset dos campos
  document.getElementById("descricao_montagem").value = "";
  document.getElementById("midias_montagem").value = "";
  document.getElementById("arquivos_montagem").value = "";
  checkboxes.forEach(cb => cb.checked = false);

  fecharModal("modalMontagem");
}

// Contador global
let contagemMontagem = 1;
