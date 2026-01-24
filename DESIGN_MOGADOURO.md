# Design A Casa do Gi - Paleta Mogadouro

## Conceito Visual

O design foi inspirado na natureza de Mogadouro:
- **Céu Azul**: Tons de azul representando o céu limpo de Mogadouro
- **Verde Natureza**: Tons de verde dos campos, trilhos e natureza
- **Água**: Azul-verde inspirado nos rios e água da região
- **Terra**: Tons neutros representando a terra
- **Pureza**: Brancos e tons claros representando pureza e acolhimento

## Paleta de Cores

### 🌤️ Sky (Azul Céu)
```
sky-50:  #f0f9ff - Muito claro
sky-100: #e0f2fe
sky-200: #bae6fd
sky-300: #7dd3fc
sky-400: #38bdf8
sky-500: #0ea5e9 - Principal
sky-600: #0284c7
sky-700: #0369a1
sky-800: #075985
sky-900: #0c4a6e - Muito escuro
```

### 🌿 Nature (Verde Natureza)
```
nature-50:  #f0fdf4 - Muito claro
nature-100: #dcfce7
nature-200: #bbf7d0
nature-300: #86efac
nature-400: #4ade80
nature-500: #22c55e - Principal
nature-600: #16a34a
nature-700: #15803d
nature-800: #166534
nature-900: #14532d - Muito escuro
```

### 💧 Water (Água)
```
water-50:  #ecfeff
water-100: #cffafe
water-200: #a5f3fc
water-300: #67e8f9
water-400: #22d3ee
water-500: #06b6d4
water-600: #0891b2
water-700: #0e7490
water-800: #155e75
water-900: #164e63
```

### 🌍 Earth (Terra - Neutros)
```
earth-50:  #fafaf9
earth-100: #f5f5f4
earth-200: #e7e5e4
earth-300: #d6d3d1
earth-400: #a8a29e
earth-500: #78716c
earth-600: #57534e
earth-700: #44403c
earth-800: #292524
earth-900: #1c1917
```

### ⚪ Pure (Pureza - Brancos)
```
pure-50:  #ffffff - Branco puro
pure-100: #fefefe
pure-200: #fafafa
pure-300: #f5f5f5
pure-400: #efefef
pure-500: #e5e5e5
```

## Aplicação das Cores

### Header (Navegação)
- **Fundo**: `bg-pure-50/95` com `backdrop-blur-md` - branco translúcido
- **Logo**: Gradiente `from-sky-500 to-nature-500`
- **Links**: `text-earth-600` hover `text-sky-600`
- **Links ativos**: `text-sky-700`
- **Ícone carrinho**: Hover `text-nature-600`

### Footer
- **Fundo**: Gradiente `from-sky-900 via-sky-800 to-water-900`
- **Texto principal**: `text-pure-200`
- **Títulos**: `text-white`
- **Links**: `text-pure-300` hover `text-nature-300`
- **Ícones**: `text-nature-400`
- **Botões sociais**: `bg-sky-700/50` hover `bg-nature-500`
- **Botões reserva**: `bg-nature-600` hover `bg-nature-700`

### Flash Messages
- **Sucesso**: `bg-nature-500 text-white`
- **Erro**: `bg-red-500 text-white`
- **Aviso**: `bg-amber-500 text-white`
- **Info**: `bg-sky-600 text-white`

### Body
- **Fundo**: `bg-pure-200`
- **Texto**: `text-earth-700`

## Fontes

- **Serif (Títulos)**: Merriweather - elegante e clássica
- **Sans (Corpo)**: Poppins - moderna e legível

## Animações e Efeitos

### Animações Tailwind Customizadas
```javascript
animation: {
  'fade-in': 'fadeIn 0.6s ease-in-out',
  'slide-up': 'slideUp 0.8s ease-out',
  'float': 'float 3s ease-in-out infinite',
}
```

### Efeitos Visuais
- **Parallax**: Fundo fixo com scroll suave
- **Hover**: Transições suaves em todos os elementos interativos
- **Backdrop Blur**: Header com efeito de vidro fosco
- **Gradientes**: Uso extensivo para criar profundidade
- **Sombras**: `hover:shadow-lg` para botões importantes
- **Scale**: `hover:scale-110` em ícones sociais

## Princípios de Design

1. **Harmonia Visual**: Cores complementares (azul + verde)
2. **Contraste**: Texto escuro em fundos claros, texto claro em fundos escuros
3. **Hierarquia**: Tamanhos de fonte e pesos bem definidos
4. **Espaçamento**: Uso generoso de padding e margin
5. **Responsividade**: Mobile-first com breakpoints bem definidos
6. **Acessibilidade**: Contraste adequado (WCAG AA)
7. **Performance**: Animações suaves com GPU acceleration

## Componentes UI

### Botões
- **Primário**: `bg-sky-600 hover:bg-sky-700 text-white`
- **Secundário**: `bg-nature-600 hover:bg-nature-700 text-white`
- **Outline**: `border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white`

### Cards
- **Fundo**: `bg-white`
- **Sombra**: `shadow-md hover:shadow-xl`
- **Border**: `rounded-lg`

### Inputs
- **Border**: `border-earth-300 focus:border-sky-500`
- **Ring**: `focus:ring-sky-200`

## Próximos Passos

- [ ] Redesenhar homepage com hero section parallax
- [ ] Página de alojamento com galeria imersiva
- [ ] Página da loja com grid responsivo
- [ ] Página de atividades com cards interativos
- [ ] Atualizar cores do back office (admin)

## Inspiração

**Mogadouro**: Natureza, turismo rural, água, trilhos, ar puro, calor humano, boa comida e bebida.

O design reflete a essência de Mogadouro - um local onde o céu azul encontra os campos verdes, onde a natureza e o acolhimento se unem para criar uma experiência única e memorável.
