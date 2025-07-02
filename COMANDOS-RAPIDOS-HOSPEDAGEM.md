# ⚡ **COMANDOS DE UMA LINHA - HOSPEDAGEM RÁPIDA**

## 🎯 **CENÁRIOS ESPECÍFICOS**

### **🏢 HOSPEDAGEM COMPARTILHADA (Hostinger, HostGator, etc.)**
```bash
# Preparar sistema e criar ZIP para upload
composer run package-host-windows && echo "✅ ZIP criado! Faça upload do arquivo sistema-arquitetura-hospedagem.zip"
```

### **☁️ VPS/CLOUD LINUX (DigitalOcean, AWS, Vultr)**
```bash
# Deploy completo em uma linha (execute no servidor)
curl -sSL https://raw.githubusercontent.com/seu-usuario/sistema-arquitetura/main/scripts/deploy.sh | sudo bash
```

### **🪟 SERVIDOR WINDOWS (IIS)**
```powershell
# Deploy Windows IIS em PowerShell
composer run deploy-windows-ps; if ($?) { Write-Host "✅ Deploy Windows concluído!" -ForegroundColor Green }
```

### **📦 XAMPP LOCAL PARA PRODUÇÃO**
```bash
# Transformar XAMPP local em produção
composer run deploy-xampp && echo "✅ XAMPP configurado para produção!"
```

---

## 🚀 **AUTOMAÇÃO COMPLETA**

### **📤 PREPARAR E TESTAR HOSPEDAGEM:**
```bash
# Preparar + testar + empacotar
composer run prod-install && composer run deploy-test && composer run package-host-windows
```

### **💾 BACKUP ANTES DO DEPLOY:**
```bash
# Fazer backup + preparar hospedagem
composer run backup-windows && composer run package-host-windows && echo "✅ Backup criado e sistema empacotado!"
```

### **🔄 DEPLOY AUTOMÁTICO (Detecta ambiente):**
```bash
# Uma linha que resolve tudo
composer run deploy-auto && echo "✅ Deploy automático concluído!"
```

---

## 🌐 **HOSPEDAGENS ESPECÍFICAS**

### **HOSTINGER:**
```bash
# Comando específico para Hostinger
composer run package-host-windows && echo "📤 Para Hostinger: 1) Upload ZIP via File Manager 2) Extrair em public_html/ 3) Configurar MySQL via hPanel"
```

### **AWS/DIGITALOCEAN:**
```bash
# SSH + Deploy em uma linha
ssh usuario@ip "git clone https://github.com/seu-usuario/sistema-arquitetura.git && cd sistema-arquitetura && sudo ./scripts/deploy.sh"
```

### **HOSTGATOR cPanel:**
```bash
composer run package-host-windows && echo "📤 Para HostGator: 1) cPanel File Manager 2) Upload ZIP 3) Extract 4) MySQL Databases"
```

---

## 🔧 **TROUBLESHOOTING RÁPIDO**

### **🚨 SISTEMA QUEBROU? RESTAURAR:**
```bash
# Restaurar backup e redeployar
composer run backup-restore && composer run deploy-auto
```

### **🔍 VERIFICAR SAÚDE DO SISTEMA:**
```bash
# Health check completo
composer run health-check-windows && echo "✅ Sistema verificado!"
```

### **🧹 LIMPAR E RECONSTRUIR:**
```bash
# Limpar cache + reinstalar + testar
composer run clear-cache-windows && composer run prod-install && composer run deploy-test
```

---

## 📋 **CHECKLISTS DE UMA LINHA**

### **✅ PRÉ-DEPLOY:**
```bash
composer run prod-install && composer run deploy-test && echo "✅ Sistema testado e pronto!"
```

### **✅ PÓS-DEPLOY:**
```bash
composer run health-check-windows && echo "✅ Deploy verificado!"
```

### **✅ SECURITY CHECK:**
```bash
composer run security-check && echo "✅ Auditoria de segurança concluída!"
```

---

## 🎯 **COMANDOS POR SITUAÇÃO**

### **🆕 PRIMEIRA VEZ (Sistema novo):**
```bash
composer install && composer run deploy-test && composer run package-host-windows
```

### **🔄 ATUALIZAÇÃO (Sistema existente):**
```bash
composer run backup-windows && composer run prod-update && composer run package-host-windows
```

### **🚨 EMERGÊNCIA (Sistema fora do ar):**
```bash
composer run health-check-windows && composer run deploy-auto
```

### **📊 MONITORAMENTO:**
```bash
composer run health-check-windows && echo "Sistema monitorado em $(date)"
```

---

## 🌟 **SUPER COMANDO (Faz tudo):**

```bash
# O comando definitivo que prepara sistema para qualquer hospedagem
composer run prod-install && composer run deploy-test && composer run backup-windows && composer run package-host-windows && echo "🚀 SISTEMA COMPLETAMENTE PRONTO PARA HOSPEDAGEM! Arquivo: sistema-arquitetura-hospedagem.zip"
```

---

## 📱 **COMANDOS MOBILE-FRIENDLY**

### **Hostinger Mobile:**
```bash
composer run package-host-windows
# Upload via Hostinger App → File Manager → Extract ZIP
```

### **cPanel Mobile:**
```bash
composer run package-host-windows  
# cPanel App → File Manager → Upload → Extract
```

---

## 🎮 **COMANDOS PARA DESENVOLVEDORES**

### **🔥 Deploy Ninja (Ultra-rápido):**
```bash
composer run deploy-auto && echo "⚡ Ninja deploy complete!"
```

### **🛡️ Modo Seguro:**
```bash
composer run backup-windows && composer run security-check && composer run deploy-test && echo "🛡️ Deploy seguro!"
```

### **🚀 Modo Produção:**
```bash
composer run prod-install && composer run deploy-auto && echo "🚀 Produção ativada!"
```

---

**💡 DICA: Salve este arquivo como referência rápida!**

**Comando mais usado:**
```bash
composer run package-host-windows
```

**Este comando cria o ZIP pronto para upload em qualquer hospedagem! 🎯**
