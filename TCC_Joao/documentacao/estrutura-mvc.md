# Estrutura do projeto VivaMente

O projeto foi organizado no padrao MVC.

## Pastas principais

- `config/`: configuracoes gerais do sistema e conexao com o banco.
- `controller/`: regras que recebem as acoes do usuario e chamam os models.
- `model/dao/`: classes responsaveis por acessar o banco de dados.
- `model/dto/`: objetos usados para transportar dados entre camadas.
- `view/`: telas exibidas para o usuario.
- `view/assets/`: arquivos visuais usados pelas telas, como CSS.
- `documentacao/`: arquivos de banco, diagramas e documentos do projeto.
- `documentacao/legado/`: arquivos antigos guardados apenas como historico.

## Entrada do sistema

O arquivo `index.php` na raiz e o roteador principal. Ele recebe a rota pela URL e carrega a controller ou view correta.

Exemplos:

- `index.php?rota=home`
- `index.php?rota=login`
- `index.php?rota=cadastro-paciente`
- `index.php?rota=cadastro-profissional`
- `index.php?rota=lista-profissionais`
- `index.php?rota=painel-paciente`
- `index.php?rota=painel-profissional`

O arquivo `.htaccess` mantem compatibilidade com URLs antigas, como `login.html` e `cadastroProfissional.html`, redirecionando para as rotas novas.
