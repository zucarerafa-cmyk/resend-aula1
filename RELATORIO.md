Relatório do Projeto — Formulário de Contato com Resend

1. Objetivo
O projeto consiste em desenvolver um formulário de contato utilizando HTML, PHP e a API da Resend para realizar o envio de e-mails. A aplicação recebe os dados preenchidos pelo visitante, envia uma mensagem de notificação para o responsável pelo site e encaminha uma confirmação para o e-mail informado pelo visitante.

2. Tecnologias utilizadas
- HTML: estrutura do formulário de contato.
- CSS: estilização e organização visual da página.
- PHP: recebimento, validação e processamento dos dados do formulário.
- Resend API: serviço utilizado para realizar o envio dos e-mails.
- cURL: comunicação entre o PHP e a API da Resend.
- XAMPP: ambiente local utilizado para executar a aplicação.
- Visual Studio Code: editor utilizado no desenvolvimento.

3. Funcionamento da aplicação
O visitante acessa a página inicial e preenche os campos de nome, e-mail e mensagem. Ao enviar o formulário, os dados são encaminhados para o arquivo enviar.php, que verifica as informações e realiza a comunicação com a API da Resend.

Quando o envio é realizado com sucesso, a aplicação:
1. Envia um e-mail de notificação para o responsável pelo projeto.
2. Envia um e-mail de confirmação para o endereço informado pelo visitante.
3. Registra os resultados dos envios no arquivo envios.log.
4. Exibe uma mensagem informando o resultado da operação.
Os e-mails utilizam conteúdo em HTML, organizado em cabeçalho, corpo e rodapé, com estilos aplicados diretamente no código da mensagem.

4. Organização dos arquivos
Arquivo	Função
index.html	Página com o formulário de contato.
style.css	Estilos visuais do formulário.
enviar.php	Validação dos dados, envio dos e-mails e tratamento das respostas da API.
config.php	Configurações privadas da integração com a Resend, incluindo a chave de API.
config.exemplo.php	Modelo de configuração sem a chave real.
envios.log	Registro das tentativas de envio e dos erros encontrados.
.gitignore	Impede que arquivos privados e arquivos de dependências sejam incluídos no controle de versão.
.htaccess	Restringe o acesso direto aos arquivos de configuração e de log pelo servidor web.

5. Desafios desenvolvidos
Desafio 1 — Envio de e-mails com confirmação
O formulário recebe nome, e-mail e mensagem do visitante. Após a validação, o sistema envia uma notificação ao responsável pelo site e uma confirmação ao visitante.
Desafio 2 — Registro de envios
As tentativas de envio são registradas no arquivo envios.log, incluindo informações como data, destinatário, status e resultado da operação. Esse registro permite acompanhar os envios e identificar possíveis falhas.
Desafio 3 — Tratamento de erros
O código trata respostas da API e falhas de conexão. Foram considerados os seguintes casos:
- 401: problema de autenticação, como uma chave de API inválida.
- 403: acesso não autorizado à operação.
- 422: dados enviados inválidos.
- 429: limite de requisições excedido.
- Falha de rede: erro de comunicação com a API.
Para cada situação, a aplicação registra o resultado e apresenta uma mensagem correspondente ao usuário.
Desafio 4 — Modelos de e-mail em HTML
Foram criados modelos de e-mail com cabeçalho, corpo e rodapé. A formatação utiliza CSS inline para manter a apresentação visual das mensagens nos clientes de e-mail.

6. Segurança e organização
A chave de API da Resend é mantida no arquivo config.php, separado dos arquivos públicos da página. O arquivo .gitignore foi configurado para evitar o envio do arquivo de configuração real e do log para o repositório.

Também foi criado o arquivo config.exemplo.php, contendo valores fictícios para demonstrar o formato da configuração sem expor credenciais.

O arquivo .htaccess restringe o acesso direto a config.php e envios.log.

Importante: antes de compartilhar ou entregar o projeto, deve-se conferir se a chave real não está presente em arquivos, capturas de tela ou no repositório. O arquivo config.php e o arquivo envios.log não devem ser incluídos em uma entrega pública.

7. Testes realizados
Durante o desenvolvimento, foram realizados testes de envio para verificar o recebimento dos dois e-mails e o registro das operações no arquivo de log.

Também foram simuladas situações de erro para verificar as mensagens e o tratamento dos códigos HTTP previstos no desafio.

Os testes de envio bem-sucedido retornaram status HTTP 200 e geraram registros no arquivo envios.log. Os testes de erro devem ser conferidos no log e nas capturas de tela utilizadas como evidência da execução.

8. Evidências
As evidências da execução podem ser organizadas com capturas de tela dos seguintes itens:
- Formulário de contato preenchido.
- E-mail de notificação recebido pelo responsável.
- E-mail de confirmação recebido pelo visitante.
- Arquivo envios.log com os registros de envio.
- Mensagens apresentadas durante os testes de erro (401, 403, 422, 429 e falha de rede).
- Estrutura dos arquivos do projeto.

9. Conclusão
O desenvolvimento permitiu integrar um formulário HTML a uma aplicação PHP e a um serviço externo de envio de e-mails.

Também foram trabalhados conceitos de validação de dados, comunicação com APIs, tratamento de erros, registro de operações e proteção de informações sensíveis.

Com isso, o projeto reúne os principais recursos propostos para o formulário de contato e demonstra o funcionamento do envio de notificações e confirmações por e-mail.