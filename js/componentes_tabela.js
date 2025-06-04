document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const novoComponente = urlParams.get("novoComponente");

    if (novoComponente) {
        const dados = JSON.parse(decodeURIComponent(novoComponente));
        const tabela = document.getElementById("corpo-tabela");

        const linha = document.createElement("tr");

        const tdId = document.createElement("td");
        tdId.textContent = dados.id;

        const tdNome = document.createElement("td");
        tdNome.textContent = dados.nome;

        const tdLink = document.createElement("td");
        const link = document.createElement("a");
        link.href = dados.link;
        link.target = "_blank";
        link.textContent = "Ver Detalhes";
        tdLink.appendChild(link);

        linha.appendChild(tdId);
        linha.appendChild(tdNome);
        linha.appendChild(tdLink);

        tabela.appendChild(linha);
    }
});
