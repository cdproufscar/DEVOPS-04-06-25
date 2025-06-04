<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Assistiverse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/contato.css">
</head>
<body class="b-contato" >
    <?php include 'header_dinamico.php'; ?>
    
    <main>
        <section class="container">
            <!-- Localização -->
            <div class="contato-bloco">
                <h2 class="titulo_contato">Localização</h2>
                <p>Universidade Federal de São Carlos</p>
                <p>Rodovia Washington Luis, km 235 - São Carlos - SP - BR</p>
                <p>CEP: 13565-905</p>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3675.3410017047086!2d-47.89528728549119!3d-22.001699785479327!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c86d5e05bb7a17%3A0x9c552013d4b3d03a!2sUniversidade%20Federal%20de%20S%C3%A3o%20Carlos%20(UFSCar)!5e0!3m2!1spt-BR!2sbr!4v1694056783456!5m2!1spt-BR!2sbr" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Formulário de Contato -->
            <div class="contato-bloco">
                <h2 class="titulo_contato">Entre em Contato</h2>
                <p>Preencha o formulário abaixo ou envie um e-mail diretamente para <b>cdpro@ufscar.br</b>.</p>
                <form target="_blank" action="https://formsubmit.co/cdpro@ufscar.com" method="POST" id="f-contato" >
                    <div class="input-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required>
                    </div>

                    <div class="input-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="Seu e-mail" required>
                    </div>

                    <div class="input-group">
                        <label for="mensagem">Mensagem:</label>
                        <textarea id="mensagem" name="mensagem" placeholder="Digite sua mensagem" required></textarea>
                    </div>

                    <input type="hidden" name="_captcha" value="false" />
                    <input
                      type="hidden"
                      name="_next"
                      value="http://localhost/DEV/contato_enviado.php"
                    />

                    <button type="submit" class="btn-enviar">Enviar</button>
                </form>
            </div>

            <!-- Contato Alternativo -->
            <div class="contato-bloco">
                <h2 class="titulo_contato">Entre em Contato (opção alternativa)</h2>
                <p>Email: <a href="mailto:cdpro@ufscar.br">cdpro@ufscar.br</a></p>
                <p>Telefone: (16) 3351-8250</p>
                <p>Horário de Atendimento: Segunda a Sexta, das 8h às 17h</p>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>
