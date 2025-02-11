# 🌐 Server-IHM

Servidor dedicado para interação com a rede social **IHM**, fornecendo funcionalidades essenciais para comunicação e integração dentro da plataforma.

---

## 📌 Funcionalidades

✅ Servidor de chat utilizando PHP<br>
✅ Configurações otimizadas para Apache e Docker<br>
✅ Gerenciamento de processos com Supervisor<br>
✅ Uso de variáveis de ambiente para segurança e configuração<br>
✅ Estrutura modular para expansão de funcionalidades

---

## 📂 Estrutura do Projeto

```
📁 Server-IHM
├── 📁 server           # Configurações do servidor
├── 📁 src              # Código-fonte principal
├── 📁 vendor           # Dependências do Composer
├── .env               # Configuração de variáveis de ambiente
├── .htaccess          # Configurações do Apache
├── Dockerfile         # Arquivo para build do contêiner
├── README.md          # Documentação do projeto
├── composer.json      # Dependências do projeto
├── composer.lock      # Versões das dependências instaladas
├── docker-compose.yml # Orquestração dos contêineres
├── entrypoint.sh      # Script de inicialização do contêiner
├── servidor_chat.php  # Script principal do servidor de chat
└── supervisord.conf   # Configuração do Supervisor
```

---

## 🚀 Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/alisonnRB/Server-IHM.git
   ```

2. **Acesse o diretório do projeto:**
   ```bash
   cd Server-IHM
   ```

3. **Instale as dependências do Composer:**
   ```bash
   composer install
   ```

4. **Configure o arquivo `.env`** com as credenciais e ajustes necessários.

---

## ▶️ Como Usar

### 🔹 Com Docker
Se quiser rodar a aplicação via Docker, use o comando:
```bash
docker-compose up -d
```

### 🔹 Sem Docker
Para iniciar o servidor manualmente, execute:
```bash
php servidor_chat.php
```

⚠️ **Nota:** Certifique-se de que o servidor Apache ou outro ambiente está configurado corretamente.

---

## 🤝 Contribuição

Quer contribuir? Siga estes passos:
1. Faça um fork do repositório
2. Crie uma branch para sua feature: `git checkout -b minha-feature`
3. Faça suas modificações e commit: `git commit -m "Minha nova feature"`
4. Envie para o branch original: `git push origin minha-feature`
5. Abra um pull request

---

## 📜 Licença

Este projeto está sob a Licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

📧 **Contato:** Para dúvidas ou suporte, abra uma issue ou entre em contato com o desenvolvedor.

🚀 *Happy Coding!*

