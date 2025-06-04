// cadastro_componente.js

// Abrir modal genérico
function abrirModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('show');
}

// Fechar modal genérico
function fecharModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove('show');
}

// Atualizar os checkboxes de Materiais & Ferramentas no modal de Passo-a-Passo
function atualizarOpcoesPasso() {
  const materiais = document.querySelectorAll("#listaMateriais tr");
  const ferramentas = document.querySelectorAll("#listaFerramentas tr");

  const boxMat = document.getElementById("passo_materiais");
  const boxFer = document.getElementById("passo_ferramentas");

  boxMat.innerHTML = "";
  boxFer.innerHTML = "";

  materiais.forEach(row => {
    const nome = row.children[1]?.textContent;
    const inputHidden = row.querySelector("input[name^='materiais']");
    const idHidden = inputHidden?.value;

    if (nome && idHidden) {
      boxMat.innerHTML += `<label><input type="checkbox" value="${idHidden}"> ${nome}</label>`;
    }
  });

  ferramentas.forEach(row => {
    const nome = row.children[1]?.textContent;
    const inputHidden = row.querySelector("input[name^='ferramentas']");
    const idHidden = inputHidden?.value;

    if (nome && idHidden) {
      boxFer.innerHTML += `<label><input type="checkbox" value="${idHidden}"> ${nome}</label>`;
    }
  });
}

// Reorganiza numeração de Materiais na tabela
function atualizarNumeracaoMateriais() {
  document.querySelectorAll("#listaMateriais tr").forEach((tr, i) => {
    tr.children[0].textContent = "Material " + (i + 1);
  });
}

// Reorganiza numeração de Ferramentas na tabela
function atualizarNumeracaoFerramentas() {
  document.querySelectorAll("#listaFerramentas tr").forEach((tr, i) => {
    tr.children[0].textContent = "Ferramenta " + (i + 1);
  });
}

// ===== Adicionar Material =====
function adicionarMaterial() {
  const select = document.getElementById("select_material");
  const nome = select.options[select.selectedIndex]?.text;
  const idMaterial = select.value;
  const descricao = select.options[select.selectedIndex]?.dataset.descricao || "";
  const quantidade = document.getElementById("qtd_material").value;
  const unidade = document.getElementById("unidade_material").value;
  const observacao = document.getElementById("obs_material").value;

  if (!idMaterial || !quantidade || !unidade) {
    alert("Preencha todos os campos do material.");
    return;
  }

  const id = Date.now();
  const tabela = document.getElementById("listaMateriais");

  const linha = document.createElement("tr");
  linha.innerHTML = `
    <td></td>
    <td>${nome}</td>
    <td>${quantidade}</td>
    <td>${unidade}</td>
    <td>${descricao}</td>
    <td>
      <button type="button" onclick="this.closest('tr').remove(); atualizarNumeracaoMateriais(); atualizarOpcoesPasso();">🗑</button>
    </td>
    <input type="hidden" name="materiais[${id}][id]" value="${idMaterial}">
    <input type="hidden" name="materiais[${id}][quantidade]" value="${quantidade}">
    <input type="hidden" name="materiais[${id}][unidade]" value="${unidade}">
    <input type="hidden" name="materiais[${id}][observacao]" value="${observacao}">
  `;
  tabela.appendChild(linha);

  // Atualiza legenda e checkbox de Passo-a-Passo
  atualizarNumeracaoMateriais();
  atualizarOpcoesPasso();

  // Reset nos campos
  select.selectedIndex = 0;
  document.getElementById("qtd_material").value = 1;
  document.getElementById("unidade_material").selectedIndex = 0;
  document.getElementById("obs_material").value = "";

  fecharModal("modalMaterial");
}

// ===== Adicionar Ferramenta =====
function adicionarFerramenta() {
  const select = document.getElementById("select_ferramenta");
  const nome = select.options[select.selectedIndex]?.text;
  const idFerramenta = select.value;
  const descricao = select.options[select.selectedIndex]?.dataset.descricao || "";
  const observacao = document.getElementById("obs_ferramenta").value;

  if (!idFerramenta || !observacao) {
    alert("Preencha todos os campos da ferramenta.");
    return;
  }

  const id = Date.now();
  const tabela = document.getElementById("listaFerramentas");

  const linha = document.createElement("tr");
  linha.innerHTML = `
    <td></td>
    <td>${nome}</td>
    <td>${observacao}</td>
    <td>${descricao}</td>
    <td>
      <button type="button" onclick="this.closest('tr').remove(); atualizarNumeracaoFerramentas(); atualizarOpcoesPasso();">🗑</button>
    </td>
    <input type="hidden" name="ferramentas[${id}][id]" value="${idFerramenta}">
    <input type="hidden" name="ferramentas[${id}][observacao]" value="${observacao}">
  `;
  tabela.appendChild(linha);

  // Atualiza legenda e checkbox de Passo-a-Passo
  atualizarNumeracaoFerramentas();
  atualizarOpcoesPasso();

  // Reset nos campos
  select.selectedIndex = 0;
  document.getElementById("obs_ferramenta").value = "";

  fecharModal("modalFerramenta");
}

// ===== Exibir descrições nos selects =====
function exibirDescricaoMaterial() {
  const select = document.getElementById("select_material");
  const desc = select.options[select.selectedIndex]?.dataset.descricao || "";
  document.getElementById("desc_material_pop").textContent = desc;
}

function exibirDescricaoFerramenta() {
  const select = document.getElementById("select_ferramenta");
  const desc = select.options[select.selectedIndex]?.dataset.descricao || "";
  document.getElementById("desc_ferramenta_pop").textContent = desc;
}

// ===== Adicionar Passo-a-Passo =====
let numeroPasso = 1;

function adicionarPasso() {
  // Agora esses IDs existem no HTML (corrigidos)
  const descricao = document.getElementById("descricao_passo").value.trim();
  const imagensInput = document.getElementById("imagens_passo");
  const arquivosInput = document.getElementById("arquivos_passo");

  if (!descricao) {
    alert("Descreva o passo.");
    return;
  }

  // Seleciona apenas checkboxes marcados
  const materiaisCheck = document.querySelectorAll("#passo_materiais input:checked");
  const ferramentasCheck = document.querySelectorAll("#passo_ferramentas input:checked");

  const materiais = [], materiaisNomes = [];
  materiaisCheck.forEach(cb => {
    materiais.push(cb.value);
    materiaisNomes.push(cb.parentElement.textContent.trim());
  });

  const ferramentas = [], ferramentasNomes = [];
  ferramentasCheck.forEach(cb => {
    ferramentas.push(cb.value);
    ferramentasNomes.push(cb.parentElement.textContent.trim());
  });

  const matTexto = materiaisNomes.length ? materiaisNomes.join(', ') : "—";
  const ferTexto = ferramentasNomes.length ? ferramentasNomes.join(', ') : "—";

  const id = Date.now();
  const tabela = document.getElementById("listaPassos");
  const form = document.querySelector("form");

  // Adiciona linha na tabela de Passos
  const linha = document.createElement("tr");
  linha.innerHTML = `
    <td>Passo ${numeroPasso}</td>
    <td>${matTexto}</td>
    <td>${ferTexto}</td>
    <td>${descricao}</td>
    <td><button type="button" onclick="this.closest('tr').remove()">🗑</button></td>
  `;
  tabela.appendChild(linha);

  // Insere campos hidden no <form> para envio
  form.insertAdjacentHTML("beforeend", `
    <input type="hidden" name="passos[${id}][descricao]" value="${descricao}">
    ${materiais.map(m => `<input type="hidden" name="passos[${id}][materiais][]" value="${m}">`).join("")}
    ${ferramentas.map(f => `<input type="hidden" name="passos[${id}][ferramentas][]" value="${f}">`).join("")}
  `);

  // Se houver imagens, clonamos o input para manter os arquivos
  if (imagensInput.files.length > 0) {
    const dtImg = new DataTransfer();
    for (const file of imagensInput.files) dtImg.items.add(file);
    const cloneImg = imagensInput.cloneNode();
    cloneImg.name = `passos_${id}[imagens][]`;
    cloneImg.files = dtImg.files;
    form.appendChild(cloneImg);
  }

  // Se houver arquivos, clonamos o input para manter os arquivos
  if (arquivosInput.files.length > 0) {
    const dtFile = new DataTransfer();
    for (const file of arquivosInput.files) dtFile.items.add(file);
    const cloneFile = arquivosInput.cloneNode();
    cloneFile.name = `passos_${id}[arquivos][]`;
    cloneFile.files = dtFile.files;
    form.appendChild(cloneFile);
  }

  numeroPasso++;

  // Limpa e desmarca tudo
  document.getElementById("descricao_passo").value = "";
  imagensInput.value = "";
  arquivosInput.value = "";
  document.querySelectorAll("#passo_materiais input").forEach(cb => cb.checked = false);
  document.querySelectorAll("#passo_ferramentas input").forEach(cb => cb.checked = false);

  fecharModal("modalPasso");
}

// Ao carregar a página, já preenche o modal de Passos
window.onload = function() {
  atualizarOpcoesPasso();
};
