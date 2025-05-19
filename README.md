# 🧾 Mini ERP - Teste Montink

Este projeto é um **mini ERP** desenvolvido como parte de um **teste técnico para a empresa Montink**.  
A aplicação foi construída utilizando **PHP 8.2**, **Apache** e **MySQL**, com suporte ao envio de e-mails por **PHPMailer**, Bootstrap no Front-End para estilizar as páginas e arquitetura MVC.  

Recomenda-se fortemente a utilização de **Docker** para garantir a execução correta e sem conflitos de ambiente.

---

## 📦 Tecnologias utilizadas

- PHP 8.2  
- Apache  
- MySQL  
- PHPMailer  
- Docker & Docker Compose

---

## 🚀 Como executar o projeto

### ✅ Rodando com Docker (recomendado)

1. Certifique-se de ter o **Docker** e o **Docker Compose** instalados.
2. Clone este repositório:
   ```bash
   git clone https://github.com/seu-usuario/mini-erp-montink.git
   cd mini-erp-montink
   ```
3. Execute os containers:
   ```bash
   docker compose up -d
   ```
4. Acesse o projeto em:  
   [http://localhost](http://localhost)

> O banco de dados será inicializado automaticamente com o script `db.sql`.

---

### ⚠️ Rodando sem Docker

Caso deseje rodar localmente sem Docker:

- Utilize PHP 8.2 para garantir compatibilidade.
- Instale o [Composer](https://getcomposer.org) caso não tenha instalado.
- Execute o comando abaixo para instalar as dependências:
  ```bash
  composer install
  ```

---

## ⚙️ Configuração do ambiente

As variáveis de ambiente estão definidas no arquivo:

```
config/env.php
```

Este arquivo define:

- **Conexão com o banco de dados**
- **Credenciais SMTP (PHPMailer)**

### Exemplo de configuração:

```php
// Banco de dados
define('DB_HOST', 'mysql');
define('DB_NAME', 'erp');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// PHPMailer
define('MAILER_HOST', 'sandbox.smtp.mailtrap.io');
define('MAILER_SMTP_AUTH', true);
define('MAILER_PORT', 2525);
define('MAILER_USERNAME', 'usuario_mailtrap');
define('MAILER_PASSWD', 'senha_mailtrap');
define('MAILER_SENDER_EMAIL', 'from@example.com');
define('MAILER_SENDER_NAME', 'ERP Montink');
```

> ⚠️ Se não estiver usando Docker, ajuste `DB_HOST` para `127.0.0.1` ou `localhost` conforme sua configuração local.

---

## 📧 Envio de e-mails

A aplicação utiliza **PHPMailer** para enviar e-mails de confirmação de pedido após uma compra.

Você pode usar **qualquer servidor SMTP**, como Gmail, SendGrid, Amazon SES etc., desde que ajuste as credenciais corretamente.  
No teste foi utilizado o **Mailtrap**, mas as configurações seguem o padrão SMTP universal.

---

```

## 🙋‍♂️ Autor

Feito com dedicação para o processo seletivo da Montink.  
Desenvolvedor: **Luiz Felipe**

---

## 📃 Licença

Este projeto é apenas para fins educacionais e de avaliação técnica.

```

## 📹 Demonstração

Confira o vídeo de apresentação do projeto:  
➡️ [Clique aqui para assistir no Google Drive](https://drive.google.com/file/d/1wCZQtQtAhtn4JtVaL_TTlQbtLzMlXqaw/view?usp=sharing)
