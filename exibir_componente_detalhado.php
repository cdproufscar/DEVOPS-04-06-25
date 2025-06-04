<?php
function exibirComponenteDetalhado($pdo, $componente) {
    $imagens = json_decode($componente['imagens'], true) ?? [];
    $arquivos = json_decode($componente['arquivos'], true) ?? [];

    // Materiais
    $stmtMat = $pdo->prepare("SELECT m.nome_material, cm.quantidade, cm.unidade, m.descricao_material
                              FROM componente_materiais cm
                              JOIN materiais m ON cm.id_material = m.id_material
                              WHERE cm.id_componente = ?");
    $stmtMat->execute([$componente['id_componente']]);
    $materiais = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

    // Ferramentas
    $stmtFer = $pdo->prepare("SELECT f.nome_ferramenta, cf.dimensoes, f.descricao_ferramenta
                              FROM componente_ferramentas cf
                              JOIN ferramentas f ON cf.id_ferramenta = f.id_ferramenta
                              WHERE cf.id_componente = ?");
    $stmtFer->execute([$componente['id_componente']]);
    $ferramentas = $stmtFer->fetchAll(PDO::FETCH_ASSOC);

    // Passo a passo
    $stmtPassos = $pdo->prepare("SELECT * FROM passo_a_passo WHERE id_componente = ?");
    $stmtPassos->execute([$componente['id_componente']]);
    $passos = $stmtPassos->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='componente-bloco'>";
    echo "<h3>" . htmlspecialchars($componente['nome_componente']) . "</h3>";
    echo "<p><strong>Descrição:</strong> " . nl2br(htmlspecialchars($componente['descricao'])) . "</p>";

    // Imagens
    if (!empty($imagens)) {
        echo "<div class='produto-imagens'>";
        foreach ($imagens as $img) {
            echo "<img src='" . htmlspecialchars($img) . "' class='produto-img'>";
        }
        echo "</div>";
    }

    // Arquivos
    if (!empty($arquivos)) {
        echo "<div class='midia-bloco'>";
        echo "<h4>Arquivos do Componente</h4><ul>";
        foreach ($arquivos as $arq) {
            echo "<li><a href='" . htmlspecialchars($arq) . "' target='_blank'>📂 " . basename($arq) . "</a></li>";
        }
        echo "</ul></div>";
    }

    // Materiais
    if ($materiais) {
        echo "<div class='midia-bloco'>";
        echo "<h4>Materiais Utilizados</h4><ul>";
        foreach ($materiais as $m) {
            echo "<li><strong>" . htmlspecialchars($m['nome_material']) . "</strong>: "
               . htmlspecialchars($m['quantidade']) . " " . htmlspecialchars($m['unidade'])
               . " — " . htmlspecialchars($m['descricao_material']) . "</li>";
        }
        echo "</ul></div>";
    }

    // Ferramentas
    if ($ferramentas) {
        echo "<div class='midia-bloco'>";
        echo "<h4>Ferramentas Utilizadas</h4><ul>";
        foreach ($ferramentas as $f) {
            echo "<li><strong>" . htmlspecialchars($f['nome_ferramenta']) . "</strong>: "
               . htmlspecialchars($f['dimensoes']) . " — " . htmlspecialchars($f['descricao_ferramenta']) . "</li>";
        }
        echo "</ul></div>";
    }

    // Passo a passo
    if ($passos) {
        echo "<div class='passo-bloco'>";
        echo "<h4>Passo a Passo</h4>";
        $contador = 1;
        foreach ($passos as $p) {
            echo "<p><strong>Passo {$contador}:</strong> " . nl2br(htmlspecialchars($p['descricao'])) . "</p>";
            if (!empty($p['arquivo_upload'])) {
                echo "<p class='passo-arquivo'><a href='" . htmlspecialchars($p['arquivo_upload']) . "' target='_blank'>📎 Ver Arquivo</a></p>";
            }
            echo "<hr>";
            $contador++;
        }
        echo "</div>";
    }

    echo "</div><hr>";
}
?>
