# 🧪 Guia de Teste do Sistema de Relatórios

## 👥 **Usuários Criados para Teste**

### 🔑 **Credenciais de Acesso:**
- **Admin**: `admin@sistema.com` / `123456`
- **Teste**: `teste@sistema.com` / `123456`  
- **Operador**: `operador@sistema.com` / `123456`

## 🚀 **Como Testar**

### 1. **Acesso ao Sistema**
```bash
# Servidor rodando em:
http://localhost:8001
```

### 2. **Login**
- Acesse: `http://localhost:8001/login`
- Use qualquer uma das credenciais acima

### 3. **Funcionalidades para Testar**

#### 📍 **Gestão de Locais**
- ✅ Listar locais existentes
- ✅ Criar novo local
- ✅ Editar local existente
- ✅ Filtros de busca

#### 🔧 **Gestão de Equipamentos**
- ✅ Listar equipamentos por local
- ✅ Criar novo equipamento
- ✅ Editar equipamento existente
- ✅ Filtros avançados

#### 📊 **Relatórios (NOVAS FUNCIONALIDADES)**
- ✅ **Slider de Progresso Animado**
  - Slider visual com gradiente de cores (vermelho → amarelo → verde)
  - Animações suaves de 300ms
  - Hover effects com escala 1.1x no thumb
  - Marcadores de progresso (0%, 25%, 50%, 75%, 100%)

- ✅ **Status Dinâmico**
  - **0%** = "Aberta" (vermelho)
  - **1-99%** = "Em Andamento" (amarelo)
  - **100%** = "Concluída" (verde)
  - Campo readonly com cores dinâmicas e indicador circular
  - Tooltip explicativo sobre atualização automática

- ✅ **Upload de Múltiplas Imagens**
  - Drag & drop ou clique para selecionar
  - Preview em grid responsivo (2 colunas mobile, 4 desktop)
  - Exclusão individual com hover effects
  - Validação: máximo 10MB por imagem, tipos aceitos (PNG, JPG, GIF)
  - Suporte a múltiplas imagens simultâneas

#### 🎨 **Tema Dark/Light**
- ✅ Alternar entre temas
- ✅ Verificar consistência de cores
- ✅ Testar em diferentes telas

#### 🎯 **Seleção de Múltiplos Equipamentos**
- **NOVA FUNCIONALIDADE**: Checkboxes para selecionar múltiplos equipamentos
- Lista filtrada por local selecionado
- Scroll vertical para muitos equipamentos
- Informações detalhadas: TAG, nome, tipo, status
- Contador de equipamentos selecionados
- Hover effects e transições suaves

#### 🎯 **Gestão de Imagens na Edição**
- Preview e exclusão de imagens existentes
- Upload adicional sem perder imagens atuais
- Sincronização entre imagens mantidas e novas

## 🧩 **Cenários de Teste Específicos**

### **Cenário 1: Criação de Relatório Completo**
1. Acesse "Relatórios" → "Novo"
2. Preencha todos os campos obrigatórios
3. Selecione um local (equipamentos carregam automaticamente)
4. Ajuste o progresso com o slider
5. Observe a mudança automática do status
6. Adicione 2-3 imagens
7. Remova uma imagem
8. Salve o relatório

### **Cenário 2: Edição de Relatório**
1. Acesse um relatório existente
2. Clique em "Editar"
3. Modifique o progresso
4. Adicione novas imagens
5. Remova uma imagem existente
6. Salve as alterações

### **Cenário 3: Teste de Responsividade**
1. Teste em diferentes tamanhos de tela
2. Verifique o grid de imagens
3. Teste os filtros em mobile
4. Verifique a navegação

### **Cenário 4: Teste de Múltiplos Equipamentos**
1. **Criar Novo Relatório**:
   - Acesse: http://localhost:8001/relatorios/create
   - Preencha: Título, Setor, Atividade, Data
   - Selecione um local (ex: "Produção A")
   - Observe que a lista de equipamentos aparece
   - Selecione múltiplos equipamentos usando checkboxes
   - Verifique o contador de equipamentos selecionados
   - Preencha o campo "Detalhes" (obrigatório)
   - Ajuste o progresso e veja o status mudar
   - Adicione imagens se desejar
   - Salve o relatório

2. **Editar Relatório**:
   - Acesse um relatório existente para edição
   - Altere o local e observe que os equipamentos são recarregados
   - Mantenha alguns equipamentos e adicione outros
   - Modifique os detalhes
   - Salve as alterações (deve funcionar sem erro)

3. **Visualizar Relatório**:
   - Acesse a visualização de um relatório
   - Veja todos os equipamentos em cards
   - Observe as imagens em grid
   - Clique em uma imagem para abrir o modal
   - Navegue entre as imagens no modal
   - Leia os detalhes do relatório

### **Cenário 5: Teste de Progresso e Status**
1. **Slider Animado**:
   - Mova o slider de progresso
   - Observe as animações suaves
   - Teste hover effects no thumb
   - Verifique marcadores de progresso

2. **Status Dinâmico**:
   - Mude o progresso para 0% → Status = "Aberta"
   - Mude para 50% → Status = "Em Andamento"
   - Mude para 100% → Status = "Concluída"
   - Observe cores e indicadores visuais

### **Cenário 6: Teste de Upload de Imagens**
1. **Upload Múltiplo**:
   - Arraste múltiplas imagens para a área de upload
   - Ou clique e selecione várias imagens
   - Observe previews em grid responsivo
   - Teste exclusão individual

2. **Validação**:
   - Tente upload de arquivo > 10MB
   - Tente upload de arquivo não-imagem
   - Verifique mensagens de erro

### **Cenário 7: Teste de Responsividade**
1. **Mobile**:
   - Redimensione janela para mobile
   - Verifique grid de equipamentos (scroll vertical)
   - Teste grid de imagens (2 colunas)
   - Verifique slider de progresso

2. **Desktop**:
   - Teste em tela grande
   - Verifique grid de imagens (4 colunas)
   - Teste hover effects

## 🔍 **Pontos de Atenção**

### **Funcionalidades Críticas:**
- [ ] Slider funciona suavemente
- [ ] Status muda automaticamente
- [ ] Upload de imagens funciona
- [ ] Preview das imagens aparece
- [ ] Remoção de imagens funciona
- [ ] Formulário salva corretamente
- [ ] Validações client/server funcionam

### **Performance:**
- [ ] Carregamento rápido de páginas
- [ ] Transições suaves
- [ ] Responsividade fluida
- [ ] Sem erros no console

### **UX/UI:**
- [ ] Interface intuitiva
- [ ] Cores consistentes
- [ ] Feedback visual claro
- [ ] Mensagens de erro/sucesso

## 🐛 **Problemas Conhecidos**
- Nenhum problema conhecido no momento

## 📝 **Dados de Teste Disponíveis**
- **8 Locais** pré-cadastrados
- **15 Equipamentos** distribuídos
- **Seeders** executados com sucesso

## 🔧 **Comandos Úteis**
```bash
# Recriar banco de dados
php artisan migrate:fresh --seed

# Apenas usuários admin
php artisan db:seed --class=AdminUserSeeder

# Servidor de desenvolvimento
php artisan serve --port=8001

# Compilar assets
npm run build

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Executar migrations
php artisan migrate

# Executar seeders
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=RelatorioSeeder
```

## 🎯 **Próximos Testes**
1. Teste de carga com múltiplos usuários
2. Teste de upload de imagens grandes
3. Teste de compatibilidade entre browsers
4. Teste de performance em mobile 

## 🎯 **Próximos Testes**
1. Teste de carga com múltiplos usuários
2. Teste de upload de imagens grandes
3. Teste de compatibilidade entre browsers
4. Teste de performance em mobile 