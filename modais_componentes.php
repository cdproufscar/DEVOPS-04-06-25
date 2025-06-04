<!-- Modal: Adicionar Material -->
<div id="modalMaterial" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalMaterial')">✖</button>
    <h3>Adicionar Material</h3>

    <label>Selecione o material:</label>
    <select id="select_material" onchange="exibirDescricaoMaterial()">
      <option value="">-- Escolha um material --</option>
      <?php foreach ($materiais as $mat): ?>
        <option value="<?= $mat['id_material'] ?>" data-descricao="<?= htmlspecialchars($mat['descricao_material']) ?>">
          <?= htmlspecialchars($mat['nome_material']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p id="desc_material_pop" class="descricao-mini"></p>

    <label>Quantidade:</label>
    <input type="number" id="qtd_material" min="1" value="1">

    <label>Unidade de Medida:</label>
    <select id="unidade_material">
      <option value="un">Unidade (un)</option>
      <option value="m">Metro (m)</option>
      <option value="cm">Centímetro (cm)</option>
      <option value="mm">Milímetro (mm)</option>
      <option value="g">Grama (g)</option>
      <option value="kg">Quilo (kg)</option>
      <option value="l">Litro (l)</option>
      <option value="ml">Mililitro (ml)</option>
    </select>

    <label>Observações / Comentários:</label>
    <textarea id="obs_material" placeholder="Espaço para observações adicionais sobre o material..."></textarea>

    <button type="button" onclick="adicionarMaterial()">Adicionar Material</button>
    <a href="cadastrar_material.php" target="_blank" class="btn-secundario">Cadastrar novo material externamente</a>
  </div>
</div>

<!-- Modal: Adicionar Ferramenta -->
<div id="modalFerramenta" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalFerramenta')">✖</button>
    <h3>Adicionar Ferramenta</h3>

    <label>Selecione a ferramenta:</label>
    <select id="select_ferramenta" onchange="exibirDescricaoFerramenta()">
      <option value="">-- Escolha uma ferramenta --</option>
      <?php foreach ($ferramentas as $fer): ?>
        <option value="<?= $fer['id_ferramenta'] ?>" data-descricao="<?= htmlspecialchars($fer['descricao_ferramenta']) ?>">
          <?= htmlspecialchars($fer['nome_ferramenta']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p id="desc_ferramenta_pop" class="descricao-mini"></p>

    <label>Observações / Comentários:</label>
    <textarea id="obs_ferramenta" placeholder="Espaço para observações adicionais sobre a ferramenta..."></textarea>

    <button type="button" onclick="adicionarFerramenta()">Adicionar Ferramenta</button>
    <a href="cadastrar_ferramenta.php" target="_blank" class="btn-secundario">Cadastrar nova ferramenta externamente</a>
  </div>
</div>

<!-- Modal: Adicionar Passo-a-Passo -->
<div id="modalPasso" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalPasso')">✖</button>
    <h3>Adicionar Passo a Passo</h3>

    <label>Descrição do passo:</label>
    <textarea id="descricao_passo" rows="3" placeholder="Descreva o que será feito..."></textarea>

    <label>Materiais utilizados:</label>
    <div id="passo_materiais" class="scroll-box">
      <!-- Populado dinamicamente pelo JS -->
    </div>

    <label>Ferramentas utilizadas:</label>
    <div id="passo_ferramentas" class="scroll-box">
      <!-- Populado dinamicamente pelo JS -->
    </div>

    <label>Imagens do passo:</label>
    <input type="file" id="imagens_passo" multiple accept="image/*">

    <label>Arquivos adicionais:</label>
    <input type="file" id="arquivos_passo" multiple accept=".zip,.rar,.pdf,.cad,.sql,.txt,.dwg,.mp4,.avi">

    <button type="button" onclick="adicionarPasso()">Adicionar Passo</button>
  </div>
</div>
