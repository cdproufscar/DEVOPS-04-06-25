<?php $componentes_temp = $_SESSION['componentes_temp'] ?? []; ?>

<div id="modalMontagem" class="modal">
  <div class="modal-overlay" onclick="fecharModal('modalMontagem')"></div>
  <div class="modal-content">
    <button type="button" class="close-modal" onclick="fecharModal('modalMontagem')">✖</button>

    <h3>Adicionar Processo de Montagem</h3>

    <label>Selecione os Componentes envolvidos:</label>
    <div class="scroll-box" id="montagem_componentes">
      <!-- Inserido via JS -->
    </div>

    <label>Descrição / Observações:</label>
    <textarea id="descricao_montagem" placeholder="Explique o que é feito neste processo de montagem..." rows="4"></textarea>

    <label>Mídias (Imagens ou Vídeos):</label>
    <input type="file" id="midias_montagem" accept="image/*,video/*" multiple>

    <label>Arquivos complementares:</label>
    <input type="file" id="arquivos_montagem" accept=".pdf,.doc,.txt,.sql,.cad,.zip,.rar" multiple>

    <button type="button" onclick="adicionarProcessoMontagem()">Adicionar Processo</button>
  </div>
</div>
