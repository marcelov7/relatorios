# Sistema de Tema Dark/Light

## Visão Geral

O sistema de relatórios agora possui um sistema completo de alternância entre tema claro, escuro e automático (baseado na preferência do sistema operacional).

## Funcionalidades

### 🌟 Características Principais

- **Tema Claro**: Interface com cores claras e texto escuro
- **Tema Escuro**: Interface com cores escuras e texto claro
- **Tema Automático**: Segue a preferência do sistema operacional
- **Persistência**: A preferência do usuário é salva no localStorage
- **Transições Suaves**: Animações ao alternar entre temas
- **Responsivo**: Funciona perfeitamente em desktop e mobile

### 🎛️ Controles de Tema

#### Botão no Header (Simples)
- Localizado no canto superior direito
- Cicla entre os temas: Claro → Escuro → Sistema → Claro
- Ícones visuais para cada estado:
  - ☀️ Sol = Tema Claro
  - 🌙 Lua = Tema Escuro
  - 💻 Monitor = Tema Sistema

#### Menu na Sidebar (Completo)
- Localizado no footer da sidebar (desktop)
- Dropdown com opções completas
- Indicação visual do tema ativo
- Opções: Tema Claro, Tema Escuro, Sistema

## Implementação Técnica

### 📁 Estrutura de Arquivos

```
resources/js/
├── composables/
│   └── useTheme.js          # Lógica principal do tema
├── Components/
│   └── ThemeToggle.vue      # Componente de alternância
└── app.js                   # Inicialização do tema
```

### 🔧 Como Funciona

1. **Inicialização**: O tema é aplicado antes do carregamento do Vue
2. **Detecção**: Sistema detecta preferência salva ou do SO
3. **Aplicação**: Classes CSS são aplicadas ao `<html>`
4. **Persistência**: Preferência é salva no localStorage
5. **Reatividade**: Mudanças são aplicadas instantaneamente

### 🎨 Classes CSS

O sistema utiliza as classes do Tailwind CSS:

```css
/* Tema Claro (padrão) */
.bg-white
.text-gray-900
.border-gray-200

/* Tema Escuro */
.dark .bg-white -> .dark:bg-gray-800
.dark .text-gray-900 -> .dark:text-white
.dark .border-gray-200 -> .dark:border-gray-700
```

### 📱 Responsividade

- **Desktop**: Botão no header + menu na sidebar
- **Mobile**: Apenas botão no header
- **Touch**: Suporte completo a dispositivos touch

## Uso para Desenvolvedores

### 🛠️ Usando o Composable

```javascript
import { useTheme } from '@/composables/useTheme'

export default {
  setup() {
    const { isDark, theme, toggleTheme, setTheme } = useTheme()
    
    return {
      isDark,      // Boolean: true se tema escuro
      theme,       // String: 'light', 'dark', 'system'
      toggleTheme, // Function: alterna entre light/dark
      setTheme     // Function: define tema específico
    }
  }
}
```

### 🎯 Usando o Componente

```vue
<template>
  <!-- Botão simples (cicla entre temas) -->
  <ThemeToggle />
  
  <!-- Menu dropdown completo -->
  <ThemeToggle variant="dropdown" />
</template>

<script setup>
import ThemeToggle from '@/Components/ThemeToggle.vue'
</script>
```

### 🎨 Estilização com Tema

```vue
<template>
  <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Conteúdo que se adapta ao tema
  </div>
</template>
```

## Configuração Avançada

### 🔧 Personalizando Temas

Para adicionar novos temas ou modificar os existentes, edite:

1. **useTheme.js**: Lógica de aplicação
2. **ThemeToggle.vue**: Interface de controle
3. **app.js**: Inicialização

### 📊 Estados do Tema

| Estado | Valor | Comportamento |
|--------|-------|---------------|
| `light` | `'light'` | Sempre claro |
| `dark` | `'dark'` | Sempre escuro |
| `system` | `'system'` | Segue o SO |

### 🎯 Detecção do Sistema

O sistema monitora mudanças na preferência do SO:

```javascript
const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
mediaQuery.addEventListener('change', handleSystemChange)
```

## Benefícios

### 👥 Para Usuários
- **Conforto Visual**: Escolha baseada na preferência pessoal
- **Economia de Bateria**: Tema escuro em telas OLED
- **Acessibilidade**: Melhor legibilidade em diferentes condições
- **Consistência**: Segue padrões do sistema operacional

### 👨‍💻 Para Desenvolvedores
- **Reutilizável**: Composable pode ser usado em qualquer componente
- **Performático**: Mudanças aplicadas via CSS nativo
- **Manutenível**: Código organizado e bem documentado
- **Extensível**: Fácil adicionar novos temas

## Suporte

- ✅ Chrome 76+
- ✅ Firefox 67+
- ✅ Safari 12.1+
- ✅ Edge 79+
- ✅ Todos os dispositivos móveis modernos

## Próximos Passos

- [ ] Temas personalizados por usuário
- [ ] Paleta de cores customizável
- [ ] Tema de alto contraste
- [ ] Integração com configurações do usuário no backend 