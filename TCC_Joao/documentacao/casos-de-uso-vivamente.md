# Diagrama de Caso de Uso - VivaMente

O sistema VivaMente possui dois atores principais: o paciente e o profissional. O paciente utiliza a plataforma para buscar profissionais, enviar formularios de atendimento, acompanhar mensagens e visualizar consultas agendadas. O profissional utiliza a plataforma para cadastrar seu perfil, receber formularios enviados pelos pacientes e criar consultas com data, horario e link da chamada.

## Atores

- **Paciente:** usuario que busca atendimento, escolhe um profissional, envia formulario e acompanha o historico.
- **Profissional:** usuario que recebe solicitacoes dos pacientes, analisa os formularios e agenda consultas.
- **Sistema:** responsavel por gerar mensagens automaticas, atualizar status e registrar informacoes no banco de dados.

## Casos de uso do paciente

- **Cadastrar-se como paciente:** permite que o usuario crie uma conta de paciente.
- **Realizar login:** permite acessar o painel do paciente.
- **Visualizar lista de profissionais:** mostra os profissionais cadastrados na plataforma.
- **Escolher profissional:** permite selecionar para qual profissional o formulario sera enviado.
- **Enviar formulario de atendimento:** registra a descricao informada pelo paciente e envia ao profissional escolhido.
- **Consultar historico de formularios:** mostra os formularios enviados e seus respectivos status.
- **Receber mensagens:** exibe notificacoes do sistema, como consultas agendadas.
- **Visualizar consultas agendadas:** mostra data, horario, profissional e status das consultas.
- **Entrar na consulta online:** permite acessar o link da chamada quando informado pelo profissional.

## Casos de uso do profissional

- **Cadastrar-se como profissional:** permite criar uma conta de profissional.
- **Informar perfil profissional:** registra dados como descricao, especialidade, valor da consulta, cidade, estado e registro profissional.
- **Realizar login:** permite acessar o painel do profissional.
- **Visualizar formularios recebidos:** mostra os formularios enviados pelos pacientes para aquele profissional.
- **Analisar solicitacao do paciente:** permite ler a descricao enviada pelo paciente.
- **Agendar consulta:** cria uma consulta para o paciente com data, horario e link da chamada.
- **Informar link da chamada:** registra o link que sera usado para o atendimento online.

## Casos de uso automaticos do sistema

- **Enviar notificacao ao paciente:** quando o profissional agenda uma consulta, o sistema cria uma mensagem no painel do paciente com dia, horario, profissional e link.
- **Atualizar status do formulario:** quando uma consulta e criada, o formulario relacionado pode ser marcado como respondido.
